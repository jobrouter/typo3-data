<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Exception;

final class TableNotFoundException extends \RuntimeException
{
    public static function forUid(int $uid): self
    {
        return new self(
            \sprintf(
                'Table with uid "%d" not found.',
                $uid,
            ),
            1672647403,
        );
    }

    public static function forHandle(string $handle): self
    {
        return new self(
            \sprintf(
                'Table with handle "%s" not found.',
                $handle,
            ),
            1672647404,
        );
    }
}
