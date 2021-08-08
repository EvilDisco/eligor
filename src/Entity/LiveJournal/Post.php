<?php

namespace App\Entity\LiveJournal;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="livejournal_post")
 * @ORM\Entity()
 */
class Post extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity=Author::class)
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
    */
    protected Author $author;

    /**
     * @ORM\Column(type="string"): string
     */
    protected string $title;

    /**
     * @ORM\Column(type="text"): string
     */
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
