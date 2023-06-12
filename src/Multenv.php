<?php

namespace IBroStudio\Multenv;

use Dotenv\Dotenv;
use IBroStudio\Multenv\Exceptions\DecryptException;
use IBroStudio\Multenv\Exceptions\EmptyVariablesException;
use IBroStudio\Multenv\Traits\EncryptedFiles;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class Multenv
{
    use EncryptedFiles;

    public function __construct(
        public Collection $variables
    ) {
    }

    public function load(): void
    {
        $this->files(function (string $file, array $properties) {
            $this->loadVariablesFromFile($file);
        });
    }

    public function merge(): bool
    {
        $this->load();

        return File::put(base_path('.env'), $this->format());
    }

    public function format(): string
    {
        if (! $this->variables->count()) {
            throw new EmptyVariablesException('No variables found!');
        }

        return Arr::join(
            $this->variables
                ->map(fn ($value, $key) => preg_match('/\s/', $value)
                        ? "$key='$value'"
                        : "$key=$value"
                )
                ->toArray(),
            PHP_EOL
        );
    }

    private function files(\Closure $callback): void
    {
        foreach (config('multenv') as $file => $properties) {
            if (Arr::exists($properties, 'pattern')) {
                $file = Str::of($properties['pattern'])
                    ->replace('*', $this->getCurrentGitBranch())
                    ->prepend('.env.');
            }
            $callback($file, $properties);
        }
    }

    private function loadVariablesFromFile(string $file): void
    {
        $this->variables = $this->variables->merge(
            Dotenv::parse(File::get(base_path($file)))
        );
    }

    private function getCurrentGitBranch(): string
    {
        $process = Process::path(base_path())
            ->run('git rev-parse --abbrev-ref HEAD');

        if ($process->failed()) {
            throw new DecryptException(Str::squish($process->output()));
        }

        return Str::squish($process->output());
    }
}
