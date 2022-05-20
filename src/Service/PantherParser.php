<?php

namespace App\Service;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\Client;

class PantherParser
{
    public function createGetRequest(string $url): Client
    {
        $client = $this->createClient();
        $client->request(Request::METHOD_GET, $url);

        return $client;
    }

    // TODO: client types
    public function createClient(): Client
    {
        $chromeOptions = new ChromeOptions();
        $chromeOptions->setExperimentalOption('w3c', false);

        return Client::createChromeClient(null, null,
            [
                'capabilities' => [
                    ChromeOptions::CAPABILITY => $chromeOptions,
                ],
            ]
        );
    }
}