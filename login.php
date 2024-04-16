<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex flex-column min-vh-100">
<header>
    <?php
    require 'Components/header.php';
    ?>
</header>
<div class="container-fluid d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="container p-5">
        <form id="loginForm" action="usersService.php" method="post" class="text-center">
            <div class="form-group  row">
                <label for="login" class="col-form-label text-start col-sm-2">Логин:</label>
                <div class="col-sm-10">
                    <input name="login" type="text" id="login" class="form-control">
                </div>
            </div>
            <div class="form-group mt-2  row">
                <label for="password" class="col-form-label text-start col-sm-2">Пароль:</label>
                <div class="col-sm-10">
                    <input name="password" type="password" id="password" class="form-control">
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Войти</button>
            </div>
        </form>
    </div>
</div>
<?php
require_once "Components/footer.php"
?>
</body>
<script src="jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script src="script.js"></script>
<script>
    const form = $("#loginForm")
    form.submit(function (e) {
        e.preventDefault()
        let formData = form.serializeArray()
        let data = {
            login: formData[0].value,
            password: formData[1].value,
        }
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: {"login": data},
            success: function (data) {
                location.href = "userProfile.php"

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