<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider;

/*
 *  Copyright notice
 *
 *  (c) 2013-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Currency;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Model\Territory;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository;
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Processor for TCA select items
 */
class TcaSelectItemsProcessor
{
    /**
     * Translate and sort the territories selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     */
    public function translateTerritoriesSelector($PA): void
    {
        switch ($PA['table'] ?? '') {
            case 'static_territories':
                // Avoid circular relation
                $row = $PA['row'] ?? [];
                foreach (($PA['items'] ?? []) as $index => $item) {
                    if ($item[1] == $row['uid']) {
                        unset($PA['items'][$index]);
                    }
                }
                break;
        }
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_territories');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the countries selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     */
    public function translateCountriesSelector($PA): void
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_countries');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the country zones selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     */
    public function translateCountryZonesSelector($PA): void
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_country_zones');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the currencies selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     */
    public function translateCurrenciesSelector($PA): void
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_currencies');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the languages selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     */
    public function translateLanguagesSelector($PA): void
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_languages');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate selector items array
     *
     * @param array $items: array of value/label pairs
     * @param string $tableName: name of static info tables
     *
     * @return array array of value/translated label pairs
     */
    protected function translateSelectorItems($items, $tableName)
    {
        $translatedItems = $items;
        if (isset($translatedItems) && is_array($translatedItems)) {
            foreach ($translatedItems as $key => $item) {
                if (is_array($translatedItems[$key]) && array_key_exists(1, $translatedItems[$key]) && ($translatedItems[$key][1] ?? '')) {
                    //Get isocode if present
                    $code = strstr($item[0], '(');
                    $code2 = strstr(substr($code, 1), '(');
                    $code = $code2 ? $code2 : $code;
                    // Translate
                    $translatedItems[$key][0] = LocalizationUtility::translate(['uid' => $item[1]], $tableName);
                    // Re-append isocode, if present
                    $translatedItems[$key][0] = $translatedItems[$key][0] . ($code ? ' ' . $code : '');
                }
            }
            $currentLocale = setlocale(LC_COLLATE, '0');
            $locale = LocalizationUtility::setCollatingLocale();
            if ($locale !== false) {
                uasort($translatedItems, [$this, 'strcollOnLabels']);
            }
            setlocale(LC_COLLATE, $currentLocale);
        }
        $items = $translatedItems;
        return $items;
    }

    /**
     * Using strcoll comparison on labels
     *
     * @return int see strcoll
     *
     * @param mixed $itemA
     * @param mixed $itemB
     */
    protected function strcollOnLabels($itemA, $itemB)
    {
        return strcoll($itemA[0], $itemB[0]);
    }

    /**
     * Replace the selector's uid index with configured indexField
     *
     * @param array	 $PA: TCA select field parameters array
     * @return array The new $items array
     */
    protected function replaceSelectorIndexField($PA)
    {
        $items = $PA['items'] ?? [];
        $indexFields = GeneralUtility::trimExplode(',', $PA['config']['itemsProcFunc_config']['indexField'] ?? '', true);
        if (!empty($indexFields)) {
            $rows = [];
            // Collect items uid's
            $uids = [];
            foreach ($items as $key => $item) {
                if (is_array($items[$key]) && array_key_exists(1, $items[$key]) && ($items[$key][1] ?? 0)) {
                    $uids[] = $item[1];
                }
            }
            $uidList = implode(',', $uids);
            if (!empty($uidList)) {
                switch ($PA['config']['foreign_table'] ?? '') {
                    case 'static_territories':
                        /** @var $territoryRepository TerritoryRepository */
                        $territoryRepository = GeneralUtility::makeInstance(TerritoryRepository::class);
                        $objects = $territoryRepository->findAllByUidInList($uidList)->toArray();
                        break;
                    case 'static_countries':
                        /** @var $countryRepository CountryRepository */
                        $countryRepository = GeneralUtility::makeInstance(CountryRepository::class);
                        $objects = $countryRepository->findAllByUidInList($uidList)->toArray();
                        break;
                    case 'static_country_zones':
                        /** @var $countryZoneRepository CountryZoneRepository */
                        $countryZoneRepository = GeneralUtility::makeInstance(CountryZoneRepository::class);
                        $objects = $countryZoneRepository->findAllByUidInList($uidList)->toArray();
                        break;
                    case 'static_languages':
                        /** @var $languageRepository LanguageRepository */
                        $languageRepository = GeneralUtility::makeInstance(LanguageRepository::class);
                        $objects = $languageRepository->findAllByUidInList($uidList)->toArray();
                        break;
                    case 'static_currencies':
                        /** @var $currencyRepository CurrencyRepository */
                        $currencyRepository = GeneralUtility::makeInstance(CurrencyRepository::class);
                        $objects = $currencyRepository->findAllByUidInList($uidList)->toArray();
                        break;
                    default:
                        break;
                }
                if (!empty($objects)) {
                	$columnsMapping = $this->getColumnsMapping($objects[0]);
                    // Map table column to object property
                    $indexProperties = [];
                    foreach ($indexFields as $indexField) {
                        if ($columnsMapping[$indexField]['mapOnProperty'] ?? '') {
                            $indexProperties[] = $columnsMapping[$indexField]['mapOnProperty'];
                        } else {
                            $indexProperties[] = GeneralUtility::underscoredToLowerCamelCase($indexField);
                        }
                    }
                    // Index rows by uid
                    $uidIndexedRows = [];
                    foreach ($objects as $object) {
                        $uidIndexedObjects[$object->getUid()] = $object;
                    }
                    // Replace the items index field
                    foreach ($items as $key => $item) {
                        if (is_a($item, SelectItem::class) && $item->getValue() > 0) {
                        	// Since TYPO3 12 LTS
                            $object = $uidIndexedObjects[$item->getValue()] ?? false;
                            if ($object) {
                                $value = $object->_getProperty($indexProperties[0]);
                                if ($indexFields[1] && $object->_getProperty($indexProperties[1])) {
                                    $value .=  '_' . $object->_getProperty($indexProperties[1]);
                                }
                                $item->offsetSet('value', $value);
                            }
                        } elseif (is_array($items[$key]) && array_key_exists(1, $items[$key]) && ($items[$key][1] ?? 0)) {
                            $object = $uidIndexedObjects[$items[$key][1]];
                            $items[$key][1] = $object->_getProperty($indexProperties[0]);
                            if ($indexFields[1] && $object->_getProperty($indexProperties[1])) {
                                $items[$key][1] .=  '_' . $object->_getProperty($indexProperties[1]);
                            }
                        }
                    }
                }
            }
        }
        return $items;
    }

    /**
     * Get the columns mapping for the object
     *
     * @param object $object
     * @return array
     */
    protected function getColumnsMapping($object)
    {
        $columnsMapping = [];
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        $className = get_class($object);
        $dataMap = $dataMapper->getDataMap($className);
        $properties = $object->_getProperties();
        foreach ($properties as $propertyName => $propertyValue) {
        	if (!$dataMap->isPersistableProperty($propertyName)) {
                continue;
            }
            $columnMap = $dataMap->getColumnMap($propertyName);
            $columnName = $columnMap->getColumnName();
            $columnsMapping[$columnName] = ['mapOnProperty' => $propertyName];
        }
        return $columnsMapping;
    }
}