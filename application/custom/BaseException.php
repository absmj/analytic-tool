<?php

class BaseException
{
    public static function api_error_handler($severity, $message, $file, $line)
    {
        self::api_error($message, 500);
    }

    public static function api_error($error, $errno = 500)
    {
        http_response_code($errno);
        echo json_encode([
            'success' => false,
            'status'  => $errno,
            'errno'   => $errno,
            'message' => $error,
        ]);
        exit;
    }

    public static function api_exception_handler($exception)
    {
        self::api_error($exception->getMessage(), $exception->getCode());
    }
}
