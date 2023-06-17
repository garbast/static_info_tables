<?php
// Currency reference data from ISO 4217
return [
    'ctrl' => [
        'label' => 'cu_name_en',
        'label_alt' => 'cu_iso_3',
        'label_alt_force' => 1,
        'label_userFunc' => \SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaLabelProcessor::class . '->addIsoCodeToLabel',
        'adminOnly' => true,
        'rootLevel' => 1,
        'is_static' => 1,
        'readOnly' => 1,
        'default_sortby' => 'ORDER BY cu_name_en',
        'delete' => 'deleted',
        'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies.title',
        'iconfile' => 'EXT:static_info_tables/Resources/Public/Images/Icons/static_currencies.svg',
        'searchFields' => 'cu_name_en',
    ],
    'columns' => [
        'deleted' => [
            'readonly' => 1,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:deleted',
            'config' => [
                'type' => 'check',
            ],
        ],
        'cu_iso_3' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_iso_3',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '3',
                'eval' => '',
                'default' => '',
            ],
        ],
        'cu_iso_nr' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_iso_nr',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '7',
                'max' => '3',
                'eval' => '',
                'default' => '0',
            ],
        ],
        'cu_name_en' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_name_en',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '18',
                'max' => '40',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cu_sub_name_en' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_name_en',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '18',
                'max' => '20',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cu_symbol_left' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_symbol_left',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '12',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cu_symbol_right' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_symbol_right',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '12',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cu_thousands_point' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_thousands_point',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '3',
                'max' => '1',
                'eval' => '',
                'default' => '',
            ],
        ],
        'cu_decimal_point' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_decimal_point',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '3',
                'max' => '1',
                'eval' => '',
                'default' => '',
            ],
        ],
        'cu_decimal_digits' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_decimal_digits',
            'exclude' => false,
            'config' => [
                'type' => 'number',
				'range' => [
					'lower' => 0
				],
				'size' => 20,
                'default' => 0
            ],
        ],
        'cu_sub_divisor' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_divisor',
            'exclude' => false,
            'config' => [
                'type' => 'number',
				'range' => [
					'lower' => 1
				],
				'size' => 20,
                'default' => 1
            ]
        ],
        'cu_sub_symbol_left' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_symbol_left',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '12',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'cu_sub_symbol_right' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_symbol_right',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '12',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'cu_name_en,--palette--;;1,--palette--;;2,cu_sub_name_en,--palette--;;3'
        ]
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'cu_iso_nr,cu_iso_3',
            'canNotCollapse' => '1'
        ],
        '2' => [
            'showitem' => 'cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point',
            'canNotCollapse' => '1'
        ],
        '3' => [
            'showitem' => 'cu_sub_symbol_left,cu_sub_symbol_right,cu_decimal_digits,cu_sub_divisor',
            'canNotCollapse' => '1'
        ]
    ]
];
