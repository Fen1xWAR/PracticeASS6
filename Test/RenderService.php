<?php
require_once "../pdoConnection.php";
if(isset($_GET['componentId'])){
    $componentId = $_GET['componentId'];
    $json = getComponentJson($componentId);
    if ($json){
        try{
            $component = json_decode($json,true);
            if(isset($component['Images'])){
                downloadImagesByComponentIdToDirectory($componentId);
            }
            $component['Type'] == "Lecture" ? renderLecture($component) : http_response_code(500);
        }
        catch(Exception $e){
            http_response_code(400);
        }

    }
    else{
        http_response_code(404);
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
function getComponentJson($componentId){

    global $dbh;
    $query = $dbh->prepare("SELECT data FROM components WHERE component_id = :id");
    $query->bindParam(':id', $componentId);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC)["data"];
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



function renderLecture($block): string
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