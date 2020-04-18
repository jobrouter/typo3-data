<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Transfer extends AbstractEntity
{
    /**
     * @var int
     */
    protected $tableUid = 0;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $data = '';

    /**
     * @var bool
     */
    protected $transmitSuccess = false;

    /**
     * @var \DateTime
     */
    protected $transmitDate;

    /**
     * @var string
     */
    protected $transmitMessage = '';

    public function getTableUid(): int
    {
        return $this->tableUid;
    }

    public function setTableUid(int $tableUid): void
    {
        $this->tableUid = $tableUid;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function isTransmitSuccess(): bool
    {
        return $this->transmitSuccess;
    }

    public function setTransmitSuccess(bool $transmitSuccess): void
    {
        $this->transmitSuccess = $transmitSuccess;
    }

    public function getTransmitDate(): ?\DateTime
    {
        return $this->transmitDate;
    }

    public function setTransmitDate(\DateTime $transmitDate): void
    {
        $this->transmitDate = $transmitDate;
    }

    public function getTransmitMessage(): string
    {
        return $this->transmitMessage;
    }

    public function setTransmitMessage(string $transmitMessage): void
    {
        $this->transmitMessage = $transmitMessage;
    }
}
