<?php

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use \Sg3w\Inlinefluid\Controller\InlinefluidController;

(static function () {

    ExtensionUtility::configurePlugin(
        'Inlinefluid',
        'Editor',
        [InlinefluidController::class => 'show'],
        [InlinefluidController::class => 'show'],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionUtility::configurePlugin(
        'Inlinefluid',
        'EditorCached',
        [InlinefluidController::class => 'show'],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:inlinefluid/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );

    ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:inlinefluid/Configuration/TypoScript/setup.typoscript'"
    );

})();