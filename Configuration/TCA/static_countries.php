<?php
// Country reference data from ISO 3166-1
return [
    'ctrl' => [
        'label' => 'cn_short_en',
        'label_alt' => 'cn_iso_2',
        'label_alt_force' => 1,
        'label_userFunc' => \SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaLabelProcessor::class . '->addIsoCodeToLabel',
        'adminOnly' => true,
        'rootLevel' => 1,
        'is_static' => 1,
        'readOnly' => 1,
        'default_sortby' => 'ORDER BY cn_short_en',
        'delete' => 'deleted',
        'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries.title',
        'iconfile' => 'EXT:static_info_tables/Resources/Public/Images/Icons/static_countries.svg',
        'searchFields' => 'cn_short_en,cn_official_name_local,cn_official_name_en',
    ],
    'columns' => [
        'cn_official_name_local' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_official_name_local',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '25',
                'max' => '128',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cn_official_name_en' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_official_name_en',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '25',
                'max' => '50',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'deleted' => [
            'readonly' => 1,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:deleted',
            'config' => [
                'type' => 'check',
            ],
        ],
        'cn_iso_2' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_iso_2',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '2',
                'eval' => '',
                'default' => '',
            ],
        ],
        'cn_iso_3' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_iso_3',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '3',
                'eval' => '',
                'default' => '',
            ],
        ],
        'cn_iso_nr' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_iso_nr',
            'exclude' => false,
            'config' => [
                'type' => 'number',
				'range' => [
					'lower' => 0
				],
				'size' => 20,
                'default' => 0
            ]
        ],
        'cn_parent_territory_uid' => [
            'exclude' => false,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_parent_territory_uid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                    	'label' => '', 
                    	'value' => 0
                    ]
                ],
                'foreign_table' => 'static_territories',
                'foreign_table_where' => 'ORDER BY static_territories.tr_name_en',
                'itemsProcFunc' => \SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaSelectItemsProcessor::class . '->translateTerritoriesSelector',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0
            ],
        ],
        'cn_parent_tr_iso_nr' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'cn_capital' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_capital',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '15',
                'max' => '45',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cn_tldomain' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_tldomain',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '',
                'eval' => '',
                'default' => '',
            ],
        ],
        'cn_currency_uid' => [
            'exclude' => false,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_currency_uid',
            'config' => [
                'type' => 'group',
                'allowed' => 'static_currencies',
                'foreign_table' => 'static_currencies',
                'foreign_table_where' => 'ORDER BY static_currencies.cu_name_en',
                'suggestOptions' => [
                    'default' => [
                        'pidList' => '0',
                    ],
                ],
                'fieldWizard' => [
                    'recordsOverview' => [
                        'disabled' => true,
                    ],
                    'tableList' => [
                        'disabled' => true,
                    ],
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'cn_currency_iso_nr' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'cn_currency_iso_3' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'cn_phone' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_phone',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '20',
                'eval' => '',
                'default' => '0',
            ],
        ],
        'cn_eu_member' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_eu_member',
            'exclude' => false,
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'cn_uno_member' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_uno_member',
            'exclude' => false,
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'cn_address_format' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format',
            'exclude' => false,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                    	'label' => '',
                    	'value' => 0
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_1',
                        'value' => 1
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_2',
                        'value' => 2
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_3',
                        'value' => 3
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_4',
                        'value' => 4
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_5',
                        'value' => 5
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_6',
                        'value' => 6
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_7',
                        'value' => 7
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_8',
                        'value' => 8
                    ],
                    [
                        'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_address_format_9',
                        'value' => 9
                    ]
                ],
                'default' => 0
            ]
        ],
        'cn_zone_flag' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_zone_flag',
            'exclude' => false,
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'cn_short_local' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_short_local',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '25',
                'max' => '50',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cn_short_en' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_short_en',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '25',
                'max' => '50',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cn_country_zones' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries_item.cn_country_zones',
            'exclude' => false,
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'static_country_zones',
                'foreign_field' => 'zn_country_uid',
                'foreign_table_field' => 'zn_country_table',
                'foreign_default_sortby' => 'zn_name_local',
                'maxitems' => '200',
                'appearance' => [
                    'expandSingle' => 1,
                    'newRecordLinkAddTitle' => 1,
                ],
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'cn_short_local,cn_official_name_local,cn_official_name_en,--palette--;;1,--palette--;;5,--palette--;;2,--palette--;;3,--palette--;;6,--palette--;;4,cn_short_en,cn_country_zones'
        ]
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'cn_iso_nr,cn_iso_2,cn_iso_3',
            'canNotCollapse' => '1'
        ],
        '2' => [
            'showitem' => 'cn_currency_uid,cn_currency_iso_nr,cn_currency_iso_3',
            'canNotCollapse' => '1'
        ],
        '3' => [
            'showitem' => 'cn_capital,cn_uno_member,cn_eu_member',
            'canNotCollapse' => '1'
        ],
        '4' => [
            'showitem' => 'cn_address_format,cn_zone_flag',
            'canNotCollapse' => '1'
        ],
        '5' => [
            'showitem' => 'cn_parent_territory_uid,cn_parent_tr_iso_nr',
            'canNotCollapse' => '1'
        ],
        '6' => [
            'showitem' => 'cn_phone,cn_tldomain',
            'canNotCollapse' => '1'
        ]
    ]
];
