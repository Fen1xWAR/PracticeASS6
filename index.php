
<link rel="icon" type="image/x-icon" href="assets/StockImg/favicon.ico">
<link rel="stylesheet" href="bootstrap-5.3.3/bootstrap-5.3.3/css/bootstrap.css">
<script src="script.js"></script>

<?php
require_once "Services/pdoConnection.php";
$request = $_SERVER["REQUEST_URI"];
switch ($request) {
    case "/":

    case "":
        require "RoutingPages/home.php";
        break;
    case "/materials":
        require "RoutingPages/materials.php";
        break;
    case "/profile":
        require "RoutingPages/userProfile.php";
        break;
    case "/login":
        require "RoutingPages/login.php";
        break;
    case "/register":
        require "RoutingPages/register.php";
        break;
    case "/education":
        require "RoutingPages/blocks.php";
        break;
    case "/section":
        require "RoutingPages/test.php";
        break;
    case (preg_match('/^\/material\/(\d+)$/', $request, $matches) ? true : false):
        $materialId = $matches[1];
        require "material_view.php"; // файл для просмотра материала
        break;
    default:
        require "RoutingPages/404.php";
        break;


}
