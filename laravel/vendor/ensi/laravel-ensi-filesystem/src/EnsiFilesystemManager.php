<?php

namespace Ensi\LaravelEnsiFilesystem;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;

class EnsiFilesystemManager extends FilesystemManager
{
    /**
     * Get protected disk for domain or current domain.
     * @deprecated deprecated, use `Storage::disk($fileManager->protectedDiskName())` instead.
     */
    public function protected(?string $domain = null): Filesystem
    {
        return $this->disk($this->protectedDiskName($domain));
    }

    /**
     * Get protected disk name for domain or current domain.
     */
    public function protectedDiskName(?string $domain = null): string
    {
        $domain = $domain ?? $this->app->make('config')->get('ensi-filesystem.default_domain_code', '');

        return EnsiStorageConfig::protectedDiskName($domain);
    }

    /**
     * Get protected prefix path.
     */
    public function protectedPrefixPath(?string $domain = null): string
    {
        $domain = $domain ?? $this->app->make('config')->get('ensi-filesystem.default_domain_code', '');

        return EnsiStorageConfig::protectedPrefixPath($domain);
    }

    /**
     * Get public disk for domain or current domain.
     * @deprecated deprecated, use `Storage::disk($fileManager->publicDiskName())` instead.
     */
    public function public(?string $domain = null): Filesystem
    {
        return $this->disk($this->publicDiskName($domain));
    }

    /**
     * Get public disk name for domain or current domain.
     */
    public function publicDiskName(?string $domain = null): string
    {
        $domain = $domain ?? $this->app->make('config')->get('ensi-filesystem.default_domain_code', '');

        return EnsiStorageConfig::publicDiskName($domain);
    }

    /**
     * Get public prefix path.
     */
    public function publicPrefixPath(?string $domain = null): string
    {
        $domain = $domain ?? $this->app->make('config')->get('ensi-filesystem.default_domain_code', '');

        return EnsiStorageConfig::publicPrefixPath($domain);
    }

    /**
     * Get root disk name.
     */
    public function rootDiskName(): string
    {
        return EnsiStorageConfig::rootDiskName();
    }

    /**
     * returns e.g. "/ab/3f/"
     */
    public function getHashedDirsForFileName(string $fileName): string
    {
        $md5 = md5($fileName);

        return "{$md5[0]}{$md5[1]}/{$md5[2]}{$md5[3]}";
    }
}
