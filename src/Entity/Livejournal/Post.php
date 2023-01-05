<?php

namespace App\Entity\Livejournal;

use App\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'livejournal_post')]
class Post extends BaseEntity
{
    /** @param Author $author */
    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected Author $author;

    /** @param string $title */
    #[ORM\Column(type: Types::STRING)]
    protected string $title;

    /** @param string $text */
    #[ORM\Column(type: Types::TEXT)]
    protected string $text;

    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
