<?php
require_once "pdoConnection.php";
function CheckLoginData($email, $password): array|string
{
    global $dbh;
    try {
        $query = $dbh->prepare("SELECT user_id, password, roles.role FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.email = :email");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $query->bindValue(":email", $email);
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

    $logData = $_POST['login'];
    $email = $logData['email'];
    $password = $logData['password'];


    $data = CheckLoginData($email, $password);

    if (is_string($data)) {
        http_response_code(404);
        echo json_encode(["message" => "Неверный логин или пароль!"], JSON_UNESCAPED_UNICODE);
    } else {
        $role = $data['userRole'];
        $id = $data['userID'];

        session_start();
        $_SESSION['userRole'] = $role;
        $_SESSION['userID'] = $id;

        http_response_code(200);

    }


}





if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    exit();
}
