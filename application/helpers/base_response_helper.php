<?php

class BaseResponse
{
    public static function post()
    {
        $post = file_get_contents("php://input");

        if (!$post) {
            $post = $_POST;
        }

        return $post;
    }

    public static function ok($message = "Successfull", $data = [], $status = StatusCodes::HTTP_OK, $br = true)
    {
        http_response_code($status);
        return json_encode($br ? [
            "status" => $status,
            "type" => "Success",
            "message" => $message,
            "data" => $data
        ] : $data);
    }

    public static function error($message = "Error", $status = StatusCodes::HTTP_INTERNAL_SERVER_ERROR)
    {
        http_response_code($status);
        return json_encode([
            "status" => $status,
            "type" => "error",
            "message" => $message,
        ]);
    }
}
