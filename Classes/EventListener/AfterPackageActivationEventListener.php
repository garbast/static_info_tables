<?php
namespace SJBR\StaticInfoTables\EventListener;

/*
 *  Copyright notice
 *
 *  (c) 2022 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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

use TYPO3\CMS\Core\Package\Event\AfterPackageActivationEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/*
 * AfterPackageActivation event listener
 *
 * Always run the extension update script except on first install of base extension
 */
class AfterPackageActivationEventListener extends AbstractEventListener
{
    /**
     * If the installed extension is static_info_tables or a language pack, execute the update
     *
     * @param AfterPackageActivationEvent $event
     * @return void
     */
    public function __invoke(AfterPackageActivationEvent $event): void
    {
        $extensionKey = $event->getPackageKey();
        $packageType = $event->getType();
        if ($packageType === 'typo3-cms-extension' && strpos($extensionKey, 'static_info_tables') === 0) {
            $extensionKeyParts = explode('_', $extensionKey);
            if (count($extensionKeyParts) === 3) {
                $extTablesStaticSqlRelFile = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath($extensionKey)) . 'ext_tables_static+adt.sql';
            }
            if (
                // Base extension with data already imported once
                (count($extensionKeyParts) === 3 && $this->registry->get('extensionDataImport', $extTablesStaticSqlRelFile))
                // Language pack
                || (count($extensionKeyParts) === 4 && strlen($extensionKeyParts[3]) === 2)
                || (count($extensionKeyParts) === 5 && strlen($extensionKeyParts[3]) === 2 && strlen($extensionKeyParts[4]) === 2)
            ) {
                $this->executeUpdate();
            }
        }
    }
}