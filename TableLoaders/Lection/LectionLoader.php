<?php



require_once "../../Services/pdoConnection.php";

$data =  $_POST['component'];

$blockId = $data['blockId'];
$lectureData = $data['data'];


global $dbh;
try{
    $query = $dbh->prepare("INSERT INTO components (components.block_id, components.data, type) VALUES(:blockId, :data, 'Lecture')");
    $query->bindParam(':blockId', $blockId);
    $query->bindParam(':data', $lectureData);
    $query->execute();
}
catch(PDOException $e){
    echo $e->getMessage();
}

