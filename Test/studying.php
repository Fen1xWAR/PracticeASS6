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
    <link rel="stylesheet" href="../block.css">
    <title>Document</title>
</head>
<?php
require_once "../Components/header.php"
?>
<body class="d-flex flex-column min-vh-100">
<div class="container-fluid d-flex   flex-grow-1">
    <div class="container-xxl align-self-center  mt-5">
        <div class="row">
            <?php
            require_once "RenderService.php";
            $blocks = getAllBlocks();
            foreach ($blocks as $block){

                renderBlock($block['block_name'],$block['block_description'],$block['block_id'],$block['block_image_name']);
            }


            ?>
<!--                        <div class="card-wrapper d-flex align-items-center justify-content-center  col-lg-4 col-md-5 col-xs-12">-->
<!--                            <div class="card">-->
<!--                                <div class="card-img-wrapper">-->
<!--                                    <img class="card-img-top" src="computer.png"  alt="Card image cap">-->
<!--                                </div>-->
<!--                                <div class="card-body">-->
<!--                                    <h5 class="card-title">Комьютеры и по!</h5>-->
<!--                                    <div class="card-content">-->
<!--                                        <p class="card-text">LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem</p>-->
<!--                                        <a href="test.php?blockId=1" class="btn btn-primary">Пройти тему</a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="card-wrapper d-flex align-items-center justify-content-center  col-lg-4 col-md-5 col-xs-12">-->
<!--                            <div class="card">-->
<!--                                <div class="card-img-wrapper">-->
<!--                                    <img class="card-img-top" src="computer.png"  alt="Card image cap">-->
<!--                                </div>-->
<!--                                <div class="card-body">-->
<!--                                    <h5 class="card-title">Комьютеры и по!</h5>-->
<!--                                    <div class="card-content">-->
<!--                                        <p class="card-text">LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem</p>-->
<!--                                        <a href="test.php?blockId=1" class="btn btn-primary">Пройти тему</a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="card-wrapper d-flex align-items-center justify-content-center  col-lg-4 col-md-5 col-xs-12">-->
<!--                            <div class="card">-->
<!--                                <div class="card-img-wrapper">-->
<!--                                    <img class="card-img-top" src="computer.jpg"  alt="Card image cap">-->
<!--                                </div>-->
<!--                                <div class="card-body">-->
<!--                                    <h5 class="card-title">Комьютеры и по!</h5>-->
<!--                                    <div class="card-content">-->
<!--                                        <p class="card-text">LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem</p>-->
<!--                                        <a href="test.php?blockId=1" class="btn btn-primary">Пройти тему</a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->


        </div>

        <!--        <div class="card-wrapper col-lg-4 col-md-5 col-xs-12">-->
        <!--            <div class="card">-->
        <!--                <div class="card-img-wrapper">-->
        <!--                    <img class="card-img-top" src="https://www.fortech.ro/wp-content/uploads/2018/12/DigitalCommerceSoftwarePlatform-e1554472385425.jpg" alt="Card image cap">-->
        <!--                </div>-->
        <!--                <div class="card-body">-->
        <!--                    <h5 class="card-title">Special title treatment Special</h5>-->
        <!--                    <div class="card-content">-->
        <!--                        <p class="card-text">With supporting text below as a natural lead-in to additional content. With supporting text below as a natural</p>-->
        <!--                        <a href="#" class="btn btn-primary">Go somewhere</a>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->

        <!--        <div class="col-lg-4 col-md-5 col-xs-12 flex-grow-0">-->
        <!---->
        <!---->
        <!--            <a href="Test.php?blockId=1" class=" card-link">-->
        <!--                <div class="card ">-->
        <!--                    <img src="computer.png"   class="card-img-top" alt="Устройство компьютера">-->
        <!--                    <div class="card-body">-->
        <!--                        <h5 class="card-title">Устройство компьютера и ПО</h5>-->
        <!--                        <p class="card-text">With supporting text below as a natural lead-in to additional content. With supporting text below as a natural</p>-->
        <!---->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </a>-->
    </div>

</div>


</div>


<?php
require_once "../Components/footer.php"
?>
</body>

</html>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<?php
