<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel=”stylesheet” href=”https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css”/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="components.css">
    <title>Document</title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php
require_once "Components/header.php";
?>
<div class="container-fluid d-flex  flex-grow-1">
    <div class="container-xxl d-flex  flex-column p-5">
        <div class="container flex-grow-1" id="content">
            <?php
            if (!isset($_SESSION['role'])){
                header("Location: login.php");
            }
            switch ($_SESSION['role']) {
                case 'student':
                    require_once "Profiles/studentProfile.php";
                    break;
                case 'teacher':
                    require_once "Profiles/teacherProfile.php";
                    break;
                case 'admin':
                    require_once "Profiles/adminProfile.php";
                    break;
                default :
                    exit();
            }
            ?>
        </div>
        <div class="container d-flex justify-content-center align-items-center" id="btns">
            <button class="btn btn-primary w-25" id="logout">Выйти</button>
        </div>


    </div>
</div>
<?php
require_once "Components/footer.php";
?>

</body>

<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    $("#logout").click(function () {
        $.ajax({
            type: "POST",
            url: "usersService.php",
            data: {"logout": true},
            success: function (data) {
                console.log(data)
                location.href='index.php'

            },
            error: function (data) {
                // console.log(data)
                console.log('An error occurred.');
                //Сделать всплывашку с ошибкой
            },
        });
    })
</script>
</html>
<?php
