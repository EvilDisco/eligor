<?php

namespace App\Command;

use App\Entity\Parser\FileLink;
use App\Service\FileLinkService;
use App\Service\Mp3iq\Mp3iqDownloader;
use App\Util\WatchableTrait;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Mp3iqDownloadCommand extends Command
{
    use LockableTrait, WatchableTrait;

    private const LIMIT_PARAM = 'limit';

    public function __construct(
        protected Mp3iqDownloader $downloader,
        protected FileLinkService $fileLinkService,
    ) {
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('parse:mp3iq:download')
            ->addOption(
                self::LIMIT_PARAM,
                'l',
                InputOption::VALUE_REQUIRED,
                'limit',
                10
            )
        ;
    }

    /**
     * @throws GuzzleException
     */
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

        $limit = (int) $input->getOption(self::LIMIT_PARAM);
        $fileLinks = $this->fileLinkService->getNotDownloaded($limit);

        if (count($fileLinks) === 0) {
            $io->text('All files are downloaded!');
        }

        foreach ($fileLinks as $key => $fileLink) {
            /** @var FileLink $fileLink */
            $io->section($key + 1 . '. Download ' . $fileLink->getLink());

            if ($this->downloader->download($io, $fileLink->getLink(), $fileLink->getTitle())) {
                $this->fileLinkService->markAsDownloaded($fileLink);
            }

            sleep(3);

            $io->newLine(3);

            if ($key === array_key_last($fileLinks)) {
                $io->text('Still to go: ' . $this->fileLinkService->countNotDownloaded());
            }
        }

        $io->success($this->getStopwatchInfo());

        // снимаем блокировку от параллельного выполнения
        $this->release();

        return Command::SUCCESS;
    }
}