<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Register plugin
ExtensionUtility::registerPlugin(
    'simon_integration',
    'Settings',
    'SIMON Settings'
);

// Add scheduler task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Simon\Integration\Task\SubmitDataTask::class] = [
    'extension' => 'simon_integration',
    'title' => 'SIMON Data Submission',
    'description' => 'Submit site data to SIMON API',
    'additionalFields' => \Simon\Integration\Task\SubmitDataAdditionalFieldProvider::class,
];
