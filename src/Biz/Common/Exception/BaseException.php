<?php
namespace Biz\Common\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseException extends HttpException
{
    public function __construct($statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
