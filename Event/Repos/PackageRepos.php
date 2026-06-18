<?php
require_once __DIR__ . '/../config/DB.php';

function getAllPackages($page = 1, $limit = 10) {
    global $connection;
    $offset = (int)(($page - 1) * $limit);
    $limit  = (int)$limit;
    $getAll = $connection->query("SELECT * FROM `packages` LIMIT $limit OFFSET $offset");
    return $getAll->fetchAll(PDO::FETCH_ASSOC);
}

function getPackageById($id) {
    global $connection;
    $getById = $connection->prepare("SELECT * FROM `packages` WHERE PackageID = ?");
    $getById->execute([$id]);
    return $getById->fetch(PDO::FETCH_ASSOC);
}

function createPackage($vendorId, $title, $description, $price, $activity_status = 'Active') {
    global $connection;
    $create = $connection->prepare("INSERT INTO `packages` (VendorID, Title, Description, Price, ActivityStatus) VALUES (?, ?, ?, ?, ?)");
    $create->execute([$vendorId, $title, $description, $price, $activity_status]);
    return $connection->lastInsertId();
}

function updatePackage($id, $title, $description, $price, $activity_status) {
    global $connection;
    $update = $connection->prepare("UPDATE `packages` SET Title = ?, Description = ?, Price = ?, ActivityStatus = ? WHERE PackageID = ?");
    return $update->execute([$title, $description, $price, $activity_status, $id]);
}

function deletePackage($id) {
    global $connection;
    $delete = $connection->prepare("DELETE FROM `packages` WHERE PackageID = ?");
    return $delete->execute([$id]);
}

function canReview($vendorId, $userId) {
    global $connection;
    $check = $connection->prepare("SELECT OrderID FROM orders 
    WHERE VendorID = ? AND UserID = ? AND Status = 'Completed'");
    $check->execute([$vendorId, $userId]);
    return $check->fetch(PDO::FETCH_ASSOC);
}

function addReview($orderId, $userId, $rating, $comment) {
    global $connection;
    $canReview = canReview($orderId, $userId);
    if (!$canReview){
        return false;
    }
    $query =$connection->prepare("SELECT * FROM reviews WHERE OrderID=?");

    $query->execute([$canReview['OrderID']]);
    $Query = $query->fetch(PDO::FETCH_ASSOC);
        if ($Query){
        return false;
    }
    $update = $connection->prepare("INSERT INTO reviews VALUES (Null,?,?,?,?,?) ");
    return $update->execute([$userId, $orderId, $canReview['OrderID'], $rating,$comment]);
}
