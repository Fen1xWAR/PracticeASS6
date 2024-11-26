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

function saveLog($userId): void
{
    global $dbh;
    $query = $dbh->prepare("INSERT INTO userLogs (user_id, logIn_dateTime) VALUES(:user_id, :log_time)");
    $query->bindValue(":user_id", $userId);
    $query->bindValue(":log_time", date("Y-m-d H:i:s"));
    $query->execute();
}

function checkRegData($email): string
{
    global $dbh;
    try {
        $query = $dbh->prepare("SELECT user_id FROM users WHERE users.email = :email");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $query->bindValue(":email", $email);
    $query->execute();
    $result = $query->fetch();
    if ($result > 0) {
        return "Пользователь с таким Email уже существует!";

    }
    return "";
}

if (isset($_POST['register'])) {

    $registerData = $_POST['register'];

    $email = $registerData['email'];


    $data = checkRegData($email);

    if ( $data != '') {
        http_response_code(404);
        echo json_encode(["message" => $data], JSON_UNESCAPED_UNICODE);
    } else {
        $password = $registerData['password'];
        register($registerData);
        login($email, $password);
        http_response_code(200);

    }


}

if (isset($_POST['editUser'])) {
    global $dbh;
    $editedData = $_POST['editUser'];
    $query = $dbh->prepare("UPDATE users SET role_id = :role_id, email = :email WHERE user_id = :user_id");
    $query->bindValue(":role_id", $editedData['userRole']);
    $query->bindValue(":email", $editedData['userEmail']);
    $query->bindValue(":user_id", $editedData['userId']);
    $query->execute();
    if ($editedData['userPassword'] != '') {
        $query = $dbh->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
        $query->bindValue(":password", password_hash($editedData['userPassword'], PASSWORD_DEFAULT));
        $query->bindValue(":user_id", $editedData['userId']);
        $query->execute();

    }
    $query = $dbh->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $query->bindValue(":user_id", $editedData['userId']);
    $query->execute();
    $result = $query->fetch();
    $query = $dbh->prepare("UPDATE human_data SET name = :name, surname = :surname, lastname = :lastname WHERE data_id = :data_id");
    $query->bindValue(":data_id", $result['data_id']);
    $query->bindValue(":name", $editedData['userName']);
    $query->bindValue(":surname", $editedData['userSurname']);
    $query->bindValue(":lastname", $editedData['userLastname']);
    $query->execute();
    http_response_code(200);

}
if (isset($_POST['removeUser'])) {
    global $dbh;
    $query = $dbh->prepare("SELECT data_id FROM users WHERE user_id = :user_id");
    $query->bindValue(":user_id", $_POST['removeUser']);
    $query->execute();
    $result = $query->fetch();
    $dataId = $result['data_id'];
    $query = $dbh->prepare("DELETE FROM users WHERE user_id = :user_id");
    $query->bindValue(":user_id", $_POST['removeUser']);
    $query->execute();
    $query = $dbh->prepare("DELETE FROM human_data WHERE data_id = :data_id");
    $query->bindValue(":data_id", $dataId);
    $query->execute();
}
if (isset($_POST['login'])) {


    $logData = $_POST['login'];
    $email = $logData['email'];
    $password = $logData['password'];
    login($email, $password);
}


if (isset($_POST['dataToRegisterRedirect'])) {


    $dataToRegisterRedirect = $_POST['dataToRegisterRedirect'];
    setcookie("dataToRegisterRedirect", json_encode($dataToRegisterRedirect), time()+  20, "/");
    http_response_code(200);
}

if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    exit();
}

function register($dataToRegister): void
{
    global $dbh;
    $query = $dbh->prepare("INSERT INTO human_data (name, surname, lastname) values (:name, :surname, :lastname)");
    $query->bindValue(":name", $dataToRegister['name']);
    $query->bindValue(":surname", $dataToRegister['surname']);
    $query->bindValue(":lastname", $dataToRegister['lastname']);
    $query->execute();
    $dataIdQ = $dbh->query('SELECT data_id FROM human_data ORDER BY data_id DESC LIMIT 1');
    $dataId = $dataIdQ->fetch()['data_id'];

    $query = $dbh->prepare("INSERT INTO users  (email, password, role_id, data_id) Values ( :email, :password, :roleId,:dataId)");
    $query->bindValue(":dataId", $dataId);
    $query->bindValue(":email", $dataToRegister['email']);
    $query->bindValue(":password", password_hash($dataToRegister['password'], PASSWORD_DEFAULT));
    $query->bindValue(":roleId", $dataToRegister['role']);
    $query->execute();
    $userIdQ = $dbh->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1 ");
    $userId = $userIdQ->fetch()['user_id'];
    switch ($dataToRegister['role']) {
        case '2':
            $dbh->query('INSERT into teachers (user_id) values (' . $userId . ')');
            break;
        default:
            $dbh->query('INSERT into students (user_id,group_id) values (' . $userId . ', ' . $dataToRegister['group_id'] . ')');
            break;

    }
}

function login($email, $password): void
{


    $data = CheckLoginData($email, $password);

    if (is_string($data)) {
        http_response_code(400);
        echo json_encode(["message" => "Неверная почта или пароль!"], JSON_UNESCAPED_UNICODE);
    } else {
        $role = $data['userRole'];
        $id = $data['userID'];

        session_start();
        saveLog($id);
        $_SESSION['userRole'] = $role;
        $_SESSION['userID'] = $id;

        http_response_code(200);

    }

}


