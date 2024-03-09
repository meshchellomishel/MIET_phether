<?php

namespace Ensi\LaravelEnsiFilesystem;

use Illuminate\Support\Facades\Facade;

/**
 * Class EnsiStorageFacade
 * @package Ensi\LaravelEnsiFilesystem
 *
 * @method static string protectedDiskName(?string $domain = null)
 * @method static string protectedPrefixPath(?string $domain = null)
 * @method static string publicDiskName(?string $domain = null)
 * @method static string publicPrefixPath(?string $domain = null)
 * @method static string getHashedDirsForFileName(string $fileName)
 */
class EnsiStorageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ensi.filesystem';
    }
}
