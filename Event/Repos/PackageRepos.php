<?php
require_once '../config/DB.php';
function getAllPackages() {
    global $pdo;
    $getAll = $pdo->prepare("SELECT * FROM `Package` WHERE ActivityStat = 1");
    $getAll->execute();
    return $getAll->fetchAll();
}

function getPackageById($id) {
    global $pdo;
    $getById = $pdo->prepare("SELECT * FROM `Package` WHERE PackageID = ?");
    $getById->execute([$id]);
    return $getById->fetch();
}

function createPackage($title, $description, $price, $activity_stat = 1) {
    global $pdo;
    $create = $pdo->prepare("INSERT INTO `Package` (Title, Description, Price, ActivityStat) VALUES (?, ?, ?, ?)");
    $create->execute([$title, $description, $price, $activity_stat]);
    return $pdo->lastInsertId();
}

function updatePackage($id, $title, $description, $price, $activity_stat) {
    global $pdo;
    $update = $pdo->prepare("UPDATE `Package` SET Title = ?, Description = ?, Price = ?, ActivityStat = ? WHERE PackageID = ?");
    return $update->execute([$title, $description, $price, $activity_stat, $id]);
}

function deletePackage($id) {
    global $pdo;
    $delete = $pdo->prepare("DELETE FROM `Package` WHERE PackageID = ?");
    return $delete->execute([$id]);
}
