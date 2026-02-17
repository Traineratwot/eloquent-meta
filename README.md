# Eloquent Meta

[![Latest Version on Packagist](https://img.shields.io/packagist/v/traineratwot/eloquent-meta.svg?style=flat-square)](https://packagist.org/packages/traineratwot/eloquent-meta)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/traineratwot/eloquent-meta/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/traineratwot/eloquent-meta/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/traineratwot/eloquent-meta/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/traineratwot/eloquent-meta/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/traineratwot/eloquent-meta.svg?style=flat-square)](https://packagist.org/packages/traineratwot/eloquent-meta)

Мощный пакет для управления метаданными колонок Laravel моделей. Сохраняйте дополнительные данные в JSONB формате с поддержкой Filament PHP.

## Возможности

- 📦 Легко добавлять метаданные к любым моделям
- 🎯 Работа с вложенными ключами в метаданных
- 💾 Хранение в JSONB формате для быстрого поиска
- 🔗 Полиморфные отношения (MorphMany)
- 🎨 Интеграция с Filament PHP (TODO!)
- ⚡ Простой и интуитивный API
- 🧪 Полностью протестировано

## Установка

Установите пакет через Composer:

```bash
composer require traineratwot/eloquent-meta
```

Опубликуйте и запустите миграции:

```bash
php artisan vendor:publish --tag="eloquent-meta-migrations"
php artisan migrate
```

Опубликуйте конфиг файл (опционально):

```bash
php artisan vendor:publish --tag="eloquent-meta-config"
```

## Использование

### Подготовка модели

Добавьте трейт `Meta` и интерфейс `UseMeta` к вашей модели:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Traineratwot\EloquentMeta\Traits\Meta;
use Traineratwot\EloquentMeta\Interfaces\UseMeta;

class Post extends Model implements UseMeta
{
    use Meta;

    protected $fillable = ['title', 'content'];
}
```

### Основные операции

#### Получить метаданные

```php
$post = Post::find(1);

// Получить конкретный ключ
$bio = $post->getMeta('profile', 'bio');

// Получить конкретный ключ с значением по умолчанию
$bio = $post->getMeta('profile', 'bio', 'No bio');

// Получить все метаданные колонки
$allProfileData = $post->getMeta('profile');
```

#### Создать или обновить метаданные

```php
$post->setMeta('profile', 'bio', 'Мой профиль');
$post->setMeta('profile', 'avatar_url', 'https://example.com/avatar.jpg');

// Цепочка вызовов
$post->setMeta('settings', 'theme', 'dark')
     ->setMeta('settings', 'language', 'en')
     ->save();
```

#### Добавить значение в массив

```php
// Добавить тег
$post->pushMeta('profile', 'tags', 'php');
$post->pushMeta('profile', 'tags', 'laravel');

// Результат: ['tags' => ['php', 'laravel']]
```

#### Удалить метаданные

```php
// Удалить конкретный ключ
$post->forgetMeta('profile', 'bio');

// Удалить всю ячейку метаданных
$post->forgetMeta('profile');
```

#### Проверить наличие метаданных

```php
// Проверить наличие колонки
if ($post->hasMeta('profile')) {
    // ...
}

// Проверить наличие конкретного ключа
if ($post->hasMeta('profile', 'bio')) {
    // ...
}
```

#### Получить все метаданные

```php
$profileData = $post->getAllMeta('profile');
// Результат: ['bio' => '...', 'avatar_url' => '...']
```

### Примеры использования

```php
// Сохранение SEO метаданных
$post->setMeta('seo', 'title', 'Мой пост')
     ->setMeta('seo', 'description', 'Описание поста')
     ->setMeta('seo', 'keywords', 'php, laravel')
     ->save();

// Сохранение пользовательских настроек
$user->setMeta('preferences', 'notifications', true)
     ->setMeta('preferences', 'theme', 'dark')
     ->setMeta('preferences', 'language', 'ru')
     ->save();

// Работа с массивами
$post->pushMeta('comments', 'ids', 1);
$post->pushMeta('comments', 'ids', 2);
$post->pushMeta('comments', 'ids', 3);

$commentIds = $post->getMeta('comments', 'ids');
// Результат: [1, 2, 3]
```

## Структура базы данных

Пакет создает таблицу `eloquent_metas` со следующей структурой:

| Колонка    | Тип       | Описание                    |
|------------|-----------|-----------------------------|
| id         | bigint    | Первичный ключ              |
| model_id   | bigint    | ID модели                   |
| model_type | string    | Тип модели (класс)          |
| column     | string    | Название колонки метаданных |
| data       | jsonb     | Данные в формате JSON       |
| created_at | timestamp | Дата создания               |
| updated_at | timestamp | Дата обновления             |

Уникальный индекс на `(model_id, model_type, column)` обеспечивает, что для каждой модели и колонки существует только одна запись метаданных.

## Интеграция с Filament PHP (TODO!)


Пакет БУДЕТ полностью интегрироваться c фрейворком Филамент php https://filamentadmin.com/docs

## Тестирование

```bash
composer test
```

## Changelog

Смотрите [CHANGELOG](CHANGELOG.md) для информации об изменениях.

## Вклад

Смотрите [CONTRIBUTING](CONTRIBUTING.md) для деталей.

## Безопасность

Пожалуйста, смотрите [политику безопасности](../../security/policy) для информации о сообщении об уязвимостях.

## Авторы

- [Traineratwot](https://github.com/Traineratwot)
- [Все контрибьюторы](../../contributors)

## Лицензия

The MIT License (MIT). Смотрите [License File](LICENSE.md) для деталей.
