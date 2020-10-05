<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData;

use Brotkrueml\JobRouterBase\Widgets\TransferStatusWidget;
use Brotkrueml\JobRouterData\Widgets\Provider\TransferStatusDataProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

return function (ContainerConfigurator $configurator): void {
    if (!ExtensionManagementUtility::isLoaded('dashboard')) {
        return;
    }

    $services = $configurator->services();

    $services
        ->set('dashboard.widget.brotkrueml.jobrouter_data.statusOfDataTransmissions')
        ->class(TransferStatusWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
        ->arg('$dataProvider', new Reference(TransferStatusDataProvider::class))
        ->tag('dashboard.widget', [
            'identifier' => 'jobrouter_data.statusOfDataTransmissions',
            'groupNames' => 'jobrouter',
            'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfDataTransmissions.title',
            'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfDataTransmissions.description',
            'iconIdentifier' => 'content-widget-number',
            'height' => 'small',
        ]);
};
