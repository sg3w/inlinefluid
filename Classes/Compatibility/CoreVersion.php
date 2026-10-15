<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\Compatibility;

use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * Tiny helper around Typo3Version.
 *
 * EXT:inlinefluid supports TYPO3 v11 LTS up to v14. Several registration APIs and
 * TCA/FlexForm details changed within that range, so configuration files need to
 * branch on the running core version.
 */
final class CoreVersion
{
    private static ?int $majorVersion = null;

    public static function getMajorVersion(): int
    {
        if (self::$majorVersion === null) {
            self::$majorVersion = (new Typo3Version())->getMajorVersion();
        }
        return self::$majorVersion;
    }

    public static function isAtLeast(int $majorVersion): bool
    {
        return self::getMajorVersion() >= $majorVersion;
    }

    public static function isBelow(int $majorVersion): bool
    {
        return self::getMajorVersion() < $majorVersion;
    }
}
