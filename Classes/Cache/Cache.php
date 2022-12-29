<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Cache;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class Cache
{
    private const CACHE_TAG_TEMPLATE = 'tx_jobrouterdata_table_%d';

    public static function clearCacheByTable(int $tableUid): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        static::getCacheManager()->flushCachesInGroupByTag('pages', static::getCacheTagForTable($tableUid));
    }

    private static function getCacheTagForTable(int $tableUid): string
    {
        return \sprintf(static::CACHE_TAG_TEMPLATE, $tableUid);
    }

    private static function getCacheManager(): CacheManager
    {
        return GeneralUtility::makeInstance(CacheManager::class);
    }
}
