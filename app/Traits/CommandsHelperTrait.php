<?php

namespace App\Traits;

trait CommandsHelperTrait
{
    protected function clear(): void
    {
        passthru(strncasecmp(PHP_OS, 'WIN', 3) === 0 ? 'cls' : 'clear');
    }
}
