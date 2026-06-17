<?php

require_once '../config/DB.php';

function getAllcategories($limit, $offset){

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
        
        $redis->setex($cacheKey, CACHE_EXPIRATION, json_encode($result));

        response(200, "Success", $result);

    } catch (Exception $e) {
        response(500, $e->getMessage());
    }

function getcategoriesById($id) {

    global $pdo;

    $getById = $pdo->prepare("SELECT * FROM categories WHERE CategoryID = ?");

    $getById->execute([$id]);

    return $getById->fetch();
}


function update_categorie($id, $name, $description){

     global $connection;

    $sql = "UPDATE categories
            SET Name = ?, Description = ?
            WHERE CategoryID = ?";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $name,
        $description,
        $id
    ]);

}


function delete($id)
{
    
    global $connection;
    
    $sql = "DELETE FROM categories WHERE CategoryID = ?";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([$id]);
}

function insert($name, $description)
{
     global $connection;

    $sql = "INSERT INTO categories (Name, Description)
            VALUES (?, ?)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $name,
        $description
    ]);
}
