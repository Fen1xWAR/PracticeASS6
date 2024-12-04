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
    <div class="container-xl d-flex justify-content-center flex-column p-5">

        <div class="row justify-content-center ">

            <div class="col-md-6">
                <?php
                $email = '';
                $name = '';
                $surname = '';
                $lastname = '';
                $nameDisabled = '';

                if (isset($_COOKIE['dataToRegisterRedirect'])) {
                    $data = json_decode($_COOKIE['dataToRegisterRedirect']);
                    $email = $data->email;
                    $name = $data->name;
                    $surname = $data->surname;
                    $lastname = $data->lastname;
                    $nameDisabled = 'disabled';
                }
                ?>
                <h1 class=" mb-3 text-center text-primary">Регистрация:</h1>
                <form id="loginForm" class="d-flex flex-column  fs-5" action="../Services/usersService.php"
                      method="post">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Фамилия</label>
                        <?php
                        echo "<input type='text' required value='{$surname}' {$nameDisabled}  class='form-control form-control-sm' id='surname' name='surname' >";
                        ?>

                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Имя</label>
                        <?php
                        echo "<input required type='text' value='{$name}' {$nameDisabled}  class='form-control form-control-sm' id='name' name='name' >";
                        ?>

                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Отчество</label>
                        <?php
                        echo "<input type='text' value='{$lastname}' {$nameDisabled}  class='form-control form-control-sm' id='lastname' name='lastname' >";
                        ?>

                    </div>
                    <div class="mb-3 ">

                        <label for="email" class="form-label">Email</label>
                        <input name="email" value="<?php echo $email ?>" type="text" required class="form-control"
                               id="email"
                               aria-describedby="emailHelp">

                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input name="password" required type="password" class="form-control" id="password">


                    </div>
                    <div class="mb-3">
                        <label for="passwordRepeat" class="form-label">Повторите пароль</label>
                        <input type="password" required class="form-control form-control-sm"
                               aria-describedby='passwordHelp' id="passwordRepeat">
                        <div id="passwordHelp" class="form-text">Никому не сообщайте ваш пароль.</div>
                    </div>

                    <div class="mb-3">
                        <label for="groupSelect" class="form-label">Группа:</label>
                        <select id="groupSelect" required class="form-select">
                            <option value="" >Выберите класс:</option>
                            <?php
                            require "Services/pdoConnection.php";
                            global $dbh;

                            $query = $dbh->prepare("SELECT group_id, group_name FROM education_system.groups");
                            $query->execute();
                            $result = $query->fetchAll();

                            foreach ($result as $group) {

                                echo "<option VALUE='" . $group['group_id'] . "' >" . $group['group_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Создать аккаунт</button>
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
<script src="../script.min.js"></script>
<script>
    const form = $("#loginForm")
    const select = $("#groupSelect")
    form.submit(function (e) {
        e.preventDefault()
        const password = $("#password").val()
        if(password !== $('#passwordRepeat').val()){
            Notification('Пароли не совпадают!').show()
            return
        }
        let data = {
            surname: $('#surname').val(),
            name : $('#name').val(),
            lastname : $('#lastname').val(),
            email : $('#email').val(),
            password: password,
            role: '1',
            group_id: select.val()

        }
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: {"register": data},
            success: function () {
                form.addClass("was-validated")
                setTimeout(() => {
                    location.href = "/profile"
                }, 800)


            },
            error: ajaxErrorHandling


        });


    })

</script>
</html>