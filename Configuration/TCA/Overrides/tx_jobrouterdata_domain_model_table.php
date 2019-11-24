<?php
defined('TYPO3_MODE') || die();

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('jobrouter_form')) {
    $GLOBALS['TCA']['tx_jobrouterdata_domain_model_table']['columns']['type']['config']['items'][] = [
        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.type.other_usage',
        \Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_OTHER_USAGE
    ];

    $GLOBALS['TCA']['tx_jobrouterdata_domain_model_table']['types'][(string)\Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_OTHER_USAGE] = [
        'showitem' => '
            type, connection, name, table_guid,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '
    ];
}
