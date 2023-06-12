<?php

namespace IBroStudio\Multenv\Commands;

use IBroStudio\Multenv\Exceptions\EmptyVariablesException;
use IBroStudio\Multenv\Facades\Multenv;
use Illuminate\Console\Command;

class MergeCommand extends Command
{
    public $signature = 'multenv:merge';

    public $description = 'Merge env files';

    public function handle(): int
    {
        try {
            Multenv::merge();
        } catch (EmptyVariablesException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Env files merged!');

        return self::SUCCESS;
    }
}
