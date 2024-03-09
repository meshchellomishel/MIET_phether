<?php

namespace Ensi\LaravelEnsiFilesystem\Eloquent\Events;

use Ensi\LaravelEnsiFilesystem\EnsiFilesystemManager;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;

/**
* @deprecated Delete files explicitly like POST /banners/1:delete-file
*/
class FileRemover
{
    const DISK_TYPE_PUBLIC = 'public';
    const DISK_TYPE_PROTECTED = 'protected';

    protected $filesystemManager;

    public function __construct(EnsiFilesystemManager $filesystemManager)
    {
        $this->filesystemManager = $filesystemManager;
    }

    public function deleteFileOnUpdating(Model $model, array $columns, string $diskType): void
    {
        $changedColumns = array_keys($model->getChanges());

        foreach ($columns as $column) {
            $oldFile = $model->getOriginal($column);
            if ($oldFile && $oldFile->path && in_array($column, $changedColumns)) {
                $this->getDiskByType($diskType)->delete($oldFile->path);
            }
        }
    }

    public function deleteFileOnDeleted(Model $model, array $columns, string $diskType): void
    {
        $disk = $this->getDiskByType($diskType);

        foreach ($columns as $column) {
            if ($model[$column] && $model[$column]->path) {
                $disk->delete($model[$column]->path);
            }
        }
    }

    protected function getDiskByType(string $diskType): Filesystem
    {
        return $diskType === self::DISK_TYPE_PROTECTED ? $this->filesystemManager->protected() : $this->filesystemManager->public();
    }
}
