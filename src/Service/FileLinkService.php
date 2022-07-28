<?php

namespace App\Service;

use App\Entity\Parser\FileLink;
use App\Entity\Parser\FileLinkStatusEnum;
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

    public function getNotDownloaded(?int $limit = 1): array
    {
        return $this->fileLinkRepo->findBy(['status' => FileLinkStatusEnum::NotDownloaded], ['id' => 'ASC'], $limit);
    }

    public function markAsDownloaded(FileLink $fileLink): void
    {
        $fileLink->setStatus(FileLinkStatusEnum::Downloaded);
        $this->em->persist($fileLink);
        $this->em->flush();
    }

    public function countNotDownloaded(): int
    {
        return count($this->fileLinkRepo->findBy(['status' => FileLinkStatusEnum::NotDownloaded]));
    }
}