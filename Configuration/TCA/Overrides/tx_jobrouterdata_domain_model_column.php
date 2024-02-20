<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;

defined('TYPO3') || die();

if ((new Typo3Version())->getMajorVersion() === 11) {
    $GLOBALS['TCA']['tx_jobrouterdata_domain_model_column']['columns']['decimal_places']['config'] = array_merge(
        $GLOBALS['TCA']['tx_jobrouterdata_domain_model_column']['columns']['decimal_places']['config'],
        [
            'type' => 'input',
            'eval' => 'int',
        ],
    );

    $GLOBALS['TCA']['tx_jobrouterdata_domain_model_column']['columns']['field_size']['config'] = array_merge(
        $GLOBALS['TCA']['tx_jobrouterdata_domain_model_column']['columns']['field_size']['config'],
        [
            'type' => 'input',
            'eval' => 'int',
        ],
    );
}
