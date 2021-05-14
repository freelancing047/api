<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        /* application wide exception handler */
        $this->renderable(function (ApplicationException $exception, $request) {
            if (request()->ajax() || request()->is('api/*')) {
                return apiError($exception->getMessage(), 500);
            }

            return back()->withInput()->with(['info' => $exception->getMessage()]);
        });

        /* validation handler for api */
        $this->renderable(function (\Illuminate\Validation\ValidationException $exception, $request) {
            if (request()->ajax() || request()->is('api/*')) {
                return apiError('Validation Exception', 411, $exception->errors());
            }
        });

        /* for global exception if request is api request throw json error in our global format */
        $this->renderable(function (\Exception $e) {
            if (request()->ajax() || request()->is('api/*')) {
                return apiError($e->getMessage(), 500);
            }
        });
    }
}
