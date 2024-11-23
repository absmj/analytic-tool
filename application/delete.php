<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$connection = new PDO("mysql:host=localhost;dbname=1205257", "1205257", "22542254");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fileName  = $_GET['uniqueField'] ?? '';

$requestMethod = $_SERVER['REQUEST_METHOD'];

$response = [];
try {
    if ($requestMethod == 'GET' && $fileName) {
        $query = "SELECT id, location FROM analytic_files WHERE unique_field=?";
        $stmt = $connection->prepare($query); 
        $stmt->execute([$fileName]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if(file_exists($data['location']))
            unset($data['location']);
        $query = "DELETE FROM analytic_files WHERE unique_field=?";
        $stmt = $connection->prepare($query); 
        $stmt->execute([$fileName]);
        $response = [
            'data' => 'Success',
            'status' => 200,
        ];
    }
} catch (Exception $e) {
    $response = [
        'error' => $e->getMessage(),
        'status' => 500,
    ];
}


echo json_encode($response);
