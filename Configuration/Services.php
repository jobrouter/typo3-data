<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData;

use Brotkrueml\JobRouterBase\Widgets\TransferReportWidget;
use Brotkrueml\JobRouterBase\Widgets\TransferStatusWidget;
use Brotkrueml\JobRouterData\Widgets\Provider\TransferReportDataProvider;
use Brotkrueml\JobRouterData\Widgets\Provider\TransferStatusDataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Dashboard\Dashboard;

return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder): void {
    if (! $containerBuilder->hasDefinition(Dashboard::class)) {
        return;
    }

    $services = $configurator->services();

    $services
        ->set('dashboard.widget.brotkrueml.jobrouter_data.statusOfDataTransmissions')
        ->class(TransferStatusWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
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
        ->set('dashboard.widget.brotkrueml.jobrouter_data.transferReport')
        ->class(TransferReportWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
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
};
