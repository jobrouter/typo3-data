<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\UserFunctions\FormEngine;

use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Table\TableProvider;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @internal
 */
#[Autoconfigure(public: true)]
final class CustomTables
{
    public function __construct(
        private readonly TableProvider $tableProvider,
        private readonly TableRepository $tableRepository,
    ) {}

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    public function getTables(array $config): array
    {
        $alreadyAssignedTables = $this->tableRepository->findAssignedCustomTables();
        $alreadyAssignedTables = \array_diff(
            $alreadyAssignedTables,
            [$config['row']['custom_table']],
        );

        $tables = [];
        foreach ($this->tableProvider->getCustomTables() as $tableName) {
            if (\in_array($tableName, $alreadyAssignedTables, true)) {
                continue;
            }

            $tables[] = [$tableName, $tableName];
        }

        $config['items'] = \array_merge($config['items'], $tables);

        return $config;
    }
}
