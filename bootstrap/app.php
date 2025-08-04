<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('cache:clear')
            ->cron('0 0 1,15 * *');

        $schedule->command('view:clear')
            ->cron('0 0 1,15 * *');

        $schedule->command('todos:delete-completed')
            ->everyFiveMinutes();

        $schedule->command('todos:alert --days=30')
            ->cron('*/5 * * * *');
    })
    ->create();
