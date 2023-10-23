<?php
if(!function_exists("dd")) {
    function dd(...$argv) {
        echo "<pre>";
        print_r($argv);
        exit;
    }
}


if(!function_exists("href")) {
    function route_to($href) {
        return BASE_PATH . $href;
    }
}



if(!function_exists("active")) {
    function active($href) {
        $url = (uri_string());
        return preg_match("/$href/mui", $url) ? 'active' : 'collapsed';
    }
}

if(!function_exists("dblist")) {
    function dblist() {
        require APPPATH . "config/database.php";

        return array_keys($db);
    }
}

if(!function_exists('isPostRequest')) {
    function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

if(!function_exists('post')) {
    function post($field) {
        return $_POST[$field] ?? null;
    }
}

?>