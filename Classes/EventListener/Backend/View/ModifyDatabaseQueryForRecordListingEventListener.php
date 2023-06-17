<?php
declare(strict_types=1);

namespace SJBR\StaticInfoTables\EventListener\Backend\View;

/*
 *  Copyright notice
 *
 *  (c) 2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
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

use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\View\Event\ModifyDatabaseQueryForRecordListingEvent;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Order records according to language field of current language
 */
final class ModifyDatabaseQueryForRecordListingEventListener
{
    /**
     * @param ModifyDatabaseQueryForRecordListingEvent $event
     * @return void
     */
    public function __invoke(ModifyDatabaseQueryForRecordListingEvent $event): void
    {
    	$table = $event->getTable();
        if (in_array($table, array_keys(LocalizationUtility::TABLES ?? []))) {
            $lang = substr(strtolower($this->getLanguageService()->lang), 0, 2);
            $labelFields = LocalizationUtility::TABLES[$table]['label_fields'] ?? [];
            if (ExtensionManagementUtility::isLoaded('static_info_tables_' . $lang) && is_array($labelFields) && !empty($labelFields)) {
            	$label = array_key_first($labelFields);
            	if ($label) {
					$orderBy = str_replace('##', $lang, $label);
					$orderByFields = QueryHelper::parseOrderBy((string)$orderBy);
					$queryBuilder = $event->getQueryBuilder();
					foreach ($orderByFields as $fieldNameAndSorting) {
						list($fieldName, $sorting) = $fieldNameAndSorting;
						$queryBuilder->orderBy($fieldName, $sorting);
					}
                }
            }
        }
    }

    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}