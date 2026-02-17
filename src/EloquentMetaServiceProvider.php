<?php

namespace Traineratwot\EloquentMeta;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Traineratwot\EloquentMeta\Commands\EloquentMetaCommand;

class EloquentMetaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('eloquent-meta')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_eloquent_meta_table')
            ->hasCommand(EloquentMetaCommand::class);
    }
}
