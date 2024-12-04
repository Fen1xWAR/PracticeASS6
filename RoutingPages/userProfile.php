<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel=”stylesheet” href=”https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css”/>
    <link rel="stylesheet" href="../style.css">
    <script src="../jquery-3.7.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../bootstrap-print.css">
    <title>Личный кабинет   </title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php
require_once "Components/header.php";
?>
<div class="container-fluid d-flex  flex-grow-1">
    <div class="container-xxl d-flex  flex-column p-5">
        <div class="container-md flex-grow-1 p-3 rounded-5  bg-light" id="content">
            <?php
            if (!isset($_SESSION['userRole'])) {
                header("Location: /login");
            }
            switch ($_SESSION['userRole']) {
                case 'student':
                    require "Profiles/studentProfile.php";
                    break;
                case 'teacher':
                    require "Profiles/teacherProfile.php";
                    break;
                case 'admin':
                    require "Profiles/adminProfile.php";
                    break;
                default :
                    exit();
            }
            ?>
        </div>
        <div class="container mt-2 d-flex justify-content-center align-items-center" id="btns">
            <button class="btn btn-primary mw-25" id="logout">Выйти</button>
        </div>


    </div>
</div>
<?php
require_once "Components/footer.php";
?>

</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    $("#logout").click(function () {
        $.ajax({
            type: "POST",
            url: "Services/usersService.php",
            data: {"logout": true},
            success: function (data) {
                location.href = '/'

            },
            error: function (data) {

            },
        });
    })
</script>
</html>
<?php
