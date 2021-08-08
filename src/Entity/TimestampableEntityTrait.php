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
    protected DateTimeInterface $dateCreate;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected DateTimeInterface $dateUpdate;

    /**
     * this method should be called from constructor.
     */
    private function initDates()
    {
        try {
            $this->dateCreate = new DateTimeImmutable();
            $this->dateUpdate = new DateTimeImmutable();
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    public function getDateUpdate(): ?DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function getDateCreate(): ?DateTimeInterface
    {
        return $this->dateCreate;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        try {
            $now = new DateTimeImmutable();
            $this->dateUpdate = $now;
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
