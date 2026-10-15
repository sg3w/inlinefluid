<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\Compatibility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Tells which code editor the running core provides.
 *
 * v11 and v12 get it from EXT:t3editor as renderType "t3editor", v13 moved it into
 * EXT:backend and renamed the renderType to "codeEditor".
 */
final class CodeEditor
{
    /**
     * The renderType of the code editor, or NULL when no code editor is available
     * because EXT:t3editor is not installed on v11/v12.
     */
    public static function renderType(): ?string
    {
        if (CoreVersion::isAtLeast(13)) {
            // Part of EXT:backend since v13
            return 'codeEditor';
        }

        return ExtensionManagementUtility::isLoaded('t3editor') ? 't3editor' : null;
    }

    public static function isAvailable(): bool
    {
        return self::renderType() !== null;
    }
}
