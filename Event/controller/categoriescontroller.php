<?php

require_once "../Repos/categoriesRepos.php";

    function getAll()
    {
        try {

            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

            $offset = ($page - 1) * $limit;

            $result = getAllcategories($limit, $offset);

            echo json_encode([
                "status" => true,
                "data" => $result
            ]);

      catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

     function getById($id)
    {
        try {

            $row = getcategoriesById($id);

            if (!$row) {

                http_response_code(404);

                echo json_encode([
                    "status" => false,
                    "message" => "Category Not Found"
                ]);

                return;
            }

            echo json_encode([
                "status" => true,
                "data" => $row
            ]);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    function update($id)
    {
        try {

            $body = json_decode(file_get_contents("php://input"), true);

            update_categorie(
                $id,
                $body['Name'],
                $body['Description']
            );

            echo json_encode([
                "status" => true,
                "message" => "Category Updated Successfully"
            ]);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

 function delete($id)
    {
        try {

            delete($id);

            echo json_encode([
                "status" => true,
                "message" => "Category Deleted Successfully"
            ]);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
