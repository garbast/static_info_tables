<?php
namespace SJBR\StaticInfoTables\ViewHelpers\Form;

/*
 *  Copyright notice
 *
 *  (c) 2014 Carsten Biebricher <carsten.biebricher@hdnet.de>
 *  (c) 2016-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * StaticInfoTables SelectViewHelper
 *
 * Display the Values of the selected StaticInfoTable.
 *
 * Default usage:
 * <code>
 * <sit:form.select name="staticInfoTablesTestCountry" staticInfoTable="country" options="{}"/>
 * <sit:form.select name="staticInfoTablesTestLanguage" staticInfoTable="language" options="{}"/>
 * <sit:form.select name="staticInfoTablesTestTerritory" staticInfoTable="territory" options="{}"/>
 * <sit:form.select name="staticInfoTablesTestCurrency" staticInfoTable="currency" options="{}"/>
 * <sit:form.select name="staticInfoTablesTestCountryZones" staticInfoTable="countryZone" options="{}"/>
 * </code>
 *
 * Optional Usage:
 * <code>
 * <sit:form.select name="staticInfoTablesTestCountry" id="staticInfoTablesTestCountry" staticInfoTable="country" options="{}" optionLabelField="isoCodeA2"/>
 * <sit:form.select name="staticInfoTablesTestCountry" id="staticInfoTablesTestCountry" staticInfoTable="country" options="{}" optionLabelField="capitalCity"/>
 * </code>
 *
 * Subselect Usage: (only CountryZones of Germany)
 * <sit:form.select name="staticInfoTablesTestCountryZones" id="staticInfoTablesTestCountryZones" staticInfoTable="countryZone" options="{}" staticInfoTableSubselect="{country: 54}"/>
 *
 * if you specify the Label-Field for the table use the Variable-Name from the StaticInfoTable-Model. (@see \SJBR\StaticInfoTables\Domain\Model\Country, ...)
 *
 * use name or property!
 *
 * Available Tables:
 * country
 * language
 * territory
 * currency
 * countryZone
 */
class SelectViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'select';

    /**
     * Extension name
     *
     * @var string
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * Settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Country repository
     *
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository
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
     * Language repository
     *
     * @var \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository
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
     * Territory repository
     *
     * @var \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository
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
     * Currency repository
     *
     * @var \SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository
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
     * Country Zone repository
     *
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
     */
    protected $countryZoneRepository;

    /**
     * Dependency injection of the CountryZone Repository
     *
     * @param CountryZoneRepository $countryZoneRepository
     * @return void
     */
    public function injectCountryZoneRepository(CountryZoneRepository $countryZoneRepository)
    {
        $this->countryZoneRepository = $countryZoneRepository;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('size', 'string', 'Size of select field, a numeric value to show the amount of items to be visible at the same time - equivalent to HTML <select> site attribute');
        $this->registerTagAttribute('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
        $this->registerArgument('options', 'array', 'Associative array with internal IDs as key, and the values are displayed in the select box. Can be combined with or replaced by child f:form.select.* nodes.');
        $this->registerArgument('optionsAfterContent', 'boolean', 'If true, places auto-generated option tags after those rendered in the tag content. If false, automatic options come first.', false, false);
        $this->registerArgument('optionValueField', 'string', 'If specified, will call the appropriate getter on each object to determine the value.');
        $this->registerArgument('optionLabelField', 'string', 'If specified, will call the appropriate getter on each object to determine the label.');
        $this->registerArgument('sortByOptionLabel', 'boolean', 'If true, List will be sorted by label.', false, false);
        $this->registerArgument('selectAllByDefault', 'boolean', 'If specified options are selected if none was set before.', false, false);
        $this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this ViewHelper', false, 'f3-form-error');
        $this->registerArgument('prependOptionLabel', 'string', 'If specified, will provide an option at first position with the specified label.');
        $this->registerArgument('prependOptionValue', 'string', 'If specified, will provide an option at first position with the specified value.');
        $this->registerArgument('multiple', 'boolean', 'If set multiple options may be selected.', false, false);
        $this->registerArgument('required', 'boolean', 'If set no empty value is allowed.', false, false);

        $this->registerArgument('staticInfoTable', 'string', 'set the tablename of the StaticInfoTable to build the Select-Tag.');
        $this->registerArgument('staticInfoTableSubselect', 'array', '{fieldname: fieldvalue}');
        $this->registerArgument('defaultOptionLabel', 'string', 'if set, add default option with given label');
        $this->registerArgument('defaultOptionValue', 'string', 'if set, add default option with given label');
    }

    public function render(): string
    {
        if ($this->arguments['required']) {
            $this->tag->addAttribute('required', 'required');
        }
        $name = $this->getName();
        if ($this->arguments['multiple']) {
            $this->tag->addAttribute('multiple', 'multiple');
            $name .= '[]';
        }
        $this->tag->addAttribute('name', $name);
        $options = $this->getOptions();

        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();

        $this->addAdditionalIdentityPropertiesIfNeeded();
        $this->setErrorClassAttribute();
        $content = '';

        // register field name for token generation.
        $this->registerFieldNameForFormTokenGeneration($name);
        // in case it is a multi-select, we need to register the field name
        // as often as there are elements in the box
        if ($this->arguments['multiple']) {
            $content .= $this->renderHiddenFieldForEmptyValue();
            // Register the field name additional times as required by the total number of
            // options. Since we already registered it once above, we start the counter at 1
            // instead of 0.
            $optionsCount = count($options);
            for ($i = 1; $i < $optionsCount; $i++) {
                $this->registerFieldNameForFormTokenGeneration($name);
            }
            // save the parent field name so that any child f:form.select.option
            // tag will know to call registerFieldNameForFormTokenGeneration
            // this is the reason why "self::class" is used instead of static::class (no LSB)
            $viewHelperVariableContainer->addOrUpdate(
                self::class,
                'registerFieldNameForFormTokenGeneration',
                $name
            );
        }

        $viewHelperVariableContainer->addOrUpdate(self::class, 'selectedValue', $this->getSelectedValue());
        $prependContent = $this->renderPrependOptionTag();
        $tagContent = $this->renderOptionTags($options);
        $childContent = $this->renderChildren();
        $viewHelperVariableContainer->remove(self::class, 'selectedValue');
        $viewHelperVariableContainer->remove(self::class, 'registerFieldNameForFormTokenGeneration');
        if (isset($this->arguments['optionsAfterContent']) && $this->arguments['optionsAfterContent']) {
            $tagContent = $childContent . $tagContent;
        } else {
            $tagContent .= $childContent;
        }
        $tagContent = $prependContent . $tagContent;

        $this->tag->forceClosingTag(true);
        $this->tag->setContent($tagContent);
        $content .= $this->tag->render();
        return $content;
    }

    /**
     * Render prepended option tag
     */
    protected function renderPrependOptionTag(): string
    {
        $output = '';
        if ($this->hasArgument('prependOptionLabel')) {
            $value = $this->hasArgument('prependOptionValue') ? $this->arguments['prependOptionValue'] : '';
            $label = $this->arguments['prependOptionLabel'];
            $output .= $this->renderOptionTag((string)$value, (string)$label, false) . LF;
        }
        return $output;
    }

    /**
     * Render the option tags.
     */
    protected function renderOptionTags(array $options): string
    {
        $output = '';
        foreach ($options as $value => $label) {
            $isSelected = $this->isSelected($value);
            $output .= $this->renderOptionTag((string)$value, (string)$label, $isSelected) . LF;
        }
        return $output;
    }

    /**
     * Render the Options.
     *
     * @throws Exception
     * @return array
     */
    public function getOptions(): array
    {
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, $this->extensionName);
        if (!$this->hasArgument('staticInfoTable') || ($this->arguments['staticInfoTable'] ?? '') == '') {
            throw new \Exception('Please configure the "staticInfoTable"-Argument for this ViewHelper.', 1378136534);
        }
        /** @var \SJBR\StaticInfoTables\Domain\Repository\AbstractEntityRepository $repository */
        $repository = lcfirst($this->arguments['staticInfoTable']) . 'Repository';
        if (!array_key_exists($repository, get_object_vars($this))) {
            throw new \Exception('Please configure the right table in the "staticInfoTable"-Argument for this ViewHelper.', 1378136533);
        }
        /** @var array $items */
        $items = $this->getItems($repository);
        /** @var string $valueFunction */
        $valueFunction = $this->getMethodnameFromArgumentsAndUnset('optionValueField', 'uid');
        /** @var string $labelFunction */
        $labelFunction = $this->getMethodnameFromArgumentsAndUnset('optionLabelField', 'nameLocalized');
        if (!($this->settings['countriesAllowed'] ?? false) && (!$this->hasArgument('sortByOptionLabel') || ($this->arguments['sortByOptionLabel'] ?? '') == '')) {
            $this->arguments['sortByOptionLabel'] = true;
        }
        /** @var bool $test Test only the first item if they have the needed functions */
        $test = true;
        $options = [];
        /** @var \SJBR\StaticInfoTables\Domain\Model\AbstractEntity $item */
        foreach ($items as $item) {
            if ($test && !method_exists($item, $valueFunction)) {
                throw new \Exception('Wrong optionValueField.', 1378136535);
            }
            if ($test && !method_exists($item, $labelFunction)) {
                throw new \Exception('Wrong optionLabelField.', 1378136536);
            }
            $test = false;
            $value = $item->{$valueFunction}();
            $label = $item->{$labelFunction}();
            if ($value != '' && $label != '') {
                $options[$value] = $label;
            }
        }
        $this->arguments['options'] = $options;
        $sortedOptions = $this->getParentOptions();
        $sortedOptions = $options;
        // Put default option after sorting to get it to the top of the items
        if ($this->hasArgument('defaultOptionLabel')) {
            $defaultOptionLabel = $this->arguments['defaultOptionLabel'];
            $defaultOptionValue = $this->hasArgument('defaultOptionValue') ? $this->arguments['defaultOptionValue'] : 0;
            $sortedOptions = [$defaultOptionValue => $defaultOptionLabel] + $sortedOptions;
        }
        return $sortedOptions;
    }

    /**
     * Render the option tags.
     *
     * @return array An associative array of options, key will be the value of the option tag
     */
    protected function getParentOptions(): array
    {
        if (!is_array($this->arguments['options']) && !$this->arguments['options'] instanceof \Traversable) {
            return [];
        }
        $options = [];
        $optionsArgument = $this->arguments['options'];
        foreach ($optionsArgument as $key => $value) {
            if (is_object($value) || is_array($value)) {
                if ($this->hasArgument('optionValueField')) {
                    $key = ObjectAccess::getPropertyPath($value, $this->arguments['optionValueField']);
                    if (is_object($key)) {
                        if (method_exists($key, '__toString')) {
                            $key = (string)$key;
                        } else {
                            throw new Exception('Identifying value for object of class "' . get_debug_type($value) . '" was an object.', 1247827428);
                        }
                    }
                } elseif ($this->persistenceManager->getIdentifierByObject($value) !== null) {
                    // @todo use $this->persistenceManager->isNewObject() once it is implemented
                    $key = $this->persistenceManager->getIdentifierByObject($value);
                } elseif (is_object($value) && method_exists($value, '__toString')) {
                    $key = (string)$value;
                } elseif (is_object($value)) {
                    throw new Exception('No identifying value for object of class "' . get_class($value) . '" found.', 1247826696);
                }
                if ($this->hasArgument('optionLabelField')) {
                    $value = ObjectAccess::getPropertyPath($value, $this->arguments['optionLabelField']);
                    if (is_object($value)) {
                        if (method_exists($value, '__toString')) {
                            $value = (string)$value;
                        } else {
                            throw new Exception('Label value for object of class "' . get_class($value) . '" was an object without a __toString() method.', 1247827553);
                        }
                    }
                } elseif (is_object($value) && method_exists($value, '__toString')) {
                    $value = (string)$value;
                } elseif ($this->persistenceManager->getIdentifierByObject($value) !== null) {
                    // @todo use $this->persistenceManager->isNewObject() once it is implemented
                    $value = $this->persistenceManager->getIdentifierByObject($value);
                }
            }
            $options[$key] = $value;
        }
        if ($this->arguments['sortByOptionLabel']) {
            asort($options, SORT_LOCALE_STRING);
        }
        return $options;
    }

    /**
     * Get Items
     *
     * @param string $repository
     * @return array
     */
    protected function getItems($repository)
    {
        if ($this->hasArgument('staticInfoTableSubselect')) {
            $items = $this->getItemsWithSubselect($repository);
        } elseif ($repository === 'countryRepository') {
            if (isset($this->settings['countriesAllowed']) && $this->settings['countriesAllowed']) {
                $items = $this->{$repository}->findAllowedByIsoCodeA3($this->settings['countriesAllowed']);
            } else {
                $items = $this->{$repository}->findAll()
                    ->toArray();
            }
        } elseif ($repository === 'languageRepository') {
            $items = $this->{$repository}->findAllNonConstructedNonSacred()
                ->toArray();
        } else {
            $items = $this->{$repository}->findAll()
                ->toArray();
        }
        return $items;
    }

    /**
     * Get items with custom sub select.
     *
     * @param string $repository
     * @return array
     */
    protected function getItemsWithSubselect($repository)
    {
        $items = [];
        $subselects = $this->arguments['staticInfoTableSubselect'] ?? [];
        foreach ($subselects as $fieldname => $fieldvalue) {
            // default implemented Subselect
            if (strtolower($fieldname) === 'country' && MathUtility::canBeInterpretedAsInteger($fieldvalue)) {
                $findby = 'findBy' . ucfirst($fieldname);
                $fieldvalue = $this->countryRepository->findByUid((int)$fieldvalue);
                $items = call_user_func_array([
                    $this->{$repository},
                    $findby,
                ], [$fieldvalue]);
                $items = $items->toArray();
            }
        }
        return $items;
    }

    /**
     * Return the in the arguments defined field, prepend 'get' and return it.
     * If the field is in the arguments not set it return the in the default defined value.
     *
     * @param string $field   fieldname like 'optionLabelField'
     * @param string $default default value like 'nameLocalized'
     * @return string
     */
    protected function getMethodnameFromArgumentsAndUnset($field, $default)
    {
        if (!$this->hasArgument($field) || $this->arguments[$field] == '') {
            $this->arguments[$field] = $default;
        }
        $methodName = 'get' . ucfirst($this->arguments[$field]);
        unset($this->arguments[$field]);
        return $methodName;
    }

    /**
     * Render the option tags.
     *
     * @param mixed $value Value to check for
     * @return bool True if the value should be marked as selected.
     */
    protected function isSelected($value): bool
    {
        $selectedValue = $this->getSelectedValue();
        if ($value === $selectedValue || (string)$value === $selectedValue) {
            return true;
        }
        if ($this->hasArgument('multiple')) {
            if ($selectedValue === null && $this->arguments['selectAllByDefault'] === true) {
                return true;
            }
            if (is_array($selectedValue) && in_array($value, $selectedValue)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves the selected value(s)
     *
     * @return mixed value string or an array of strings
     */
    protected function getSelectedValue()
    {
        $this->setRespectSubmittedDataValue(true);
        $value = $this->getValueAttribute();
        if (!is_array($value) && !$value instanceof \Traversable) {
            return $this->getOptionValueScalar($value);
        }
        $selectedValues = [];
        foreach ($value as $selectedValueElement) {
            $selectedValues[] = $this->getOptionValueScalar($selectedValueElement);
        }
        return $selectedValues;
    }

    /**
     * Get the option value for an object
     *
     * @param mixed $valueElement
     * @return string @todo: Does not always return string ...
     */
    protected function getOptionValueScalar($valueElement)
    {
        if (is_object($valueElement)) {
            if ($this->hasArgument('optionValueField')) {
                return ObjectAccess::getPropertyPath($valueElement, $this->arguments['optionValueField']);
            }
            // @todo use $this->persistenceManager->isNewObject() once it is implemented
            if ($this->persistenceManager->getIdentifierByObject($valueElement) !== null) {
                return $this->persistenceManager->getIdentifierByObject($valueElement);
            }
            return (string)$valueElement;
        }
        return $valueElement;
    }

    /**
     * Render one option tag
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     * @param bool $isSelected specifies whether to add selected attribute
     * @return string the rendered option tag
     */
    protected function renderOptionTag(string $value, string $label, bool $isSelected): string
    {
        $output = '<option value="' . htmlspecialchars($value) . '"';
        if ($isSelected) {
            $output .= ' selected="selected"';
        }
        $output .= '>' . htmlspecialchars($label) . '</option>';
        return $output;
    }
}
