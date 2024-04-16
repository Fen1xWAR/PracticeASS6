<?php
require_once "../pdoConnection.php";
if (isset($_GET['componentId'])) {
    $componentId = $_GET['componentId'];
    try{
        $data = getComponentJson($componentId);
        if ($data) {
            try {
                $component = json_decode($data['data'], true);
                if (isset($component['Images'])) {
                    downloadImagesByComponentIdToDirectory($componentId);
                }
                $data['type'] == "Lecture" ? renderLecture($component) : http_response_code(500);
            } catch (Exception $e) {
                http_response_code(400);
            }

        } else {
            http_response_code(404);
        }
    }
    catch (Exception $exception){
        http_response_code(500);
    }



//    $json = file_get_contents("block".-$componentId.".json");
//    $block = json_decode($json, true);
//    if($block){
//        echo renderLecture($block);
//
//    }
//   else{
//      http_response_code(404);
//   }

}


if(isset($_GET['blockId'])){

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
    $directory = "../assets";

    // Create the directory if it doesn't exist
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    global $dbh;
    // Get the image records for the given component ID
    $stmt = $dbh->prepare('SELECT image_name, image_content FROM images WHERE component_id = :component_id');
    $stmt->bindParam(':component_id', $component_id);
    $stmt->execute();
    $images = $stmt->fetchAll();

    // Check if any images were found

    // Download each image and save it to the directory
    foreach ($images as $image) {
        // Generate a unique filename for the image
        $filename = $image['image_name'];

        // Save the image content to a file
        file_put_contents("$directory/$filename", $image['image_content']);

    }
}


function generateNavButton($componentButtonType, $componentId): void
{
    $svgs = ['<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">' .
        '<path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>' .
        '</svg>', ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/> </svg>'];
    $svgCode = $componentButtonType === "L" ? $svgs[0] : $svgs[1]; // replace with the desired SVG code

    $listItem = '<li class="w-100 d-flex justify-content-center m-0 p-0 nav-item">';
    $link = '<a class="nav-link w-100 d-flex align-items-center  justify-content-center" href="#" style="min-width: 40px; min-height:40px" data-component-id="' . $componentId . '">';
    $link .= $svgCode;
    $listItem .= $link . "</a></li>";

    echo $listItem;

}


function renderLecture($block): void
{
    $lectureElement = "<div class='card'>";

    $titleElement = "<div class='card-header'>" . $block['Title'] . "</div>";

    $textElement = "<div class='p-4 card-body'>";

    $images = $block['Images'] ?? [];
    $text = $block['Text'];

    foreach ($images as $image) {
        $imagePosition = $image['mark'];
        $textBefore = substr($text, 0, $imagePosition);
        $imgElement = "<img style='max-width: 100%;' src='../assets/" . $image['src'] . "'>";
        $imgDivElement = "<div>" . $imgElement . "</div>";
        $textElement .= $textBefore . $imgDivElement;
        $text = substr($text, $imagePosition);
    }

    $textElement .= $text;


    $lectureElement .= $titleElement . $textElement;

    echo $lectureElement;
}