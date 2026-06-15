<?php

require "./Config/DB.php";
require "./OrderController/OrderController.php";

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method == "GET" AND $path == "/order" AND isset($_GET['OrderID'])){
    GetOrderById ($_GET['OrderID']);
}elseif($method == "GET" AND $path == "/order"){
    GetAllOrders ();
}elseif($method == "POST" AND $path == "/order"){
    CreateOrder ($data);
}elseif ($method == "PATCH" AND $path == "/order" AND isset($_GET['OrderID'])){
    UpdateOrder ($_GET['OrderID'],
    $data['status'],
    $data['StartTime']);
}elseif ($method == "DELETE" AND $path == "/order" AND isset($_GET['OrderID'])){
    CancelOrder ($_GET['OrderID']);
}
?>