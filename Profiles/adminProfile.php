<?php
require_once "Services/pdoConnection.php";

?>


<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-tab-pane" type="button" role="tab" aria-controls="users-tab-pane" aria-selected="true">Пользователи</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="log-tab" data-bs-toggle="tab" data-bs-target="#logs-tab-pane" type="button" role="tab" aria-controls="logs-tab-pane" aria-selected="false">Логи входов</button>
    </li>

</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active " id="users-tab-pane" role="tabpanel" aria-labelledby="users-tab" tabindex="0">
        <div class="overflow-auto">

            <?php
            require_once "Services/renderService.php";
            renderUsersTable();

            ?>

        </div>

    </div>


    <div class="tab-pane fade" id="logs-tab-pane" role="tabpanel" aria-labelledby="logs-tab" tabindex="0">
        <?php

        renderLogsTable();

        ?>
    </div>

</div>



