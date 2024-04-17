<?php
require_once "pdoConnection.php";

function CheckLoginData($login, $password): string
{
    global $dbh;
    try {
        $query = $dbh->prepare("SELECT users.password, roles.role FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.login = :login");
        $query->bindValue(":login", $login);
        $query->execute();
        $result = $query->fetch();
        if ($result > 0 and password_verify($password, $result['password'])) {
            return $result['role'];
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    return '';
};

    if (isset($_POST['login'])) {

        $logData = $_POST['login'];
        $login = $logData['login'];
        $password = $logData['password'];
        $role = CheckLoginData($login, $password);
        if ($role) {
            session_start();
            $_SESSION["role"] = $role;
            echo $_SESSION["role"];
            header("Location : userProfile.php");
            exit();
        }
    }
    if(isset($_POST['logout'])){
        session_start();
        session_unset();
        session_destroy();
        exit();
    }
