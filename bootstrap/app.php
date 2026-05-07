<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // <--- INI JALUR API YANG KITA AKTIFKAN
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Middleware bawaan Anda sebelumnya
            'role' => \App\Http\Middleware\RoleMiddleware::class,

            // Middleware khusus Admin (Ghost System)
            'admin' => \App\Http\Middleware\AdminMiddleware::class,

            // Middleware Kasta/Role Admin (BARU)
            'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
        ]);

        // --- PENGECUALIAN CSRF TOKEN ---
        // Biarkan Midtrans bisa mengakses route ini tanpa diblokir sistem keamanan Laravel
        $middleware->validateCsrfTokens(except: [
            '/webhook/midtrans',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
