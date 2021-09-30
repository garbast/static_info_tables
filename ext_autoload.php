<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
/*
 * Register necessary class names with autoloader
 */
$extensionPath = ExtensionManagementUtility::extPath('static_info_tables');
return [
    'tx_staticinfotables_pi1' => $extensionPath . 'pi1/class.tx_staticinfotables_pi1.php',
];
