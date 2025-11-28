<?php

namespace common\exceptions;

use yii\base\Model;
use yii\web\HttpException;
use common\components\ErrorStandarts;

class ApiException extends HttpException
{
    public string $errorKey;

    public function __construct(string|array $errorKey, int $statusCode = 400, \Throwable $previous = null)
    {
        $this->errorKey = $errorKey;

        $error = ErrorStandarts::get($errorKey);

        parent::__construct(
            $statusCode,
            $error['message'],
            $error['code'],
            $previous
        );
    }

    public static function fromModel(Model $model, int $statusCode = 400): self
    {
        $firstError = $model->getFirstErrors();
        if (empty($firstError)) {
            return new self('UNEXPECTED_ERROR', $statusCode);
        }

        $firstKey = array_key_first($firstError);
        $errorKey = $firstError[$firstKey];

        return new self($errorKey, $statusCode);
    }
}
