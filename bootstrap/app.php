<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use function App\Helpers\errorResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'set.locale' => \App\Http\Middleware\SetLocale::class,
        ]);
        // Ensure locale is set before any API group middleware runs (e.g., auth)
        $middleware->prependToGroup('api', \App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $exception, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                switch (true) {
                    case $exception instanceof ValidationException:
                        return errorResponse($exception->errors(), 422);

                    case $exception instanceof ModelNotFoundException:
                        $modelName = strtolower(class_basename($exception->getModel()));
                        return errorResponse(__('app.messages.exception.not_found', ['model' => __('app.resources.' . $modelName . '.label')]), 404);

                    case $exception instanceof AuthenticationException:
                        return errorResponse(__('app.messages.exception.unauthenticated'), 401);

                    case $exception instanceof AuthorizationException:
                        $message = $exception->getMessage();
                        return errorResponse(__('auth.' . $message), 403);

                    case $exception instanceof NotFoundHttpException:
                        // Check if this was originally a ModelNotFoundException
                        $previous = $exception->getPrevious();
                        if ($previous instanceof ModelNotFoundException) {
                            $modelName = strtolower(class_basename($previous->getModel()));

                            return errorResponse(__('app.messages.exception.not_found', ['model' => __('app.resources.' . $modelName . '.label')]), 404);
                        }
                        return errorResponse(__('app.messages.exception.not_found', ['model' => 'resource']), 404);

                    case $exception instanceof MethodNotAllowedHttpException:
                        return errorResponse(__('app.messages.exception.method_not_allowed'), 405);

                    case $exception instanceof HttpException:
                        return errorResponse($exception->getMessage(), $exception->getStatusCode());

                    case $exception->getCode() === 23000
                        && str_contains($exception->getMessage(), 'Integrity constraint violation'):
                        return errorResponse(__('app.messages.exception.integrity_constraint_violation'), 409);
                }
            }
        });
    })->create();
