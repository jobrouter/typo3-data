<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Transfer;

/**
 * @internal
 */
final class TransferResult
{
    /**
     * @var int
     * @readonly
     */
    public $total;
    /**
     * @var int
     * @readonly
     */
    public $errors;

    public function __construct(int $total, int $errors)
    {
        $this->total = $total;
        $this->errors = $errors;
    }
}
