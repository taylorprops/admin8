<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Auth;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler {
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $_dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $_dontReport = [
        //
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception) {

        if ($exception instanceof TokenMismatchException) {
            // axios requests token mismatch
            return redirect() -> route('logout');
        }

        if (auth() -> user() == null) {
            // axios requests 500 error
            echo '<script>top.location.href="/";</script>';
        }

        return parent::render($request, $exception);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception) {
        parent::report($exception);
    }
}
