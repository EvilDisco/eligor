<?php

namespace App\Command\Mp3iq;

use App\Entity\Parser\Parser;
use App\Repository\FileLinkRepository;
use App\Service\FileLinkService;
use App\Service\Mp3iq\Mp3iqMediaManipulator;
use App\Service\Mp3iq\Mp3iqParser;
use App\Util\WatchableTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Mp3iqMergeCommand extends Command
{
    use LockableTrait, WatchableTrait;

    public const LIMIT_PARAM = 'limit';

    protected Parser $parser;

    public function __construct(
        protected Mp3iqParser $mp3iqParser,
        protected FileLinkService $fileLinkService,
        protected FileLinkRepository $fileLinkRepo,
        protected Mp3iqMediaManipulator $mediaManipulator,
    ) {
        parent::__construct();
        $this->parser = $mp3iqParser->getParser();
    }

    public function configure(): void
    {
        $this
            ->setName('parse:mp3iq:merge')
            ->addOption(
                self::LIMIT_PARAM,
                'l',
                InputOption::VALUE_REQUIRED,
                'limit',
                10
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // TODO: auto checkIsParserRunning + startParser - without name

        // блокируем команду от параллельного выполнения
        if (!$this->lock()) {
            $io->warning('Эта команда сейчас выполняется в другом процессе, нужно подождать завершения.');

            return Command::FAILURE;
        }

        $this->startStopwatch();

        $limit = (int)$input->getOption(self::LIMIT_PARAM);
        $this->fileLinkService->mergeByParser($io, $this->parser, $limit);

        $io->newLine();
        $io->success($this->getStopwatchInfo());

        // снимаем блокировку от параллельного выполнения
        $this->release();

        return Command::SUCCESS;
    }
}