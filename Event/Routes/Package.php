<?php
require_once "../controllers/PackageController.php";

$data = json_decode(file_get_contents("php://input"), true);
$path = $_SERVER['PATH_INFO'];

if ($_SERVER['REQUEST_METHOD'] == "GET" && $path == '/package') {
    getAllPackages();
}

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['id']) && $path == '/package') {
    getPackageById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && $path == '/package') {
    createPackage($data);
}

if ($_SERVER['REQUEST_METHOD'] == "PUT" && $path == '/package' && isset($_GET['id'])) {
    updatePackage($_GET['id'], $data);
}

if ($_SERVER['REQUEST_METHOD'] == "DELETE" && $path == '/package' && isset($_GET['id'])) {
    deletePackage($_GET['id']);
}
