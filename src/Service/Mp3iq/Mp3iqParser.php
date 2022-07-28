<?php

namespace App\Service\Mp3iq;

use App\Entity\Parser\FileLink;
use App\Service\PantherParser;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\DomCrawler\Crawler;

class Mp3iqParser
{
    public const PARSER_NAME = 'Mp3iqParser';

    protected const BASE_URL = 'https://mp3iq.net/m/488415-pervyj-otryad-na-mayake';
    protected const PLAYLIST_EL = 'ul[class="playlist"]';

    public function __construct(
        protected PantherParser $pantherParser,
        protected EntityManagerInterface $em,
    ) {}

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
        $this->saveFileLinks($fileLinks);
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
                    $nodeCrawler->attr('data-mp3'),
                    $nodeCrawler->filter('h2 em a')->text()
                );
            })
        ;
    }

    protected function saveFileLinks(array $fileLinks): void
    {
        $fileLinkRepo = $this->em->getRepository(FileLink::class);

        foreach ($fileLinks as $fileLink) {
            if ($fileLinkRepo->findOneBy(['link' => $fileLink->getLink()])) {
                continue;
            }

            $this->em->persist($fileLink);
        }

        $this->em->flush();
    }

    public function getParserName(): string
    {
        return self::PARSER_NAME;
    }
}