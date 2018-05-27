<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Google Sign In',
    'description' => '',
    'category' => 'module',
    'author' => 'backend',
    'author_email' => 'mail@ringer.it',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
