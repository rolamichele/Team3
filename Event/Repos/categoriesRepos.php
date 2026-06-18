<?php
require_once '../config/DB.php';

function getAllcategories()
{   
    global $connection; 
    
    $getAll = $connection->prepare("SELECT * FROM categories ORDER BY CategoryID DESC");
    $getAll->execute();
    
    return $getAll->fetchAll(PDO::FETCH_ASSOC); 
}

function getcategoriesById($id)
{
    global $connection;
    
    
    $getById = $connection->prepare("SELECT * FROM categories WHERE CategoryID = ?");
    $getById->execute([$id]);
    
    return $getById->fetch(PDO::FETCH_ASSOC);
}

function update_categorie($id, $name, $description)
{
    global $connection;
    
    
    $sql = "UPDATE categories 
            SET Name = ?, Description = ? 
            WHERE CategoryID = ?";

    $stmt = $connection->prepare($sql);

    return $stmt->execute([
        $name,
        $description,
        $id
    ]);
}

function delete_categorie($id) 
{
    global $connection;
    
    $sql = "DELETE FROM categories WHERE CategoryID = ?";

    $stmt = $connection->prepare($sql);

    return $stmt->execute([$id]);
}

function insert_category($name, $description) 
{
    global $connection;

    $sql = "INSERT INTO categories (Name, Description) VALUES (?, ?)";

    $stmt = $connection->prepare($sql);

    return $stmt->execute([
        $name,
        $description
    ]);
}
