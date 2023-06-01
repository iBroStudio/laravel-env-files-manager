<?php

namespace IBroStudio\Multenv\Commands;

use IBroStudio\Multenv\Exceptions\KeyExistsException;
use IBroStudio\Multenv\Facades\Multenv;
use Illuminate\Console\Command;

class KeyCommand extends Command
{
    public $signature = 'multenv:key';

    public $description = 'Generate key for multenv encryption methods';

    public function handle(): int
    {
        try {
            Multenv::generateKey();
        } catch (KeyExistsException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Key successfully generated!');

        return self::SUCCESS;
    }
}
