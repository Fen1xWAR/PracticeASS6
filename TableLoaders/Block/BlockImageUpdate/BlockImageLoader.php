<?php

require_once "../../../Services/pdoConnection.php";


$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Get the form data
$block_id = $_POST['block_id'];
$image_file = $_FILES['image']['tmp_name'];
$image_name = $_FILES['image']['name'];
$image_size = $_FILES['image']['size'];
$image_type = $_FILES['image']['type'];

// Check if the file is an image
if ($image_type != 'image/png') {
    die('Invalid file type. Only PNG  allowed.');
}

// Check if the file size is less than 5MB
if ($image_size > 5000000) {
    die('File size is too large. Maximum allowed size is 5MB.');
}

// Read the image file
$image_content = file_get_contents($image_file);
global  $dbh;
try{
    $stmt = $dbh->prepare('UPDATE blocks SET block_image_name = :blockImageName, block_image = :blockImage WHERE block_id = :block_id');
    $stmt->bindParam(":block_id", $block_id);
    $stmt->bindParam(':blockImageName', $image_name);
    $stmt->bindParam(':blockImage', $image_content);
    $stmt->execute();

}
// Insert the image into the database
catch(PDOException $e){
    echo $e->getMessage();
}
// Redirect to the upload form

?>