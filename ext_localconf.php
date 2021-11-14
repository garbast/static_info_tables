<?php
defined('TYPO3') or die();

call_user_func(
    function ($extKey) {
        // Get the extensions's configuration
        $extConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get($extKey);
        // Register cache static_info_tables
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey])) {
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
        // Names of static entities
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['entities'] =
            ['Country', 'CountryZone', 'Currency', 'Language', 'Territory'];
        // Register cached domain model classes autoloader
        require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey)
            . 'Classes/Cache/CachedClassLoader.php');
        \SJBR\StaticInfoTables\Cache\CachedClassLoader::registerAutoloader();
        // Possible label fields for different languages. Default as last.
        $labelTable = [
            'static_territories' => [
                'label_fields' => [
                    'tr_name_##' => ['mapOnProperty' => 'name##'],
                    'tr_name_en' => ['mapOnProperty' => 'nameEn']
                ],
                'isocode_field' => [
                    'tr_iso_##'
                ]
            ],
            'static_countries' => [
                'label_fields' => [
                    'cn_short_##' => ['mapOnProperty' => 'shortName##'],
                    'cn_short_en' => ['mapOnProperty' => 'shortNameEn']
                ],
                'isocode_field' => [
                    'cn_iso_##'
                ]
            ],
            'static_country_zones' => [
                'label_fields' => [
                    'zn_name_##' => ['mapOnProperty' => 'name##'],
                    'zn_name_local' => ['mapOnProperty' => 'localName']
                ],
                'isocode_field' => [
                    'zn_code',
                    'zn_country_iso_##'
                ]
            ],
            'static_languages' => [
                'label_fields' => [
                    'lg_name_##' => ['mapOnProperty' => 'name##'],
                    'lg_name_en' => ['mapOnProperty' => 'nameEn']
                ],
                'isocode_field' => [
                    'lg_iso_##',
                    'lg_country_iso_##'
                ]
            ],
            'static_currencies' => [
                'label_fields' => [
                    'cu_name_##' => ['mapOnProperty' => 'name##'],
                    'cu_name_en' => ['mapOnProperty' => 'nameEn']
                ],
                'isocode_field' => [
                    'cu_iso_##'
                ]
            ]
        ];
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tables'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tables'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tables'] =
                array_merge($labelTable, $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tables']);
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tables'] = $labelTable;
        }
        // Add data handling hook to manage ISO codes redundancies on records
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
            \SJBR\StaticInfoTables\Hook\Core\DataHandling\ProcessDataMap::class;
        // Make the extension version and constraints available when creating language packs and to other extensions
        $emConfUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extensionmanager\Utility\EmConfUtility::class);
		$emConf =
			$emConfUtility->includeEmConf(
				$extKey, \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey)
			);
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['version'] = $emConf['version'];
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['constraints'] = $emConf['constraints'];
        // Configure translation of suggestions labels
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'
            . $extKey . '/Configuration/PageTSconfig/Suggest.tsconfig">');
        // In backend lists, order records according to language field of current language
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList::class]['modifyQuery'][] =
            \SJBR\StaticInfoTables\Hook\Backend\Recordlist\ModifyQuery::class;
    },
    'static_info_tables'
);