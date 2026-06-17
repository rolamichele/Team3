<?php

require_once "../controller/VendorController.php";

$data = json_decode(file_get_contents("php://input"), true);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

if ($method == "GET" && isset($_GET['vendor_id'])) {
    getVendorById($_GET['vendor_id']); 
} 
if ($method == "GET" && $action == "active-vendors") {
        getActiveVendors();
}
if ($method == "GET" && $action == "top-rated") {
        getTopRatedVendors();
}
if ($method == "GET") {

    getAllVendors(); 
}
if ($method == "PATCH") {
    VendorStatus($connection);
}
if ($method== "PUT") {
    updateVendor($_GET['vendor_id']);
}
