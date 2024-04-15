<?php
require_once "pdoConnection.php";
function GetPageByRole($role)
{
    switch ($role){
        case 'student':
            return "studentProfile.php";
        case 'teacher':
            return "teacherProfile.php";
        case 'admin':
            return "adminProfile.php";
        default :
            return "";
    }
}
function CheckLoginData($login, $password){
    global $dbh;
    try{
        $query = $dbh->prepare("SELECT users.password, roles.role FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.login = :login");
        $query->bindValue(":login",$login);
        $query->execute();
        $result = $query->fetch();
        if($result> 0 and password_verify($password,$result['password'])){
                echo GetPageByRole($result['role']);


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

