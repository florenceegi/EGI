<?php

namespace App\Exceptions;


use Ultra\ErrorManager\Exceptions\UltraErrorException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        UltraErrorException::class, // <-- Aggiungi questa riga
    ];

    // app/Exceptions/Handler.php

    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('label.session.expired')], 419);
            }
            return redirect()->route('login')->with('status', __('label.session.expired'));
        }
        return parent::render($request, $e);
    }

}
