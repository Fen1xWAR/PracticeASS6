<nav class="fs-5 navbar bg-primary navbar-expand-lg navbar-dark ">
    <div class="container-fluid">
        <a class="navbar-brand " href="/">
            <img src="/assets/StockImg/Logo.png"  alt="Logo" width="60px" height="50px" class="d-inline-block rounded-3">
            Обучающая система
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse px-sm-4 navbar-collapse justify-content-between" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/education">Обучение</a>
                </li>
                <?php
                session_start();
                if (isset($_SESSION['userRole']) and ($_SESSION['userRole'] == 'teacher')) {
                    echo ' <li class="nav-item">
                    <a class="nav-link" href="/materials">Методические материалы</a>
                </li>';
                }
                ?>

            </ul>
            <hr class="d-lg-none text-white">
            <?php

            if (!isset($_SESSION['userID'])) {
                echo '
                    <button class="btn btn-light" onclick="location.href=\'../login\'">Войти</button>';
            } else {
                echo '
                    <button class="btn btn-light" onclick="location.href=\'../profile\'">Личный кабинет</button>';
            }
            ?>


        </div>
    </div>
</nav>


