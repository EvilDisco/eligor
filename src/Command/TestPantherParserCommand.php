<?php

namespace App\Command;

use App\Service\PantherParser;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TestPantherParserCommand extends Command
{
    private const URL_PARAM = 'url';
    private const SEARCH_TAG_PARAM = 'search_tag';

    public function __construct(
        private PantherParser $pantherParser
    ) {
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('parser:panther:test')
            ->setDescription('test panther parser')
            ->setDefinition(array())
            ->addArgument(
                self::URL_PARAM,
                InputArgument::REQUIRED,
                'Set url for parsing'
            )
            ->addArgument(
                self::SEARCH_TAG_PARAM,
                InputArgument::OPTIONAL,
                'Set HTML tag for search on page',
                'title'
            )
        ;
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument(self::URL_PARAM);
        $client = $this->pantherParser->createGetRequest($url);

        $searchTag = $input->getArgument(self::SEARCH_TAG_PARAM);
        $crawler = $client->waitFor($searchTag);

        $io->success(sprintf(
            'Page is parsed, %s = %s',
            $searchTag,
            $crawler->filter($searchTag)->first()->html()
        ));

        return Command::SUCCESS;
    }
}
