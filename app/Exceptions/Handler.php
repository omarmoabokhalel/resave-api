<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * قائمة استثناءات محددة لا يتم الإبلاغ عنها.
     */
    protected $dontReport = [];

    /**
     * الحقول التي لا يتم إظهارها عند الـ validation errors.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * تسجيل أي استثناءات إضافية.
     */
    public function register(): void
    {
        //
    }

    /**
     * التعامل مع الـ authentication exceptions
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // لو الـ request API => رجع JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // لو مش API (web) ممكن تعمل redirect لأي صفحة login
        return redirect()->guest(route('login'));
    }

    /**
     * التعامل مع أي استثناء عام
     */
    public function render($request, Throwable $exception)
    {
        // لو request API وحدث أي خطأ
        if ($request->expectsJson()) {
            $status = ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) ? $exception->getStatusCode() : 500;

            return response()->json([
                'message' => $exception->getMessage() ?: 'Server Error',
                'exception' => class_basename($exception),
                'trace' => config('app.debug') ? $exception->getTrace() : null
            ], $status);
        }

        // web default
        return parent::render($request, $exception);
    }
}
