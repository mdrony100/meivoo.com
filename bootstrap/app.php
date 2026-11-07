<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;

// Auth & User Middleware
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;

// Spatie Roles & Permissions
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

// Custom Middleware
use App\Http\Middleware\XSSProtection;
use App\Support\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        /**
         * ---------------------------------------------------------
         * GLOBAL MIDDLEWARE
         * ---------------------------------------------------------
         */

        // Only keep your custom global middleware
        $middleware->append(\App\Http\Middleware\RemoveWwwPrefix::class);


        /**
         * ---------------------------------------------------------
         * MIDDLEWARE GROUPS
         * ---------------------------------------------------------
         */

        // WEB Group
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // API Group
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);


        /**
         * ---------------------------------------------------------
         * ROUTE MIDDLEWARE (valid ones only)
         * ---------------------------------------------------------
         */
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,

            // Spatie permissions
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,

            // Custom
            'XSS' => XSSProtection::class,
            'profile.completed' => \App\Http\Middleware\EnsureProfileCompleted::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Validation failed.', $e->errors(), 422);
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error($e->getMessage() ?: 'Unauthenticated.', null, 401);
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error($e->getMessage() ?: 'This action is unauthorized.', null, 403);
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Resource not found.', null, 404);
        });

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Too many requests. Please try again later.', null, 429);
        });

        // Handle CSRF token expiration
        $exceptions->render(function (TokenMismatchException $e, $request) {
            Log::warning('CSRF token mismatch - session may have expired', [
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
                'ip'     => $request->ip(),
                'user_id' => optional($request->user())->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please refresh the page and try again.',
                ], 419);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Your session has expired. Please refresh the page and try again.');
        });
    })

    ->create();
