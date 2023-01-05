<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

class CurlParser
{
    public function __construct(
        protected CurlService $curlService
    ) {}

    public function getPageContentViaCurl(string $url, bool $isRawResponse = false): bool|string|Crawler
    {
        $curl = $this->curlService->getCurlFullResponse($url);
        if (false === $curl) {
            return false;
        }

        $response = $this->curlService->getCurlResponse($curl);

        if (false === $isRawResponse) {
            $response = $this->getDomFromString($response);
        }

        return $response;
    }

    public function getDomFromString(string $string): Crawler
    {
        return new Crawler($string);
    }
}
