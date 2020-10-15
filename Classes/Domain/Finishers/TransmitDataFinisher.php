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
use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\InvalidFieldTypeException;
use Brotkrueml\JobRouterData\Exception\MissingColumnException;
use Brotkrueml\JobRouterData\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterData\Exception\TableNotAvailableException;
use Brotkrueml\JobRouterData\Transfer\Preparer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @internal
 */
final class TransmitDataFinisher extends AbstractTransferFinisher implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var Preparer */
    private $preparer;

    /** @var TableRepository */
    private $tableRepository;

    /** @var Transfer */
    private $transfer;

    /** @var Table|null */
    private $table;

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
        $this->determineTable($this->parseOption('handle'));
        $data = $this->prepareData();
        $this->preparer->store($this->table->getUid(), $this->transferIdentifier, \json_encode($data));
    }

    private function determineTable(?string $handle): void
    {
        if (empty($handle)) {
            $message = \sprintf(
                'Table handle in TransmitDataFinisher of form with identifier "%s" is not defined.',
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new MissingFinisherOptionException($message, 1601728021);
        }

        $this->table = $this->tableRepository->findOneByHandle($handle);

        if (empty($this->table)) {
            $message = \sprintf(
                'Table with handle "%s" is not available, defined in form with identifier "%s"',
                $handle,
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new TableNotAvailableException($message, 1601728085);
        }
    }

    private function prepareData(): array
    {
        if (!isset($this->options['columns']) || !\is_array($this->options['columns'])) {
            return [];
        }

        $formValues = (new FormFieldValuesPreparer())->prepareForSubstitution(
            $this->finisherContext->getFormRuntime()->getFormDefinition()->getElements(),
            $this->finisherContext->getFormValues()
        );

        $definedTableColumns = $this->getTableColumns();
        $data = [];
        foreach ($this->options['columns'] as $column => $value) {
            if (!\array_key_exists($column, $definedTableColumns)) {
                throw new MissingColumnException(
                    \sprintf(
                        'Column "%s" is assigned in form with identifier "%s" but not defined in table link "%s"',
                        $column,
                        $this->getFormIdentifier(),
                        $this->table->getName()
                    ),
                    1601736690
                );
            }

            $value = $this->variableResolver->resolve(
                $definedTableColumns[$column]->getType(),
                $value
            );

            $value = $this->resolveFormFields($formValues, $value);

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
    private function getTableColumns(): array
    {
        /** @var Column[] $fields */
        $columns = $this->table->getColumns();

        $tableFields = [];
        foreach ($columns as $column) {
            $tableFields[$column->getName()] = $column;
        }

        return $tableFields;
    }

    private function considerTypeForFieldValue($value, int $type, int $fieldSize)
    {
        switch ($type) {
            case FieldTypeEnumeration::TEXT:
                $value = (string)$value;

                if ($fieldSize) {
                    $value = \substr($value, 0, $fieldSize);
                }

                return $value;
            case FieldTypeEnumeration::INTEGER:
                return $value === '' ? '' : (int)$value;
            case FieldTypeEnumeration::DECIMAL:
                return $value === '' ? '' : (float)$value;
            case FieldTypeEnumeration::DATE:
            case FieldTypeEnumeration::DATETIME:
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
