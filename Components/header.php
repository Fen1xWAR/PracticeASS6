<?php
//echo "
//<header class='bg-light'>
//    <nav class='navbar  bg-primary  navbar-expand-lg navbar-dark bg-primary'>
//        <a class='navbar-brand' href='../index.php'>Главная</a>
//        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarNav'
//                aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
//            <span class='navbar-toggler-icon'></span>
//        </button>

//    </nav>
//</header>
//";
?>
<nav class="fs-5 navbar bg-primary navbar-expand-lg navbar-dark ">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Обучающая система</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <?php
//                session_start();
//                if (!isset($_SESSION["role"])) {
//                    echo ' <li class="nav-item">
//                    <a class="nav-link" href="../login.php">Войти</a>
//                </li>';
//                } else {
//                    echo ' <li class="nav-item">
//                    <a class="nav-link" href="../userProfile.php">Личный кабинет</a>
//                </li>';
//                }
                ?>

                <li class="nav-item">
                    <a class="nav-link" href="../Test/studying.php">Тесты</a>
                </li>
            </ul>
            <hr class="d-lg-none text-white">
                <?php
                session_start();
                if (!isset($_SESSION['userID'])) {
                    echo '
                    <button class="btn btn-light" onclick="location.href=\'../login.php\'">Войти</button>';
                } else {
                    echo '
                    <button class="btn btn-light" onclick="location.href=\'../userProfile.php\'">Личный кабинет</button>';
                }
                ?>


        </div>
    </div>
</nav>


