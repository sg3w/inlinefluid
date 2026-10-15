<?php

declare(strict_types=1);

use Sg3w\Inlinefluid\Compatibility\CodeEditor;
use Sg3w\Inlinefluid\Compatibility\CoreVersion;
use Sg3w\Inlinefluid\Configuration\FlexFormDataStructure;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || defined('TYPO3_MODE') || die();

(static function (): void {
    $iconIdentifier = 'extensions-inlinefluid-editor';
    $cTypes = [
        'inlinefluid_editor' => 'Inlinefluid Editor',
        'inlinefluid_editorcached' => 'Inlinefluid Editor (cached)',
    ];

    // The "Fluid" field: a code editor where available, a plain textarea otherwise.
    $bodytextConfig = [
        'wrap' => 'off',
    ];
    $codeEditorRenderType = CodeEditor::renderType();
    if ($codeEditorRenderType !== null) {
        $bodytextConfig['renderType'] = $codeEditorRenderType;
        $bodytextConfig['format'] = 'html';
    }

    $typesConfig = [
        'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,bodytext,pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ',
        'columnsOverrides' => [
            'bodytext' => [
                'label' => 'Fluid',
                'config' => $bodytextConfig,
            ],
        ],
    ];

    ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['tt_content'], [
        'ctrl' => [
            // Add type icon class
            'typeicon_classes' => array_fill_keys(array_keys($cTypes), $iconIdentifier),
        ],
        'types' => array_fill_keys(array_keys($cTypes), $typesConfig),
    ]);

    $dataStructure = FlexFormDataStructure::get();

    foreach ($cTypes as $cType => $label) {
        ExtensionManagementUtility::addTcaSelectItem(
            'tt_content',
            'CType',
            CoreVersion::isAtLeast(12)
                ? ['label' => $label, 'value' => $cType, 'icon' => $iconIdentifier]
                : [$label, $cType, $iconIdentifier],
            'html',
            'after'
        );

        if (CoreVersion::isAtLeast(14)) {
            // addPiFlexFormValue() is deprecated since v14, the data structure goes
            // straight into the columnsOverrides of the content type.
            $GLOBALS['TCA']['tt_content']['types'][$cType]['columnsOverrides']['pi_flexform']['config']['ds']
                = $dataStructure;
        } else {
            ExtensionManagementUtility::addPiFlexFormValue('*', $dataStructure, $cType);
        }
    }
})();
