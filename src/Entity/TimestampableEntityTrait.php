<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

trait TimestampableEntityTrait
{
    /** @var DateTimeInterface */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTimeInterface $createdAt;

    /** @var DateTimeInterface */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTimeInterface $updatedAt;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * this method should be called from constructor.
     */
    private function initDates(): void
    {
        try {
            $this->createdAt = new DateTimeImmutable();
            $this->updatedAt = new DateTimeImmutable();
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        try {
            $this->updatedAt = new DateTimeImmutable();
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
