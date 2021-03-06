<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Scheduler',
    'description' => 'The TYPO3 Scheduler let\'s you register tasks to happen at a specific time',
    'category' => 'misc',
    'version' => '8.1.0',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearcacheonload' => 0,
    'author' => 'Francois Suter',
    'author_email' => 'francois@typo3.org',
    'author_company' => '',
    'constraints' => array(
        'depends' => array(
            'typo3' => '8.1.0-8.1.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
