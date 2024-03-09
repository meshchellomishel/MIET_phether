# openapi-client-generator-php-templates

Данный пакет представляет из себя набор кастомизированных шаблонов для [laravel-openapi-client-generator](https://github.com/greensight/laravel-openapi-client-generator),


## Установка

```bash
composer require --dev ensi/openapi-client-generator-php-templates
```

## Использование

Для того чтобы шаблоны начали применяться пакетом, в его конфиге нужно указать в `php_args`:

```php
/**
* Directory where you can place templates to override default ones. . Used in -t
*/
'template_dir' => base_path('vendor/ensi/openapi-client-generator-php-templates/templates'),
```

Полный список изначальных шаблонов можно найти [здесь](https://github.com/OpenAPITools/openapi-generator/tree/5.2.x/modules/openapi-generator/src/main/resources/php).  
Плюс доступны:
1. `templates/LICENSE-template.md`. Данный файл подтянется в клиент как `LICENSE.md`.

## Что изменено относительно первоначальных шаблонов

1. `model_generic.mustache` и `ObjectSerializer.mustache` пропатчены для поддержки nullable полей в соответствии с непринятым [PR](https://github.com/OpenAPITools/openapi-generator/pull/3493)
2. `.phpcs` приведен к нашим стандартам
3. Значительно расширен `.gitignore`
4. В `ApiException.mustache` добавлено свойство `responseErrors` в которое попадают ошибки из json ответа по API Design Guide
5. В `api.mustache` добавлена проверка на RequestException в обработку ошибок асинхронных запросов. Без этого с guzzle 7 получается ошибка дальше `Call to undefined method GuzzleHttp\Exception\ConnectException::getResponse()`
6. Мелкие правки код-стайла

### Лицензия

[Открытая лицензия на право использования программы для ЭВМ Greensight Ecom Platform (GEP)](LICENSE.md).
