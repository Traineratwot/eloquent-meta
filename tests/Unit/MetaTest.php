<?php

use Traineratwot\EloquentMeta\Tests\TestModel;
use Traineratwot\EloquentMeta\Models\EloquentMeta;
function createTestTables()
{
    $connection = app('db')->connection();

    if (!$connection->getSchemaBuilder()->hasTable('test_models')) {
        $connection->getSchemaBuilder()->create('test_models', function ($table) {
            $table->id();
            $table->string('name');
        });
    }

    if (!$connection->getSchemaBuilder()->hasTable('eloquent_metas')) {
        $connection->getSchemaBuilder()->create('eloquent_metas', function ($table) {
            $table->id();
            $table->morphs('model');
            $table->string('column');
            $table->jsonb('data');
            $table->unique(['model_id', 'model_type', 'column']);
            $table->timestamps();
        });
    }
}
beforeEach(function () {
    createTestTables();
});

test('can set and get meta', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio');

    expect($model->getMeta('profile', 'bio'))->toBe('My bio');
});

test('can get all meta data', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio')
        ->setMeta('profile', 'avatar', 'avatar.jpg');

    $data = $model->getAllMeta('profile');

    expect($data)
        ->toBeArray()
        ->toHaveKey('bio', 'My bio')
        ->toHaveKey('avatar', 'avatar.jpg');
});

test('can get meta with default value', function () {
    $model = TestModel::create(['name' => 'Test']);

    $value = $model->getMeta('profile', 'bio', 'default bio');

    expect($value)->toBe('default bio');
});

test('can push value to meta array', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->pushMeta('tags', 'items', 'php')
        ->pushMeta('tags', 'items', 'laravel');

    $items = $model->getMeta('tags', 'items');

    expect($items)
        ->toBeArray()
        ->toContain('php', 'laravel')
        ->toHaveCount(2);
});

test('can forget meta key', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio')
        ->setMeta('profile', 'avatar', 'avatar.jpg');

    $model->forgetMeta('profile', 'bio');

    expect($model->getMeta('profile', 'bio'))->toBeNull();
    expect($model->getMeta('profile', 'avatar'))->toBe('avatar.jpg');
});

test('can forget entire meta column', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio')
        ->setMeta('profile', 'avatar', 'avatar.jpg');

    $model->forgetMeta('profile');

    expect($model->hasMeta('profile'))->toBeFalse();
});

test('can check if meta exists', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio');

    expect($model->hasMeta('profile'))->toBeTrue();
    expect($model->hasMeta('profile', 'bio'))->toBeTrue();
    expect($model->hasMeta('profile', 'avatar'))->toBeFalse();
    expect($model->hasMeta('settings'))->toBeFalse();
});

test('can chain set meta calls', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio')
        ->setMeta('profile', 'avatar', 'avatar.jpg')
        ->setMeta('settings', 'theme', 'dark');

    expect($model->getMeta('profile', 'bio'))->toBe('My bio');
    expect($model->getMeta('profile', 'avatar'))->toBe('avatar.jpg');
    expect($model->getMeta('settings', 'theme'))->toBe('dark');
});

test('can update existing meta', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'Old bio');
    expect($model->getMeta('profile', 'bio'))->toBe('Old bio');

    $model->setMeta('profile', 'bio', 'New bio');
    expect($model->getMeta('profile', 'bio'))->toBe('New bio');
});

test('meta persists after reload', function () {
    $model = TestModel::create(['name' => 'Test']);
    $model->setMeta('profile', 'bio', 'My bio');

    $reloadedModel = TestModel::find($model->id);

    expect($reloadedModel->getMeta('profile', 'bio'))->toBe('My bio');
});

test('can work with nested keys', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('settings', 'appearance.theme', 'dark')
        ->setMeta('settings', 'appearance.font_size', 14);

    expect($model->getMeta('settings', 'appearance.theme'))->toBe('dark');
    expect($model->getMeta('settings', 'appearance.font_size'))->toBe(14);
});

test('can store complex data types', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('data', 'array', [1, 2, 3])
        ->setMeta('data', 'object', ['key' => 'value'])
        ->setMeta('data', 'number', 42)
        ->setMeta('data', 'boolean', true)
        ->setMeta('data', 'null', null);

    expect($model->getMeta('data', 'array'))->toBe([1, 2, 3]);
    expect($model->getMeta('data', 'object'))->toBe(['key' => 'value']);
    expect($model->getMeta('data', 'number'))->toBe(42);
    expect($model->getMeta('data', 'boolean'))->toBeTrue();
    expect($model->getMeta('data', 'null'))->toBeNull();
});

test('returns null for non existent meta', function () {
    $model = TestModel::create(['name' => 'Test']);

    expect($model->getMeta('profile', 'bio'))->toBeNull();
});

test('returns empty array for non existent column', function () {
    $model = TestModel::create(['name' => 'Test']);

    expect($model->getAllMeta('profile'))->toBe([]);
});

test('push to non existent key creates array', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->pushMeta('tags', 'items', 'php');

    expect($model->getMeta('tags', 'items'))->toBe(['php']);
});

test('push converts non array to array', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('tags', 'items', 'php')
        ->pushMeta('tags', 'items', 'laravel');

    expect($model->getMeta('tags', 'items'))->toBe(['php', 'laravel']);
});

test('forget key keeps other keys', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio')
        ->setMeta('profile', 'avatar', 'avatar.jpg')
        ->setMeta('profile', 'website', 'example.com');

    $model->forgetMeta('profile', 'avatar');

    expect($model->getAllMeta('profile'))
        ->toHaveCount(2)
        ->toHaveKey('bio')
        ->toHaveKey('website')
        ->not->toHaveKey('avatar');
});

test('multiple models have separate meta', function () {
    $model1 = TestModel::create(['name' => 'Model 1']);
    $model2 = TestModel::create(['name' => 'Model 2']);

    $model1->setMeta('profile', 'bio', 'Bio 1');
    $model2->setMeta('profile', 'bio', 'Bio 2');

    expect($model1->getMeta('profile', 'bio'))->toBe('Bio 1');
    expect($model2->getMeta('profile', 'bio'))->toBe('Bio 2');
});

test('can delete model with meta', function () {
    $model = TestModel::create(['name' => 'Test']);
    $model->setMeta('profile', 'bio', 'My bio');

    $modelId = $model->id;
    $model->delete();

    expect(EloquentMeta::where('model_id', $modelId)->count())->toBe(0);
});

test('metas relationship works', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio')
        ->setMeta('settings', 'theme', 'dark');

    expect($model->metas()->count())->toBe(2);
});

test('can get meta without loading relation', function () {
    $model = TestModel::create(['name' => 'Test']);
    $model->setMeta('profile', 'bio', 'My bio');

    $freshModel = TestModel::find($model->id);

    expect($freshModel->relationLoaded('metas'))->toBeFalse();
    expect($freshModel->getMeta('profile', 'bio'))->toBe('My bio');
    expect($freshModel->relationLoaded('metas'))->toBeTrue();
});

test('empty meta column is deleted', function () {
    $model = TestModel::create(['name' => 'Test']);

    $model->setMeta('profile', 'bio', 'My bio');
    expect(EloquentMeta::count())->toBe(1);

    $model->forgetMeta('profile', 'bio');
    expect(EloquentMeta::count())->toBe(0);
});

test('returns self for method chaining', function () {
    $model = TestModel::create(['name' => 'Test']);

    $result = $model->setMeta('profile', 'bio', 'My bio');

    expect($result)->toBeInstanceOf(TestModel::class);
    expect($result->id)->toBe($model->id);
});
