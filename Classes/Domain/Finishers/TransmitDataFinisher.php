<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Finishers;

use JobRouter\AddOn\Typo3Base\Domain\Finishers\AbstractTransferFinisher;
use JobRouter\AddOn\Typo3Base\Domain\Preparers\FormFieldValuesPreparer;
use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;
use JobRouter\AddOn\Typo3Data\Domain\Demand\TableDemand;
use JobRouter\AddOn\Typo3Data\Domain\Demand\TableDemandFactory;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Column;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Exception\InvalidFieldTypeException;
use JobRouter\AddOn\Typo3Data\Exception\MissingColumnException;
use JobRouter\AddOn\Typo3Data\Exception\MissingFinisherOptionException;
use JobRouter\AddOn\Typo3Data\Exception\TableNotAvailableException;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use JobRouter\AddOn\Typo3Data\Transfer\Preparer;

/**
 * @internal
 */
final class TransmitDataFinisher extends AbstractTransferFinisher
{
    public function __construct(
        private readonly Preparer $preparer,
        private readonly TableDemandFactory $tableDemandFactory,
        private readonly TableRepository $tableRepository,
    ) {}

    protected function process(): void
    {
        $tableDemand = $this->getTable();
        $data = $this->prepareData($tableDemand);
        $this->preparer->store($tableDemand->uid, $this->correlationId, \json_encode($data, \JSON_THROW_ON_ERROR));
    }

    private function getTable(): TableDemand
    {
        $handle = $this->parseOption('handle');
        if (! \is_string($handle) || $handle === '') {
            $message = \sprintf(
                'Table handle in TransmitDataFinisher of form with identifier "%s" is not defined correctly.',
                $this->getFormIdentifier(),
            );

            throw new MissingFinisherOptionException($message, 1601728021);
        }

        try {
            $table = $this->tableDemandFactory->create($this->tableRepository->findByHandle($handle));
        } catch (TableNotFoundException) {
            throw new TableNotAvailableException(
                \sprintf(
                    'Table with handle "%s" is not available, defined in form with identifier "%s"',
                    $handle,
                    $this->getFormIdentifier(),
                ),
                1601728085,
            );
        }

        return $table;
    }

    /**
     * @return mixed[]
     */
    private function prepareData(TableDemand $tableDemand): array
    {
        if (! isset($this->options['columns'])) {
            return [];
        }
        if (! \is_array($this->options['columns'])) {
            return [];
        }
        $formValues = (new FormFieldValuesPreparer())->prepareForSubstitution(
            $this->finisherContext->getFormRuntime()->getFormDefinition()->getElements(),
            $this->finisherContext->getFormValues(),
        );

        $definedTableColumns = $this->getTableColumns($tableDemand);
        $data = [];
        foreach ($this->options['columns'] as $column => $value) {
            if (! \array_key_exists($column, $definedTableColumns)) {
                throw new MissingColumnException(
                    \sprintf(
                        'Column "%s" is assigned in form with identifier "%s" but not defined in table link "%s"',
                        $column,
                        $this->getFormIdentifier(),
                        $tableDemand->name,
                    ),
                    1601736690,
                );
            }

            $value = $this->variableResolver->resolve(
                FieldType::from($definedTableColumns[$column]->type),
                $value,
            );

            $value = $this->resolveFormFields($formValues, (string)$value);

            $data[$column] = $this->considerTypeForFieldValue(
                $value,
                FieldType::from($definedTableColumns[$column]->type),
                $definedTableColumns[$column]->fieldSize,
            );
        }

        return $data;
    }

    /**
     * @return array<string, Column>
     */
    private function getTableColumns(TableDemand $table): array
    {
        $columns = $table->columns;

        $tableFields = [];
        foreach ($columns as $column) {
            $tableFields[$column->name] = $column;
        }

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
                1601884157,
            ),
            FieldType::Attachment => throw new InvalidFieldTypeException(
                'The field type "Attachment" cannot be used in the form finisher',
                1672405347,
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
