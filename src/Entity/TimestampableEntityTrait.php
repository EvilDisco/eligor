<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

trait TimestampableEntityTrait
{
    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected DateTimeInterface $createdAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
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
    private function initDates()
    {
        try {
            $this->createdAt = new DateTimeImmutable();
            $this->updatedAt = new DateTimeImmutable();
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        try {
            $this->updatedAt = new DateTimeImmutable();
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
