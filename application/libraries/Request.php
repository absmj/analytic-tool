<?php

class Request {  
    private function __construct()
    {
        
    }

    public static function get($url, $header = []) {
        return self::request($url, 'GET', $header);
    }

    public static function post($url, $body, $header = []) {
        return self::request($url, 'POST', $body, $header);
    }

    public static function put($url, $body, $header = []) {
        return self::request($url, 'PUT', $body, $header);
    }

    public static function delete($url, $header = []) {
        return self::request($url, 'DELETE', $header);
    }

    public static function patch($url, $body, $header = []) {
        return self::request($url, 'PATCH', $body, $header);
    }

    public static function options($url, $header = []) {
        return self::request($url, 'OPTIONS', $header);
    }

    private static function request($url, $method, $body = [], $header = []) {
        $start = time();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if(!empty($body))
            curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($body));

        if(!empty($header))
            curl_setopt($ch, CURLOPT_HEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $end = time();
        return [
            'status' => $httpCode,
            'timestamp' => $end,
            'duration' => $end - $start,
            'data' => self::isJson($server_output) ? json_decode($server_output, true) : $server_output
        ];
    }

    protected static function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
     }
}