<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$connection = new PDO("mysql:host=localhost;dbname=1205257", "1205257", "22542254");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fileName  = $_FILES['file']['name'] ?? '';
$tempPath  = $_FILES['file']['tmp_name'] ?? '';
$fileSize  = $_FILES['file']['size'] ?? 0;
$fileError = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == 'POST') {

    if (!empty($fileName)) {

        $upload_path = __DIR__ . "/reports/"; // Set upload folder path 

        // Generate random file name
        $fileExt = explode('.', $fileName);
        $fileActExt = strtolower(end($fileExt));

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true) || chmod($upload_path, 0777);
        }


            if ($fileSize <= 20000000 && $fileError == UPLOAD_ERR_OK) {
                $location = uniqid();
                if (move_uploaded_file($tempPath, $upload_path . $location . "." . $fileActExt)) {
                    $query = "INSERT INTO analytic_files (file_name, location, unique_field) VALUES(?, ?, ?)";
                    $stmt = $connection->prepare($query);

                    if ($stmt->execute([$fileName, $upload_path . $fileName, $location])) {
                        $response = [
                            'status' => true,
                            'message' => 'File uploaded successfully',
                            'data' => [
                                'uniqueField' => $location
                            ]
                        ];
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Database error: ' . implode(', ', $stmt->errorInfo())
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Failed to move uploaded file",
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => "File size exceeds 20 MB or file error occurred",
                ];
            }
        

    } else {
        $response = [
            'status' => false,
            'message' => "No file selected. Please upload a file",
        ];
    }
} else {
    $response = [
        'status' => false,
        'message' => $requestMethod . ' method not allowed',
    ];
}

echo json_encode($response);
