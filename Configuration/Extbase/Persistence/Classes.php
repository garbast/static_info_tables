<?php
/**
 * Classes.php
 *
 * @author Klaus Fiedler <klaus@tollwerk.de> / @jkphl
 * @copyright Copyright © 2019 Klaus Fiedler <klaus@tollwerk.de>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2019 Klaus Fiedler <klaus@tollwerk.de>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/


declare(strict_types=1);
return [
    \SJBR\StaticInfoTables\Domain\Model\Country::class => [
        'tableName' => 'static_countries',
        'properties' => [
            'addressFormat' => [
                'fieldName' => 'cn_address_format',
            ],
            'capitalCity' => [
                'fieldName' => 'cn_capital',
            ],
            'currencyIsoCodeA3' => [
                'fieldName' => 'cn_currency_iso_3',
            ],
            'currencyIsoCodeNumber' => [
                'fieldName' => 'cn_currency_iso_nr',
            ],
            'euMember' => [
                'fieldName' => 'cn_eu_member',
            ],
            'isoCodeA2' => [
                'fieldName' => 'cn_iso_2',
            ],
            'isoCodeA3' => [
                'fieldName' => 'cn_iso_3',
            ],
            'isoCodeNumber' => [
                'fieldName' => 'cn_iso_nr',
            ],
            'officialNameLocal' => [
                'fieldName' => 'cn_official_name_local',
            ],
            'officialNameEn' => [
                'fieldName' => 'cn_official_name_en',
            ],
            'parentTerritoryUnCodeNumber' => [
                'fieldName' => 'cn_parent_tr_iso_nr',
            ],
            'phonePrefix' => [
                'fieldName' => 'mapOnProperty',
            ],
            'shortNameLocal' => [
                'fieldName' => 'cn_short_local',
            ],
            'shortNameEn' => [
                'fieldName' => 'cn_short_en',
            ],
            '###' => [
                'fieldName' => '###',
            ],
            'topLevelDomain' => [
                'fieldName' => 'cn_tldomain',
            ],
            'unMember' => [
                'fieldName' => 'cn_uno_member',
            ],
            'zoneFlag' => [
                'fieldName' => 'cn_zone_flag',
            ],
            'countryZones' => [
                'fieldName' => 'cn_country_zones',
            ],
            'deleted' => [
                'fieldName' => 'deleted',
            ],
        ],
    ],
    \SJBR\StaticInfoTables\Domain\Model\CountryZone::class => [
        'tableName' => 'static_country_zones',
        'properties' => [
            'countryIsoCodeA2' => [
                'fieldName' => 'zn_country_iso_2',
            ],
            'countryIsoCodeA3' => [
                'fieldName' => 'zn_country_iso_3',
            ],
            'countryIsoCodeNumber' => [
                'fieldName' => 'zn_country_iso_nr',
            ],
            'isoCode' => [
                'fieldName' => 'zn_code',
            ],
            'localName' => [
                'fieldName' => 'zn_name_local',
            ],
            'nameEn' => [
                'fieldName' => 'zn_name_en',
            ],
            'deleted' => [
                'fieldName' => 'deleted',
            ],
        ],
    ],
    \SJBR\StaticInfoTables\Domain\Model\Currency::class => [
        'tableName' => 'static_currencies',
        'properties' => [
            'decimalDigits' => [
                'fieldName' => 'cu_decimal_digits',
            ],
            'decimalPoint' => [
                'fieldName' => 'cu_decimal_point',
            ],
            'divisor' => [
                'fieldName' => 'cu_sub_divisor',
            ],
            'isoCodeA3' => [
                'fieldName' => 'cu_iso_3',
            ],
            'isoCodeNumber' => [
                'fieldName' => 'cu_iso_nr',
            ],
            '###' => [
                'fieldName' => '###',
            ],
            'nameEn' => [
                'fieldName' => 'cu_name_en',
            ],
            'subdivisionNameEn' => [
                'fieldName' => 'cu_sub_name_en',
            ],
            'subdivisionSymbolLeft' => [
                'fieldName' => 'cu_sub_symbol_left',
            ],
            'subdivisionSymbolRight' => [
                'fieldName' => 'cu_sub_symbol_right',
            ],
            'symbolLeft' => [
                'fieldName' => 'cu_symbol_left',
            ],
            'symbolRight' => [
                'fieldName' => 'cu_symbol_right',
            ],
            'thousandsPoint' => [
                'fieldName' => 'cu_thousands_point',
            ],
            'deleted' => [
                'fieldName' => 'deleted',
            ],
        ],
    ],
    \SJBR\StaticInfoTables\Domain\Model\Language::class => [
        'tableName' => 'static_languages',
        'properties' => [
            'collatingLocale' => [
                'fieldName' => 'lg_collate_locale',
            ],
            'countryIsoCodeA2' => [
                'fieldName' => 'lg_country_iso_2',
            ],
            'constructedLanguage' => [
                'fieldName' => 'lg_constructed',
            ],
            'isoCodeA2' => [
                'fieldName' => 'lg_iso_2',
            ],
            'localName' => [
                'fieldName' => 'lg_name_local',
            ],
            'nameEn' => [
                'fieldName' => 'lg_name_en',
            ],
            'lg_sacred' => [
                'fieldName' => 'sacredLanguage',
            ],
            'lg_typo3' => [
                'fieldName' => 'typo3Code',
            ],
            'deleted' => [
                'fieldName' => 'deleted',
            ],
        ],
    ],
    \SJBR\StaticInfoTables\Domain\Model\Territory::class => [
        'tableName' => 'static_territories',
        'properties' => [
            'unCodeNumber' => [
                'fieldName' => 'tr_iso_nr',
            ],
            'nameEn' => [
                'fieldName' => 'tr_name_en',
            ],
            'parentTerritoryUnCodeNumber' => [
                'fieldName' => 'tr_parent_iso_nr',
            ],
            'deleted' => [
                'fieldName' => 'deleted',
            ],
        ],
    ],
    \SJBR\StaticInfoTables\Domain\Model\SystemLanguage::class => [
        'tableName' => 'sys_language',
        'properties' => [
            'title' => [
                'fieldName' => 'title'
            ],
            'isoLanguage' => [
                'fieldName' => 'static_lang_isocode'
            ],
        ],
    ],
];