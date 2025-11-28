<?php

namespace common\components;

use yii\web\ErrorHandler;

class ApiErrorHandler extends ErrorHandler
{
    protected function convertExceptionToArray($exception)
    {
        $isException = $exception instanceof \yii\base\UserException
        || $exception instanceof \yii\web\HttpException
        || $exception instanceof \yii\base\Exception;

        $isError = $exception instanceof \Error
        || $exception instanceof \TypeError
        || $exception instanceof \yii\base\ErrorException;

        $response = [
            'success' => false,
            'message' => $exception->getMessage() ?? 'Something went wrong"',
            'error_code' => $exception->getCode() !== 0 ? $exception->getCode() : $exception->statusCode ?? 400,
            'data' => [],
        ];

        if (YII_DEBUG) {
            $response['origin'] = $isException ? 'exception' : ($isError ? 'php_error' : 'unknown');
            $response['isException'] = $isException;
            $response['isError'] = $isError;
            $response['exception_class'] = get_class($exception);
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
            $response['trace'] = $exception->getTraceAsString();
        }

        return $response;
    }
}
