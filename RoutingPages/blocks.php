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
    <title>Обучение</title>
</head>
<?php
require_once "Components/header.php"
?>
<body class="d-flex flex-column min-vh-100">
<div class="container-fluid d-flex   flex-grow-1">
    <div class="container-xxl align-self-center  mt-5">
        <div class="row">
            <?php
            require_once "Services/renderService.php";
            $blocks = getAllBlocks();
            foreach ($blocks as $block) {

                renderBlock($block['block_name'], $block['block_description'], $block['block_id']);
            }


            ?>


        </div>


    </div>

</div>


<?php
require_once "Components/footer.php"
?>
</body>

</html>
<script src="jquery-3.7.1.js"></script>


<script>

</script>
<?php
