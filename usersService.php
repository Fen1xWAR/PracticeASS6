<?php
require_once "pdoConnection.php";
function CheckLoginData($login, $password)
{
    global $dbh;

    $query = $dbh->prepare("SELECT user_id, password, roles.role FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.login = :login");
    $query->bindValue(":login", $login);
    $query->execute();
    $result = $query->fetch();
    if ($result > 0 and password_verify($password, $result['password'])) {
        return [
            "userID" => $result['user_id'],
            "userRole" => $result['role']
        ];

    }
    return '';
}

if (isset($_POST['login'])) {
    $responseData = [];

    set_error_handler(function ($severity, $message, $file, $line) use (&$responseData) {
        $responseData['error'] = $message;
    }, E_ALL);

    $logData = $_POST['login'];
    $login = $logData['login'];
    $password = $logData['password'];

    try {
        $data = CheckLoginData($login, $password);
    } catch (Exception $e) {
        $responseData['error'] = $e->getMessage();
    }

    $role = $data['userRole'];
    $id = $data['userID'];

    if ($id) {
        session_start();
        $_SESSION['userRole'] = $role;
        $_SESSION['userID'] = $id;
        $responseData['success'] = true;
    } else {
        $responseData['error'] = 'User not found';
    }

    header('Content-Type: application/json');
    echo json_encode($responseData);
}
if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    exit();
}
