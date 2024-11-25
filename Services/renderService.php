<?php
require "pdoConnection.php";
if (isset($_GET['componentId'])) {
    $componentId = $_GET['componentId'];
    session_start();
    $_SESSION['currentComponentId'] = $componentId;
    try {
        $data = getComponentJson($componentId);

        if ($data) {
            try {
                $component = json_decode($data['data'], true);
                $_SESSION['currentComponentData'] = $component;
                if (isset($component['Images'])) {

                    downloadImagesByComponentIdToDirectory($componentId);
                }
                $data['type'] == "Lecture" ? renderLecture($componentId, $component) : renderTest($component);
            } catch (Exception $e) {
                http_response_code(400);
                echo $e->getMessage();
            }

        } else {
            http_response_code(404);
        }
    } catch (Exception $exception) {
        http_response_code(500);
        echo $exception->getMessage();
    }


}

if (isset($_POST['userAnswers'])) {
    $userAnswers = $_POST['userAnswers'];
    echo json_encode(["html" => proceedUserAnswers($userAnswers)]);
}

if (isset($_GET['groupId'])) {
    session_start();
    $groupArr = $_GET['groupId'];
    $groupId = $groupArr['groupId'];
    $blockId = $groupArr['blockId'];
    $_SESSION['selectedGroupId'] = $groupId;
    $studentResult = getStudentListByGroupId($groupId, $blockId);


    echo json_encode(["html" => createStudentList($studentResult)]);



}

if (isset($_GET['sectionId'])) {
    session_start();
    $result = [];
    $blockId = $_GET['sectionId'];
    $userId = $_SESSION['userID'];
    $_SESSION['selectedBlockId'] = $blockId;
    $dataToRender = getResultByBlockAndUserId($blockId, $userId);
    if (count($dataToRender) == 0) {
        echo json_encode(["html" => "<h3 class='text-center'>Вы еще не проходили ни одного теста из данного раздела!</h3>"]);
    } else {
        $accordion = [];
        $counter = 0;
        foreach ($dataToRender as $item) {
            $counter++;
            $result[] =  createUserResultAccordion($counter, $item);
        }
        echo json_encode(["html" =>$result]);
    }

}


if (isset($_GET['blockId'])) {
    session_start();
    $_SESSION['blockId'] = $_GET['blockId'];
    header("Location: /section");;
}
function getResultByBlockAndUserId($blockId, $userId): array|string
{
    global $dbh;
    $query = $dbh->prepare("SELECT component_id, data from components WHERE block_id=:blockId and type='Test'");
    $query->bindValue(":blockId", $blockId);
    $query->execute();
    $components = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($components) == 0) {
        return [];
    }
    $query = $dbh->prepare("SELECT id from students where user_id=:userId");
    $query->bindValue(":userId", $userId);
    $query->execute();
    $studentId = $query->fetch(PDO::FETCH_ASSOC)['id'];
    $dataToRender = [];
    foreach ($components as $test) {
        $testId = $test['component_id'];
        $testTitle = json_decode($test['data'], true)['Title'];
        $query = $dbh->prepare('SELECT result, answers from complete_components_by_student JOIN education_system.test_answers ta on complete_components_by_student.component_id = ta.component_id    where complete_components_by_student.component_id=:testId AND student_id=:studentId');
        $query->bindValue(":testId", $testId);
        $query->bindValue(":studentId", $studentId);
        $query->execute();
        if ($query->rowCount() > 0) {
            $result = $query->fetch(PDO::FETCH_ASSOC);

            $outputData = ["testId" => $testId, "testTitle" => $testTitle, "testResult" => $result['result'], "answers" => $result['answers']];
            $dataToRender[] = $outputData;
        }

    }
    return $dataToRender;
}


function createUserResultAccordion($counter, $dataToRender): string
{
    global $dbh;
    $query = $dbh->prepare("SELECT isFinalTest FROM  components  where component_id = :componentId");
    $query->bindParam(':componentId', $_SESSION['currentComponentId']);
    $query->execute();
    $result = $query->fetch();
    $isFinalTest = $result['isFinalTest'];
    $testId = $dataToRender['testId'];
    $testTitle = $dataToRender['testTitle'];
    $result = json_decode($dataToRender['testResult'], true);
    $userAnswers = $result['UserAnswers'];
    $userResult = $result['UserResult'];
    $maxResult = $result['MaxResult'];
    $answers = json_decode($dataToRender['answers']);




    $content = generateTestResultTable(["№", "Ваш ответ"], false,  $isFinalTest, $userAnswers, $answers)['table'];
    $content .= '<p class="text-center">Количество правильных ответов: ' . $userResult . ' из ' . $maxResult . '</p>';
    $show = $counter === 1 ? ' show' : '';
    $collapsed = $counter != 1 ? "collapsed" : '';
    $accordionItem = <<<EOF
<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button {$collapsed}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{$testId}"
                aria-expanded="true" aria-controls="collapse{$testId}">
            {$testTitle}
        </button>
    </h2>
    <div id="collapse{$testId}" class="accordion-collapse collapse {$show}" data-bs-parent="#result">
        <div class="accordion-body overflow-auto">
            {$content}
        </div>
    </div>
</div>
EOF;

    return $accordionItem;

}

