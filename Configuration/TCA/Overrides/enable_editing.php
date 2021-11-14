<?php
defined('TYPO3') or die();

call_user_func(
    function ($extKey) {
		if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['enableManager']) {
			// Enable editing Static Info Tables
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['static_info_tables']['tables'])) {
				$tableNames = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tables']);
				foreach ($tableNames as $tableName) {
					$GLOBALS['TCA'][$tableName]['ctrl']['readOnly'] = 0;
				}
			}
		}
    },
    'static_info_tables'
);