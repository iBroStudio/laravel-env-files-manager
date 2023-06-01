<?php

namespace IBroStudio\Multenv\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;
use IBroStudio\Multenv\MultenvServiceProvider;

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
        File::copy(__DIR__ . '/Mocks/.env.primary', base_path('.env.primary'));
        File::copy(__DIR__ . '/Mocks/.env.branch', base_path('.env.branch'));
        File::copy(__DIR__ . '/Mocks/.env.custom', base_path('.env.custom'));
    }
}