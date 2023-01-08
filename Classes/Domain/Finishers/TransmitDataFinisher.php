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
use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterData\Domain\Entity\Column;
use Brotkrueml\JobRouterData\Domain\Entity\Table;
use Brotkrueml\JobRouterData\Domain\Hydrator\TableColumnsHydrator;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\InvalidFieldTypeException;
use Brotkrueml\JobRouterData\Exception\MissingColumnException;
use Brotkrueml\JobRouterData\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterData\Exception\TableNotAvailableException;
use Brotkrueml\JobRouterData\Exception\TableNotFoundException;
use Brotkrueml\JobRouterData\Transfer\Preparer;

/**
 * @internal
 */
final class TransmitDataFinisher extends AbstractTransferFinisher
{
    /**
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        private readonly Preparer $preparer,
        private readonly TableColumnsHydrator $tableColumnsHydrator,
        private readonly TableRepository $tableRepository
    ) {
    }

    protected function process(): void
    {
        $table = $this->getTable();
        $data = $this->prepareData($table);
        $this->preparer->store($table->uid, $this->correlationId, \json_encode($data, \JSON_THROW_ON_ERROR));
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

        try {
            $table = $this->tableColumnsHydrator->hydrate($this->tableRepository->findByHandle($handle));
        } catch (TableNotFoundException) {
            throw new TableNotAvailableException(
                \sprintf(
                    'Table with handle "%s" is not available, defined in form with identifier "%s"',
                    $handle,
                    $this->getFormIdentifier()
                ),
                1601728085
            );
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
                        $table->name
                    ),
                    1601736690
                );
            }

            $value = $this->variableResolver->resolve(
                FieldType::from($definedTableColumns[$column]->type),
                $value
            );

            $value = $this->resolveFormFields($formValues, (string)$value);

            $data[$column] = $this->considerTypeForFieldValue(
                $value,
                FieldType::from($definedTableColumns[$column]->type),
                $definedTableColumns[$column]->fieldSize
            );
        }

        return $data;
    }

    /**
     * @return array<string, Column>
     */
    private function getTableColumns(Table $table): array
    {
        /** @var Column[] $columns */
        $columns = $table->columns;

        $tableFields = [];
        foreach ($columns as $column) {
            $tableFields[$column->name] = $column;
        }

        // @phpstan-ignore-next-line Use another value object over array with string-keys and objects, array<string, ValueObject>
        return $tableFields;
    }

    private function considerTypeForFieldValue(mixed $value, FieldType $type, int $fieldSize): string|int|float
    {
        return match ($type) {
            FieldType::Text => $this->cutStringValueToLength((string)$value, $fieldSize),
            FieldType::Integer => $value === '' ? '' : (int)$value,
            FieldType::Decimal => $value === '' ? '' : (float)$value,
            FieldType::Date,
            FieldType::DateTime => throw new InvalidFieldTypeException(
                \sprintf('The field type "%d" is not implemented in the form finisher', $type->name),
                1601884157
            ),
            FieldType::Attachment => throw new InvalidFieldTypeException(
                'The field type "Attachment" cannot be used in the form finisher',
                1672405347
            ),
        };
    }

    private function cutStringValueToLength(string $value, int $fieldSize): string
    {
        if ($fieldSize !== 0) {
            return \mb_substr($value, 0, $fieldSize);
        }

        return $value;
    }
}
