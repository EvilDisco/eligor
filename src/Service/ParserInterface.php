<?php

namespace App\Service;

use App\Entity\Parser\Parser;

interface ParserInterface
{
    public function getName(): string;

    public function getParser(): Parser;
}