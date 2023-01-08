<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterData\Domain\Entity\Transfer;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    /**
     * @test
     */
    public function fromArray(): void
    {
        $actual = Transfer::fromArray([
            'uid' => '1',
            'crdate' => '1234567890',
            'table_uid' => '42',
            'correlation_id' => 'some correlation id',
            'data' => 'some data',
            'transmit_success' => 0,
            'transmit_date' => '2345678901',
            'transmit_message' => 'some transmit message',
        ]);

        self::assertInstanceOf(Transfer::class, $actual);
        self::assertSame(1, $actual->uid);
        self::assertSame(1234567890, $actual->crdate);
        self::assertSame(42, $actual->tableUid);
        self::assertSame('some correlation id', $actual->correlationId);
        self::assertSame('some data', $actual->data);
        self::assertFalse($actual->transmitSuccess);
        self::assertSame(2345678901, $actual->transmitDate->getTimestamp());
        self::assertSame('some transmit message', $actual->transmitMessage);
    }
}
