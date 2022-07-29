<?php

namespace App\Service;

use App\Entity\Parser\Parser;
use Doctrine\ORM\EntityManagerInterface;

class ParserService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected PantherParser $pantherParser,
    ) {}

    public function getParser(string $name): Parser
    {
        $parser = $this->em->getRepository(Parser::class)->findOneBy(['name' => $name]);
        if (!$parser) {
            $parser = $this->createParser($name);
        }

        return $parser;
    }

    private function createParser(string $name): Parser
    {
        $parser = new Parser($name);
        $this->em->persist($parser);
        $this->em->flush();

        return $parser;
    }
}