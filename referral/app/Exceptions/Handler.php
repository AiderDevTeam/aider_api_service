<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response|RedirectResponse
    {
        // if ($exception instanceof ModelNotFoundException) {
        //     return errorJsonResponse(message: 'Model resource not found', statusCode: 404);
        // }
        // return parent::render($request, $exception);

        if (strpos($request->getRequestUri(), '/api/', 0) === 0 &&
            get_class($e) === NotFoundHttpException::class) {
                return response()->json(['status' => false, 'message' => 'This Poynt Referral EndPoint Does not Exist'], 404);
        }

    if (get_class($e) ===  QueryException::class) {
            $ticket = strtoupper(Str::random(5).rand(100, 1000));
            logger()->error([
                'auth' => (!empty(auth()->user())) ? auth()->user()->toArray() : "",
                'ticket' => $ticket,
                'route' => Route::getFacadeRoot()->current()->uri() ?? $request->url(),
                'exception' => $e->getMessage()
            ]);
            return response()->json(['status' => 'false','message' =>"Something went wrong please report this ticket($ticket) to Poynt Support"], 500);
    }

    if (get_class($e) ===  ErrorException::class) {
        $ticket = strtoupper(Str::random(5).rand(100, 1000));
        logger()->error([
            'auth' => (!empty($request->user)) ? $request->user : "",
            'ticket' => $ticket,
            'route' => Route::getFacadeRoot()->current()->uri() ?? $request->url(),
            'exception' => $e->getMessage()
        ]);
        return response()->json(['status' => 'false','message' =>"Something went wrong please report this ticket($ticket) to Poynt Support"], 500);
}
    
    // Default to the parent class' implementation of handler
    return parent::render($request, $e);

    }
}
