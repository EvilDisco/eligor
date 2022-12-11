<?php

namespace App\Service\Mp3iq;

use App\Entity\Parser\FileLink;
use App\Entity\Parser\Parser;
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

    protected Parser $parser;

    public function __construct(
        protected EntityManagerInterface $em,
        protected PantherParser $pantherParser,
    )
    {
        parent::__construct($em, $pantherParser);
        $this->parser = $this->getParser();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     * @return array<int, FileLink>|null
     */
    public function parseFileLinks(?int $page = 1): ?array
    {
        $url = $this->getPageUrl($page);

        $client = $this->pantherParser->createGetRequest($url);
        $crawler = $client->waitFor(self::PLAYLIST_EL);

        return $this->parseFileLinksFromPage($crawler);
    }

    protected function getPageUrl(int $page): string
    {
        return self::BASE_URL . '/page/' . $page;
    }

    /**
     * @return array<int, FileLink>|null
     */
    protected function parseFileLinksFromPage(Crawler $crawler): ?array
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