<?php

namespace App\Service\Mp3iq;

use App\Entity\Parser\FileLink;
use App\Entity\Parser\Parser;
use App\Service\FileLinkService;
use App\Service\PantherParser;
use App\Service\ParserInterface;
use App\Service\ParserService;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\DomCrawler\Crawler;

class Mp3iqParser extends ParserService implements ParserInterface
{
    public const NAME = 'Mp3iqParser';

    protected const BASE_URL = 'https://mp3iq.net/m/488415-pervyj-otryad-na-mayake';
    protected const PLAYLIST_EL = 'ul[class="playlist"]';

    public Parser $parser;

    public function __construct(
        protected EntityManagerInterface $em,
        protected PantherParser $pantherParser,
        protected FileLinkService $fileLinkService,
    )
    {
        parent::__construct($em, $pantherParser);
        $this->parser = $this->getParser(self::getName());
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function parseFileLinks(?int $page = 1): void
    {
        $url = $this->getPageUrl($page);

        $client = $this->pantherParser->createGetRequest($url);
        $crawler = $client->waitFor(self::PLAYLIST_EL);

        $fileLinks = $this->parseFileLinksFromPage($crawler);

        $this->fileLinkService->save($fileLinks);
    }

    protected function getPageUrl(int $page): string
    {
        return self::BASE_URL . '/page/' . $page;
    }

    protected function parseFileLinksFromPage(Crawler $crawler): array
    {
        return $crawler
            ->filter('.track')
            ->each(function (Crawler $nodeCrawler) {
                return new FileLink(
                    $this->parser,
                    $nodeCrawler->attr('data-mp3'),
                    $nodeCrawler->filter('h2 em a')->text()
                );
            })
        ;
    }

    public function getParser(): Parser
    {
        return parent::getParserByName(self::NAME);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}