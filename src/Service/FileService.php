<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
//use League\Flysystem\FilesystemException;
//use League\Flysystem\FilesystemOperator;
//use Safe\Exceptions\FilesystemException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public function __construct(
        //private FilesystemOperator $defaultStorage,
        private ParameterBagInterface $params,
        //private EntityManagerInterface $em
    ) {}

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