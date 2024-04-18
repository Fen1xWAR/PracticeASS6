<?php
require_once "pdoConnection.php";
global $dbh;
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
        <div class="overflow-auto" style="height: 450px">


  <?php
        $query = "SELECT user_id, login, password, role, name,surname FROM users JOIN roles ON roles.role_id = users.role_id
        JOIN education_system.human_data hd on hd.data_id = users.data_id";
        $result = $dbh->query($query);;
        if ($result->rowCount() > 0) {
        echo "<div class='d-flex flex-column'>";
            echo "<table class='table table-hover'>";
                echo "<tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>ФИО</th>
                </tr>";


                foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {

                echo "<tr class='user' data-id='" . $row['user_id'] . "' >";
                echo "<td>" . $row['user_id'] . "</td>";
                echo "<td>" . $row['login'] . "</td>";
                echo "<td>" . $row['role'] . "</td>";
                $fullName = $row['surname'] . " " . mb_substr($row['name'], 0, 1) . '.';
                echo "<td>" . $fullName . "</td>";

                echo "</tr>";
                }

                echo "</table>";
            echo "</div>";
        } else {
        echo "<h1>Записи не найдены!</h1>";
        }
  ?>
        </div>

    </div>
    <div class="tab-pane fade" id="logs-tab-pane" role="tabpanel" aria-labelledby="logs-tab" tabindex="0">...</div>

</div>


