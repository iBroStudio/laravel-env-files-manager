<?php

use Dotenv\Dotenv;
use IBroStudio\Multenv\Commands\DecryptCommand;
use IBroStudio\Multenv\Commands\EncryptCommand;
use IBroStudio\Multenv\Commands\KeyCommand;
use IBroStudio\Multenv\Commands\MergeCommand;
use IBroStudio\Multenv\Multenv;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use function Pest\Laravel\artisan;
use Symfony\Component\Console\Command\Command;

it('can merge env files', function () {

    File::delete(base_path('.env'));
    File::copy(__DIR__.'/Mocks/.env.primary', base_path('.env.primary'));
    File::copy(__DIR__.'/Mocks/.env.branch-main', base_path('.env.branch-main'));
    File::copy(__DIR__.'/Mocks/.env.custom', base_path('.env.custom'));

    config([
        'multenv' => [
            '.env.primary' => ['encrypt' => true],
            '.env.branch' => ['encrypt' => true, 'pattern' => 'branch-*'],
            '.env.custom' => ['encrypt' => false],
        ],
    ]);

    artisan(MergeCommand::class)
        ->expectsOutput('Env files merged!')
        ->assertExitCode(Command::SUCCESS);

    expect(base_path('.env'))->toBeFile();

    expect(
        Dotenv::parse(File::get(base_path('.env')))
    )->toMatchArray([
        'APP_NAME' => 'BRANCH_NAME',
        'APP_ENV' => 'local',
        'APP_URL' => 'MAIN_URL',
    ]);

});

it('can generate envkey file', function () {

    File::delete(base_path(Multenv::KEY));

    Process::shouldReceive('run')
        ->with('php artisan key:generate --show')
        ->andReturnSelf();

    Process::shouldReceive('output')
        ->andReturn('3UVsEgGVK36XN82KKeyLFMhvosbZN1aF');

    artisan(KeyCommand::class)
        ->expectsOutput('Key successfully generated!')
        ->assertExitCode(Command::SUCCESS);

    expect(base_path(Multenv::KEY))->toBeFile();

    expect(File::get(base_path(Multenv::KEY)))->toEqual('3UVsEgGVK36XN82KKeyLFMhvosbZN1aF');
});

it('can encrypt env files', function () {
    File::copy(__DIR__.'/Mocks/'.Multenv::KEY, base_path(Multenv::KEY));
    File::delete(base_path('.env.primary.encrypted'));
    File::delete(base_path('.env.branch-main.encrypted'));

    config([
        'multenv' => [
            '.env.primary' => ['encrypt' => true],
            '.env.branch' => ['encrypt' => true, 'pattern' => 'branch-*'],
        ],
    ]);

    artisan(EncryptCommand::class)
        ->expectsOutput('Env files encrypted!')
        ->assertExitCode(Command::SUCCESS);

    expect(base_path('.env.primary.encrypted'))->toBeFile();
    expect(base_path('.env.branch-main.encrypted'))->toBeFile();
});

it('can decrypt env files', function () {
    File::copy(__DIR__.'/Mocks/'.Multenv::KEY, base_path(Multenv::KEY));
    File::copy(__DIR__.'/Mocks/.env.primary.encrypted', base_path('.env.primary.encrypted'));
    File::copy(__DIR__.'/Mocks/.env.branch-main.encrypted', base_path('.env.branch-main.encrypted'));
    File::delete(base_path('.env.primary'));
    File::delete(base_path('.env.branch-main'));

    config([
        'multenv' => [
            '.env.primary' => ['encrypt' => true],
            '.env.branch' => ['encrypt' => true, 'pattern' => 'branch-*'],
        ],
    ]);

    artisan(DecryptCommand::class)
        ->expectsOutput('Env files decrypted!')
        ->assertExitCode(Command::SUCCESS);

    expect(base_path('.env.primary'))->toBeFile();
    expect(base_path('.env.branch-main'))->toBeFile();

    expect(
        [Dotenv::parse(File::get(base_path('.env.primary')))]
    )->toMatchArray([
        Dotenv::parse(File::get(__DIR__.'/Mocks/.env.primary')),
    ]);

    expect(
        [Dotenv::parse(File::get(base_path('.env.branch-main')))]
    )->toMatchArray([
        Dotenv::parse(File::get(__DIR__.'/Mocks/.env.branch-main')),
    ]);
});
