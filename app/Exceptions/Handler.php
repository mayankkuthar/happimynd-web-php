<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * Games Hub requests use the API_SPEC §1 error envelope.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($request instanceof Request) {
            // Unknown API routes always get the API_SPEC JSON envelope,
            // whether or not they belong to a hub prefix.
            if ($request->is('api/*') || $request->is('auth/*')) {
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'error' => 'NOT_FOUND',
                        'message' => 'Cannot ' . $request->method() . ' ' . $request->getPathInfo(),
                    ], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'error' => 'METHOD_NOT_ALLOWED',
                        'message' => 'Cannot ' . $request->method() . ' ' . $request->getPathInfo(),
                    ], 405);
                }
            }

            if ($this->isHubRequest($request)) {
                return $this->renderHubError($request, $e);
            }
        }

        return parent::render($request, $e);
    }

    private function isHubRequest(Request $request): bool
    {
        foreach ([
            'api/health',
            'api/auth/*',
            'api/track/*',
            'api/content/*',
            'api/catalog',
            'api/admin/*',
            'auth/*',
        ] as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    private function renderHubError(Request $request, Throwable $e): Response
    {
        if ($e instanceof ValidationException) {
            $message = $e->validator->errors()->first();

            return response()->json(['success' => false, 'message' => $message, 'error' => $message], 400);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'NOT_FOUND',
                'message' => 'Cannot ' . $request->method() . ' ' . $request->getPathInfo(),
            ], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => 'METHOD_NOT_ALLOWED',
                'message' => 'Cannot ' . $request->method() . ' ' . $request->getPathInfo(),
            ], 405);
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $message = $e->getMessage() !== '' ? $e->getMessage() : 'Request failed.';

            return response()->json(['success' => false, 'message' => $message, 'error' => $message], $status);
        }

        return parent::render($request, $e);
    }
}
