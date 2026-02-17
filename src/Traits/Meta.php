<?php

namespace Traineratwot\EloquentMeta\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Traineratwot\EloquentMeta\Interfaces\UseMeta;
use Traineratwot\EloquentMeta\Models\EloquentMeta;

/**
 * @mixin Model&UseMeta
 */
trait Meta
{
    /**
     * Инициализация трейта
     */
    public static function bootMeta(): void
    {
        static::deleting(function (Model $model) {
            // Удалить все метаданные при удалении модели
            $model->metas()->delete();
        });
    }

    public function meta(string $column, ?string $key = null, mixed $value = null)
    {
        if (blank($key)) {
            return $this->getMeta($column);
        }
        if (blank($value)) {
            return $this->getMeta($column, $key);
        }

        return $this->setMeta($column, $key, $value);
    }

    /**
     * Получить метаданные по колонке и ключу
     */
    public function getMeta(string $column, ?string $key = null, mixed $default = null): mixed
    {
        if (! $this->relationLoaded('metas')) {
            $this->load('metas');
        }

        $meta = $this->metas->firstWhere('column', $column);

        if (! $meta) {
            return $default;
        }

        if ($key === null) {
            return $meta->data;
        }

        return data_get($meta->data, $key, $default);
    }

    /**
     * Создать или обновить метаданные в ячейке
     */
    public function setMeta(string $column, string $key, mixed $value): self
    {
        $meta = $this->metas()->firstOrCreate(
            ['column' => $column],
            ['data' => []]
        );

        $data = $meta->data ?? [];
        data_set($data, $key, $value);
        $meta->update(['data' => $data]);

        // Обновляем загруженное отношение
        if ($this->relationLoaded('metas')) {
            $this->metas = $this->metas->map(fn ($m) => $m->id === $meta->id ? $meta : $m);
        }

        return $this;
    }

    /**
     * Добить значение в массив метаданных (если это массив)
     */
    public function pushMeta(string $column, string $key, mixed $value): self
    {
        $meta = $this->metas()->firstOrCreate(
            ['column' => $column],
            ['data' => []]
        );

        $data = $meta->data ?? [];
        $current = data_get($data, $key, []);

        if (! is_array($current)) {
            $current = [$current];
        }

        $current[] = $value;
        data_set($data, $key, $current);
        $meta->update(['data' => $data]);

        // Обновляем загруженное отношение
        if ($this->relationLoaded('metas')) {
            $this->metas = $this->metas->map(fn ($m) => $m->id === $meta->id ? $meta : $m);
        }

        return $this;
    }

    /**
     * Удалить метаданные по ключу
     */
    public function forgetMeta(string $column, ?string $key = null): self
    {
        $meta = $this->metas()->where('column', $column)->first();

        if (! $meta) {
            return $this;
        }

        if ($key === null) {
            // Удалить всю ячейку метаданных
            $meta->delete();

            if ($this->relationLoaded('metas')) {
                $this->metas = $this->metas->reject(fn ($m) => $m->id === $meta->id);
            }
        } else {
            // Удалить конкретный ключ из данных
            $data = $meta->data ?? [];
            data_forget($data, $key);

            if (empty($data)) {
                $meta->delete();
                if ($this->relationLoaded('metas')) {
                    $this->metas = $this->metas->reject(fn ($m) => $m->id === $meta->id);
                }
            } else {
                $meta->update(['data' => $data]);
                if ($this->relationLoaded('metas')) {
                    $this->metas = $this->metas->map(fn ($m) => $m->id === $meta->id ? $meta : $m);
                }
            }
        }

        return $this;
    }

    /**
     * Проверить наличие метаданных
     */
    public function hasMeta(string $column, ?string $key = null): bool
    {
        if (! $this->relationLoaded('metas')) {
            $this->load('metas');
        }

        $meta = $this->metas->firstWhere('column', $column);

        if (! $meta) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return data_has($meta->data, $key);
    }

    /**
     * Получить все метаданные по колонке
     */
    public function getAllMeta(string $column): array
    {
        if (! $this->relationLoaded('metas')) {
            $this->load('metas');
        }

        $meta = $this->metas->firstWhere('column', $column);

        return $meta?->data ?? [];
    }

    /**
     * Отношение к метаданным
     */
    public function metas(): MorphMany
    {
        return $this->morphMany(EloquentMeta::class, 'model');
    }
}
