<?php

namespace Traineratwot\EloquentMeta\Tests;

use Illuminate\Database\Eloquent\Model;
use Traineratwot\EloquentMeta\Traits\Meta;
use Traineratwot\EloquentMeta\Interfaces\UseMeta;

class TestModel extends Model implements UseMeta
{
    use Meta;

    protected $table = 'test_models';
    protected $fillable = ['name'];
    public $timestamps = false;
}
