<?php

require_once "../controller/VendorController.php";

$data = json_decode(file_get_contents("php://input"), true);
$path=$_SERVER['PATH_INFO']?? '/';
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "POST" &&$path=='/vendor/register') {
    vendorRegister($data);
}
if ($method == "POST" &&$path=='/vendor/login') {
    vendorLogin($data);
}
if ($method == "GET" &&$path=='/vendor/me') {
    getVendorMe();
}

if ($method == "GET") {
     getAllVendors();
}
if ($method == "PATCH") {
    VendorStatus($connection);
}
