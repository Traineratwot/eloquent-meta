<?php

namespace Traineratwot\EloquentMeta\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Traineratwot\EloquentMeta\EloquentMeta
 */
class EloquentMeta extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Traineratwot\EloquentMeta\EloquentMeta::class;
    }
}
