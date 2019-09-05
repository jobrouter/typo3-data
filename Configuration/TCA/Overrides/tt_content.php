<?php
defined('TYPO3_MODE') || die();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['jobrouterdata_pi'] = 'recursive,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['jobrouterdata_pi'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'jobrouterdata_pi',
    'FILE:EXT:jobrouter_data/Configuration/FlexForms/Table.xml'
);
