<?php
defined('TYPO3') or die();

use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(
    function ($extKey) {
		if (GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($extKey, 'enableManager') ?? false) {
			// Enable editing Static Info Tables
			$tableNames = array_keys(LocalizationUtility::TABLES ?? []);
			foreach ($tableNames as $tableName) {
				$GLOBALS['TCA'][$tableName]['ctrl']['readOnly'] = 0;
			}
		}
    },
    'static_info_tables'
);