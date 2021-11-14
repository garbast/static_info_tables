<?php
namespace SJBR\StaticInfoTables\Configuration\TypoScript;

/*
 *  Copyright notice
 *
 *  (c) 2013-2021 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Utility\HtmlElementUtility;
use TYPO3\CMS\Core\TypoScript\ExtendedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class providing TypoScript configuration help for Static Info Tables
 */
class ConfigurationHelper
{
    /**
     * Renders a select element to select an entity
     *
     * @param array $params: Field information to be rendered
     * @param \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $pObj: The calling parent object.
     * @param mixed $arg
     *
     * @return string The HTML input field
     */
    public function buildEntitySelector(array $params, ExtendedTemplateService $pObj, $arg = '')
    {
        $field = '';
        switch ($params['fieldName']) {
            case 'data[plugin.tx_staticinfotables_pi1.countryCode]':
            case 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]':
                $repository = GeneralUtility::makeInstance(CountryRepository::class);
                $entities = $repository->findAllOrderedBy('nameLocalized');
                break;
            case 'data[plugin.tx_staticinfotables_pi1.countryZoneCode]':
                $repository = GeneralUtility::makeInstance(CountryZoneRepository::class);
                $countryCode = $this->getConfiguredCountryCode();
                if ($countryCode) {
                    $countryRepository = GeneralUtility::makeInstance(CountryRepository::class);
                    $country = $countryRepository->findOneByIsoCodeA3($countryCode);
                    if (is_object($country)) {
                        $entities = $repository->findByCountryOrderedByLocalizedName($country);
                    }
                }
                if (!$countryCode || (empty($entities) && $params['fieldValue'])) {
                    $entities = $repository->findAllOrderedBy('nameLocalized');
                }
                break;
            case 'data[plugin.tx_staticinfotables_pi1.currencyCode]':
                $repository = GeneralUtility::makeInstance(CurrencyRepository::class);
                $entities = $repository->findAllOrderedBy('nameLocalized');
                break;
            case 'data[plugin.tx_staticinfotables_pi1.languageCode]':
                $repository = GeneralUtility::makeInstance(LanguageRepository::class);
                $entities = $repository->findAllNonConstructedNonSacred();
                $entities = $repository->localizedSort($entities);
                break;
        }
        if (is_array($entities) && count($entities)) {
            $options = [];
            foreach ($entities as $entity) {
                switch ($params['fieldName']) {
                    case 'data[plugin.tx_staticinfotables_pi1.countryZoneCode]':
                        $value = $entity->getIsoCode();
                        $options[] = ['name' => $entity->getNameLocalized() . ' (' . $value . ')', 'value' => $value];
                        break;
                    case 'data[plugin.tx_staticinfotables_pi1.countryCode]':
                    case 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]':
                    case 'data[plugin.tx_staticinfotables_pi1.currencyCode]':
                        $value = $entity->getIsoCodeA3();
                        $options[] = ['name' => $entity->getNameLocalized() . ' (' . $value . ')', 'value' => $value];
                        break;
                    case 'data[plugin.tx_staticinfotables_pi1.languageCode]':
                        $countryCode = $entity->getCountryIsoCodeA2();
                        $value = $entity->getIsoCodeA2() . ($countryCode ? '_' . $countryCode : '');
                        $options[] = ['name' => $entity->getNameLocalized() . ' (' . $value . ')', 'value' => $value];
                        break;
                }
            }
            $outSelected = [];
            $size = $params['fieldName'] == 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]' ? 5 : 1;
            $field = HtmlElementUtility::selectConstructor($options, [$params['fieldValue']], $outSelected, $params['fieldName'], '', '', '', '', $size);
        }
        return $field;
    }

    /**
     * Gets the configured default country code
     *
     * @return string The configured default country code
     */
    protected function getConfiguredCountryCode()
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        return $settings['plugin.']['tx_staticinfotables_pi1.']['countryCode'];
    }
}