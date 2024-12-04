<?php


require "pdoConnection.php";
require "queryService.php";
session_start();
//Точки входа через запросы
if (isset($_GET['componentId'])) {
    handleComponentRequest($_GET['componentId']);
}

if (isset($_POST['userAnswers'])) {
    handleUserAnswersRequest($_POST['userAnswers']);
}

if (isset($_GET['groupId'])) {
    handleGroupRequest($_GET['groupId']);
}

if (isset($_GET['sectionId'])) {
    handleSectionRequest($_GET['sectionId']);
}

if (isset($_GET['blockId'])) {
    handleBlockRequest($_GET['blockId']);
}
//обработчики запросов
function handleComponentRequest($componentId): void
{
    $_SESSION['currentComponentId'] = $componentId;

    try {
        $data = getComponentJson($componentId);

        if (!$data) {
            http_response_code(404);
            echo "Component not found.";
            return;
        }

        $component = json_decode($data['data'], true);

        if (!$component) {
            http_response_code(400);
            echo "Invalid component data.";
            return;
        }

        $_SESSION['currentComponentData'] = $component;

        if (!empty($component['Images'])) {
            downloadImagesByComponentIdToDirectory($componentId);
        }

        if ($data['type'] === "Lecture") {
            renderLecture($componentId, $component);
        } else {
            renderTest($component);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo "Server error: " . $e->getMessage();
    }
}

function handleUserAnswersRequest($userAnswers): void
{
    try {
        $html = proceedUserAnswers($userAnswers);
        echo json_encode(["html" => $html]);
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error processing user answers: " . $e->getMessage();
    }
}

function handleGroupRequest($groupArr): void
{
    if (!isset($groupArr['groupId'], $groupArr['blockId'])) {
        http_response_code(400);
        echo "Invalid group parameters.";
        return;
    }

    $_SESSION['selectedGroupId'] = $groupArr['groupId'];
    try {
        $studentResult = getStudentListByGroupId($groupArr['groupId'], $groupArr['blockId']);
        $html = createStudentList($studentResult);
        echo json_encode(["html" => $html]);
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error fetching group data: " . $e->getMessage();
    }
}

function handleSectionRequest($blockId): void
{
    $_SESSION['selectedBlockId'] = $blockId;

    try {
        $userId = $_SESSION['userID'];
        $dataToRender = getResultByBlockAndUserId($blockId, $userId);

        if (empty($dataToRender)) {
            echo json_encode(["html" => "<h3 class='text-center'>Вы еще не проходили ни одного теста из данного раздела!</h3>"]);
            return;
        }

        $result = [];
        foreach ($dataToRender as $index => $item) {
            $result[] = createUserResultAccordion($index + 1, $item);
        }

        echo json_encode(["html" => $result]);
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error fetching section data: " . $e->getMessage();
    }
}

function handleBlockRequest($blockId): void
{
    $_SESSION['blockId'] = $blockId;
    header("Location: /section");
    exit;
}


function getResultByBlockAndUserId($blockId, $userId): array|string
{
    // Получаем компоненты типа "Test" для указанного блока
    $components = executeQuery(
        "SELECT component_id, data, isFinalTest FROM components WHERE block_id = :blockId AND type = 'Test'",
        [":blockId" => $blockId]
    );

    if (empty($components)) {
        return [];
    }

    // Получаем ID студента по ID пользователя
    $student = executeQuery(
        "SELECT id FROM students WHERE user_id = :userId",
        [":userId" => $userId],
        false
    );

    if (!$student) {
        return [];
    }

    $studentId = $student['id'];
    $dataToRender = [];

    // Получаем результаты тестов для студента
    foreach ($components as $test) {
        $testId = $test['component_id'];
        $testTitle = json_decode($test['data'], true)['Title'];

        $result = executeQuery(
            "SELECT result, answers 
             FROM complete_components_by_student 
             JOIN education_system.test_answers ta 
             ON complete_components_by_student.component_id = ta.component_id
             WHERE complete_components_by_student.component_id = :testId AND student_id = :studentId",
            [
                ":testId" => $testId,
                ":studentId" => $studentId
            ],
            false
        );

        if ($result) {
            $dataToRender[] = [
                "testId" => $testId,
                "testTitle" => $testTitle,
                "isFinalTest" => $test['isFinalTest'],
                "testResult" => $result['result'],
                "answers" => $result['answers']
            ];
        }
    }

    return $dataToRender;
}



function createUserResultAccordion($counter, $dataToRender): string
{


    $isFinalTest = $dataToRender['isFinalTest'];

    $testId = $dataToRender['testId'];
    $testTitle = $dataToRender['testTitle'];
    $result = json_decode($dataToRender['testResult'], true);
    $userAnswers = $result['UserAnswers'];
    $userResult = $result['UserResult'];
    $maxResult = $result['MaxResult'];
    $answers = json_decode($dataToRender['answers']);

    // Генерируем таблицу результатов
    $content = generateTestResultTable(
        ["№", "Ваш ответ"],
        false,
        $isFinalTest,
        $userAnswers,
        $answers
    )['table'];

    $content .= '<p class="text-center">Количество правильных ответов: ' . $userResult . ' из ' . $maxResult . '</p>';
    $show = $counter === 1 ? ' show' : '';
    $collapsed = $counter !== 1 ? "collapsed" : '';

    return <<<EOF
<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button $collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse$testId"
                aria-expanded="true" aria-controls="collapse$testId">
            $testTitle
        </button>
    </h2>
    <div id="collapse$testId" class="accordion-collapse collapse $show" data-bs-parent="#result">
        <div class="accordion-body overflow-auto">
            $content
        </div>
    </div>
</div>
EOF;
}


function createStudentList(array $data): string
{
    if (empty($data)) {
        return '<h2>Нет данных</h2>';
    }

    $html = '<table class="table table-stripped table-hover">';

    // Генерация заголовков таблицы
    $headers = $data[0];
    $html .= '<thead><tr>';
    foreach ($headers as $header) {
        $html .= '<th class="table-primary text-center">' . htmlspecialchars($header) . '</th>';
    }
    $html .= '</tr></thead>';

    // Генерация строк таблицы
    $html .= '<tbody>';
    $columnCount = count($data);
    $rowCount = count($data[1]);

    for ($i = 0; $i < $rowCount; $i++) {
        $html .= '<tr>';

        // Добавление номера строки
        $html .= '<td class="text-center">' . ($i + 1) . '</td>';

        // Генерация данных строки
        for ($j = 1; $j < $columnCount; $j++) {
            $cellData = $data[$j][$i] ?? '';
            $html .= '<td class="text-center">' . htmlspecialchars($cellData) . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}


function generateTestResultTable(array $headers, bool $isTeacher, bool $isFinalTest, ...$columns): array
{
    $correctAnswersCount = 0;
    $columnCount = $isTeacher ? count($columns) : count($columns) - 1;
    $rowCount = count($columns[0]);

    $table = '<table class="table table-bordered">';
    $table .= '<thead><tr>';

    // Генерация заголовков
    for ($i = 0; $i <= $columnCount; $i++) {
        $table .= '<th class="table-primary text-center">' . $headers[$i] . '</th>';
    }
    $table .= '</tr></thead><tbody>';

    // Генерация строк таблицы
    for ($i = 0; $i < $rowCount; $i++) {
        if (!$isFinalTest || $isTeacher) {
            $isCorrect = $columns[0][$i] == $columns[1][$i];
            $colorClass = $isCorrect ? "success" : "danger";
            if ($isCorrect) {
                $correctAnswersCount++;
            }
        } else {
            $colorClass = "light";
        }

        $table .= "<tr class='table-$colorClass'>";

        // Номер строки
        $table .= '<td class="text-center">' . ($i + 1) . '</td>';

        // Данные строк
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


function proceedUserAnswers(array $userAnswers): string
{
    session_start();
    $componentId = $_SESSION['currentComponentId'];
    $userRole = $_SESSION['userRole'];

    // Получение ответов на тест
    $query = "SELECT answers FROM test_answers WHERE component_id = :componentId";
    $result = executeQuery($query, [':componentId' => $componentId], false);
    $answers = json_decode($result['answers'] ?? '[]', true);
    $numQuestions = count($userAnswers);

    // Проверка, является ли тест финальным
    $query = "SELECT isFinalTest FROM components WHERE component_id = :componentId";
    $result = executeQuery($query, [':componentId' => $componentId], false);
    $isFinalTest = $result['isFinalTest'] ?? false;

    // Генерация таблицы результатов
    $tableResultArray = generateTestResultTable(
        ["№", "Ваш ответ", "Правильный ответ"],
        $userRole === 'teacher',
        $isFinalTest,
        $userAnswers,
        $answers
    );

    $table = $tableResultArray['table'];
    $numCorrect = $tableResultArray['correctAnswersCount'];
    $table .= '<p class="text-center">Количество правильных ответов: ' . $numCorrect . ' из ' . $numQuestions . '</p>';

    // Сохранение результатов для студента
    if ($userRole === "student") {
        if (checkExistingResult()) {
            saveUserAnswersToDb([
                "UserAnswers" => $userAnswers,
                "UserResult" => $numCorrect,
                "MaxResult" => $numQuestions,
            ]);
            return $table;
        }

        $table .= '<h4 class="text-center">Результат не был записан: вы уже проходили это тестирование</h4>';
    }

    return $table;
}


function checkExistingResult(): bool
{
    $query = "SELECT id 
              FROM complete_components_by_student 
              WHERE student_id = (SELECT id FROM students WHERE user_id = :user_id) 
              AND component_id = :component_id";

    $result = executeQuery($query, [
        ':user_id' => $_SESSION['userID'],
        ':component_id' => $_SESSION['currentComponentId']
    ], false);

    return empty($result);
}


function saveUserAnswersToDb(array $userAnswers): void
{
    $query = "INSERT INTO complete_components_by_student (component_id, student_id, result, result_date)
              VALUES (:component_id, 
              (SELECT id FROM students WHERE user_id = :user_id),
              :result, :result_date)";

    executeQuery($query, [
        ':component_id' => $_SESSION['currentComponentId'],
        ':user_id' => $_SESSION['userID'],
        ':result' => json_encode($userAnswers),
        ':result_date' => date("Y-m-d H:i:s")
    ]);
}


function renderLecture(int $component_id, array $data): void
{
    $textElement = "";
    $images = $data['Images'] ?? [];
    $text = $data['Text'];
    $k = 0;

    foreach ($images as $image) {
        $k++;
        $imagePosition = $image['mark'];
        $textBefore = substr($text, 0, $imagePosition);
        $imgElement = "<img style='max-width: 50%;' src='../assets/comp_{$component_id}_$k.png' alt='рисунок'>";
        $textElement .= $textBefore . "<div>" . $imgElement . "</div>";
        $text = substr($text, $imagePosition);
    }

    $textElement .= $text . "\n";

    echo json_encode([
        "header" => $data['Title'],
        "html" => $textElement
    ]);
}


function renderTest(array $data): void
{
    $textElement = htmlspecialchars($data['Text']) . "\n";
    $userRole = $_SESSION['userRole'] ?? null;

    $footer = "<button type='button' onclick='displayQuestion(0)' "
        . ($userRole === null ? ' disabled' : '')
        . " id='startTestButton' class='btn btn-primary'>Старт</button>\n";

    echo json_encode([
        "header" => $data['Title'],
        "html" => $textElement,
        "questions" => $data['Questions'] ?? [],
        "footer" => $footer
    ]);
}



function renderUsersTable(): void
{
    $query = "
        SELECT user_id, email, users.role_id, role, name, surname, lastname 
        FROM users 
        JOIN roles ON roles.role_id = users.role_id
        JOIN education_system.human_data hd ON hd.data_id = users.data_id 
        ORDER BY roles.role_id DESC
    ";
    $result = executeQuery($query);

    $table = "<table class='table table-hover table-striped'>";
    $table .= "
        <thead>
            <th class='text-center'>ID</th>
            <th class='text-center'>Email</th>
            <th class='text-center'>ФИО</th>
            <th class='text-center'>Роль</th>
            <th class='text-center'>Настроить</th>
        </thead>
    ";

    foreach ($result as $row) {
        $fullName = sprintf(
            "%s %s. %s.",
            $row['surname'],
            substr($row['name'], 0, 2),
            substr($row['lastname'], 0, 2)
        );
        $encodedRow = json_encode($row, JSON_UNESCAPED_UNICODE);
        $table .= "
            <tr class='text-center align-middle'>
                <td>{$row['user_id']}</td>
                <td>{$row['email']}</td>
                <td>$fullName</td>
                <td>{$row['role']}</td>
                <td>
                    <button onclick='openEditModal($encodedRow)' class='btn btn-outline-primary' style='padding: 5px'>Изменить</button>
                </td>
            </tr>
        ";
    }

    $table .= "</table>";
    $table .= includeEditUserModal();
    echo $table;
}


function includeEditUserModal(): string
{
    return "
        <div class='modal fade' id='editUserModal' tabindex='-1' role='dialog' aria-labelledby='editUserModalLabel' aria-hidden='true'>
            <div class='modal-dialog' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Изменить пользователя</h5>
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
                            <h6 class='mt-3'>Личная информация</h6>    
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
                    <div class='modal-footer justify-content-between'>
                        <button type='button' class='btn btn-danger' onclick='removeUser()'>Удалить</button>
                        <div>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Закрыть</button>
                            <button type='button' class='btn btn-primary' onclick='saveUserChanges()'>Сохранить изменения</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ";
}


function renderLogsTable(): void
{
    $query = "SELECT log_id, email, logIn_dateTime FROM userLogs JOIN education_system.users u on userLogs.user_Id = u.user_id ORDER BY logIn_dateTime desc ";
    $logs = executeQuery($query);

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


function getStudentListByGroupId($groupId, $blockId): false|array
{
    // Получение тестов в блоке
    $query = "SELECT components.component_id, data FROM components WHERE type= 'test' AND block_id = :blockId";
    $testData = executeQuery($query, [':blockId' => $blockId]);

    $testIdsInBlock = array_column($testData, 'component_id');
    $testsDataArray = array_column($testData, 'data');

    $headersArray = ['№', "ФИО"];
    foreach ($testsDataArray as $testData) {
        $headersArray[] = json_decode($testData, true)['Title'];
    }

    if (empty($testIdsInBlock)) {
        return [];
    }

    // Получение студентов в группе
    $query = "SELECT s.id, h.surname, h.name, h.lastname FROM human_data h 
              JOIN users u ON h.data_id = u.data_id 
              JOIN students s ON u.user_id = s.user_id 
              WHERE s.group_id = :groupId 
              ORDER BY h.surname";
    $studentData = executeQuery($query, [':groupId' => $groupId]);

    $studentIdsInGroup = array_column($studentData, "id");
    $studentFullNameInGroup = array_map(function ($student) {
        $lastName = $student['lastname'] !== '' ? substr($student['lastname'], 0, 2) . '.' : '';
        return $student['surname'] . ' ' . substr($student['name'], 0, 2) . '.' . $lastName;
    }, $studentData);

    $dataToRender = [$headersArray, $studentFullNameInGroup];

    // Получение результатов тестов по каждому студенту
    foreach ($testIdsInBlock as $testId) {
        $testData = [];
        foreach ($studentIdsInGroup as $studentId) {
            $query = "SELECT result, result_date FROM complete_components_by_student WHERE component_id = :componentId AND student_id = :studentId";
            $result = executeQuery($query, [':componentId' => $testId, ':studentId' => $studentId], false);

            if ($result) {
                $userResultData = json_decode($result['result'], true);
                $userResult = $userResultData["UserResult"];
                $MaxResult = $userResultData["MaxResult"];
                $testData[] = "$userResult/$MaxResult";
            } else {
                $testData[] = '';
            }
        }
        $dataToRender[] = $testData;
    }

    return $dataToRender;
}


function getComponentJson($componentId): mixed
{
    $query = "SELECT data, type FROM components WHERE component_id = :componentId";
    return executeQuery($query, [':componentId' => $componentId], false);
}


function ensureDirectoryExists(string $directory): void
{
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
}

function saveImageToFile(string $directory, string $filename, string $content): void
{
    file_put_contents("$directory/$filename", $content);
}

function fetchImagesByComponentId($componentId): array
{
    global $dbh;
    $query = $dbh->prepare("SELECT image_name, image_content FROM images WHERE component_id = :componentId");
    $query->bindParam(':componentId', $componentId);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function fetchBlockImageById($blockId): array|false
{
    global $dbh;
    $query = $dbh->prepare("SELECT block_image_name, block_image FROM blocks WHERE block_id = :blockId");
    $query->bindParam(':blockId', $blockId);
    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);
}


function downloadImagesByComponentIdToDirectory($componentId): void
{
    $directory = "../assets";
    ensureDirectoryExists($directory);

    $images = fetchImagesByComponentId($componentId);

    foreach ($images as $index => $image) {
        $filename = sprintf("comp_%s_%d.png", $componentId, $index + 1);
        saveImageToFile($directory, $filename, $image['image_content']);
    }
}


function downloadBlockImageById($blockId): void
{
    $directory = "assets";
    ensureDirectoryExists($directory);

    $image = fetchBlockImageById($blockId);
    if ($image) {
        $filename = sprintf("block_%s.png", $blockId);
        saveImageToFile($directory, $filename, $image['block_image']);
    }
}

function generateNavButton($componentButtonType, $componentId, $isFinalTest, $blockTitle): void
{
    $svgs = [
        'Lecture' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">' .
            '<path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>' .
            '</svg>',
        'Default' => ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/> </svg>',
        'Test' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-checklist" viewBox="0 0 16 16">
  <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
  <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0M7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
</svg>',
    ];

    $svgCode = $componentButtonType === "Lecture"
        ? $svgs['Lecture']
        : ($isFinalTest ? $svgs['Test'] : $svgs['Default']);

    $listItem = sprintf(
        '<li class="w-100 d-flex justify-content-center m-0 p-0 nav-item" data-bs-trigger="hover" data-bs-delay=\'{"show": 500, "hide": 100}\' data-bs-toggle="tooltip" data-bs-placement="right" data-bs-custom-class="custom-tooltip" data-bs-title="%s">',
        htmlspecialchars($blockTitle)
    );

    $link = sprintf(
        '<a class="nav-link w-100 d-flex align-items-center justify-content-center" href="#" style="min-width: 40px; min-height:40px" data-component-id="%s">%s</a>',
        htmlspecialchars($componentId),
        $svgCode
    );

    echo $listItem . $link . "</li>";
}


function getAllBlocks(): array
{
    $query = "SELECT * FROM blocks";
    return executeQuery($query, [], true) ?: [];
}


function getBlockStructure($blockId): array
{
    $query = "SELECT type, component_id, data, isFinalTest FROM components WHERE block_id = :blockId";
    return executeQuery($query, [':blockId' => $blockId], true) ?: [];
}


function renderBlock($title, $text, $blockId): void
{
    downloadBlockImageById($blockId);

    $html = sprintf(
        '<div class="card-wrapper d-flex align-items-center justify-content-center col-lg-4 col-md-6 col-xs-12">
            <div class="card">
                <div class="card-img-wrapper">
                    <img class="card-img-top" width="320" height="320" src="/assets/block_%s.png" alt="Card image cap">
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><u>%s</u></h5>
                    <div class="card-content">
                        <p class="card-text">%s</p>
                    </div>
                </div>
                <form action="Services/renderService.php" id="%s" method="get">
                    <input type="hidden" name="blockId" value="%s">
                </form>
                <a onclick="$(\'#%s\').submit()" class="hidden-link stretched-link"></a>
      
            </div>
        </div>',
        htmlspecialchars($blockId),
        htmlspecialchars($title),
        htmlspecialchars($text),
        htmlspecialchars($blockId),
        htmlspecialchars($blockId),
        htmlspecialchars($blockId)
    );

    echo $html;
}


