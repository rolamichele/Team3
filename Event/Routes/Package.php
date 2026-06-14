<?php
require_once "../controller/PackageController.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    getAllPackages();
}

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['id']) ) {
    getPackageById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    createPackage($data);
}

if ($_SERVER['REQUEST_METHOD'] == "PUT" && isset($_GET['id'])) {
    updatePackage($_GET['id'], $data);
}

if ($_SERVER['REQUEST_METHOD'] == "DELETE" && isset($_GET['id'])) {
    deletePackage($_GET['id']);
}
