<?php

require_once "../pdoConnection.php";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Get the form data
$component_id = $_POST['component_id'];
$image_name =$_FILES['image_file']['name'];
$image_file = $_FILES['image_file']['tmp_name'];
$image_size = $_FILES['image_file']['size'];
$image_type = $_FILES['image_file']['type'];

// Check if the file is an image
if (!in_array($image_type, ['image/jpeg', 'image/png', 'image/gif'])) {
    die('Invalid file type. Only JPG, PNG, and GIF are allowed.');
}

// Check if the file size is less than 5MB
if ($image_size > 5000000) {
    die('File size is too large. Maximum allowed size is 5MB.');
}

// Read the image file
$image_content = file_get_contents($image_file);

// Insert the image into the database
$stmt = $dbh->prepare('INSERT INTO images (component_id, image_name, image_content) VALUES (:component_id, :image_name, :image_content)');
$stmt->bindParam(':component_id', $component_id);
$stmt->bindParam(':image_name', $image_name);
$stmt->bindParam(':image_content', $image_content);
$stmt->execute();

// Redirect to the upload form
header('Location: ImageForm.html');
exit;

?>