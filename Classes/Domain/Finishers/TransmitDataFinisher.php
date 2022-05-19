<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Finishers;

use Brotkrueml\JobRouterBase\Domain\Finishers\AbstractTransferFinisher;
use Brotkrueml\JobRouterBase\Domain\Preparers\FormFieldValuesPreparer;
use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\InvalidFieldTypeException;
use Brotkrueml\JobRouterData\Exception\MissingColumnException;
use Brotkrueml\JobRouterData\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterData\Exception\TableNotAvailableException;
use Brotkrueml\JobRouterData\Transfer\Preparer;

/**
 * @internal
 */
final class TransmitDataFinisher extends AbstractTransferFinisher
{
    /**
     * @var Preparer
     * @noRector
     */
    private $preparer;

    /**
     * @var TableRepository
     * @noRector
     */
    private $tableRepository;

    public function injectPreparer(Preparer $preparer): void
    {
        $this->preparer = $preparer;
    }

    public function injectTableRepository(TableRepository $tableRepository): void
    {
        $this->tableRepository = $tableRepository;
    }

    protected function process(): void
    {
        $table = $this->getTable();
        $data = $this->prepareData($table);
        $this->preparer->store((int)$table->getUid(), $this->correlationId, \json_encode($data, \JSON_THROW_ON_ERROR));
    }

    private function getTable(): Table
    {
        $handle = $this->parseOption('handle');
        if (! \is_string($handle) || $handle === '') {
            $message = \sprintf(
                'Table handle in TransmitDataFinisher of form with identifier "%s" is not defined correctly.',
                $this->getFormIdentifier()
            );

            throw new MissingFinisherOptionException($message, 1601728021);
        }

        $table = $this->tableRepository->findOneByHandle($handle);
        if (! $table instanceof Table) {
            $message = \sprintf(
                'Table with handle "%s" is not available, defined in form with identifier "%s"',
                $handle,
                $this->getFormIdentifier()
            );

            throw new TableNotAvailableException($message, 1601728085);
        }

        return $table;
    }

    /**
     * @return mixed[]
     */
    private function prepareData(Table $table): array
    {
        if (! isset($this->options['columns'])) {
            return [];
        }
        if (! \is_array($this->options['columns'])) {
            return [];
        }
        $formValues = (new FormFieldValuesPreparer())->prepareForSubstitution(
            $this->finisherContext->getFormRuntime()->getFormDefinition()->getElements(),
            $this->finisherContext->getFormValues()
        );

        $definedTableColumns = $this->getTableColumns($table);
        $data = [];
        foreach ($this->options['columns'] as $column => $value) {
            if (! \array_key_exists($column, $definedTableColumns)) {
                throw new MissingColumnException(
                    \sprintf(
                        'Column "%s" is assigned in form with identifier "%s" but not defined in table link "%s"',
                        $column,
                        $this->getFormIdentifier(),
                        $table->getName()
                    ),
                    1601736690
                );
            }

            $value = $this->variableResolver->resolve(
                $definedTableColumns[$column]->getType(),
                $value
            );

            $value = $this->resolveFormFields($formValues, (string)$value);

            $data[$column] = $this->considerTypeForFieldValue(
                $value,
                $definedTableColumns[$column]->getType(),
                $definedTableColumns[$column]->getFieldSize()
            );
        }

        return $data;
    }

    /**
     * @return Column[]
     */
    private function getTableColumns(Table $table): array
    {
        $columns = $table->getColumns();

        $tableFields = [];
        foreach ($columns as $column) {
            $tableFields[$column->getName()] = $column;
        }

        // @phpstan-ignore-next-line
        return $tableFields;
    }

    /**
     * @param mixed $value
     * @return string|int|float
     */
    private function considerTypeForFieldValue($value, int $type, int $fieldSize)
    {
        if ($type === FieldTypeEnumeration::TEXT) {
            $value = (string)$value;

            if ($fieldSize !== 0) {
                $value = \mb_substr($value, 0, $fieldSize);
            }

            return $value;
        }

        if ($type === FieldTypeEnumeration::INTEGER) {
            return $value === '' ? '' : (int)$value;
        }

        if ($type === FieldTypeEnumeration::DECIMAL) {
            return $value === '' ? '' : (float)$value;
        }

        if ($type === FieldTypeEnumeration::DATE || $type === FieldTypeEnumeration::DATETIME) {
            throw new InvalidFieldTypeException(
                \sprintf('The field type "%d" is not implemented in the form finisher yet', $type),
                1601884157
            );
        }

        throw new InvalidFieldTypeException(
            \sprintf('The field type "%d" is invalid', $type),
            1601728329
        );
    }
}
