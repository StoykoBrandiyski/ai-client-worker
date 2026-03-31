<?php

use App\Services\JobProcess\KernalProcess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $kernalProcess = app(KernalProcess::class);

        $kernalProcess->execute($schedule);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
