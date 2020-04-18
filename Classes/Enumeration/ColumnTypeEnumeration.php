<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Enumeration;

final class ColumnTypeEnumeration
{
    public const TEXT = 1;
    public const INTEGER = 2;
    public const DECIMAL = 3;
    public const DATE = 4;
    public const DATETIME = 5;
}
