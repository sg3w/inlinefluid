<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\Configuration;

use Sg3w\Inlinefluid\Compatibility\CodeEditor;
use Sg3w\Inlinefluid\Compatibility\CoreVersion;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Picks the FlexForm data structure that fits the running core.
 *
 * Both files are generated from one definition by Build/GenerateFlexForms.php:
 *
 * - inlinefluid.xml         v13 and v14: the modern shape with type=link, type=file,
 *                           required=1, associative select items and codeEditor
 * - inlinefluid_v11_v12.xml v11 and v12: the legacy shape with the <TCEforms> wrapper,
 *                           eval=required, inputLink, inline FAL, numeric select items
 *                           and t3editor
 *
 * v11 understands the legacy shape natively. v12 runs the full TcaMigration over every
 * flex form data structure and still ships t3editor, so it uses the same file. v13
 * dropped both <TCEforms> and t3editor, which is why it needs the modern file.
 */
final class FlexFormDataStructure
{
    private const PATH = 'Configuration/FlexForms/';
    private const MODERN = 'inlinefluid.xml';
    private const LEGACY = 'inlinefluid_v11_v12.xml';

    /**
     * Returns the TCA "ds" value: a FILE: reference, so the structure stays overridable
     * and is parsed lazily by FlexFormTools.
     */
    public static function get(): string
    {
        if (CoreVersion::isAtLeast(13)) {
            return self::fileReference(self::MODERN);
        }

        if (CodeEditor::isAvailable()) {
            return self::fileReference(self::LEGACY);
        }

        return self::legacyWithoutCodeEditor();
    }

    private static function fileReference(string $fileName): string
    {
        return 'FILE:EXT:inlinefluid/' . self::PATH . $fileName;
    }

    /**
     * EXT:t3editor is not installed on this v11/v12 instance, so renderType "t3editor"
     * has no node registered and FormEngine would render an "Unknown type" warning box
     * instead of the field. Dropping the renderType and its format lets the fields fall
     * back to plain textareas.
     *
     * The generator always writes <format> directly after <renderType>t3editor</renderType>,
     * so this pair is unambiguous: select fields carry a renderType but never a format,
     * and the field named "format" of the file data provider is an array element
     * (<format type="array">) which the pattern does not match.
     *
     * Returning the structure inline instead of a FILE: reference is the exception that
     * disappears together with v11/v12 support.
     */
    private static function legacyWithoutCodeEditor(): string
    {
        $xml = (string)file_get_contents(
            ExtensionManagementUtility::extPath('inlinefluid') . self::PATH . self::LEGACY
        );

        return preg_replace(
            '#\s*<renderType>t3editor</renderType>\s*<format>[^<]*</format>#',
            '',
            $xml
        ) ?? $xml;
    }
}
