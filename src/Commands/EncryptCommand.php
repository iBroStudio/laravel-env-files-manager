<?php

namespace IBroStudio\Multenv\Commands;

use IBroStudio\Multenv\Exceptions\DecryptException;
use IBroStudio\Multenv\Exceptions\EncryptException;
use IBroStudio\Multenv\Facades\Multenv;
use Illuminate\Console\Command;

class EncryptCommand extends Command
{
    public $signature = 'multenv:encrypt';

    public $description = 'Encrypt env files';

    public function handle(): int
    {
        try {
            Multenv::encrypt();
        } catch (EncryptException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Env files encrypted!');

        return self::SUCCESS;
    }
}
