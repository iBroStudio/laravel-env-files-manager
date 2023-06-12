<?php

namespace IBroStudio\Multenv\Traits;

use IBroStudio\Multenv\Exceptions\DecryptException;
use IBroStudio\Multenv\Exceptions\EncryptException;
use IBroStudio\Multenv\Exceptions\KeyExistsException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

trait EncryptedFiles
{
    const KEY = '.multenv';

    public function generateKey(): bool
    {
        if (File::exists(base_path(self::KEY))) {
            throw new KeyExistsException('Key already exists!');
        }

        $key = Process::run('php artisan key:generate --show')
            ->output();

        return File::put(base_path(self::KEY), rtrim($key));
    }

    public function encrypt(): void
    {
        $this->files(function ($file, $properties) {
            if ($properties['encrypt'] && File::exists(base_path($file))) {

                $process = Process::path(base_path())
                    ->run('php artisan env:encrypt --env='.Str::afterLast($file, '.').' --key='.$this->getKey().' --force');

                if ($process->failed()) {
                    throw new EncryptException(Str::squish($process->output()));
                }
            }
        });
    }

    public function decrypt(): void
    {
        $this->files(function ($file, $properties) {
            if ($properties['encrypt'] && File::exists(base_path($file.'.encrypted'))) {

                $process = Process::path(base_path())
                    ->run('php artisan env:decrypt --env='.Str::afterLast($file, '.').' --key='.$this->getKey().' --force');

                if ($process->failed()) {
                    throw new DecryptException(Str::squish($process->output()));
                }
            }
        });
    }

    private function getKey(): string
    {
        return File::get(base_path(self::KEY));
    }
}
