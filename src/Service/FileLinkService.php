<?php

namespace App\Service;

use App\Entity\Parser\FileLink;
use App\Entity\Parser\FileLinkStatusEnum;
use App\Entity\Parser\Parser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class FileLinkService
{
    protected EntityRepository $fileLinkRepo;

    public function __construct(
        protected EntityManagerInterface $em,
    )
    {
        $this->fileLinkRepo = $this->em->getRepository(FileLink::class);
    }

    public function getNotDownloadedByParser(Parser $parser, ?int $limit = 1): array
    {
        return $this->fileLinkRepo->findBy(
            [
                'parser' => $parser,
                'status' => FileLinkStatusEnum::NotDownloaded
            ],
            ['id' => 'ASC'],
            $limit
        );
    }

    public function countNotDownloadedByParser(Parser $parser): int
    {
        return $this->fileLinkRepo->count([
            'parser' => $parser,
            'status' => FileLinkStatusEnum::NotDownloaded
        ]);
    }

    public function markAsDownloaded(FileLink $fileLink): void
    {
        $fileLink->setStatus(FileLinkStatusEnum::Downloaded);
        $this->em->persist($fileLink);
        $this->em->flush();
    }

    public function save(array|FileLink $data): void
    {
        $fileLinkRepo = $this->em->getRepository(FileLink::class);

        if ($data instanceof FileLink) {
            $this->em->persist($data);
        } else {
            foreach ($data as $datum) {
                if ($fileLinkRepo->findOneBy(['link' => $datum->getLink()])) {
                    continue;
                }

                $this->em->persist($datum);
            }
        }

        $this->em->flush();
    }
}