<?php

namespace IBroStudio\Multenv\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmptyVariablesException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response|bool
    {
        return false;
    }
}
