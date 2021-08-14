<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParserService
{
    public function __construct(
        protected CurlService $curlService,
        protected PhantomJsService $phantomJsService
    ) {}

    public function getPageContentViaCurl(string $url, bool $isRawResponse = false): bool|string|Crawler
    {
        $curl = $this->curlService->getCurlFullResponse($url);
        if (false === $curl) {
            return false;
        }

        $response = $this->curlService->getCurlResponse($curl);

        if (false === $isRawResponse) {
            $response = $this->getHtmlDomFromString($response);
        }

        return $response;
    }

    public function getHtmlDomFromString(string $string): Crawler
    {
        return new Crawler($string);
    }

    public function getPageContentViaPhantomJs(string $url, bool $isRawResponse = false): string|Crawler
    {
        $phantomJsResponse = $this->phantomJsService->getPhantomjsResponse($url);

        $response = $phantomJsResponse->getContent(); // TODO: в phantomJsService

        if (false === $isRawResponse) {
            $response = $this->getHtmlDomFromString($response);
        }

        return $response;
    }
}
