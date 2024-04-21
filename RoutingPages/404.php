<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ошибка 404 - Страница не найдена</title>
    <link rel=”stylesheet” href=”https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css”/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="../style404.css" rel="stylesheet">
</head>
<header>
    <?php
    require_once "Components/header.php"
    ?>
</header>
<body class=" d-flex flex-column min-vh-100">
<div class="container-fluid d-flex  flex-grow-1">
    <div class="container-xxl pt-1 p-0 p-sm-5">
        <section class="error-404-section section-padding">
            <div class="error container">
                <img src="../assets/StockImg/error404.png" style="max-height: 400px; max-width: 600px" alt>
            </div>
            <div class="error-message">
                <h3>Ой! Страница не найдена!</h3>
                <a href="javascript:history.go(-1)" class="btn btn-primary">Вернуться назад</a>
            </div>
        </section>
    </div>

</div>



<?php
require_once "Components/footer.php"
?>

</body>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</html>