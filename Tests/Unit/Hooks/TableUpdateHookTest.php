<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Hooks;

use Brotkrueml\JobRouterData\Hooks\TableUpdateHook;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class TableUpdateHookTest extends TestCase
{
    /**
     * @var MockObject|Connection
     */
    private $connectionMock;

    /**
     * @var MockObject|ConnectionPool
     */
    private $connectionPoolMock;

    /**
     * @var MockObject|DataHandler
     */
    private $dataHandlerMock;

    /**
     * @var TableUpdateHook
     */
    private $subject;

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);
        $this->connectionPoolMock = $this->createMock(ConnectionPool::class);
        $this->dataHandlerMock = $this->createMock(DataHandler::class);

        $this->subject = new TableUpdateHook($this->connectionPoolMock);
    }

    /**
     * @test
     */
    public function tableIsDeletedRemovesDatasets(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('delete')
            ->with(
                self::equalTo('tx_jobrouterdata_domain_model_dataset'),
                self::equalTo([
                    'table_uid' => 42,
                ]),
                self::equalTo([
                    'table_uid' => Connection::PARAM_INT,
                ])
            );

        $this->connectionPoolMock
            ->expects(self::once())
            ->method('getConnectionForTable')
            ->with('tx_jobrouterdata_domain_model_dataset')
            ->willReturn($this->connectionMock);

        $this->subject->processCmdmap_postProcess(
            'delete',
            'tx_jobrouterdata_domain_model_table',
            42,
            '1',
            $this->dataHandlerMock
        );
    }

    /**
     * @test
     */
    public function otherTableIsProcessedDoesNothing(): void
    {
        $this->connectionPoolMock
            ->expects(self::never())
            ->method('getConnectionForTable');

        $this->subject->processCmdmap_postProcess(
            'delete',
            'some_other_table',
            42,
            '1',
            $this->dataHandlerMock
        );
    }

    /**
     * @test
     */
    public function otherActionThanDeletDoesNothing(): void
    {
        $this->connectionPoolMock
            ->expects(self::never())
            ->method('getConnectionForTable');

        $this->subject->processCmdmap_postProcess(
            'copy',
            'tx_jobrouterdata_domain_model_table',
            42,
            '1',
            $this->dataHandlerMock
        );
    }
}
