<?php

namespace IBroStudio\Multenv;

use IBroStudio\Multenv\Commands\DecryptCommand;
use IBroStudio\Multenv\Commands\EncryptCommand;
use IBroStudio\Multenv\Commands\MergeCommand;
use IBroStudio\Multenv\Commands\KeyCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MultenvServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('multenv')
            ->hasConfigFile()
            ->hasCommands(
                DecryptCommand::class,
                EncryptCommand::class,
                MergeCommand::class,
                KeyCommand::class
            );
    }
}
