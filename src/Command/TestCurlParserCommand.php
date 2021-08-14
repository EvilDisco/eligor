<?php

namespace App\Command;

use App\Service\CurlParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TestCurlParserCommand extends Command
{
    private const URL_PARAM = 'url';
    private const SEARCH_TAG_PARAM = 'search_tag';

    public function __construct(
        private CurlParser $curlParser
    ) {
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('parser:curl:test')
            ->setDescription('test curl parser')
            ->setDefinition(array())
            ->addArgument(
                self::URL_PARAM,
                InputArgument::REQUIRED,
                'Set url for parsing'
            )
            ->addArgument(
                self::SEARCH_TAG_PARAM,
                InputArgument::OPTIONAL,
                'Set HTML tag for search on page'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument(self::URL_PARAM);
        $page = $this->curlParser->getPageContentViaCurl($url);
        if (!$page) {
            $io->warning('Cannot open page for parsing.');

            return Command::INVALID;
        }

        $searchTag = $input->getArgument(self::SEARCH_TAG_PARAM);
        if ($searchTag) {
            $parseResult = $page->filter($searchTag)->first();
            if (false === $parseResult) {
                $io->warning('Page is parsed, tag is not found.');
            }

            $io->success(sprintf('Page is parsed, tag is found on page: %s', $parseResult->text()));
        } else {
            $io->success(sprintf('Page is parsed, page title: %s', $page->filter('title')->text()));
        }

        return Command::SUCCESS;
    }
}
