<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class BaseEntity
{
    use TimestampableEntityTrait;

    public function __construct()
    {
        $this->initDates();
    }

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
