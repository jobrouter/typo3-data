<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data;

use JobRouter\AddOn\Typo3Base\Widgets\TransferReportWidget;
use JobRouter\AddOn\Typo3Base\Widgets\TransferStatusWidget;
use JobRouter\AddOn\Typo3Data\EventListener\DateFormatter;
use JobRouter\AddOn\Typo3Data\EventListener\DateTimeFormatter;
use JobRouter\AddOn\Typo3Data\EventListener\DecimalFormatter;
use JobRouter\AddOn\Typo3Data\EventListener\IntegerFormatter;
use JobRouter\AddOn\Typo3Data\EventListener\ToolbarItemProvider;
use JobRouter\AddOn\Typo3Data\Widgets\Provider\TransferReportDataProvider;
use JobRouter\AddOn\Typo3Data\Widgets\Provider\TransferStatusDataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Dashboard\Dashboard;

return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder): void {
    $services = $configurator->services();
    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services
        ->load('JobRouter\AddOn\Typo3Data\\', '../Classes/*')
        ->exclude('../Classes/{Domain/Dto,Domain/Entity,Exception,Extension.php,Hooks,UserFunctions}');

    $services
        ->set(ToolbarItemProvider::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-data/toolbar-item-provider',
            ],
        );

    $services
        ->set(IntegerFormatter::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-data/integer-formatter',
            ],
        );

    $services
        ->set(DecimalFormatter::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-data/decimal-formatter',
            ],
        );

    $services
        ->set(DateFormatter::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-data/date-formatter',
            ],
        );

    $services
        ->set(DateTimeFormatter::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-data/datetime-formatter',
            ],
        );

    if ($containerBuilder->hasDefinition(Dashboard::class)) {
        $services
            ->set('dashboard.widget.jobrouter.typo3_data.statusOfDataTransmissions')
            ->class(TransferStatusWidget::class)
            ->arg('$dataProvider', new Reference(TransferStatusDataProvider::class))
            ->arg(
                '$options',
                [
                    'refreshAvailable' => true,
                ],
            )
            ->tag('dashboard.widget', [
                'identifier' => 'jobrouter_data.statusOfDataTransmissions',
                'groupNames' => 'jobrouter',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfDataTransmissions.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfDataTransmissions.description',
                'iconIdentifier' => 'content-widget-number',
                'height' => 'small',
            ]);

        $services
            ->set('dashboard.widget.jobrouter.typo3_data.transferReport')
            ->class(TransferReportWidget::class)
            ->arg('$dataProvider', new Reference(TransferReportDataProvider::class))
            ->arg(
                '$options',
                [
                    'refreshAvailable' => true,
                ],
            )
            ->tag('dashboard.widget', [
                'identifier' => 'jobrouter_data.transferReport',
                'groupNames' => 'jobrouter',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.transferReport.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.transferReport.description',
                'iconIdentifier' => 'content-widget-table',
                'height' => 'medium',
                'width' => 'large',
            ]);
    }
};
