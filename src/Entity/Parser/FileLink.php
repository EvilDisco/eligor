<?php

namespace App\Entity\Parser;

use App\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class FileLink extends BaseEntity
{
    /** @var string */
    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Assert\NotBlank]
    protected string $link;

    /** @var string */
    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Assert\NotBlank]
    protected string $title;

    /** @var bool */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    protected bool $isDownloaded = false;

    public function __construct(
        string $link,
        string $title,
    ) {
        parent::__construct();
        $this->link = $link;
        $this->title = $title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
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
     * @return bool
     */
    public function isDownloaded(): bool
    {
        return $this->isDownloaded;
    }

    /**
     * @param bool $isDownloaded
     */
    public function setIsDownloaded(bool $isDownloaded): void
    {
        $this->isDownloaded = $isDownloaded;
    }
}
