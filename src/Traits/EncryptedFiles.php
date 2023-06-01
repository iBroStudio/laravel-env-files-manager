<?php

namespace IBroStudio\Multenv\Traits;

use IBroStudio\Multenv\Exceptions\DecryptException;
use IBroStudio\Multenv\Exceptions\EncryptException;
use IBroStudio\Multenv\Exceptions\KeyExistsException;
use Illuminate\Support\Facades\Artisan;
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
        foreach (config('multenv.include') as $file => $properties) {
            if ($properties['encrypt']) {

                Artisan::call('env:encrypt', [
                    '--env' => Str::afterLast($file, '.'),
                    '--key' => $this->getKey(),
                    '--force' => true,
                ]);

                $output = Str::squish(Artisan::output());

                if (in_array("ERROR", explode(' ', $output))) {
                    throw new EncryptException($output);
                }
            }
        }
    }

    public function decrypt(): void
    {
        foreach (config('multenv.include') as $file => $properties) {
            if ($properties['encrypt']) {
                Artisan::call('env:decrypt', [
                    '--env' => Str::afterLast($file, '.'),
                    '--key' => $this->getKey(),
                    '--force' => true,
                ]);

                $output = Str::squish(Artisan::output());

                if (in_array("ERROR", explode(' ', $output))) {
                    throw new DecryptException($output);
                }
            }
        }
    }

    private function getKey(): string
    {
        return File::get(base_path(self::KEY));
    }
}