<?php

namespace Traineratwot\EloquentMeta\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @method mixed getMeta(string $column, string $key = null, mixed $default = null)
 * @method self setMeta(string $column, string $key, mixed $value)
 * @method self pushMeta(string $column, string $key, mixed $value)
 * @method self forgetMeta(string $column, string $key = null)
 * @method bool hasMeta(string $column, string $key = null)
 * @method array getAllMeta(string $column)
 */
interface UseMeta
{
    // Интерфейс-маркер для моделей, использующих метаданные
}
