<?php

namespace IBroStudio\Multenv;

use Dotenv\Dotenv;
use IBroStudio\Multenv\Exceptions\EmptyVariablesException;
use IBroStudio\Multenv\Traits\EncryptedFiles;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Multenv
{
    use EncryptedFiles;

    public function __construct(
        public Collection $variables
    )
    {}

    public function load(): void
    {
        foreach (array_keys(config('multenv.include')) as $file) {
            if (File::exists(base_path($file))) {
                $this->variables = $this->variables->merge(
                    Dotenv::parse(File::get(base_path($file)))
                );
            }
        }
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
                ->map(fn ($value, $key) =>
                    preg_match('/\s/', $value)
                        ? "$key='$value'"
                        : "$key=$value"
                )
                ->toArray(),
            PHP_EOL
        );
    }
}
