<?php

class BaseResponse
{
    public static function ok($message = "Successfull", $data = [], $status = StatusCodes::HTTP_OK) {
        header("Content-Type: application/json", true, $status);
        return json_encode([
            "status" => $status,
            "type" => "Success",
            "message" =>$message,
            "data" => $data
        ]);
    }

    public static function error($message = "Error", $status = StatusCodes::HTTP_INTERNAL_SERVER_ERROR) {
        header("Content-Type: application/json", true, $status);
        return json_encode([
            "status" => $status,
            "type" => "error",
            "message" =>$message,
        ]);
    }
    
}
