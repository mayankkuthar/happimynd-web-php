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
            // Intern-project APIs use their own error envelopes, so check them
            // before the generic api/* handling below.
            if ($this->isHrRequest($request)) {
                return $this->renderHrError($request, $e);
            }

            if ($this->isInvoiceRequest($request)) {
                return $this->renderInvoiceError($request, $e);
            }

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
            'api/hub/health',
            'api/hub/auth/*',
            'api/hub/track/*',
            'api/hub/content/*',
            'api/hub/catalog',
            'api/hub/progress/*',
            'api/hub/admin/*',
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

    private function isHrRequest(Request $request): bool
    {
        return $request->is('api/hr*');
    }

    /**
     * HR Auto errors use the FastAPI envelope (API_SPEC §1):
     *   { "detail": "..." }  or  422 { "detail": [ { loc, msg, type } ] }.
     */
    private function renderHrError(Request $request, Throwable $e): Response
    {
        if ($e instanceof ValidationException) {
            $detail = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $detail[] = [
                        'loc' => ['body', $field],
                        'msg' => $message,
                        'type' => 'value_error',
                    ];
                }
            }

            return response()->json(['detail' => $detail], 422);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json(['detail' => 'Not Found'], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(['detail' => 'Method Not Allowed'], 405);
        }

        if ($e instanceof HttpExceptionInterface) {
            $message = $e->getMessage() !== '' ? $e->getMessage() : 'Internal Server Error';

            return response()->json(['detail' => $message], $e->getStatusCode());
        }

        // Never leak SQL errors, connection strings or paths (API_SPEC §1).
        return response()->json(['detail' => 'Internal Server Error'], 500);
    }

    private function isInvoiceRequest(Request $request): bool
    {
        return $request->is('api/invoice*');
    }

    /**
     * Invoice Platform errors use { "error": "<message>", "details"?: {...} }
     * (API_SPEC error table). Domain errors are returned directly by the
     * controllers; this covers routing/validation/unexpected errors.
     */
    private function renderInvoiceError(Request $request, Throwable $e): Response
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'Invalid request',
                'details' => $e->errors(),
            ], 400);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        if ($e instanceof HttpExceptionInterface) {
            $message = $e->getMessage() !== '' ? $e->getMessage() : 'Internal server error';

            return response()->json(['error' => $message], $e->getStatusCode());
        }

        // Unexpected errors: never leak SQL or internals (API_SPEC error table).
        return response()->json(['error' => 'Internal server error'], 500);
    }
}
