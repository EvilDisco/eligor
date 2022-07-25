<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilesystemService
{
    public const KB = 'KB';
    public const MB = 'MB';
    public const GB = 'GB';

    private Filesystem $filesystem;
    private ContainerInterface $container;

    public function __construct(Filesystem $filesystem, ContainerInterface $container)
    {
        $this->filesystem = $filesystem;
        $this->container = $container;
    }

    public function saveToFile(string $filePath, string $content): void
    {
        $this->filesystem->dumpFile($filePath, $content);
    }

    public function exists(string $filePath): bool
    {
        return $this->filesystem->exists($filePath);
    }

    public function getFilesize(string $filePath, ?string $measure = null): bool|string
    {
        if ($this->exists($filePath) === false) {
            return false;
        }

        // TODO: проверка на существование файла
        $filesize = filesize($filePath); // байты

        return $this->getReadableFilesize($filesize, $measure);
    }

    public function getReadableFilesize(int $filesize, ?string $measure = self::MB, bool $withMeasureUnit = true): float|int|string
    {
        switch ($measure) {
            case self::KB:
                $filesize = round($filesize / 1024, 2);
                break;
            case self::MB:
                $filesize = round($filesize / 1024 / 1024, 2);
                break;
            case self::GB:
                $filesize = round($filesize / 1024 / 1024 / 1024, 2);
                break;
            default:
                break;
        }

        return $withMeasureUnit ? sprintf('%s %s', $filesize, $measure) : $filesize;
    }

    public function getRootDir(): string
    {
         return $this->container->getParameter('kernel.root_dir');
    }

    public function getProjectDir(): string
    {
         return $this->container->getParameter('kernel.project_dir');
    }

    public function getWebRootDir(): string
    {
         return $this->container->getParameter('kernel.project_dir') . '/web';
    }
}
