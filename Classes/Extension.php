<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData;

/**
 * @internal
 */
final class Extension
{
    public const KEY = 'jobrouter_data';

    private const LANGUAGE_PATH = 'LLL:EXT:' . self::KEY . '/Resources/Private/Language/';
    public const LANGUAGE_PATH_BACKEND_MODULE = self::LANGUAGE_PATH . 'BackendModule.xlf';
    public const LANGUAGE_PATH_CONTENT_ELEMENT = self::LANGUAGE_PATH . 'ContentElement.xlf';
    public const LANGUAGE_PATH_DATABASE = self::LANGUAGE_PATH . 'Database.xlf';
    public const LANGUAGE_PATH_FORMAT = self::LANGUAGE_PATH . 'Format.xlf';
    public const LANGUAGE_PATH_REPORT = self::LANGUAGE_PATH . 'Report.xlf';
    public const LANGUAGE_PATH_TOOLBAR = self::LANGUAGE_PATH . 'Toolbar.xlf';

    public const REGISTRY_NAMESPACE = 'tx_' . self::KEY;
}
