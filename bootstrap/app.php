<?php

use App\Http\Middleware\EnsureUserAccountIsActive;
use App\Http\Middleware\NoCacheHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', NoCacheHeaders::class);
        $middleware->appendToGroup('web', EnsureUserAccountIsActive::class);

        $middleware->validateCsrfTokens(except: [
            'login',
            'logout',
            '*/login',
            '*/logout',
            'capstone_system/public/login',
            'capstone_system/public/logout',
            'instructor/consultations/*/approve',
            'instructor/consultations/*/decline',
            'instructor/consultations/*/start',
            'instructor/consultations/*/end',
            'instructor/consultations/*/no-answer',
            'instructor/consultations/*/mark-incomplete',
            'consultations/*/answer',
            'consultations/*/decline-call',
            'consultations/*/end-call',
            'webrtc/signal',
            '*/instructor/consultations/*/approve',
            '*/instructor/consultations/*/decline',
            '*/instructor/consultations/*/start',
            '*/instructor/consultations/*/end',
            '*/instructor/consultations/*/no-answer',
            '*/instructor/consultations/*/mark-incomplete',
            '*/consultations/*/answer',
            '*/consultations/*/decline-call',
            '*/consultations/*/end-call',
            '*/webrtc/signal',
            'capstone_system/public/instructor/consultations/*/approve',
            'capstone_system/public/instructor/consultations/*/decline',
            'capstone_system/public/instructor/consultations/*/start',
            'capstone_system/public/instructor/consultations/*/end',
            'capstone_system/public/instructor/consultations/*/no-answer',
            'capstone_system/public/instructor/consultations/*/mark-incomplete',
            'capstone_system/public/consultations/*/answer',
            'capstone_system/public/consultations/*/decline-call',
            'capstone_system/public/consultations/*/end-call',
            'capstone_system/public/webrtc/signal',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
