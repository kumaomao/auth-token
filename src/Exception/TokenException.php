<?php
namespace Kumaomao\AuthToken\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class TokenException extends ServerException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}