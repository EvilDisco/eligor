<?php

namespace App\Entity\Livejournal;

use App\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'livejournal_author')]
class Author extends BaseEntity
{
    /** @var string */
    #[ORM\Column(type: Types::STRING)]
    protected string $name;

    /** @var string */
    #[ORM\Column(type: Types::STRING)]
    protected string $blog;

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getBlog(): string
    {
        return $this->blog;
    }

    /**
     * @param string $blog
     */
    public function setBlog(string $blog): void
    {
        $this->blog = $blog;
    }
}
