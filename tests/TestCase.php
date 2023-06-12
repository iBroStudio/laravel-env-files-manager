<?php

namespace IBroStudio\Multenv\Tests;

use IBroStudio\Multenv\MultenvServiceProvider;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MultenvServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        File::copy(__DIR__.'/Mocks/artisan', base_path('artisan'));
        File::copy(__DIR__.'/Mocks/.env.primary', base_path('.env.primary'));
        File::copy(__DIR__.'/Mocks/.env.branch-main', base_path('.env.branch-main'));
        File::copy(__DIR__.'/Mocks/.env.custom', base_path('.env.custom'));
    }
}
