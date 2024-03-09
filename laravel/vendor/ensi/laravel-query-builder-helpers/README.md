# laravel-query-builder-helpers

Пакет laravel-query-builder-helpers представляет из себя набор классов, упрощающих создание доступных фильтров для пакета laravel-query-builder от Spatie.

## Установка

Пакет доступен к установке через composer:

```bash
composer require ensi/laravel-query-builder-helpers
```

## Использование

### Создание фильтра

Создание фильтров осуществляется путем применения статического метода **make** и дальнейшего вызова цепочки методов-фильтров.

```php
use Ensi\QueryBuilderHelpers\Filters\StringFilter;

StringFilter::make('name')->contain()->endWith()->empty();
```

В данный момент доступны следующие классы-фильтры:

- StringFilter
- NumericFilter
- DateFilter
- ExtraFilter (описан в разделе "Дополнительные фильтры")

Каждый тип фильтра имеет свой суффикс, который добавляется к названию, переданному в метод **make**.
Например, по умолчанию фильтр **empty** имеет суффикс **_empty**, а фильтр **gt** - **_gt**:

```php
NumericFilter::make('rank')->exact()->empty()->gt()->lt();
```
В результате мы получим четыре варианта фильтрации, доступных при поисковых запросах.

- rank
- rank_empty
- rank_gt
- rank_lt

### Передача фильтров в метод allowedFilters

Для передачи полученных фильтров в метод allowedFilters пакета от Spatie потребуется деструктуризация массива.

```php
$this->allowedFilters([
    ...NumericFilter::make('rank')->exact()->empty()->gt()->lt(),
]);
```

### Дополнительные фильтры

Класс **ExtraFilter** используется вышеупомянутыми классами, но также может быть задействован отдельно.

Из полезных методов можно выделить:

- nested (регистрирует набор вложенных фильтров)
- predefined (создает предопределенный фильтр, заключающий в себе сложную фильтрацию)
- и другие

```php
...ExtraFilter::nested('page', [
    ...InputFilter::make('title', 'page_title')->empty()->exact()->contain(),
]),
```

## Конфигурация

В файле **config.php** можно настроить применяемые суффиксы.
Там же можно настроить like-оператор, используемый в поисковых запросах.

```php
'suffixes' => [
    'equal' => '',
    'greater' => '_gt',
    'less' => '_lt',
    ...
],

'like_operator' => 'LIKE',
```

### Тестирование

```bash
composer test
```

По умолчанию тестирование проходит с использованием in-memory DataBase SQLite. 
SQLite не поддерживает часть функций, например: json_contains. 
Для тестирования этих функций укажите в **phpunit.xml.dist** в секции *php* конфигурацию с подключением к другой базе данных.
При написании таких тестов используете функцию *skip* для пропуска тестов с использованием подключения по умолчанию.

```php
->skip(fn () => DB::getDriverName() === 'sqlite', 'db driver does not support this test');
```
