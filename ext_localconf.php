<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

// @todo Remove once compatibility with TYPO3 v13 is dropped
if ((new Typo3Version())->getMajorVersion() < 14) {
    ExtensionManagementUtility::addTypoScriptSetup('
        module.tx_form {
          settings {
            yamlConfigurations {
              1601140510 = EXT:jobrouter_data/Configuration/Form/Base/Finishers/JobRouterTransmitData.yaml
            }
          }
        }

        plugin.tx_form {
          settings {
            yamlConfigurations {
              1601140510 = EXT:jobrouter_data/Configuration/Form/Base/Finishers/JobRouterTransmitData.yaml
            }
          }
        }
    ');
}

