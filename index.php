<?php
require_once "Services/pdoConnection.php";
$request = $_SERVER["REQUEST_URI"];
//echo $request;
switch ($request) {
    case "/":

    case "":
        require "RoutingPages/home.php";
        break;
    case "/profile":
        require "RoutingPages/userProfile.php";
        break;
    case "/login":
        require "RoutingPages/login.php";
        break;
    case "/education":
        require "RoutingPages/blocks.php";
        break;
    case "/section":
        require "RoutingPages/test.php";
        break;

}