function createStudentList($data): string
{

    $html = '<table class="table table-stripped table-hover">';
    $html .= '<tr>';
    $headers = $data[0];
    foreach ($headers as $header) {
        $html .= '<th class="table-primary text-center">' . $header . '</th>';
    }
    $html.= "</tr>";

    $columnCount = count($data);
    $rowCount = count($data[1]);
    for ($i = 0; $i < $rowCount; $i++) {


        // Add the row number cell
        $html .= '<td class="text-center">' . ($i + 1) . '</td>';

        for ($j = 1; $j < $columnCount; $j++) {
            $html .= '<td class="text-center">' . $data[$j][$i] . '</td>';
        }


        $html.= '</tr>';
    }



    return $html;
}

function generateTestResultTable(array $headers, bool $isTeacher, bool $isFinalTest, ...$columns): array
{
    $correctAnswersCount = 0;
    $columnCount = $isTeacher ? count($columns) : count($columns) - 1;
    $rowCount = count($columns[0]);

    $table = '<table class="table table-bordered">';
    $table .= '<thead>';
    $table .= '<tr>';

    for ($i = 0; $i <= $columnCount; $i++) {
        $table .= '<th class="table-primary text-center">' . $headers[$i] . '</th>';
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody>';


    // Generate the table rows
    for ($i = 0; $i < $rowCount; $i++) {
        if (!$isFinalTest or $isTeacher){
            if ($columns[0][$i] == $columns[1][$i]) {
                $correctAnswersCount++;
                $colorClass = "success";
            } else {
                $colorClass = "danger";
            }
        }
        else{
            $colorClass = "light";
        }
        $table .= "<tr  class='table-" . $colorClass . "'>";

        // Add the row number cell
        $table .= '<td class="text-center">' . ($i + 1) . '</td>';

        for ($j = 0; $j < $columnCount; $j++) {
            $table .= '<td class="text-center">' . $columns[$j][$i] . '</td>';
        }


        $table .= '</tr>';
    }

    $table .= '</tbody></table>';
    return [
        "table" => $table,
        "correctAnswersCount" => $correctAnswersCount,
    ];
}


function proceedUserAnswers($userAnswers): string
{
    global $dbh;
    $query = $dbh->prepare("SELECT answers FROM  test_answers  where component_id = :componentId");
    session_start();
    $query->bindParam(':componentId', $_SESSION['currentComponentId']);
    $query->execute();
    $result = $query->fetch();
    $json = $result['answers'];
    $answers = json_decode($json, true);
    $numQuestions = count($userAnswers);;

    $isTeacher = $_SESSION['userRole'] == 'teacher';

    $query = $dbh->prepare("SELECT isFinalTest FROM  components  where component_id = :componentId");
    $query->bindParam(':componentId', $_SESSION['currentComponentId']);
    $query->execute();
    $result = $query->fetch();
    $isFinalTest = $result['isFinalTest'];

    $tableResultArray = generateTestResultTable(["№", "Ваш ответ", "Правильный ответ"], $isTeacher ,$isFinalTest, $userAnswers, $answers);

    $table = $tableResultArray['table'];
    $numCorrect = $tableResultArray['correctAnswersCount'];
    $table .= '<p class="text-center">Количество правильных ответов: ' . $numCorrect . ' из ' . $numQuestions . '</p>';

    if ($_SESSION['userRole'] === "student") {

        if (checkExistingResult()) {

            saveUserAnswersToDb([
                "UserAnswers" => $userAnswers,
                "UserResult" => $numCorrect,
                "MaxResult" => $numQuestions
            ]);

            return $table;

        }
        $table .= '<h4 class="text-center">Результат не был записан: вы уже проходили это тестирование</h4>';

    }


    return $table;

}

function checkExistingResult(): bool
{
    global $dbh;
    $query = $dbh->prepare("SELECT id from complete_components_by_student where student_id = (SELECT students.id FROM students WHERE user_id = :user_id) AND component_id= :component_id");
    $query->bindValue(":component_id", $_SESSION['currentComponentId']);
    $query->bindValue(":user_id", $_SESSION['userID']);
    $query->execute();
    if ($query->rowCount() == 0) {
        return true;
    }
    return false;
}

function saveUserAnswersToDb($userAnswers): void
{
    global $dbh;
    $query = $dbh->prepare("INSERT INTO complete_components_by_student (component_id, student_id, result, result_date )
VALUES (:component_id,
(SELECT students.id FROM students WHERE user_id = :user_id),
:result, :result_date);");
    $query->bindValue(":component_id", $_SESSION['currentComponentId']);
    $query->bindValue(":user_id", $_SESSION['userID']);
    $query->bindValue(":result", json_encode($userAnswers));
    $query->bindValue(":result_date", date("Y-m-d H:i:s"));
    $query->execute();
}

function renderLecture(int $component_id, array $data): void
{
    $textElement = "";

    $images = $data['Images'] ?? [];
    $text = $data['Text'];

    $k = 0;
    foreach ($images as $image) {
        $k += 1;
        $imagePosition = $image['mark'];
        $textBefore = substr($text, 0, $imagePosition);
        $imgElement = "<img style='max-width: 50%;' src='../assets/comp_{$component_id}_$k.png' alt='рисунок'>";
        $imgDivElement = "<div>" . $imgElement . "</div>";

        $textElement .= $textBefore . $imgDivElement;
        $text = substr($text, $imagePosition);
    }

    $textElement .=  $text . "\n";

    echo json_encode(["header" => $data['Title'], "html" => $textElement]);
}




function renderTest(array $data): void
{
    $textElement = "";
    $selectedGroupId = $_SESSION['userRole'] ?? null;
    $textElement .= htmlspecialchars($data['Text']) . "\n";

    $footer = "<button type='button' onclick='displayQuestion(0)' " . ($selectedGroupId === null ? ' disabled' : '') . " id='startTestButton' class='btn btn-primary'>Старт</button>\n";


    echo json_encode(["header" => $data['Title'], "html" => $textElement, "questions" => $data["Questions"], "footer" => $footer]);
}


function renderUsersTable(): void
{
    global $dbh;
    $query = "SELECT user_id, email, users.role_id, role, name, surname, lastname FROM users JOIN roles ON roles.role_id = users.role_id
                                                               JOIN education_system.human_data hd on hd.data_id = users.data_id ORDER BY roles.role_id desc ";
    $result = $dbh->query($query);
    $result->setFetchMode(PDO::FETCH_ASSOC);

    // Начало таблицы
    $table = "<table class='table table-hover table-striped'>";
    $table .= "<thead>
                            <th class='text-center'>ID</th>
                            <th class='text-center'>Email</th>
                            <th class='text-center'>ФИО</th>
                            <th class='text-center'>Роль</th>
                            <th class='text-center'>Настроить</th>
                           </thead>";

    // Заполнение таблицы данными
    foreach ($result as $row) {
        $fullName = $row['surname'] . " " . substr($row['name'], 0, 2) . "." . substr($row['lastname'], 0, 2) . ".";
        $table .= "<tr>";
        $table .= "<td class='text-center'>" . $row['user_id'] . "</td>";
        $table .= "<td class='text-center'>" . $row['email'] . "</td>";
        $table .= "<td class='text-center'>" . $fullName . "</td>";
        $table .= "<td class='text-center'>" . $row['role'] . "</td>";
        $table .= "<td class='text-center'><button onclick=openEditModal(".json_encode($row,JSON_UNESCAPED_UNICODE).") class='btn btn-secondary' style='padding: 2px'>Изменить</button></td>";
        $table .= "</tr>";
    }

    $table .= "</table>";

    // Модальное окно
    $table .= "
    <div class='modal fade' id='editUserModal' tabindex='-1' role='dialog' aria-labelledby='editUserModalLabel' aria-hidden='true'>
      <div class='modal-dialog' role='document'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title' id='editUser ModalLabel'>Изменить пользователя</h5>
          </div>
          <div class='modal-body'>
            <form id='editUserForm'>
              <h6>Системная информация</h6>
              <input type='hidden' id='userId' name='userId'>
              <div class='form-group'>
                <label for='userEmail'>Email</label>
                <input type='email' class='form-control' id='userEmail' name='userEmail' required>
              </div>
              
              <div class='form-group'>
                <label for='userPassword'>Новый пароль</label>
                <div class='input-group'>
                    <input type='password' class='form-control' id='userPassword' name='userPassword' required>
                    <button type='button' class='btn btn-outline-primary' onclick='setDefaultPassword()'>Стандартный пароль</button>    
                </div> 
              </div>
              <div class='form-group'>
                <label for='userRole'>Роль</label>
                <select class='form-control' id='userRole' required>
                        <option value='3'>Admin</option>
                        <option value='2'>Teacher</option>
                        <option value='1'>Student</option>
                </select>      
              </div>
              <br>
              <h6>Личная информация</h6>    
              <div class='form-group'>
                <label for='userSurname'>Фамилия</label>
                <input type='text' class='form-control' id='userSurname' name='userSurname' required>
              </div>
              <div class='form-group'>
                <label for='userName'>Имя</label>
                <input type='text' class='form-control' id='userName' name='userName' required>
              </div>
              <div class='form-group'>
                <label for='userLastname'>Отчество</label>
                <input type='text' class='form-control' id='userLastname' name='userLastname' required>
              </div>
              
            </form>
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
            <button type='button' class='btn btn-primary' onclick='saveUserChanges()'>Сохранить изменения</button>
          </div>
        </div>
      </div>
    </div>
    ";

    echo $table;
}

function renderLogsTable(): void
{

    global $dbh;
    $query = "SELECT log_id, email, logIn_dateTime FROM userLogs JOIN education_system.users u on userLogs.user_Id = u.user_id ORDER BY logIn_dateTime desc ";
    $logs = $dbh->query($query);
    $logs->setFetchMode(PDO::FETCH_ASSOC);
    $table = "<table class='table  table-hover table-striped'>";
    $table .= "<thead>
                            <th class='text-center'>ID</th>
                            <th class='text-center'>Email</th>
                            <th class='text-center'>Дата/время входа</th>
                           </thead>";
    foreach ($logs as $log) {
        $datetime = new DateTime($log['logIn_dateTime']);
        $formatted_datetime = $datetime->format("d/m/y H:i");
        $table .= "<tr>";
        $table .= "<td class='text-center'>" . $log['log_id'] . "</td>";
        $table .= "<td class='text-center'>" . $log['email'] . "</td>";
        $table .= "<td class='text-center'>" . $formatted_datetime . "</td>";
        $table .= "</tr>";
    }
    $table .= "</table>";
    echo $table;
}

function getStudentListByGroupId($groupId,$blockId): false|array
{

    global $dbh;
    //определяем тесты в блоке
    $query = $dbh->prepare("select components.component_id, data from components WHERE type= 'test' and block_id = :blockId");
    $query->bindValue(':blockId',$blockId);
    $query->execute();

    $testData = $query->fetchAll(PDO::FETCH_ASSOC);
    $testIdsInBlock = array_values(array_column($testData, 'component_id'));

    $testsDataArray  = array_values(array_column($testData,"data"));

    $headersArray = ['№',"ФИО"];
    foreach ($testsDataArray as $testData) {
            $headersArray[] = json_decode($testData, true)['Title'];
    }
    if (count($testIdsInBlock)==0){
        return  [];
    }
    //ищем студентов в группе и их данные
    $query = $dbh->prepare("SELECT s.id, h.surname, h.name , h.lastname FROM human_data h JOIN users u ON h.data_id = u.data_id JOIN students s ON u.user_id = s.user_id WHERE s.group_id = :groupId ORDER BY h.surname");
    $query->bindValue(':groupId',$groupId);
    $query->execute();
    $studentData = $query->fetchAll(PDO::FETCH_ASSOC);

    $studentIdsInGroup = array_values(array_column($studentData, "id"));
    $studentFullNameInGroup = [];
    foreach ($studentData as $student) {
        $lastName = $student['lastname'] !== '' ? substr($student['lastname'], 0, 2).'.' : '';
        $studentFullNameInGroup[] = $student['surname'] . ' ' . substr($student['name'], 0, 2) . '.' .$lastName;
    }

    $dataToRender = [];
    $dataToRender[] = $headersArray;
    $dataToRender[] = $studentFullNameInGroup;
    //достаем результаты тестов по каждому студенту
    for ($i = 0; $i < count($testIdsInBlock); $i++) {
        $testData = [];
        for ($j = 0; $j < count($studentIdsInGroup); $j++) {
            $query = $dbh->prepare("SELECT result, result_date from complete_components_by_student where component_id = :componentId and student_id = :studentId");
            $query->bindValue(':componentId', $testIdsInBlock[$i]);
            $query->bindValue(':studentId', $studentIdsInGroup[$j]);
            $query->execute();
            if ($query->rowCount() == 0) {
                $testData[$j] = '';
            } else {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $userResultData = json_decode($result['result'], true);
                $userResult = $userResultData["UserResult"];
                $MaxResult = $userResultData["MaxResult"];
                $testData[$j] = $userResult . "/" . $MaxResult;

            }

        }
        $dataToRender[] = $testData;


    }
    return $dataToRender;
}


function getComponentJson($componentId): mixed
{

    global $dbh;
    $query = $dbh->prepare("SELECT data, type FROM components WHERE component_id = :id");
    $query->bindParam(':id', $componentId);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}


function downloadImagesByComponentIdToDirectory($component_id): void
{

    global $dbh;
    $DIRECTORY = "../assets";

    if (!file_exists($DIRECTORY)) {
        mkdir($DIRECTORY, 0777, true);

    }
    $stmt = $dbh->prepare('SELECT image_name, image_content FROM images WHERE component_id = :component_id');
    $stmt->bindParam(':component_id', $component_id);
    $stmt->execute();
    $images = $stmt->fetchAll();


    foreach ($images as $index => $image) {
        $index++;
        $filename = "comp_{$component_id}_$index.png";
        file_put_contents("$DIRECTORY/$filename", $image['image_content']);
    }
}

function downloadBlockImageById($block_id): void
{
    global $dbh;
    $DIRECTORY ="assets";

    if (!file_exists($DIRECTORY)) {
        mkdir($DIRECTORY, 0777, true);
    }


    $stmt = $dbh->prepare('SELECT block_image_name, block_image FROM blocks WHERE block_id = :block_id');
    $stmt->bindParam(':block_id', $block_id);
    $stmt->execute();
    $image = $stmt->fetch();

    if ($image) {
        $filename = "block_$block_id.png";
        file_put_contents("$DIRECTORY/$filename", $image['block_image']);

    }
}

function generateNavButton($componentButtonType, $componentId,$isFinalTest,$blockTitle): void
{
    $svgs = ['<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">' .
        '<path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>' .
        '</svg>', ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/> </svg>', '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-checklist" viewBox="0 0 16 16">
  <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
  <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0M7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
</svg>'];
    $svgCode = $componentButtonType === "Lecture" ? $svgs[0] :  ($isFinalTest ? $svgs[2]: $svgs[1]);

    $listItem = '<li class="w-100 d-flex  justify-content-center m-0 p-0 nav-item" data-bs-trigger="hover" data-bs-delay: { "show": 500, "hide": 100 }  data-bs-toggle="tooltip" data-bs-placement="right"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="'.$blockTitle.'">';
    $link = '<a class="nav-link w-100  d-flex align-items-center  justify-content-center" href="#" style="min-width: 40px; min-height:40px" data-component-id="' . $componentId . '">';
    $link .= $svgCode;
    $listItem .= $link . "</a></li>";

    echo $listItem;

}

function getAllBlocks(): false|array
{
    global $dbh;
    $query = $dbh->prepare("SELECT * FROM blocks");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getBLockStructure($blockId): false|array
{
    global $dbh;
    $query = $dbh->prepare("SELECT type, component_id, data, isFinalTest FROM components WHERE block_id= :blockId");
    $query->bindParam(':blockId', $blockId);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function renderBlock($title, $text, $blockId): void
{
    downloadBlockImageById($blockId);
    $html = <<<HTML
<div class="card-wrapper d-flex align-items-center justify-content-center col-lg-4 col-md-6 col-xs-12">
    <div class="card">
        
        <div class="card-img-wrapper">
            <img class="card-img-top" width="320" height="320" src="/assets/block_$blockId.png" alt="Card image cap">
        </div>
        <div class="card-body d-flex flex-column ">
            <h5 class="card-title"><u>$title</u> </h5>
            <div class="card-content">
                <p class="card-text">$text</p>
                
            </div>
        </div>
        <form action="Services/renderService.php" id="$blockId" method="get">
        <input  type="hidden" name="blockId" value="$blockId">
        </form>
        <a  onclick="$('#$blockId').submit()" class="hidden-link stretched-link">Пройти тему</a>
        
    </div>
</div>
HTML;

    echo $html;
}

