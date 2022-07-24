<?php

namespace App\Entity\Parser;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class FileLink extends BaseEntity
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=500)
     */
    protected string $link;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=500)
     */
    protected string $title;

    public function __construct(
        string $link,
        string $title,
    ) {
        parent::__construct();
        $this->link = $link;
        $this->title = $title;
    }

    /**
     * @return string
     */
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
}
