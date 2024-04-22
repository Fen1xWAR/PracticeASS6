<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Вход</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class=" d-flex flex-column min-vh-100">
<header>
    <?php
    require 'Components/header.php';
    ?>
</header>
<div id="toastRoot" class="container-fluid d-flex flex-grow-1">
    <div  class="container-xl d-flex justify-content-center flex-column p-5">

        <div class="row justify-content-center ">

            <div class="col-md-6">

                <form id="loginForm" class="d-flex flex-column  fs-5" action="../Services/usersService.php" method="post">
                    <div class="mb-3 ">
                        <label for="InputEmail"  class="form-label">Email</label>
                        <input name="email" type="text" required class="form-control" id="InputEmail" >

                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword" class="form-label">Пароль</label>
                        <input name="password" required type="password" class="form-control"  id="exampleInputPassword">


                    </div>

                    <button type="submit" class="btn text-white btn-primary">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="toast-container position-fixed  end-0" style="bottom: 3.5rem">

</div>
<?php
require_once "Components/footer.php"
?>
</body>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script src="../script.js"></script>
<script>
    const form = $("#loginForm")

    form.submit(function (e) {
        e.preventDefault()

        let formData = form.serializeArray()
        let data = {
            email: formData[0].value,
            password: formData[1].value,
        }
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: {"login": data},
            success: function () {
                    form.addClass("was-validated")
                setTimeout(()=>{
                    location.href = "/profile"
                },800)



            },
            error: ajaxErrorHandling


        });


    })

</script>
</html>