<?php

namespace Ensi\LaravelEnsiFilesystem\Models\Tests\Factories;

use Ensi\LaravelEnsiFilesystem\EnsiStorageConfig;
use Ensi\TestFactories\Factory;
use Illuminate\Support\Facades\Storage;

class EnsiFileFactory extends Factory
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';

    protected string $path = 'model_dir/subdir/file.ext';
    protected string $visibility = self::VISIBILITY_PUBLIC;

    protected function definition(): array
    {
        return [
            'path' => $this->path,
            'root_path' => "{$this->visibility}/domain/{$this->path}",
            'url' => "https://storage.ru/domain/{$this->path}",
        ];
    }

    public function make(array $extra = []): array
    {
        return $this->makeArray($extra);
    }

    public function makeReal(?string $disk = null, array $extra = []): array
    {
        $disk = $disk ?: EnsiStorageConfig::rootDiskName();
        $arr = $this->make($extra);
        Storage::fake($disk);
        Storage::disk($disk)->put($arr['root_path'], 1);

        return $arr;
    }

    public function public(): self
    {
        $this->visibility = self::VISIBILITY_PUBLIC;

        return $this;
    }

    public function protected(): self
    {
        $this->visibility = self::VISIBILITY_PROTECTED;

        return $this;
    }

    public function fileName(string $fileName): self
    {
        $this->path = pathinfo($this->path, PATHINFO_DIRNAME) . '/' . $fileName . '.' . pathinfo($this->path, PATHINFO_EXTENSION);

        return $this;
    }

    public function fileExt(string $ext): self
    {
        $this->path = pathinfo($this->path, PATHINFO_DIRNAME) . '/' . pathinfo($this->path, PATHINFO_FILENAME) . '.' . $ext;

        return $this;
    }
}
