<?php

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function () {


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
        'columnsOverrides' => []
    ];


    $typesConfig['columnsOverrides'] = [
        'bodytext' => [
            'label' => 'Fluid',
            'config' => [
                'renderType' => 't3editor',
                'wrap' => 'off',
                'format' => 'html',
            ],
        ],
    ];
    //}


    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['tt_content'], [
        'ctrl' => [
            // Add type icon class
            'typeicon_classes' => [
                'inlinefluid_editor' => 'extensions-inlinefluid-editor',
                'inlinefluid_editorcached' => 'extensions-inlinefluid-editor',
            ],
        ],
        'types' => [
            'inlinefluid_editor' => $typesConfig,
            'inlinefluid_editorcached' => $typesConfig,
        ],
    ]);




    $flexFile = 'FILE:EXT:inlinefluid/Configuration/FlexForms/InlinefluidEditor.xml';
    foreach (['inlinefluid_editor' => 'Inlinefluid Editor', 'inlinefluid_editorcached' => 'Inlinefluid Editor (cached)'] as $ctype => $label) {


        ExtensionManagementUtility::addTcaSelectItem(
            'tt_content',
            'CType',
            [
                $label,
                $ctype,
                'extensions-inlinefluid-editor'
            ],
            'html',
            'after'
        );


        ExtensionManagementUtility::addPiFlexFormValue(
            '*',
            'FILE:EXT:inlinefluid/Configuration/FlexForms/flexform_inlinefluid.xml',
            $ctype
        );

    }
});