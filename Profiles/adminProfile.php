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
        <div class="overflow-auto" style="height: 450px">

            <?php
            renderUsersTable();
            function renderUsersTable()

            {
                global $dbh;
                $query = "SELECT user_id, email, role, name,surname , lastname FROM users JOIN roles ON roles.role_id = users.role_id
                                                               JOIN education_system.human_data hd on hd.data_id = users.data_id";
                $result = $dbh->query($query);
                $result->setFetchMode(PDO::FETCH_ASSOC);
                $table = "<table class='table  table-hover table-striped'>";
                $table .= "<thead>
                            <th class='text-center'>ID</th>
                            <th class='text-center'>Email</th>
                            <th class='text-center'>ФИО</th>
                            <th class='text-center'>Role</th>
                           </thead>";
                foreach ($result as $row) {
                    $fullName = $row['surname'] ." ". substr($row['name'],0,2) . "." . substr($row['lastname'],0,2).".";
                    $table .= "<tr>";
                    $table .= "<td class='text-center'>" . $row['user_id'] . "</td>";
                    $table .= "<td class='text-center'>" . $row['email'] . "</td>";
                    $table .= "<td class='text-center'>" . ($fullName) . "</td>";
                    $table .= "<td class='text-center'>" . $row['role'] . "</td>";
                    $table .= "</tr>";
                }
                $table .= "</table>";
                echo $table;
            }

            ?>

        </div>

    </div>


    <div class="tab-pane fade" id="logs-tab-pane" role="tabpanel" aria-labelledby="logs-tab" tabindex="0">...</div>

</div>


