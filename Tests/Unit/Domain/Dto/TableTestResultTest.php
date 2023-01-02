<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Dto;

use Brotkrueml\JobRouterData\Domain\Dto\TableTestResult;
use PHPUnit\Framework\TestCase;

final class TableTestResultTest extends TestCase
{
    /**
     * @test
     */
    public function toJsonReturnsCorrectJsonWhenNoErrorMessageIsGiven(): void
    {
        $subject = new TableTestResult('');

        self::assertJsonStringEqualsJsonString('{"check": "ok"}', $subject->toJson());
    }

    /**
     * @test
     */
    public function toJsonReturnsCorrectJsonWhenErrorMessageIsGiven(): void
    {
        $subject = new TableTestResult('some error message');

        self::assertJsonStringEqualsJsonString('{"error": "some error message"}', $subject->toJson());
    }
}
