#!/usr/bin/env php
<?php

declare(strict_types=1);

/*  | This extension is made for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2019-2026 CCDS GmbH <kontakt@ccds.de>
 */

/**
 * Generates the two FlexForm data structures of EXT:inlinefluid.
 *
 * The data structure has to be written in two shapes, because the supported core
 * versions disagree about how a FlexForm is spelled:
 *
 *                         | v11            | v12            | v13 / v14
 *   ----------------------+----------------+----------------+----------------
 *   <TCEforms> wrapper    | required       | migrated       | unsupported
 *   required field        | eval=required  | migrated       | required=1
 *   link field            | inputLink      | migrated       | type=link
 *   image field           | inline/FAL     | migrated       | type=file
 *   select items          | numeric keys   | migrated       | label/value
 *   code editor           | t3editor       | t3editor       | codeEditor
 *
 * v12 runs the full TcaMigration over every flex form data structure
 * (FlexFormTools::migrateFlexFormTcaRecursive) and still ships the t3editor
 * render type, so one legacy file serves v11 and v12. v13 dropped both the
 * <TCEforms> support and t3editor, so v13/v14 get their own modern file.
 *
 * Both files are generated from the single definition in buildArray() below so
 * that they cannot drift apart.
 *
 * Usage:
 *   php Build/GenerateFlexForms.php           write both XML files
 *   php Build/GenerateFlexForms.php --check   verify the files are up to date
 *                                             (exit code 1 when they are not)
 */

final class FlexFormDataStructureGenerator
{
    private const LLL = 'LLL:EXT:inlinefluid/Resources/Private/Language/locallang_flexform.xlf:';

    /**
     * @param bool $legacy TRUE renders the v11/v12 shape, FALSE the v13+ shape.
     */
    public function __construct(
        private readonly bool $legacy,
        private readonly string $targetDescription
    ) {}

    public function toXml(): string
    {
        return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n"
            . '<!--' . "\n"
            . '    DO NOT EDIT - this file is generated.' . "\n"
            . '' . "\n"
            . '    Target:     ' . $this->targetDescription . "\n"
            . '    Source:     Build/GenerateFlexForms.php' . "\n"
            . '    Regenerate: php Build/GenerateFlexForms.php' . "\n"
            . '-->' . "\n"
            . '<T3DataStructure>' . "\n"
            . $this->arrayToXml($this->buildArray(), 1)
            . '</T3DataStructure>' . "\n";
    }

    /**
     * Serialises the data structure to the classic T3DataStructure XML.
     *
     * GeneralUtility::array2xml() cannot be used here: it strips every character that is
     * not alphanumeric from tag names, which would turn "sheet.variables" into
     * "sheetvariables" and "settings.addglobalvariables" into one unusable key. Dots are
     * perfectly valid in XML tag names, so the structure is written directly. Scalars are
     * written without a type attribute on purpose - TYPO3 compares some data structure
     * options against strings, for example section="1".
     *
     * @param array<string|int, mixed> $data
     */
    private function arrayToXml(array $data, int $level): string
    {
        $indent = str_repeat('    ', $level);
        $xml = '';
        foreach ($data as $key => $value) {
            $isNumeric = is_int($key) || (is_string($key) && preg_match('/^-?\d+$/', $key) === 1);
            $name = $isNumeric ? 'numIndex' : (string)$key;
            $open = $isNumeric ? 'numIndex index="' . (int)$key . '"' : $name;

            if (is_array($value)) {
                $xml .= $indent . '<' . $open . ' type="array">' . "\n"
                    . $this->arrayToXml($value, $level + 1)
                    . $indent . '</' . $name . '>' . "\n";
                continue;
            }

            $xml .= $indent . '<' . $open . '>'
                . htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1)
                . '</' . $name . '>' . "\n";
        }

