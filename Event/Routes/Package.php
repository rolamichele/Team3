<?php
require_once "../controller/PackageController.php";
$data = json_decode(file_get_contents("php://input"), true);
if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['id'])) {
    GetPackageById();
}
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    GetPackages();
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_GET['review'])) {
    AddReview();
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    CreatePackage();
}
if ($_SERVER['REQUEST_METHOD'] == "PUT" && isset($_GET['id'])) {
    UpdatePackage();
}
if ($_SERVER['REQUEST_METHOD'] == "DELETE" && isset($_GET['id'])) {
    DeletePackage();
}
