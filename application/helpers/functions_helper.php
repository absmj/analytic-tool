<?php
if (!function_exists("dd")) {
    function dd(...$argv)
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
        return !$field ? $_POST : ($_POST[$field] ?? null);
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
