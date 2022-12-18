<?php

namespace App\Repository;

use App\Entity\Parser\FileLink;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class FileLinkRepository
{
    /**
     * @var EntityRepository<FileLink>
     */
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repo = $entityManager->getRepository(FileLink::class);
    }

    public function find(int $id): ?FileLink
    {
        return $this->repo->find($id);
    }

    /**
     * @return array<FileLink>
     */
    public function findBy(mixed $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->repo->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?FileLink
    {
        return $this->repo->findOneBy($criteria, $orderBy);
    }

    public function count(array $criteria): int
    {
        return $this->repo->count($criteria);
    }

    public function findFilePartsByTitle(string $title): array
    {
        return $this->repo->createQueryBuilder('file_link')
            ->andWhere('file_link.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')
            ->addOrderBy('file_link.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}