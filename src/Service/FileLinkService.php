<?php

namespace App\Service;

use App\Entity\Parser\FileLink;
use App\Entity\Parser\FileLinkStatusEnum;
use App\Entity\Parser\Parser;
use App\Repository\FileLinkRepository;
use App\Service\Mp3iq\Mp3iqMediaManipulator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FileLinkService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected FileLinkRepository $fileLinkRepo,
        protected Mp3iqMediaManipulator $mediaManipulator,
    ) {}

    /**
     * @return array<int, FileLink>
     */
    public function getNotDownloadedByParser(Parser $parser, ?int $limit = 1): array
    {
        return $this->getByParserAndStatus($parser, FileLinkStatusEnum::NotDownloaded, $limit);
    }

    /**
     * @return array<int, FileLink>
     */
    private function getByParserAndStatus(
        Parser $parser,
        FileLinkStatusEnum $status = FileLinkStatusEnum::NotDownloaded,
        ?int $limit = null,
        string $orderBy = 'id'
    ): array
    {
        return $this->fileLinkRepo->findBy(
            [
                'parser' => $parser,
                'status' => $status
            ],
            [$orderBy => 'ASC'],
            $limit
        );
    }

    public function countNotDownloadedByParser(Parser $parser): int
    {
        return $this->countByParserAndStatus($parser);
    }

    private function countByParserAndStatus(
        Parser $parser,
        FileLinkStatusEnum $status = FileLinkStatusEnum::NotDownloaded
    ): int
    {
        return $this->fileLinkRepo->count([
            'parser' => $parser,
            'status' => $status
        ]);
    }

    public function markAsDownloaded(FileLink $fileLink): void
    {
        $fileLink->setStatus(FileLinkStatusEnum::Downloaded);
        $this->em->persist($fileLink);
        $this->em->flush();
    }

    // FIXME: param as single type
    public function save(array|FileLink $data): void
    {
        if ($data instanceof FileLink) {
            $this->em->persist($data);
        } else {
            foreach ($data as $datum) {
                if ($this->fileLinkRepo->findOneBy(['link' => $datum->getLink()])) {
                    continue;
                }

                $this->em->persist($datum);
            }
        }

        $this->em->flush();
    }

    public function mergeByParser(SymfonyStyle $io, Parser $parser, ?int $limit = 1): void
    {
        $fileLinks = $this->getByParserAndStatus($parser, FileLinkStatusEnum::Downloaded, null, 'title');
        if (count($fileLinks) === 0) {
            $io->text('nothing to process');

            return;
        }

        $this->mediaManipulator->processFileLinks($io, $fileLinks, $limit);
    }
}