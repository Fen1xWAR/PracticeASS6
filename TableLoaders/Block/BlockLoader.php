<?php

require_once "../../Services/pdoConnection.php";


global $dbh;

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Get the form data
$block_name = $_POST['block_name'];
$block_description = $_POST['block_description'];
$image_file = $_FILES['block_image']['tmp_name'];
$image_name = $_FILES['block_image']['name'];
$image_size = $_FILES['block_image']['size'];
$image_type = $_FILES['block_image']['type'];

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

// Insert the image into the database
$stmt = $dbh->prepare('INSERT INTO blocks (block_name, block_description, block_image_name,block_image) VALUES (:block_name,:block_description,:block_image_name,:block_image)');
$stmt->bindParam(':block_name', $block_name);
$stmt->bindParam(':block_description', $block_description);
$stmt->bindParam(':block_image_name', $image_name);
$stmt->bindParam(':block_image', $image_content);
$stmt->execute();

// Redirect to the upload form
header('Location: BlockForm.html');
exit;

?>