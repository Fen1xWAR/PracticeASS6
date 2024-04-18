<?php
$user = 'root';
$pass = '2203';

try {
    $dbh = new PDO('mysql:host=94.143.46.65;dbname=education_system', $user, $pass);
} catch (Exception $e) {
    $responseData['error'] = 'Database connection error';
}

if (isset($responseData['error'])) {
    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit;
}

// Use the $dbh object for further database operations.