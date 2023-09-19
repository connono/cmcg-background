<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handlers as ExceptionHandler;
use Symfony\component\HttpKernel\Exception\NoFoundHttpException;
use Symfony\component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiExceptionHandler extends Exception
{
    //
    public function render($request, Throwable $exception)
    {
        if($this->isApiException($request, $exception)){
            $statusCode = $this->getStatusCode($exception);
            $errorMessage = $exception->getMessage();
            return new JsonResponse([
                "code" => "error",
                "msg" => $errorMessage,
            ], $statusCode);
        }
        return parent::render($request, $exception);
    }

    protected function isApiException($request, Throwable $exception)
    {
        return $request->is('api/*') && $exception instanceof \Exception;
    }

    protected function getStatusCode($exception) {
        if($exception instanceof NoFoundHttpException){
            return 404;
        } else if ($exception instanceof MethodNotAllowedHttpException){
            return 405;
        } elese if ($exception instanceof UnprocessableEntityHttpException){
            return 422;
        }
        return 200;
    }
}
