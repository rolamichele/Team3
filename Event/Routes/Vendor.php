<?php

require_once "../controller/VendorController.php";

$data = json_decode(file_get_contents("php://input"), true);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
     getAllVendors();
}
if ($method == "PATCH") {
    VendorStatus($connection);
}