        return $xml;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildArray(): array
    {
        return [
            'sheets' => [
                'sDEF' => $this->sheet('sheet.settings', null, [
                    'settings.addglobalvariables' => $this->field('settings.addGlobalVariables', [
                        'type' => 'select',
                        'renderType' => 'selectCheckBox',
                        'items' => $this->selectItems([
                            self::LLL . 'globals.request' => 'request',
                            self::LLL . 'globals.contentObject' => 'contentobject',
                            self::LLL . 'globals.page' => 'page',
                            self::LLL . 'globals.tsfe' => 'tsfe',
                            self::LLL . 'globals.tssetup' => 'tssetup',
                        ]),
                        'cols' => 3,
                    ]),
                ]),

                'sheet.variables' => $this->sheet('sheet.variables', 'sheet.variables.description', [
                    'variableproviders' => $this->section('provider.variables', [
                        'input' => $this->container('variables.input', [
                            'varname' => $this->variableNameField(100),
                            'data' => $this->field('field.variableData', $this->required([
                                'type' => 'input',
                                'size' => 255,
                                'eval' => 'trim',
                            ])),
                        ]),
                        'text' => $this->container('variables.text', [
                            'varname' => $this->variableNameField(100),
                            'data' => $this->field('field.variableData', $this->required([
                                'type' => 'text',
                                'cols' => 50,
                                'rows' => 10,
                                'eval' => 'trim',
                            ])),
                        ]),
                        'link' => $this->container('variables.link', [
                            'varname' => $this->variableNameField(100),
                            'data' => $this->field('field.variableData', $this->linkConfig()),
                        ]),
                        'rte' => $this->container('variables.rte', [
                            'varname' => $this->variableNameField(255),
                            'data' => $this->field('field.variableData', $this->required([
                                'type' => 'text',
                                'cols' => 100,
                                'rows' => 5,
                                'eval' => 'trim',
                                'enableRichtext' => 1,
                            ])),
                        ]),
                    ]),
                ]),

                'media' => $this->sheet('sheet.media', null, [
                    'mediaImage' => $this->field('field.image', $this->imageConfig()),
                ]),

                'sheet.data' => $this->sheet('sheet.data', 'sheet.data.description', [
                    'staticdataproviders' => $this->section('provider.static', [
                        'json' => $this->container('JSON', $this->dataBlobFields('json'), false),
                        'yaml' => $this->container('YAML', $this->dataBlobFields('yaml'), false),
                        'xml' => $this->container('XML', $this->dataBlobFields('xml'), false),
                        'csv' => $this->container('CSV', $this->dataBlobFields('csv'), false),
                        'serialized' => $this->container('Serialized', $this->dataBlobFields('php'), false),
                    ]),
                    'filedataprovider' => $this->section('provider.staticFile', [
                        'file' => $this->container('provider.staticFile.item', [
                            'varname' => $this->variableNameField(255),
                            'format' => $this->field('field.format', [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => $this->selectItems([
                                    'JSON' => 'json',
                                    'XML' => 'xml',
                                    'YAML' => 'yaml',
                                    'CSV' => 'csv',
                                    'Plain TXT' => 'plain',
                                ]),
                                'default' => 'json',
                            ]),
                            'filename' => $this->field('field.filename', $this->required([
                                'type' => 'input',
                                'size' => 50,
                                'eval' => 'trim',
                                'softref' => 'ext_fileref',
                            ]), 'field.filename.description'),
                        ]),
                    ]),
                ]),

                'sheet.dynamic' => $this->sheet('sheet.dynamic', 'sheet.dynamic.description', [
                    'dynamicdataproviders' => $this->section('provider.dynamic', [
                        'php' => $this->container('dynamic.php.title', [
                            'varname' => $this->variableNameField(255),
                            'providerData' => $this->field('field.providerClass', $this->required([
                                'type' => 'input',
                                'size' => 255,
                                'eval' => 'trim',
                                'default' => '\\Sg3w\\Inlinefluid\\DataProvider\\External\\ExternalDemoDataProvider',
                            ])),
                        ]),
                    ]),
                ]),

                'sheet.html' => $this->sheet('sheet.html', 'sheet.html.description', [
                    'htmlelements' => $this->section('html.blocks', [
                        'htmlelement' => $this->container('html.block', [
                            'position' => $this->field('field.htmlPosition', [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => $this->selectItems([
                                    self::LLL . 'html.before' => '0',
                                    self::LLL . 'html.after' => '1',
                                ]),
                            ]),
                            'content' => $this->field('field.htmlContent', $this->codeEditorConfig('html')),
                        ]),
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $el
     * @return array<string, mixed>
     */
    private function sheet(string $titleKey, ?string $descriptionKey, array $el): array
    {
        $labels = ['sheetTitle' => self::LLL . $titleKey];
        if ($descriptionKey !== null) {
            $labels['sheetDescription'] = self::LLL . $descriptionKey;
        }

        $root = $this->legacy ? ['TCEforms' => $labels] : $labels;
        $root['type'] = 'array';
        $root['el'] = $el;

        return ['ROOT' => $root];
    }

    /**
     * A repeatable FlexForm section.
     *
     * @param array<string, mixed> $el
     * @return array<string, mixed>
     */
    private function section(string $titleKey, array $el): array
    {
        return [
            'title' => self::LLL . $titleKey,
            'type' => 'array',
            // FlexFormTools compares this against the string "1"
            'section' => '1',
            'el' => $el,
        ];
    }

    /**
     * One container type inside a section.
     *
     * @param array<string, mixed> $el
     * @return array<string, mixed>
     */
    private function container(string $title, array $el, bool $translate = true): array
    {
        return [
            'title' => $translate ? self::LLL . $title : $title,
            'type' => 'array',
            'el' => $el,
        ];
    }

    /**
     * A single FlexForm field, wrapped in <TCEforms> on TYPO3 v11/v12.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function field(string $labelKey, array $config, ?string $descriptionKey = null): array
    {
        $field = ['label' => self::LLL . $labelKey];
        if ($descriptionKey !== null) {
            $field['description'] = self::LLL . $descriptionKey;
        }
        $field['config'] = $config;

        return $this->legacy ? ['TCEforms' => $field] : $field;
    }

    /**
     * @return array<string, mixed>
     */
    private function variableNameField(int $size): array
    {
        return $this->field('field.variableName', $this->required([
            'type' => 'input',
            'size' => $size,
            'eval' => 'trim',
        ]));
    }

    /**
     * Marks a field as required - part of "eval" up to v11, an own option since v12.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function required(array $config): array
    {
        if (!$this->legacy) {
            $config['required'] = 1;
            return $config;
        }

        $eval = $config['eval'] ?? '';
        $config['eval'] = $eval === '' ? 'required' : $eval . ',required';

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    private function linkConfig(): array
    {
        if (!$this->legacy) {
            return $this->required([
                'type' => 'link',
                'size' => 100,
            ]);
        }

        return $this->required([
            'type' => 'input',
            'renderType' => 'inputLink',
            'size' => 100,
            'eval' => 'trim',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function imageConfig(): array
    {
        $allowed = 'jpg,jpeg,png,webp,svg';
        $appearance = [
            'useSortable' => 1,
            'enabledControls' => [
                'new' => 0,
                'dragdrop' => 1,
                'sort' => 1,
                'hide' => 1,
                'delete' => 1,
            ],
        ];

        if (!$this->legacy) {
            return [
                'type' => 'file',
                'allowed' => $allowed,
                'appearance' => $appearance,
                'minitems' => 0,
                'maxitems' => 20,
            ];
        }

        return [
            'type' => 'inline',
            'foreign_table' => 'sys_file_reference',
            'foreign_field' => 'uid_foreign',
            'foreign_sortby' => 'sorting_foreign',
            'foreign_table_field' => 'tablenames',
            'foreign_match_fields' => [
                'fieldname' => 'mediaImage',
            ],
            'foreign_label' => 'uid_local',
            'foreign_selector' => 'uid_local',
            'foreign_selector_fieldTcaOverride' => [
                'config' => [
                    'appearance' => [
                        'elementBrowserType' => 'file',
                        'elementBrowserAllowed' => $allowed,
                    ],
                ],
            ],
            'appearance' => $appearance,
            'minitems' => 0,
            'maxitems' => 20,
        ];
    }

    /**
     * varname + data fields of the static data providers.
     *
     * @return array<string, mixed>
     */
    private function dataBlobFields(string $format): array
    {
        return [
            'varname' => $this->field('field.variableName', [
                'type' => 'input',
                'size' => 255,
                'eval' => 'trim',
            ]),
            'data' => $this->field('field.dataBlob', $this->codeEditorConfig($format), 'field.dataBlob.description'),
        ];
    }

    /**
     * A textarea with code highlighting.
     *
     * "renderType" is always written, directly followed by "format". On v11/v12 the
     * render type comes from EXT:t3editor; when that extension is not installed the
     * TCA override strips exactly this pair again so the field degrades to a plain
     * textarea - see Configuration/TCA/Overrides/tt_content.php.
     *
     * @return array<string, mixed>
     */
    private function codeEditorConfig(string $format): array
    {
        return [
            'type' => 'text',
            'cols' => 100,
            'rows' => 10,
            'wrap' => 'off',
            'renderType' => $this->legacy ? 't3editor' : 'codeEditor',
            'format' => $this->editorMode($format),
        ];
    }

    /**
     * Maps a data format to a highlighting mode the target core actually registers -
     * an unknown mode makes the code editor throw an InvalidModeException.
     *
     * EXT:t3editor (v11/v12) ships css, html, javascript, php, typoscript and xml;
     * the backend code editor (v13+) adds json and sql. There is no YAML or CSV mode
     * in either, so those fall back to the JavaScript highlighting.
     */
    private function editorMode(string $format): string
    {
        return match ($format) {
            'json' => $this->legacy ? 'javascript' : 'json',
            'yaml', 'csv' => 'javascript',
            'xml' => 'xml',
            'php' => 'php',
            default => 'html',
        };
    }

    /**
     * Select items: numeric keys up to v11, associative keys since v12.
     *
     * @param array<string, string> $labelToValue
     * @return array<int, array<int|string, string>>
     */
    private function selectItems(array $labelToValue): array
    {
        $items = [];
        foreach ($labelToValue as $label => $value) {
            $items[] = $this->legacy
                ? [(string)$label, $value]
                : ['label' => (string)$label, 'value' => $value];
        }

        return $items;
    }
}

// ---------------------------------------------------------------------------

$targets = [
    'inlinefluid.xml' => new FlexFormDataStructureGenerator(false, 'TYPO3 v13, v14'),
    'inlinefluid_v11_v12.xml' => new FlexFormDataStructureGenerator(true, 'TYPO3 v11, v12'),
];

$directory = dirname(__DIR__) . '/Configuration/FlexForms';
if (!is_dir($directory) && !mkdir($directory, 0o775, true) && !is_dir($directory)) {
    fwrite(STDERR, 'Could not create ' . $directory . PHP_EOL);
    exit(1);
}

$check = in_array('--check', $argv, true);
$outdated = [];

foreach ($targets as $fileName => $generator) {
    $path = $directory . '/' . $fileName;
    $xml = $generator->toXml();

    if ($check) {
        $current = is_file($path) ? (string)file_get_contents($path) : null;
        if ($current !== $xml) {
            $outdated[] = $fileName;
            printf("  %-26s OUT OF DATE\n", $fileName);
            continue;
        }
        printf("  %-26s up to date (%d bytes)\n", $fileName, strlen($xml));
        continue;
    }

    file_put_contents($path, $xml);
    printf("  %-26s written (%d bytes)\n", $fileName, strlen($xml));
}

if ($check && $outdated !== []) {
    fwrite(STDERR, PHP_EOL . 'Run "php Build/GenerateFlexForms.php" and commit the result.' . PHP_EOL);
    exit(1);
}

exit(0);
