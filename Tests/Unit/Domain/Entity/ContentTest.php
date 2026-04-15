<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Domain\Entity;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Content;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $actual = Content::fromArray([
            'uid' => '42',
            'pi_flexform' => 'some flexform',
        ]);

        self::assertInstanceOf(Content::class, $actual);
        self::assertSame(42, $actual->uid);
        self::assertSame('some flexform', $actual->flexForm);
    }
}
