<?php
$user = 'root';
$pass = '2203';
try {
    $dbh = new PDO('mysql:host=localhost;dbname=education_system', $user, $pass);
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
}