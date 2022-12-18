<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemService
{
    public const WEB_DIR = 'web';

    public const KB = 'KB';
    public const MB = 'MB';
    public const GB = 'GB';

    public function __construct(
        protected Filesystem $filesystem,
        protected ParameterBagInterface $params,
    ) {}

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
         return $this->params->get('kernel.root_dir');
    }

    public function getProjectDir(): string
    {
         return $this->params->get('kernel.project_dir');
    }

    public function getWebRootDir(): string
    {
         return $this->params->get('kernel.project_dir') . DIRECTORY_SEPARATOR . self::WEB_DIR;
    }

    public function getKernelDir(): string
    {
        return $this->params->get('kernel.project_dir');
    }

    public function getUploadDir(): string
    {
        return $this->params->get('upload_dir');
    }

    public function getFileExtension(SplFileInfo|UploadedFile $file): string
    {
        $extension = $file instanceof UploadedFile ? $file->getClientOriginalExtension() : $file->getExtension();

        return strtolower($extension);
    }

    public function createDirectory(string $path): void
    {
        //$this->defaultStorage->createDirectory($path);
    }

    public function writeFileContent(string $filePath, string $content): void
    {
        //$this->defaultStorage->write($filePath, $content);
    }
}
