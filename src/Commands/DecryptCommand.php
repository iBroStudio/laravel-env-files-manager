<?php

namespace IBroStudio\Multenv\Commands;

use IBroStudio\Multenv\Exceptions\DecryptException;
use IBroStudio\Multenv\Facades\Multenv;
use Illuminate\Console\Command;

class DecryptCommand extends Command
{
    public $signature = 'multenv:decrypt';

    public $description = 'Decrypt env files';

    public function handle(): int
    {
        try {
            Multenv::decrypt();
        } catch (DecryptException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Env files decrypted!');

        return self::SUCCESS;
    }
}
