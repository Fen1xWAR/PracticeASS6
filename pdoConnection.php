<?php
$user = 'root';
$pass = '2203';
try {
    $dbh = new PDO('mysql:host=94.143.46.65;dbname=education_system', $user, $pass);
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
}