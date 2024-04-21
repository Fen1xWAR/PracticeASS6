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
     getStudentListByGroupId($groupId,$blockId);
//    if (count($studentsData) == 0) {
//
//        echo json_encode(["html" => "<h3 class='text-center'>В данной группе нет учеников</h3>"]);
//    } else {
//        echo json_encode(["html" => createStudentList($studentsData)]);
//    }


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
    $testId = $dataToRender['testId'];
    $testTitle = $dataToRender['testTitle'];
    $result = json_decode($dataToRender['testResult'], true);
    $userAnswers = $result['UserAnswers'];
    $userResult = $result['UserResult'];
    $maxResult = $result['MaxResult'];
    $answers = json_decode($dataToRender['answers']);

    $content = generateTestResultTable(["№", "Ваш ответ"], false, $userAnswers, $answers)['table'];
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
    $html .= '<th>№</th>';
    $html .= '<th>ФИО</th>';
    $html .= '</tr>';
    $indexInGroup = 0;
    foreach ($data as $row) {
        $fullName = $row['surname'] . " " . substr($row['name'], 0, 2) . "." . substr($row['lastname'], 0, 2) . ".";

        $indexInGroup++;
        $html .= '<tr data-id="' . $row['id'] . '">';
        $html .= '<td>' . $indexInGroup . '</td>';
        $html .= '<td>' . $fullName . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
}

function generateTestResultTable(array $headers, bool $isTeacher, ...$columns): array
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
        if ($columns[0][$i] == $columns[1][$i]) {
            $correctAnswersCount++;
            $colorClass = "success";
        } else {
            $colorClass = "danger";
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
    $tableResultArray = generateTestResultTable(["№", "Ваш ответ", "Правильный ответ"], $isTeacher, $userAnswers, $answers);
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

    $textElement .= htmlspecialchars($text) . "\n";

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


function getStudentListByGroupId($groupId,$blockId): false|array
{

    global $dbh;
    //определяем тесты в блоке
    $query = $dbh->prepare("select components.component_id from components WHERE type= 'test' and block_id = :blockId");
    $query->bindValue(':blockId',$blockId);
    $query->execute();


    $testIdsInBlock = array_values(array_column($query->fetchAll(PDO::FETCH_ASSOC), 'component_id'));


    if (count($testIdsInBlock)==0){
        return  [];
    }
    //ищем студентов в группе и их данные
    $query = $dbh->prepare("SELECT s.id, h.surname, h.name , h.lastname FROM human_data h JOIN users u ON h.data_id = u.data_id JOIN students s ON u.user_id = s.user_id WHERE s.group_id = :groupId ORDER BY h.surname");
    $query->bindValue(':groupId',$groupId);
    $query->execute();
    $studentData = $query->fetchAll(PDO::FETCH_ASSOC);

    $studentIdsInGroup = array_values(array_column($studentData, "id"));

    //достаем результаты тестов по каждому студенту

    return [];
}


function getComponentJson($componentId)
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

function generateNavButton($componentButtonType, $componentId): void
{
    $svgs = ['<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">' .
        '<path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>' .
        '</svg>', ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/> </svg>'];
    $svgCode = $componentButtonType === "Lecture" ? $svgs[0] : $svgs[1]; // replace with the desired SVG code

    $listItem = '<li class="w-100 d-flex justify-content-center m-0 p-0 nav-item">';
    $link = '<a class="nav-link w-100 d-flex align-items-center  justify-content-center" href="#" style="min-width: 40px; min-height:40px" data-component-id="' . $componentId . '">';
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
    $query = $dbh->prepare("SELECT type, component_id FROM components WHERE block_id= :blockId");
    $query->bindParam(':blockId', $blockId);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function renderBlock($title, $text, $blockId): void
{
    downloadBlockImageById($blockId);
    $html = <<<HTML
<div class="card-wrapper d-flex align-items-center justify-content-center col-lg-4 col-md-5 col-xs-12">
    <div class="card">
        
        <div class="card-img-wrapper">
            <img class="card-img-top" src="/assets/block_$blockId.png" alt="Card image cap">
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

