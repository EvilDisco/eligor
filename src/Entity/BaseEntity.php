<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

class BaseEntity
{
    use TimestampableEntityTrait;

    public function __construct()
    {
        $this->initDates();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;

    /** @return int|null */
    public function getId(): ?int
    {
        return $this->id;
    }
}
