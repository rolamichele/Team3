<?php
require_once __DIR__ . "/../controller/PackageController.php";

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method == "GET" && isset($_GET['id'])) {
    GET_PACKAGE_BY_ID();
}

else if ($method == "GET") {
    GET_PACKAGES();
}

else if ($method == "POST" && isset($_GET['review'])) {
    ADD_REVIEW();
}

else if ($method == "POST") {
    CREATE_PACKAGE();
}

else if ($method == "PUT" && isset($_GET['id'])) {
    UPDATE_PACKAGE();
}

else if ($method == "DELETE" && isset($_GET['id'])) {
    DELETE_PACKAGE();
}
?>
