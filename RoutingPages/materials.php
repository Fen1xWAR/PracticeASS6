<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel=”stylesheet” href=”https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css”/>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB5IXNxFwWQfE7u8Lj+XJHAxKlXiG/8rsrtpb6PEdzD828Ii" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../block.css">
    <title>Методические материалы</title>
</head>

<body  class="d-flex flex-column min-vh-100 bg-light">
<?php
require_once "Components/header.php";
?>
<div class="container-fluid d-flex flex-column   flex-grow-1 p-0 " >
    <div class="container mt-5">
        <h1 class="text-center mb-4">Методические материалы</h1>
        <div class="list-group">
            <a href="/material/1" class="list-group-item list-group-item-action">
                <h5 class="mb-1">Материал 1</h5>
                <p class="mb-1">Краткое описание материала 1. Здесь можно указать основные моменты и ключевые идеи.</p>
                <small>Дата: 2023-10-01</small>
            </a>
            <a href="/material/2" class="list-group-item list-group-item-action">
                <h5 class="mb-1">Материал 2</h5>
                <p class="mb-1">Краткое описание материала 2. Это может быть резюме или основные выводы.</p>
                <small>Дата: 2023-10-02</small>
            </a>
            <a href="/material/3" class="list-group-item list-group-item-action">
                <h5 class="mb-1">Материал 3</h5>
                <p class="mb-1">Краткое описание материала 3. Укажите, что читатель может ожидать.</p>
                <small>Дата: 2023-10-03</small>
            </a>
        </div>
    </div>
</div>

<?php
require_once "Components/footer.php";
?>
</body>
</html>