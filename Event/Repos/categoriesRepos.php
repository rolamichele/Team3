<?php

require_once '../config/DB.php';

function getAllcategories(){

    global $pdo;

    $getAll = $pdo->prepare("SELECT * FROM categories WHERE 1");

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

    global $pdo;

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
    global $pdo;

    $sql = "DELETE FROM categories WHERE CategoryID = ?";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([$id]);
}
