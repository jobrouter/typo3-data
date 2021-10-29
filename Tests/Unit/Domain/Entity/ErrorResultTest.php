<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterData\Domain\Entity\CountResult;
use PHPUnit\Framework\TestCase;

final class ErrorResultTest extends TestCase
{
    /**
     * @test
     */
    public function constructSetsPropertiesCorrectly(): void
    {
        $subject = new CountResult(42, 12);

        self::assertSame(42, $subject->total);
        self::assertSame(12, $subject->errors);
    }
}
