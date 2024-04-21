<?php
$user = 'root';
$pass = '2203';
date_default_timezone_set('Europe/Moscow');
try {
    $dbh = new PDO('mysql:host=94.143.46.65;dbname=education_system', $user, $pass);
} catch (Exception $e) {
    http_response_code(500);
    echo  json_encode(["message" => "Ошибка подключения к базе данных"], JSON_UNESCAPED_UNICODE);

}
