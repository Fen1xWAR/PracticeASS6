<?php
require_once "../pdoConnection.php";
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
if (isset($_GET['groupId'])) {
    session_start();
    $groupId = $_GET['groupId'];
    $_SESSION['selectedGroupId'] = $groupId;
    $studentsData = getStudentListByGroupId($groupId);
    echo creteStudentList($studentsData);

}

if (isset($_POST['userAnswers'])) {
    $userAnswers = $_POST['userAnswers'];
    proceedUserAnswers($userAnswers);
}
function proceedUserAnswers($userAnswers): void
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
    $title = $_SESSION['currentComponentData']['Title'];
    $numCorrect = 0;
    $table = '<div class="card">';
    $table .= "<div class='card-header'>" . htmlspecialchars($title) . "</div>\n";
    $table .= '<div class="card-body">';
    $table .= '<table class="table table-bordered table-striped">';
    $table .= '<thead><tr><th class="text-center">#</th><th class="text-center">Ответ пользователя</th><th class="text-center">Правильный ответ</th></tr></thead>';
    $table .= '<tbody>';
    foreach ($userAnswers as $questionNumber => $userAnswer) {
        $table .= '<tr>';
        $table .= '<td class="text-center">' . ($questionNumber + 1) . '</td>';
        $table .= '<td class="text-center">' . $userAnswer . '</td>';
        $table .= '<td class="text-center">' . $answers[$questionNumber] . '</td>';
        if ($userAnswer == $answers[$questionNumber]) {
            $numCorrect++;
        }
        $table .= '</tr>';
    }

    $table .= '</tbody></table>';
    $table .= '<p class="text-center">Количество правильных ответов: ' . $numCorrect . ' из ' . $numQuestions . '</p>';
    $table .= '</div>';
    $table .= '</div>';
    if($_SESSION['userRole']=== "student"){

        saveUserAnswersToDb([
            "UserAnswers" => $userAnswers,
            "UserResult" => $numCorrect,
            "MaxResult"=>$numQuestions
        ]);
        echo $table;
    }
}

function saveUserAnswersToDb($userAnswers)
{
    global $dbh;
    $query = $dbh->prepare("INSERT INTO complete_components_by_student (component_id, student_id, result)
VALUES (:component_id,
(SELECT students.id FROM students WHERE user_id = :user_id),
:result);");
    $query->bindValue(":component_id", $_SESSION['currentComponentId']);
    $query->bindValue(":user_id",$_SESSION['userID']);
    $query->bindValue(":result", json_encode($userAnswers));
    $query->execute();
}

function renderLecture(int $component_id, array $data): void
{
    $lectureElement = "<div class='card'>\n";
    $lectureElement .= "<div class='card-header'>" . htmlspecialchars($data['Title']) . "</div>\n";
    $textElement = "<div class='p-4 card-body'>\n";

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
    $lectureElement .= $textElement . "</div>\n";

    echo json_encode(["html" => $lectureElement]);
}

function renderTest(array $data): void
{
    $testElement = "<div class='card'>\n";
    $testElement .= "<div class='card-header'>" . htmlspecialchars($data['Title']) . "</div>\n";
    $textElement = "<div class='p-4 card-body d-flex flex-column justify-content-center'>\n";
    $selectedGroupId = $_SESSION['userRole'] ?? null;
    $textElement .= htmlspecialchars($data['Text']) . "\n";

    $textElement .= "<button type='button' onclick='displayQuestion(0)' " . ($selectedGroupId === null ? ' disabled' : '') . " id='startTestButton' class='btn btn-primary'>Start</button>\n";

    $testElement .= $textElement . "</div>\n";
    $testElement .= "</div>\n";

    echo json_encode(["html" => $testElement, "questions" => $data["Questions"]]);
}


function creteStudentList($data): string
{
    if (count($data) == 0) {
        return "<h2 class='text-center'>В данной группе нет учеников</h2>";
    }
    $html = '<table class="table table-hover">';
    $html .= '<tr>';
    $html .= '<th>№</th>';
    $html .= '<th>Имя</th>';
    $html .= '<th>Фамилия</th>';
    $html .= '</tr>';
    $indexInGroup = 0;
    foreach ($data as $row) {
        $indexInGroup++;
        $html .= '<tr data-id="' . $row['id'] . '">';
        $html .= '<td>' . $indexInGroup . '</td>';
        $html .= '<td>' . $row['name'] . '</td>';
        $html .= '<td>' . $row['surname'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
}


function getStudentListByGroupId($groupId): false|array
{
    global $dbh;
    $query = $dbh->prepare("SELECT s.id, h.name, h.surname FROM human_data h JOIN users u ON h.data_id = u.data_id JOIN students s ON u.user_id = s.user_id WHERE s.group_id = :groupId ORDER BY h.surname");
    $query->bindValue(":groupId", $groupId);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
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
    $DIRECTORY = "../assets";

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
        <a href="test.php?blockId=$blockId" class="hidden-link stretched-link">Пройти тему</a>
    </div>
</div>
HTML;

    echo $html;
}

