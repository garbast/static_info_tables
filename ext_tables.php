<?php
defined('TYPO3') or die();

/**
 * Registers the Static Info Tables Manager backend module, if enabled
 */
if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['static_info_tables']['enableManager'] ?? false) {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'StaticInfoTables',
		// Make module a submodule of 'tools'
		'tools',
		// Submodule key
		'staticinfomanager',
		// Position
		'',
		// An array holding the controller-action combinations that are accessible
		[
			\SJBR\StaticInfoTables\Controller\ManagerController::class => 'information,newLanguagePack,createLanguagePack,testForm,testFormResult,sqlDumpNonLocalizedData',
		],
		[
			'access' => 'user,group',
			'icon' => 'EXT:static_info_tables/Resources/Public/Icons/Extension.svg',
			'labels' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_mod.xlf',
		]
	);
	// Add module configuration setup
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
		'static_info_tables',
		'setup',
		'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:static_info_tables/Configuration/TypoScript/Manager/setup.txt">'
	);
}