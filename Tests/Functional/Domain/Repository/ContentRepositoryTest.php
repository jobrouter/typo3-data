<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Functional\Domain\Repository;

use JobRouter\AddOn\Typo3Data\Domain\Repository\ContentRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_base',
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private ContentRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ContentRepository($this->getConnectionPool());
    }

    #[Test]
    public function findByFlexFormFieldFindsReturnsRowsCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tt_content.csv');

        $actual = $this->subject->findByFlexFormField();

        self::assertIsArray($actual);
        self::assertCount(2, $actual);
        self::assertSame(1, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
    }

    #[Test]
    public function migrateUpdatesTheFieldsCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tt_content.csv');

        $this->subject->updateTableFieldAndResetFlexFormField(1, 41);

        $actual = $this->getConnectionPool()->getConnectionForTable('tt_content')
            ->select(
                ['pi_flexform', 'tx_jobrouterdata_table'],
                'tt_content',
                [
                    'uid' => 1,
                ],
            )
            ->fetchAssociative();

        self::assertSame('', $actual['pi_flexform']);
        self::assertSame(41, (int) $actual['tx_jobrouterdata_table']);
    }
}
