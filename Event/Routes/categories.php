<?php

require_once "../Controller/categoriescontroller.php";

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method == "GET" && isset($_GET['id'])) {
    getById($_GET['id']);
}
else if ($method == "GET") {
    getAll();
}
else if ($method == "POST") {
    insert($data);
}
else if ($method == "PUT" && isset($_GET['id'])) {
    update($_GET['id'],$data);
}
else if ($method == "DELETE" && isset($_GET['id'])) {
    deleteCategory($_GET['id']);
}
else {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method Not Allowed"
    ]);
}
