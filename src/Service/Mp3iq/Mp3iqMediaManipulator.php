<?php

namespace App\Service\Mp3iq;

use App\Entity\Parser\FileLink;
use App\Entity\Parser\FileLinkStatusEnum;
use App\Repository\FileLinkRepository;
use App\Service\FilesystemService;
use App\Util\ConsoleUtil;
use App\Util\StringUtil;
use Doctrine\ORM\EntityManagerInterface;
use falahati\PHPMP3\MpegAudio;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

class Mp3iqMediaManipulator
{
    public const AIRED_AT_PATTERN = '/(\d{2}).(\d{2}).(\d{4}) (\d{2}):(\d{2})/';
    public const MULTIPART_PATTERN = '/ \(\d{1}\)/';

    public const MERGED_FOLDER = 'merged';

    public function __construct(
        protected FilesystemService      $filesystem,
        protected Mp3iqDownloader        $downloader,
        protected EntityManagerInterface $em,
        protected FileLinkRepository     $fileLinkRepo,
    )
    {
    }

    /**
     * @param SymfonyStyle $io
     * @param array<int, FileLink> $fileLinks
     * @param int|null $limit
     */
    public function processFileLinks(SymfonyStyle $io, array $fileLinks = [], ?int $limit = 1): void
    {
        ConsoleUtil::defineFilesOperationProgressBarFormat();
        ConsoleUtil::defineDoneProgressBarFormat();

        $progressBar = $io->createProgressBar($limit);
        $progressBar->setFormat(ConsoleUtil::PROGRESS_BAR_FILES_OPERATION_FORMAT);
        $progressBar->start();
        $progressBar->setMessage('Merging files...');

        $completedCounter = 0;
        $mergedCounter = 0;
        $notFoundFiles = [];

        foreach ($fileLinks as $fileLink) {
            $this->em->refresh($fileLink);

            if ($fileLink->getStatus() === FileLinkStatusEnum::Processed) {
                continue;
            }

            // TODO: file content check - can be dummy file, not media

            $title = $fileLink->getTitle();

            $progressBar->setMessage($title, 'filename');
            $progressBar->advance();

            $file = new MpegAudio();

            if (self::checkIsMultipartFile($fileLink)) {
                $title = self::getTitleWithoutOrder($fileLink);

                $parts = $this->fileLinkRepo->findFilePartsByTitle($title);

                foreach ($parts as $part) {
                    /** @var FileLink $part */
                    $path = $this->downloader->getDownloadedFileLocation($part->getTitle());
                    if (!$this->filesystem->exists($path)) {
                        $notFoundFiles[] = [$path];
                        continue 2;
                    }

                    $filePart = MpegAudio::fromFile($path);

                    // !!! https://github.com/falahati/PHP-MP3/issues/12#issuecomment-1305310577
                    $file->append($filePart);

                    $part->setStatus(FileLinkStatusEnum::Processed);
                    $this->em->persist($part);

                    $mergedCounter++;
                }
            } else {
                $path = $this->downloader->getDownloadedFileLocation($fileLink->getTitle());
                if (!$this->filesystem->exists($path)) {
                    $notFoundFiles[] = [$path];
                    continue;
                }

                $file = MpegAudio::fromFile($path);

                $fileLink->setStatus(FileLinkStatusEnum::Processed);
                $this->em->persist($fileLink);

                $mergedCounter++;
            }

            $this->em->flush();

            $formattedTitle = self::formatTitle($title);
            $path = self::getMergedFileLocation($formattedTitle);
            $file->saveFile($path);

            self::fixMergedFileWithMp3val($path);

            $completedCounter++;

            if ($completedCounter === $limit) {
                $progressBar->setFormat(ConsoleUtil::PROGRESS_BAR_DONE_FORMAT);
                $progressBar->finish();
                break;
            }
        }

        $io->newLine();
        $message = sprintf('New files: %d, merged files: %d', $completedCounter, $mergedCounter);
        $io->text($message);

        if (count($notFoundFiles) > 0) {
            self::renderNotFoundFiles($io, $notFoundFiles);
        }
    }

    private static function checkIsMultipartFile(FileLink $fileLink): bool
    {
        preg_match(self::MULTIPART_PATTERN, $fileLink->getTitle(), $matches);

        return count($matches) > 0;
    }

    private static function getTitleWithoutOrder(FileLink $fileLink): string
    {
        return StringUtil::mbSubstrFromEnd($fileLink->getTitle(), 4); // " (1)"
    }

    private static function formatTitle(string $title): string
    {
        $formattedTitle = $title . '.mp3';
        $airedAt = self::getAiredAt($title);
        if (count($airedAt) > 0) {
            $formattedAiredAt = $airedAt[3] . '.' . $airedAt[2] . '.' . $airedAt[1];
            $title = StringUtil::mbStrReplace($airedAt[0], '', $title);
            $title = StringUtil::mbTrim($title);
            $formattedTitle = sprintf('%s - %s.mp3', $formattedAiredAt, $title);
        }

        return $formattedTitle;
    }

    private static function getAiredAt(string $title): array
    {
        preg_match(self::AIRED_AT_PATTERN, $title, $matches);

        return $matches;
    }

    public function getMergedFolder(): string
    {
        return $this->downloader->getDownloadFolder() . DIRECTORY_SEPARATOR . self::MERGED_FOLDER;
    }

    public function getMergedFileLocation(string $filename): string
    {
        return self::getMergedFolder() . DIRECTORY_SEPARATOR . $filename;
    }

    private static function fixMergedFileWithMp3val(string $path): void
    {
        // TODO: win/unix + is mp3val available
        shell_exec("mp3val '$path' -f -nb -t");
    }

    private static function renderNotFoundFiles(SymfonyStyle $io, array $notFoundFiles): void
    {
        $io->newLine();
        $io->text('Some files were not found:');
        $table = new Table($io);
        $table
            ->setHeaders(['Path'])
            ->setRows($notFoundFiles)
        ;
        $table->render();
    }
}