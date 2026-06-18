<?php

require_once "../Config/DB.php";
require_once "../controller/OrderController.php";

$path = $_SERVER['PATH_INFO'] ;
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method == "GET" AND $path == "/orders" AND isset($_GET['OrderID'])){
    OrderID ($_GET['OrderID']);
}elseif($method == "GET" AND $path == "/orders"){
    orders ();
}elseif ($method == "POST" AND $path == "/orders"){
    AddOrder($data);
}elseif ($method == "PATCH" AND $path == "/orders" AND isset($_GET['OrderID'])){
    EditOrder($_GET['OrderID'],
    $data['packageid'],
    $data['StartTime'],
    $data['endtime'],
    $data['status'] ?? "pending"
);
}elseif ($method == "DELETE" AND $path == "/orders" AND isset($_GET['OrderID'])){
    OrderDel ($_GET['OrderID']);
}elseif ($method == "GET" AND $path == "/wallet"){
    Wallet();
}
?>