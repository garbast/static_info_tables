<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(
    function ($extKey) {
        // Get the extensions's configuration
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($extKey);
        // Register cache static_info_tables
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]) ||
        	!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey] = [];
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]['groups'] = ['all'];
        }
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['frontend'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]['frontend'] =
                \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class;
        }
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['backend'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]['backend'] =
                \TYPO3\CMS\Core\Cache\Backend\FileBackend::class;
        }
        // Configure clear cache post processing for extended domain model
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['static_info_tables'] =
            \SJBR\StaticInfoTables\Cache\ClassCacheManager::class . '->reBuild';
        // Add data handling hook to manage ISO codes redundancies on records
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
            \SJBR\StaticInfoTables\Hook\Core\DataHandling\ProcessDataMap::class;
        // Configure translation of suggestions labels
        ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'
            . $extKey . '/Configuration/PageTSconfig/Suggest.tsconfig">');
        // Add global fluid namespace
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['sit'][] = 'SJBR\\StaticInfoTables\\ViewHelpers';
		// Add module configuration setup
		if ($extConf['enableManager'] ?? false) {
			ExtensionManagementUtility::addTypoScript(
				'static_info_tables',
				'setup',
				'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:static_info_tables/Configuration/TypoScript/Manager/setup.typoscript">'
			);
		}
    },
    'static_info_tables'
);