<?php

declare(strict_types=1);

use Sg3w\Inlinefluid\Compatibility\CoreVersion;
use Sg3w\Inlinefluid\Controller\InlinefluidController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || defined('TYPO3_MODE') || die();

(static function (): void {
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

    $extensionPath = ExtensionManagementUtility::extPath('inlinefluid');
    $setup = (string)file_get_contents($extensionPath . 'Configuration/TypoScript/setup.typoscript');

    // The rendering definition is registered twice on purpose:
    // - "defaultTypoScript_setup" is always included, so the content elements also work
    //   in setups without the content rendering templates of fluid_styled_content
    // - "defaultContentRendering" is appended after the "=< lib.contentElement" definition
    //   that configurePlugin() just registered in that very bucket, and therefore wins
    ExtensionManagementUtility::addTypoScriptSetup($setup);
    ExtensionManagementUtility::addTypoScript('inlinefluid', 'setup', $setup, 'defaultContentRendering');

    // Configuration/page.tsconfig is loaded automatically since v12 and
    // ExtensionManagementUtility::addPageTSConfig() was removed in v14.
    if (CoreVersion::isBelow(12)) {
        ExtensionManagementUtility::addPageTSConfig(
            (string)file_get_contents($extensionPath . 'Configuration/page.tsconfig')
        );
    }
})();
