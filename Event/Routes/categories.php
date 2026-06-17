<?php

require_once "../Controllers/CategoriesController.php";

$categoryController = new CategoriesController();

$requestMethod = $_SERVER['REQUEST_METHOD'];

$id = $_GET['id'] ?? null;

switch ($requestMethod) {

    case 'GET':

        if ($id) {
            $categoryController->getById($id);
        } else {
            $categoryController->getAll();
        }

        break;

    case 'PUT':

        if (!$id) {
            echo json_encode([
                "status" => false,
                "message" => "Category ID is required"
            ]);
            exit;
        }

        $categoryController->update($id);
        break;

    case 'DELETE':

        if (!$id) {
            echo json_encode([
                "status" => false,
                "message" => "Category ID is required"
            ]);
            exit;
        }

        $categoryController->delete($id);
        break;

    default:

        http_response_code(405);

        echo json_encode([
            "status" => false,
            "message" => "Method Not Allowed"
        ]);
}