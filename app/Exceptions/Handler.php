<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        try {
            $msg = $exception->getMessage();

            // if($msg) {
            //     Log::channel('slack')->critical('Collect v2: ' . $msg . "\n" . $exception->getTraceAsString());
            // }
        } finally {
            return parent::report($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $status = 500;

        if(is_a($exception, BadRequestException::class)) {
            $error = $exception->getError();
            $status = 400;

            return response()->json($error, $status);
        }

        if($request->ajax()) {
            $error = [
                'error' => $exception->getMessage(), 
            ];

            return response()->json($error, $status);
        }
        
        // form not posted for a long time -> refresh
        if ($exception instanceof TokenMismatchException){
            return redirect($request->fullUrl());
        }

        return parent::render($request, $exception);
    }
}
