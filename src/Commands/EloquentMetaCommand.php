<?php

namespace Traineratwot\EloquentMeta\Commands;

use Illuminate\Console\Command;

class EloquentMetaCommand extends Command
{
    public $signature = 'eloquent-meta';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
