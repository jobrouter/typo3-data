<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Helper\Entity;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterData\Domain\Entity\Column;

final class ColumnBuilder
{
    public function build(int $uid, FieldType $type = FieldType::Text, int $decimalPlaces = 2): Column
    {
        return Column::fromArray([
            'uid' => $uid,
            'name' => '',
            'label' => '',
            'type' => $type->value,
            'decimal_places' => $decimalPlaces,
            'field_size' => 0,
            'alignment' => '',
            'sorting_priority' => 0,
            'sorting_order' => '',
        ]);
    }
}
