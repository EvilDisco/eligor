<?php

namespace App\Service\Mp3iq;

use App\Service\FileService;
use App\Service\FilesystemService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Style\SymfonyStyle;

class Mp3iqDownloader
{
    public function __construct(
        protected FilesystemService $filesystem,
        protected FileService $fileService,
    ) {}

    /**
     * @throws GuzzleException
     */
    public function download(SymfonyStyle $io, string $remotePath, string $filename): string
    {
        $fileLocation = $this->getFileLocation($filename);
        $handle = fopen($fileLocation, 'w+');

        $progress = null;
        $guzzle = new Client([
            'progress' => function ($total, $downloaded) use ($io, &$progress) {
                if ($total > 0 && is_null($progress)) {
                    $io->text('Size: ' . $this->filesystem->getReadableFilesize($total, FilesystemService::MB));

                    $progress = $io->createProgressBar($total);
                    $progress->start();
                }

                if (!is_null($progress)) {
                    if ($total === $downloaded) {
                        $progress->finish();

                        return;
                    }

                    $progress->setProgress($downloaded);
                }
            },
            'sink' => $handle,
        ]);

        try {
            $guzzle->get($remotePath);
        } catch (Exception $e) {
            $io->text('Oh no! ' . $e->getMessage());

            return false;
        }

        return $fileLocation;
    }

    private function getFileLocation(string $filename): string
    {
        return $this->fileService->getUploadDir()
            . Mp3iqParser::PARSER_NAME
            . DIRECTORY_SEPARATOR
            . $filename
        ;
    }
}