<?php

namespace Ensi\LaravelEnsiFilesystem\Models;

use Ensi\LaravelEnsiFilesystem\EnsiStorageFacade;
use Ensi\LaravelEnsiFilesystem\Models\Tests\Factories\EnsiFileFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;

class EnsiFile implements JsonSerializable
{
    protected string $rootPath;
    protected string $url;
    protected function __construct(
        protected string $path,
        Filesystem $disk,
        string $prefixPath
    ) {
        $this->path = ltrim($this->path, '/');
        $this->rootPath = "{$prefixPath}/{$this->path}";
        $this->url = $disk->url($this->path);
    }

    public static function public(string $filePath): static
    {
        return new static($filePath, Storage::disk(EnsiStorageFacade::publicDiskName()), EnsiStorageFacade::publicPrefixPath());
    }

    public static function protected(string $filePath): static
    {
        return new static($filePath, Storage::disk(EnsiStorageFacade::protectedDiskName()), EnsiStorageFacade::protectedPrefixPath());
    }

    /**
     * @param array $filePaths
     * @return static[]
     */
    public static function collectionPublic(array $filePaths): array
    {
        if (!$filePaths) {
            return [];
        }

        $disk = Storage::disk(EnsiStorageFacade::publicDiskName());
        $prefixPath = EnsiStorageFacade::publicPrefixPath();
        $files = [];
        foreach ($filePaths as $path) {
            $files[] = new static($path, $disk, $prefixPath);
        }

        return $files;
    }

    /**
     * @param array $filePaths
     * @return static[]
     */
    public static function collectionProtected(array $filePaths): array
    {
        if (!$filePaths) {
            return [];
        }

        $disk = Storage::disk(EnsiStorageFacade::protectedDiskName());
        $prefixPath = EnsiStorageFacade::protectedPrefixPath();

        $files = [];
        foreach ($filePaths as $path) {
            $files[] = new static($path, $disk, $prefixPath);
        }

        return $files;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'path' => $this->path,
            'root_path' => $this->rootPath,
            'url' => $this->url,
        ];
    }

    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    public static function factory(): EnsiFileFactory
    {
        return EnsiFileFactory::new();
    }
}