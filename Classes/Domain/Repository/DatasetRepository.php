<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Domain\Repository;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for datasets
 */
class DatasetRepository extends Repository
{
    protected $defaultOrderings = [
        'jrid' => QueryInterface::ORDER_ASCENDING,
    ];
}
