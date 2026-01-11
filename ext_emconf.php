<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'SIMON Integration',
    'description' => 'Integration with SIMON monitoring system for TYPO3',
    'category' => 'module',
    'author' => 'SIMON Team',
    'author_email' => 'support@simon.example.com',
    'author_company' => 'SIMON',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Simon\\Integration\\' => 'Classes/',
        ],
    ],
];
