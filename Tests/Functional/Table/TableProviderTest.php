<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Table;

use Brotkrueml\JobRouterData\Table\TableProvider;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TableProviderTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_base',
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private TableProvider $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TableProvider($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function getCustomTablesWithNoCustomTablesAvailable(): void
    {
        $actual = $this->subject->getCustomTables();

        self::assertSame([], $actual);
    }
}
