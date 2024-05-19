<?php
namespace SJBR\StaticInfoTables\Controller;

/*
 *  Copyright notice
 *
 *  (c) 2013-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Model\LanguagePack;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguagePackRepository;
use SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository;
use SJBR\StaticInfoTables\Utility\LocaleUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Package\MetaData;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;

/**
 * Static Info Tables Manager controller
 */
class ManagerController extends ActionController
{
    /**
     * @var string Name of the extension this controller belongs to
     */
    protected $extensionName = 'StaticInfoTables';

    protected $actions = [
		[
			'code' => 'newLanguagePack',
			'title' => 'createLanguagePackTitle',
			'description' => 'createLanguagePackDescription',
		],
		[
			'code' => 'testForm',
			'title' => 'testFormTitle',
			'description' => 'testFormDescription',
		],
		[
			'code' => 'sqlDumpNonLocalizedData',
			'title' => 'sqlDumpNonLocalizedDataTitle',
			'description' => 'sqlDumpNonLocalizedDataDescription',
		]
    ];

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * Dependency injection of the Country Repository
     *
     * @param CountryRepository $countryRepository
     * @return void
     */
    public function injectCountryRepository(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @var CountryZoneRepository
     */
    protected $countryZoneRepository;

    /**
     * Dependency injection of the Country Zone Repository
     *
     * @param CountryZoneRepository $countryZoneRepository
     * @return void
     */
    public function injectCountryZoneRepository(CountryZoneRepository $countryZoneRepository)
    {
        $this->countryZoneRepository = $countryZoneRepository;
    }

    /**
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    /**
     * Dependency injection of the Currency Repository
     *
     * @param CurrencyRepository $currencyRepository
     * @return void
     */
    public function injectCurrencyRepository(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * Dependency injection of the Language Repository
     *
     * @param LanguageRepository $languageRepository
     * @return void
     */
    public function injectLanguageRepository(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @var TerritoryRepository
     */
    protected $territoryRepository;

    /**
     * Dependency injection of the Territory Repository
     *
     * @param TerritoryRepository $territoryRepository
     * @return void
     */
    public function injectTerritoryRepository(TerritoryRepository $territoryRepository)
    {
        $this->territoryRepository = $territoryRepository;
    }

    /**
     * Dependency injection of the Module Template Factory
     *
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @return void
     */
    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * Init module state.
     * This isn't done within __construct() since the controller
     * object is only created once in extbase when multiple actions are called in
     * one call. When those change module state, the second action would see old state.
     */
    public function initializeAction(): void
    {
        $this->moduleData = $this->request->getAttribute('moduleData');
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->uriBuilder->setRequest($this->request);
        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:static_info_tables/Resources/Private/Language/locallang_mod.xlf:mlang_labels_tablabel'));
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
        $this->makeFunctionMenu();
    }

    /**
     * Build function menu
     */
    protected function makeFunctionMenu(): void
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('ManagerFunctionMenu');
        $menuItem = $menu->makeMenuItem()
            ->setTitle(LocalizationUtility::translate('LLL:EXT:static_info_tables/Resources/Private/Language/locallang.xlf:information'))
            ->setHref($this->uriBuilder->uriFor('information'));
        if ($this->request->getControllerActionName() === 'information') {
        	$menuItem->setActive(true);
        }
        $menu->addMenuItem($menuItem);
        foreach ($this->actions as $action) {
            $menuItem = $menu->makeMenuItem()
                ->setTitle(LocalizationUtility::translate('LLL:EXT:static_info_tables/Resources/Private/Language/locallang.xlf:' . $action['title']))
                ->setHref($this->uriBuilder->uriFor($action['code']));
				if ($this->request->getControllerActionName() === $action['code']) {
					$menuItem->setActive(true);
				}
				if ($this->request->getControllerActionName() === 'testFormResult' && $action['code'] === 'testForm') {
					$menuItem->setActive(true);
				}
            $menu->addMenuItem($menuItem);
        }
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * Display general information
     */
    public function informationAction(): ResponseInterface
    {
        $this->moduleTemplate->assign('actions', $this->actions);
        return $this->moduleTemplate->renderResponse('Manager/Information');
    }

    /**
     * Display the language pack creation form
     *
     * @param LanguagePack $languagePack
     */
    public function newLanguagePackAction(LanguagePack $languagePack = null): ResponseInterface
    {
        if (!is_object($languagePack)) {
            $languagePack = new LanguagePack();
        }
        $languagePack->setVersion(ExtensionManagementUtility::getExtensionVersion(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName)));
        $languagePack->setAuthor($GLOBALS['BE_USER']->user['realName']);
        $languagePack->setAuthorEmail($GLOBALS['BE_USER']->user['email']);
        $localeUtility = GeneralUtility::makeInstance(LocaleUtility::class);
        $this->moduleTemplate->assign('locales', $localeUtility->getLocales());
        $this->moduleTemplate->assign('languagePack', $languagePack);
        return $this->moduleTemplate->renderResponse('Manager/NewLanguagePack');
    }

    /**
     * Creation/update a language pack for the Static Info Tables
     *
     * @param LanguagePack $languagePack
     */
    public function createLanguagePackAction(LanguagePack $languagePack): ResponseInterface
    {
        // Add the localization columns
        $locale = $languagePack->getLocale();
        // Get the English name of the locale
        $localeUtility = new LocaleUtility();
        $language = $localeUtility->getLanguageFromLocale($locale);
        $languagePack->setLanguage($language);
        // Get the extension constraints
        $emConfUtility = GeneralUtility::makeInstance(EmConfUtility::class);
		$emConf =
			$emConfUtility->includeEmConf(
				GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName), ExtensionManagementUtility::extPath(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName))
			);
        $constraints = $emConf['constraints'];
        $languagePack->setTypo3VersionRange($constraints['depends']['typo3'] ?? '');
        // If version is not set, use the version of the base extension
        if (!$languagePack->getVersion()) {
            $languagePack->setVersion(ExtensionManagementUtility::getExtensionVersion(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName)));
        }
        $this->countryRepository->addLocalizationColumns($locale);
        $this->countryZoneRepository->addLocalizationColumns($locale);
        $this->currencyRepository->addLocalizationColumns($locale);
        $this->languageRepository->addLocalizationColumns($locale);
        $this->territoryRepository->addLocalizationColumns($locale);
        // Store the Language Pack
        $languagePackRepository = GeneralUtility::makeInstance(LanguagePackRepository::class);
        $messages = $languagePackRepository->writeLanguagePack($languagePack);
        if (count($messages)) {
            foreach ($messages as $message) {
                $this->addFlashMessage($message, '', ContextualFeedbackSeverity::OK);
            }
        }
        return new ForwardResponse('information');
    }

    /**
     * Display a test form
     *
     * @param Country $country
     * @param CountryZone $countryZone
     * @param Language $language
     */
    public function testFormAction(Country $country = null, CountryZone $countryZone = null, Language $language = null): ResponseInterface
    {
        if (is_object($country) && (is_object($countryZone) || !$country->getCountryZones()->count())) {
            return (new ForwardResponse('testFormResult'))
               ->withControllerName('Manager')
               ->withExtensionName($this->extensionName)
               ->withArguments(['country' => $country, 'countryZone' => $countryZone, 'language' => $language]);
        }
        if (is_object($country)) {
            $this->moduleTemplate->assign('selectedCountry', $country);
        }
        if (is_object($countryZone)) {
            $this->moduleTemplate->assign('selectedCountryZone', $countryZone);
        }
        if (is_object($language)) {
            $this->moduleTemplate->assign('selectedLanguage', $language);
        }
        return $this->moduleTemplate->renderResponse('Manager/TestForm');
    }

    /**
     * Display the test form result
     *
     * @param Country $country
     * @param CountryZone $countryZone
     * @param Language $language
     */
    public function testFormResultAction(Country $country = null, CountryZone $countryZone = null, Language $language = null): ResponseInterface
    {
        $this->moduleTemplate->assign('country', $country);
        $currencies = $this->currencyRepository->findByCountry($country);
        if ($currencies->count()) {
            $this->moduleTemplate->assign('currency', $currencies[0]);
        }
        if (is_object($countryZone)) {
            $this->moduleTemplate->assign('countryZone', $countryZone);
        }
        $this->moduleTemplate->assign('language', $language);
        $territories = $this->territoryRepository->findByCountry($country);
        if ($territories->count()) {
            $this->moduleTemplate->assign('territory', $territories[0]);
        }
        return $this->moduleTemplate->renderResponse('Manager/TestFormResult');
    }

    /**
     * Creation/update a language pack for the Static Info Tables
     */
    public function sqlDumpNonLocalizedDataAction(): ResponseInterface
    {
        // Create a SQL dump of non-localized data
        $dumpContent = [];
        $dumpContent[] = $this->countryRepository->sqlDumpNonLocalizedData();
        $dumpContent[] = $this->countryZoneRepository->sqlDumpNonLocalizedData();
        $dumpContent[] = $this->currencyRepository->sqlDumpNonLocalizedData();
        $dumpContent[] = $this->languageRepository->sqlDumpNonLocalizedData();
        $dumpContent[] = $this->territoryRepository->sqlDumpNonLocalizedData();
        // Write the SQL dump file
        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
        $extensionPath = ExtensionManagementUtility::extPath($extensionKey);
        $filename = 'export-ext_tables_static+adt.sql';
        GeneralUtility::writeFile($extensionPath . $filename, implode(LF, $dumpContent));
        $message = LocalizationUtility::translate('sqlDumpCreated', $this->extensionName) . ' ' . $extensionPath . $filename;
        $this->addFlashMessage($message, '', ContextualFeedbackSeverity::OK);
        return new ForwardResponse('information');
    }

    /**
     * Get the typo3-supported locale options for the language pack creation
     *
     * @return array An array of language objects
     */
    protected function getLocales()
    {
        $localeArray = [];
        $locales = GeneralUtility::makeInstance(Locales::class);
        $languages = $locales->getLanguages();
        foreach ($languages as $locale => $language) {
            // No language pack for English
            if ($locale != 'default') {
                $languageObject = new Language();
                $languageObject->setCollatingLocale($locale);
                $localizedLanguage = LocalizationUtility::translate('lang_' . $locale, 'Lang');
                $label = ($localizedLanguage ? $localizedLanguage : $language) . ' (' . $locale . ')';
                $languageObject->setNameEn($label);
                $localeArray[$label] = $languageObject;
            }
        }
        ksort($localeArray);
        return $localeArray;
    }

    /**
     * Get language name from locale
     *
     * @param string $locale
     * @return string Language name
     */
    protected function getLanguageFromLocale($locale)
    {
        $locales = GeneralUtility::makeInstance(Locales::class);
        $languages = $locales->getLanguages();
        $language = $languages[$locale];
        return $language . ' (' . $locale . ')';
    }

    protected function getErrorFlashMessage(): string|bool
    {
    	return false;
    }
}
