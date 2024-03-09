<?php


namespace Ensi\LaravelEnsiFilesystem;


class EnsiStorageConfig
{
    /**
     * Get protected disk name for domain.
     */
    public static function protectedDiskName(string $domain): string
    {
        return "ensi_{$domain}_protected";
    }

    /**
     * Get protected prefix path.
     */
    public static function protectedPrefixPath(string $domain): string
    {
        return "protected/{$domain}";
    }

    /**
     * Get public disk name for domain.
     */
    public static function publicDiskName(string $domain): string
    {
        return "ensi_{$domain}_public";
    }

    /**
     * Get public prefix path.
     */
    public static function publicPrefixPath(string $domain): string
    {
        return "public/{$domain}";
    }

    /**
     * Get root disk name.
     */
    public static function rootDiskName(): string
    {
        return 'ensi';
    }

    /**
     * Generate configs for filesystems.php
     */
    public static function addDisk(string $domain, string $ensiStoragePath): array
    {
        $ensiStoragePath = rtrim($ensiStoragePath, '/');
        $permissions = [
            'file' => [
                'public' => 0664,
                'private' => 0600,
            ],
            'dir' => [
                'public' => 0775,
                'private' => 0700,
            ],
        ];
        return [
            static::publicDiskName($domain) => [
                'driver' => 'local',
                'root' => $ensiStoragePath . '/' . static::publicPrefixPath($domain),
                'url' => env('ENSI_PUBLIC_DISK_URL') . "/{$domain}",
                'visibility' => 'public',
                'permissions' => $permissions,
            ],
            static::protectedDiskName($domain) => [
                'driver' => 'local',
                'root' => $ensiStoragePath . '/' . static::protectedPrefixPath($domain),
                'url' => env('ENSI_PROTECTED_DISK_URL') . "/{$domain}",
                'visibility' => 'public',
                'permissions' => $permissions,
            ],
            static::rootDiskName() => [
                'driver' => 'local',
                'root' => $ensiStoragePath,
                'permissions' => $permissions,
            ],
        ];
    }
}