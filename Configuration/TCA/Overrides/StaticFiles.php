<?php
defined('TYPO3') or die();

// Configure extension static template
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'static_info_tables',
    'Configuration/TypoScript/Static',
    'Static Info Tables'
);
