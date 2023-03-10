<?php

namespace App\Entity\Parser;

use App\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ParserStats extends BaseEntity
{
    /** @param Parser $parser */
    #[ORM\ManyToOne(targetEntity: Parser::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Parser $parser;

    /** @param int|null $duration */
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    protected ?int $duration;

    /** @param Parser $parser */
    public function __construct(Parser $parser)
    {
        parent::__construct();
        $this->parser = $parser;
    }

    public function __toString(): string
    {
        return (string) $this->duration;
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

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     */
    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }
}
