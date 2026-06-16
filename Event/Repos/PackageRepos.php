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
function canReview($orderId, $userId) {
    global $pdo;

    $check = $pdo->prepare("SELECT OrderID FROM orders WHERE OrderID = ? AND UserID = ? AND Status = 'Completed' AND IsReviewed = 0");
    $check->execute([$orderId, $userId]);
    return $check->fetch(PDO::FETCH_ASSOC);
}

function addReview($orderId, $userId, $rating, $comment) {
    global $pdo;
    if (!canReview($orderId, $userId)) {
        return false;
    }
    $update = $pdo->prepare("UPDATE orders SET Rating = ?, Comment = ?, IsReviewed = 1 WHERE OrderID = ? AND UserID = ?");
    return $update->execute([$rating,$comment,$orderId,$userId]);
}
