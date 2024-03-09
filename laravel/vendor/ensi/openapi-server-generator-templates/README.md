# Ensi шаблоны для ensi/laravel-openapi-server-generator

Данный пакет представляет из себя набор кастомизированных шаблонов для [laravel-openapi-server-generator](https://github.com/ensi-platform/laravel-openapi-server-generator),

## Установка

```bash
composer require --dev ensi/openapi-server-generator-templates
```

## Использование

Для того чтобы шаблоны начали применяться пакетом, в конфиге `config/openapi-server-generator.php` нужно указать:

```php
'extra_templates_path' => base_path('vendor/ensi/openapi-server-generator-templates/templates'),
```

## Лицензия

[Открытая лицензия на право использования программы для ЭВМ Greensight Ecom Platform (GEP)](LICENSE.md).
