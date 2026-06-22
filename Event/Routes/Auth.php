<?php
require_once '../controller/AuthController.php';
require_once  '../vendor/autoload.php';

$data=json_decode(file_get_contents('php://input'),true);
$path=$_SERVER['PATH_INFO'];
if ($_SERVER['REQUEST_METHOD'] == "POST" &&$path=='/vendor/register') {
    vendorRegister($data);
}
if ($_SERVER['REQUEST_METHOD'] == "POST" &&$path=='/vendor/login') {
    vendorLogin($data);
}
if ($_SERVER['REQUEST_METHOD'] == "GET" &&$path=='/vendor/me') {
    getVendorMe();
}
if ($_SERVER['REQUEST_METHOD']=="PATCH"&&$path=='/admin/toggleVendorStatus'){
    toggleVendorStatus($data['vendorId']);
}
if ($_SERVER['REQUEST_METHOD']=="PATCH"&&$path=='/changePassword'){
    changePassword($data);
}
if ($_SERVER['REQUEST_METHOD']=="POST"&&$path=='/signup'){
    signup($data);
}
if ($_SERVER['REQUEST_METHOD']=="POST"&&$path=='/login'){
    login($data);
}
if ($_SERVER['REQUEST_METHOD']=="GET"&&$path=='/me'){
    getMe();
}
?>
