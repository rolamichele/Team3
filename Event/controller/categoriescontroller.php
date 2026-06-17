<?php

require_once "../Repos/categoriesRepos.php";
require_once "../helper/response.php";
require_once "../config/cache.php";

function getAll()
{
    global $redis;

    try {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $cacheKey = "categories:page:{$page}:limit:{$limit}";

        $cachedData = $redis->get($cacheKey);

        if ($cachedData) {
            response(200, "Success (From Redis Cache)", json_decode($cachedData, true));
            return;
        }

        $result = getAllcategories($limit, $offset);

        $expiration = defined('CACHE_EXPIRATION') ? CACHE_EXPIRATION : 3600;
        $redis->setex($cacheKey, $expiration, json_encode($result));

        response(200, "Success", $result);

    } catch (Exception $e) {
        response(500, $e->getMessage());
    }
}

function getById($id)
{
    try {
        $row = getcategoriesById($id);

        if (!$row) {
            response(404, "Category Not Found");
            return;
        }

        response(200, "Success", $row);

    } catch (Exception $e) {
        response(500, $e->getMessage());
    }
}

function insert($data) {
  
    global $redis; 

    try {
        $name = $data['Name'] ?? null;
        $description = $data['Description'] ?? null;

        if (!$name) {
            response(400, "Category Name is required");
            return;
        }

        
        insert_category($name, $description);

        
        if (isset($redis)) {
            $redis->del('categories:all');
        }

       response(201, "Category Created Successfully.");

    } catch (Exception $e) {
        response(500, $e->getMessage());
    }
}

function update($id) {
    global $redis; 
    
    try {
        $body = json_decode(file_get_contents("php://input"), true);
        $name = $body['Name'] ?? null;
        $description = $body['Description'] ?? null;

        if (!$name) {
            response(400, "Category Name is required");
            return;
        }  
        update_categorie($id, $name, $description);

        if (isset($redis)) {
            $redis->del(['categories:all', 'category:' . $id]);
        }
        
        response(200, "Category Updated Successfully.");
        
    } catch (Exception $e) {
        response(500, $e->getMessage());
    }
}
function deleteCategory($id) 
{
    global $redis;
    try {
     
        delete_categorie($id); 

        if (isset($redis)) {
            $redis->del(['categories:all', 'category:' . $id]);
        }
    
        response(200, "Category Deleted Successfully.");

    } catch (Exception $e) {
        response(500, $e->getMessage());
    }
}
