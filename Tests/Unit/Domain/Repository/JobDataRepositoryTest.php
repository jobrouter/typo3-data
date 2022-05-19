<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Repository;

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\ConnectionNotAvailableException;
use Brotkrueml\JobRouterData\Exception\TableNotAvailableException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class JobDataRepositoryTest extends TestCase
{
    /**
     * @var RestClientFactory&Stub
     */
    private $restClientFactoryStub;

    /**
     * @var TableRepository&MockObject
     */
    private $tableRepositoryStub;

    /**
     * @var Table
     */
    private $table;

    protected function setUp(): void
    {
        $this->restClientFactoryStub = $this->createStub(RestClientFactory::class);

        $this->tableRepositoryStub = $this->getMockBuilder(TableRepository::class)
            ->disableOriginalConstructor()
            ->addMethods(['findOneByHandle'])
            ->getMock();

        $this->table = new Table();
    }

    /**
     * @test
     */
    public function methodCallThrowsExceptionWhenTableNotAvailable(): void
    {
        $this->expectException(TableNotAvailableException::class);
        $this->expectExceptionCode(1595951023);
        $this->expectExceptionMessage('Table with handle "some handle" is not available!');

        $this->tableRepositoryStub
            ->method('findOneByHandle')
            ->with('some handle')
            ->willReturn(null);

        (new JobDataRepository($this->restClientFactoryStub, $this->tableRepositoryStub, 'some handle'))
            ->findAll();
    }

    /**
     * @test
     */
    public function methodCallThrowsExceptionWhenConnectionNotAvailable(): void
    {
        $this->expectException(ConnectionNotAvailableException::class);
        $this->expectExceptionCode(1595951024);
        $this->expectExceptionMessage('Connection for table with handle "some handle" is not available!');

        $this->tableRepositoryStub
            ->method('findOneByHandle')
            ->with('some handle')
            ->willReturn($this->table);

        (new JobDataRepository($this->restClientFactoryStub, $this->tableRepositoryStub, 'some handle'))
            ->findAll();
    }
}
