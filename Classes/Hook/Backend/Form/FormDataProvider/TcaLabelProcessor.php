<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider;

/*
 *  Copyright notice
 *
 *  (c) 2013-2021 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\Currency;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Model\Territory;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository;
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Processor for TCA select items
 */
class TcaLabelProcessor
{
	/**
	 * @var TerritoryRepository
	 */	
    protected $territoryRepository;

	/**
	 * @param TerritoryRepository $territoryRepository
	 */
	public function injectTerritoryRepository(TerritoryRepository $territoryRepository)
	{
		$this->territoryRepository = $territoryRepository;
	}

    /**
     * Add ISO codes to the label of entities
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @return void
     */
    public function addIsoCodeToLabel(&$PA)
    {
        $PA['title'] = LocalizationUtility::translate(['uid' => $PA['row']['uid']], $PA['table']);
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            switch ($PA['table']) {
                case 'static_territories':
                    $isoCode = $PA['row']['tr_iso_nr'];
                    if (!$isoCode) {
                        $territory = $this->territoryRepository->findByUid($PA['row']['uid']);
                        if ($territory instanceof Territory) {
                            $isoCode = $territory->getUnCodeNumber();
                        }
                    }
                    if ($isoCode) {
                        $PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
                    }
                    break;
                case 'static_countries':
                    $isoCode = $PA['row']['cn_iso_2'];
                    if (!$isoCode) {
                        $countryRepository = GeneralUtility::makeInstance(CountryRepository::class);
                        $country = $countryRepository->findByUid($PA['row']['uid']);
                        if ($country instanceof Country) {
                            $isoCode = $country->getIsoCodeA2();
                        }
                    }
                    if ($isoCode) {
                        $PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
                    }
                    break;
                case 'static_languages':
                    $isoCodes = [$PA['row']['lg_iso_2']];
                    if ($PA['row']['lg_country_iso_2']) {
                        $isoCodes[] = $PA['row']['lg_country_iso_2'];
                    }
                    $isoCode = implode('_', $isoCodes);
                    if (!$isoCode || !$PA['row']['lg_country_iso_2']) {
                        $languageRepository = GeneralUtility::makeInstance(LanguageRepository::class);
                        $language = $languageRepository->findByUid($PA['row']['uid']);
                        if ($language instanceof Language) {
                            $isoCodes = [$language->getIsoCodeA2()];
                            if ($language->getCountryIsoCodeA2()) {
                                $isoCodes[] = $language->getCountryIsoCodeA2();
                            }
                            $isoCode = implode('_', $isoCodes);
                        }
                    }
                    if ($isoCode) {
                        $PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
                    }
                    break;
                case 'static_currencies':
                    $isoCode = $PA['row']['cu_iso_3'];
                    if (!$isoCode) {
                        $currencyRepository = GeneralUtility::makeInstance(CurrencyRepository::class);
                        $currency = $currencyRepository->findByUid($PA['row']['uid']);
                        if ($currency instanceof Currency) {
                            $isoCode = $currency->getIsoCodeA3();
                        }
                    }
                    if ($isoCode) {
                        $PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
                    }
                    break;
                default:
                    break;
            }
        }
    }
}