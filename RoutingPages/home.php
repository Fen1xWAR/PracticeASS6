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
    <link rel="stylesheet" href="../components.css">
    <link rel="stylesheet" href="../style.css">
    <title>Document</title>
</head>

<body  class="d-flex flex-column min-vh-100 bg-light">
<?php
require_once "Components/header.php";
?>
<div class="container-fluid d-flex flex-column   flex-grow-1 p-0 " >
    <div id="themeCarousel" class="carousel slide "   data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#themeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#themeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#themeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner" >
            <div class="carousel-item active c-item">
                <img src="../CarouselImages/01.jpg" class="d-block w-100 c-image" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Second slide label</h5>
                    <p>Some representative placeholder content for the first slide.</p>
                </div>
            </div>
            <div class="carousel-item c-item">
                <img src="../CarouselImages/02.jpg" class="d-block w-100 c-image" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Second slide label</h5>
                    <p>Some representative placeholder content for the second slide.</p>
                </div>
            </div>
            <div class="carousel-item c-item">
                <img src="../CarouselImages/03.jpg" class="d-block w-100 c-image" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Third slide label</h5>
                    <p>Some representative placeholder content for the second slide.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#themeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#themeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container-fluid d-flex flex-grow-1 flex-column p-4 ">
        <div id="regContainer" class="row  border border-info h-100 p-4 rounded-4" style="flex: 1">

            <div class="col-md-8 d-flex justify-content-center align-items-center">
                <h1 class="text-white text-center">Еще не с нами? Присоединяйся сейчас!</h1>
            </div>
            <div class="text-white p-5 col-md-4 rounded-5 bg-primary">
                <p class="fs-4 text-break text-center">Регистрация</p>
                <form id="formReg" class=" d-flex flex-column" action="">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">ФИО</label>
                        <input type="text" class="form-control form-control-sm " id="fullName" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-control-sm " id="email" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control form-control-sm" id="password">
                    </div>
                    <div class="mb-3">
                        <label for="passwordRepeat" class="form-label">Повторите пароль</label>
                        <input type="password" class="form-control form-control-sm" id="passwordRepeat">
                    </div>

                    <button type="submit" class="btn btn-light w-50 align-self-center">Зарегистрироваться</button>
                </form>
            </div>
        </div>

<!--        <button class="btn btn-primary" onclick="location.href='login.php'">Войти</button>-->
    </div>
</div>

<?php
require_once "Components/footer.php";
?>
</body>
<script src="../script.js"></script>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</html>
<?php
