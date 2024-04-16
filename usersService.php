<?php
require_once "pdoConnection.php";

function CheckLoginData($login, $password){
    global $dbh;
    try{
        $query = $dbh->prepare("SELECT users.password, roles.role FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.login = :login");
        $query->bindValue(":login",$login);
        $query->execute();
        $result = $query->fetch();
        if($result> 0 and password_verify($password,$result['password'])){
            session_start();
            $role = $result['role'];
            $_SESSION["role"] = $role;
            header("Location : userProfile.php");
            echo $_SESSION["role"];
        }
        else{
            echo "Логин или пароль неверны";
        }
    }
    catch (Exception $e)  {
        echo $e->getMessage();
    }
}
if (isset($_POST['login'])){

    $logData = $_POST['login'];
    $login = $logData['login'];
    $password = $logData['password'];
    CheckLoginData($login,$password);

}

