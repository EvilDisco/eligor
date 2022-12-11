<?php

namespace App\Command\Test;

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
    private const NOT_FOUND_TEXT = 'tag is not found';

    public function __construct(
        protected CurlParser $curlParser
    )
    {
        parent::__construct();
    }

    public function configure(): void
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
                'Set HTML tag for search on page',
                'title'
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
        $parseResult = $page->filter($searchTag)->first()->text(self::NOT_FOUND_TEXT);
        if (self::NOT_FOUND_TEXT === $parseResult) {
            $io->warning(sprintf(
                'Page is parsed, tag %s is not found.',
                $searchTag
            ));

            return Command::INVALID;
        }

        $io->success(sprintf(
            'Page is parsed, tag found: %s = %s',
            $searchTag,
            $parseResult
        ));

        return Command::SUCCESS;
    }
}
