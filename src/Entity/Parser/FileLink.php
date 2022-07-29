<?php

namespace App\Entity\Parser;

use App\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class FileLink extends BaseEntity
{
    /** @param Parser $parser */
    #[ORM\ManyToOne(targetEntity: Parser::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Parser $parser;

    /** @var string */
    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Assert\NotBlank]
    protected string $link;

    /** @var string */
    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Assert\NotBlank]
    protected string $title;

    /** @var FileLinkStatusEnum */
    #[ORM\Column(type: Types::STRING, enumType: FileLinkStatusEnum::class)]
    protected FileLinkStatusEnum $status = FileLinkStatusEnum::NotDownloaded;

    public function __construct(
        Parser $parser,
        string $link,
        string $title,
    ) {
        parent::__construct();
        $this->parser = $parser;
        $this->link = $link;
        $this->title = $title;
    }

    /**
     * @return Parser
     */
    public function getParser(): Parser
    {
        return $this->parser;
    }

    /**
     * @param Parser $parser
     */
    public function setParser(Parser $parser): void
    {
        $this->parser = $parser;
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
     * @return FileLinkStatusEnum
     */
    public function getStatus(): FileLinkStatusEnum
    {
        return $this->status;
    }

    /**
     * @param FileLinkStatusEnum $status
     */
    public function setStatus(FileLinkStatusEnum $status): void
    {
        $this->status = $status;
    }
}
