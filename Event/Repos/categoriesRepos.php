<?php

require_once '../config/DB.php';

function getAllcategories($limit, $offset){

    global $connection;

    $getAll = $pdo->prepare(
        "SELECT * FROM categories
         LIMIT ? OFFSET ?"
    );

    $getAll->bindValue(1, $limit, PDO::PARAM_INT);
    $getAll->bindValue(2, $offset, PDO::PARAM_INT);

    $getAll->execute();

    return $getAll->fetchAll();
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
