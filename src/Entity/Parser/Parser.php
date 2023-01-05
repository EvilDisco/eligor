<?php

namespace App\Entity\Parser;

use App\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Parser extends BaseEntity
{
    /** @param string $name */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /** @var string */
    #[ORM\Column(type: Types::STRING)]
    protected string $name;

    /** @return string */
    public function getName(): string
    {
        return $this->name;
    }

    /** @param string $name */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}