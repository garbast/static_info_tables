<?php
namespace SJBR\StaticInfoTables\EventListener\Package;

/*
 *  Copyright notice
 *
 *  (c) 2022-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use SJBR\StaticInfoTables\Cache\ClassCacheManager;
use SJBR\StaticInfoTables\Utility\DatabaseUpdateUtility;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/*
 * AfterPackageActivation event listener
 *
 * Always run the extension update script except on first install of base extension
 */
abstract class AbstractPackageEventListener
{
    /**
     * @var string Name of the extension this controller belongs to
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Execute the update
     *
     * @return void
     */
    public function executeUpdate()
    {
        $databaseUpdateUtility = GeneralUtility::makeInstance(DatabaseUpdateUtility::class);
        // Clear the class cache
        $classCacheManager = GeneralUtility::makeInstance(ClassCacheManager::class);
        $classCacheManager->reBuild();
        if ($this->isUpdateRequired()) {
        	$flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
			$messageQueue = $flashMessageService->getMessageQueueByIdentifier('extbase.flashmessages.tx_extensionmanager_tools_extensionmanagerextensionmanager');
			//extbase.flashmessages.tx_extensionmanager_tools_extensionmanagerextensionmanager
			// Process the database updates of this base extension (we want to re-process these updates every time the update script is invoked)
			// unless there was no change in the version numbers of the static info tables and language packs installed
			$extensionSitePath = ExtensionManagementUtility::extPath(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName));
			if (isset($GLOBALS['BE_USER'])) {
				$GLOBALS['BE_USER']->writelog(3, 1, 0, 0, '[StaticInfoTables]: ' . LocalizationUtility::translate('updateTables', $this->extensionName) ?? '', [$GLOBALS['BE_USER']->user['username']]);
			}
			$message = GeneralUtility::makeInstance(FlashMessage::class, LocalizationUtility::translate('updateTables', $this->extensionName), '', ContextualFeedbackSeverity::OK, true);
			if (!($GLOBALS['BE_USER'] instanceof CommandLineUserAuthentication)) {
				$messageQueue->addMessage($message);
			}
			$databaseUpdateUtility->importStaticSqlFile($extensionSitePath);
			// Get the extensions which want to extend static_info_tables
			$loadedExtensions = array_unique(ExtensionManagementUtility::getLoadedExtensionListArray());
			$languagePackUpdated = false;
			foreach ($loadedExtensions as $extensionKey) {
				if ($this->isStaticInfoTablesExtension($extensionKey)) {
					// We need to reprocess the database structure update sql statements (ext_tables)
					$databaseUpdateUtility->processDatabaseUpdates($extensionKey);
					// Now we process the static data updates (ext_tables_static+adt)
					// Note: The Install Tool Utility does not handle sql update statements
					$databaseUpdateUtility->doUpdate($extensionKey);
					if (isset($GLOBALS['BE_USER'])) {
						$GLOBALS['BE_USER']->writelog(3, 1, 0, 0, '[StaticInfoTables]: ' . LocalizationUtility::translate('updateLanguageLabels', $this->extensionName, [$extensionKey]) ?? '', [$GLOBALS['BE_USER']->user['username']]);
					}
					$message = GeneralUtility::makeInstance(FlashMessage::class, LocalizationUtility::translate('updateLanguageLabels', $this->extensionName, [$extensionKey]), '', ContextualFeedbackSeverity::OK, true);
					if (!($GLOBALS['BE_USER'] instanceof CommandLineUserAuthentication)) {
						$messageQueue->addMessage($message);
					}
				    $languagePackUpdated = true;
				}
			}
			$this->storeLastUpdateStatus();
		}
    }

    /**
     * Is an update required?
     *
     * Returns true when the last stored update status is different from the current status
     * or the forceUpdate GET parameter is provided.
     *
     * @return bool
     */
    public function isUpdateRequired(): bool
    {
        $lastUpdateStatus = $this->registry->get('static_info_tables', 'last_update_status', false);
        if (!$lastUpdateStatus) {
            return true;
        }
        $extensionVersionInfo = $this->buildExtensionVersionInfo();
        return $lastUpdateStatus !== $extensionVersionInfo;
    }

    /**
     * Saves the last update status in the TYPO3 registry.
     */
    protected function storeLastUpdateStatus()
    {
        $extensionVersionInfo = $this->buildExtensionVersionInfo();
        $this->registry->set('static_info_tables', 'last_update_status', $extensionVersionInfo);
    }

    /**
     * Loops over all loaded Extensions and collects the version info of every installed static_info_tables
     * Extension. The Extension keys and version numbers are concated to a string:
     *
     * extension_key1:1.2.3
     * extension_key2:2.3.4
     * ...
     *
     * @return string
     */
    protected function buildExtensionVersionInfo()
    {
        $mainVersion = ExtensionManagementUtility::getExtensionVersion('static_info_tables');
        $extensionVersions = ['static_info_tables:' . $mainVersion];

        $loadedExtensions = array_unique(ExtensionManagementUtility::getLoadedExtensionListArray());
        foreach ($loadedExtensions as $extensionKey) {
            if (!$this->isStaticInfoTablesExtension($extensionKey)) {
                continue;
            }
            $extensionVersion = ExtensionManagementUtility::getExtensionVersion($extensionKey);
            $extensionVersions[] = $extensionKey . ':' . $extensionVersion;
        }

        return implode(LF, $extensionVersions);
    }

    /**
     * Returns true when the StaticInfoTables.txt configuration file exists in the given Extension.
     *
     * @param string $extensionKey
     * @return bool
     */
    protected function isStaticInfoTablesExtension($extensionKey)
    {
        $extensionInfoFile = ExtensionManagementUtility::extPath($extensionKey)
            . 'Configuration/DomainModelExtension/StaticInfoTables.txt';
        return file_exists($extensionInfoFile);
    }
}
