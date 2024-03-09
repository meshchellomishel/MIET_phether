# Laravel Ensi Filesystem

Пакет для работы с файловым хранилищем Ensi в Laravel приложениях

## Установка

1. Добавьте в composer.json в repositories 

```
repositories: [
    {
        "type": "vcs",
        "url": "https://gitlab.com/greensight/ensi/packages/laravel-ensi-filesystem.git"
    }
],

```

2. `composer require ensi/laravel-ensi-filesystem`
3. `php artisan vendor:publish --provider="Ensi\LaravelEnsiFilesystem\EnsiFilesystemServiceProvider"`
3. Задайте в `config/ensi-filesystem.php` код вашего домена в `default_domain_code`
4. Добавьте диски всех необходимых вам сервисов в `config/filesystems`

Пример:

```
return [
    ....
    'disks' => array_merge([
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
    ], EnsiStorageConfig::addDisk('internal-messenger', storage_path('ensi'))),

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
```

5. Задайте переменную окружения `ENSI_PUBLIC_DISK_URL` если вы используете её, например `https://es-public.project.ru`
6. Задайте переменную окружения `ENSI_PROTECTED_DISK_URL` если вы используете её, например `https://es-protected.project.ru`
7. `php artisan storage:link`

## Использование

По идеологии Ensi у каждого сервиса может быть три диска 
1. `protected` - диск с непубличными файлами домена. Доступ к его содержимому по http есть только внутри защищенной сети. 
2. `public` - диск с публичными файлами домена. Его содержимое доступно по http извне кластера.
3. `root` - корень диска с файлами всех доменом. По http недоступен и служит для чтения/раздачи файлов из другого домена по его пути.

Пакет предоставляет `Ensi\LaravelEnsiFilesystem\EnsiFilesystemManager` который является наследником `Illuminate\Filesystem\FilesystemManager` в который добавлено несколько специфических для Ensi методов, а именно

`public function protectedDiskName(): string`  
`public function publicDiskName(): string`  
`public function rootDiskName(): string`  

они позволяют получить доступ к protected и public дискам текущего домена или root диску. Например `Storage::disk($fileManager->protectedDiskName())`.

Работа с `EnsiFilesystemManager` аналогична работе с `FilesystemManager`, получить к нему доступ можно несколькими путями

1. Фасад `EnsiStorage`
2. `resolve('ensi.filesystem')` или `resolve(EnsiFilesystemManager::class)`
3. Заинжектить EnsiFilesystemManager $filesystemManager в конструктор или метод класса, инстанциированного Service Container-ом

### Примеры

`$content = Storage::disk(EnsiStorage::protectedDiskName())->get('products/12324125/image0.jpg');`  
`$path = Storage::disk(EnsiStorage::publicDiskName())->putFileAs("avatars/{$hashedSubDirs}", $request->file('file'), $fileName);`

## Лицензия

[Открытая лицензия на право использования программы для ЭВМ Greensight Ecom Platform (GEP)](LICENSE.md).
