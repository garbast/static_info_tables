<?php
/**
 * Extension Manager configuration file for ext "static_info_tables".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Static Info Tables',
    'description' => 'Data and API for countries, languages and currencies for use in TYPO3 CMS.',
    'category' => 'misc',
    'version' => '12.4.3',
    'state' => 'stable',
    'author' => 'Stanislas Rolland/René Fritz',
    'author_email' => 'typo3AAAA@sjbr.ca',
    'author_company' => 'SJBR',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'extbase' => '12.4.0-12.4.99',
            'extensionmanager' => '12.4.0-12.4.99'
        ]
    ],
    'autoload' => [
        'psr-4' => [
        	'SJBR\\StaticInfoTables\\' => 'Classes'
        ]
    ]
];
