
<?php
require_once "pdoConnection.php";
global $dbh;
$query = "SELECT user_id, login, password, role, name,surname FROM users JOIN roles ON roles.role_id = users.role_id
JOIN education_system.human_data hd on hd.data_id = users.data_id";
$result = $dbh->query($query);;
if ($result->rowCount() > 0) {
    echo "<div class='d-flex flex-column'>";
    echo "<h1 class='text-center'>Пользователи</h1>";
    echo "<table class='table table-hover m-4'>";
    echo "<tr>
        <th>ID</th>
        <th>Логин</th>
        <th>Роль</th>
        <th>ФИО</th>
        </tr>";


    foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {

        echo "<tr class='user' data-id='".$row['user_id']."' >";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['login'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        $fullName = $row['surname'] . " " .mb_substr($row['name'],0,1) . '.';
        echo "<td>" . $fullName . "</td>";

        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
} else {
    echo "<h1>Записи не найдены!</h1>";
}




?>