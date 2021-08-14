<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

class PantherParser
{
    public function getPageContentViaPanther(string $url): Crawler
    {
        // TODO: client types
        $client = Client::createChromeClient();

        return $client->request(Request::METHOD_GET, $url);
    }
}