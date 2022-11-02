<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
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

        $this->renderable(function(Exception $e, Request $request){
            return $this->handleException($e, $request);
        });
    }

    /**
     * @param \Exception $exception
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function handleException(Exception $exception, Request $request)
    {
        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json(['message' => 'The specified URL cann\'t be found'], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof AccessDeniedHttpException && $request->wantsJson()) {
            return response()->json(['message' => 'You are not allowed to access this resource.'], Response::HTTP_FORBIDDEN);
        }
    }
}
