<?php
if (!function_exists("dd")) {
    function dd($argv)
    {
        echo "<pre>";
        print_r($argv);
        exit;
    }
}


if (!function_exists("href")) {
    function route_to($href)
    {
        return BASE_PATH . $href;
    }
}



if (!function_exists("active")) {
    function active($href)
    {
        $url = (uri_string());
        return preg_match("/$href/mui", $url) ? 'active' : 'collapsed';
    }
}

if (!function_exists("dblist")) {
    function dblist()
    {
        require APPPATH . "config/database.php";

        return array_keys($db);
    }
}

if (!function_exists('isPostRequest')) {
    function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

if (!function_exists('post')) {
    function post($field = null)
    {
        $post = empty($_POST) ? json_decode(file_get_contents("php://input"), 1) : $_POST;
        return !$field ? $post : ($post[$field] ?? null);
    }
}

if (!function_exists("folderStructure")) {
    function folderStructure($folders, $parent = null)
    {
        $result = [];

        foreach ($folders as $folder) {
            if ($folder['parent_folder_id'] == $parent) {
                $children = folderStructure($folders, $folder['folder_id']);

                if ($children) {
                    $folder['children'] = $children;
                }

                $result[] = $folder;
            }
        }

        return $result;
    }
}

if (!function_exists("generateFolderStructure")) {
    function generateFolderStructure($folder)
    {
        echo '<li>
                <div class="d-flex justify-content-between">
                    <div>
                        <i class="bi bi-folder-fill"></i> ' . htmlspecialchars($folder['folder_name']) . ' (' . count($folder['children'] ?? []) . ')
                    </div>
                    <div data-id=' . $folder['folder_id'] . '>
                        <i id="createFolder" class="text-primary bi bi-folder-plus"></i>
                        <i id="deleteFolder" class="text-danger bi bi-folder-minus"></i> 
                    </div>
                </div>';

        if (!empty($folder['children'])) {
            echo '<div class="arrow"><i class="bi bi-chevron-down"></i></div>';
            echo '<ul class="collapsed">';
            foreach ($folder['children'] as $child) {
                generateFolderStructure($child);
            }
            echo '</ul>';
        }

        echo '</li>';
    }
}


function camelToSnake($camelCase)
{
    $result = '';

    for ($i = 0; $i < strlen($camelCase); $i++) {
        $char = $camelCase[$i];

        if (ctype_upper($char)) {
            $result .= '_' . strtolower($char);
        } else {
            $result .= $char;
        }
    }

    return ltrim($result, '_');
}


function csv2json($csv)
{
    $rows = explode("\n", trim($csv));
    $data = array_slice($rows, 1);
    $keys = array_fill(0, count($data), $rows[0]);
    $array = array_map(function ($row, $key) {
        return array_combine(str_getcsv($key), str_getcsv($row));
    }, $data, $keys);

    return $array;
}

function autorize()
{
    return true;
}

function mounthConverter($mounth)
{
    switch ($mounth) {
        case 1:
            return "Yanvar";
        case 2:
            return "Fevral";
        case 3:
            return "Mart";
        case 4:
            return "Aprel";
        case 5:
            return "May";
        case 6:
            return "İyun";
        case 7:
            return "İyul";
        case 8:
            return "Avqust";
        case 9:
            return "Sentyabr";
        case 10:
            return "Oktyabr";
        case 11:
            return "Noyabr";
        case 12:
            return "Dekabr";
    }
}

// Error/Exception helper
function api_error($error, $errno = 500)
{

    http_response_code($errno);
    header('Content-Type: application/json');

    return json_encode([
        'success' => false,
        'status' => $errno,
        'errno'   => $errno,
        'message'   => $error,
    ]);
}

function base64UrlDecode($input)
{
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $input .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}

function decodeJwt($jwt)
{
    // Split the JWT into its components
    $parts = explode('.', $jwt);

    if (count($parts) !== 3) {
        return ['error' => 'Invalid JWT format'];
    }

    // Decode each part
    $header = json_decode(base64UrlDecode($parts[0]), true);
    $payload = json_decode(base64UrlDecode($parts[1]), true);
    $signature = $parts[2];

    return $payload;
}

// function pkcs7_pad($data, $size)
// {
//     $length = $size - strlen($data) % $size;
//     return $data . str_repeat(chr($length), $length);
// }

// function pkcs7_unpad($data)
// {
//     return substr($data, 0, -ord($data[strlen($data) - 1]));
// }

// function cookiedecryptor($name, $iv)
// {
//     return openssl_decrypt(
//         $name,
//         'AES-256-CBC',
//         '22$#$#5fs45fd3@!!45ret&&54',
//         0,
//         $iv
//     );
// }

// function cookieencryptor($data)
// {
//     $iv_size = 16; // 128 bits
//     $iv = openssl_random_pseudo_bytes($iv_size);
//     return openssl_encrypt(
//         pkcs7_pad($data, 16), // padded data
//         'AES-256-CBC',        // cipher and mode
//         '22$#$#5fs45fd3@!!45ret&&54',      // secret key
//         0,                    // options (not used)
//         $iv                   // initialisation vector
//     );
// }

function user($key = null)
{
    return $key && isset($_SESSION['auth'][$key]) ? $_SESSION['auth'][$key] : $_SESSION['auth'];
}

function checkEmpty($field, $reserve)
{
    return empty($field) ? $reserve : $field;
}

if (!function_exists('array_is_list')) {
    function array_is_list(array $arr)
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
