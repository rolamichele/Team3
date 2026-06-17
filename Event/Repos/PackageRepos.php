<?php
require_once __DIR__ . '/../config/DB.php';

function getAllPackages($page = 1, $limit = 10) {
    global $pdo;
    $offset = (int)(($page - 1) * $limit);
    $limit  = (int)$limit;
    $getAll = $pdo->query("SELECT * FROM `packages` LIMIT $limit OFFSET $offset");
    return $getAll->fetchAll();
}

function getPackageById($id) {
    global $pdo;
    $getById = $pdo->prepare("SELECT * FROM `packages` WHERE PackageID = ?");
    $getById->execute([$id]);
    return $getById->fetch();
}
function createPackage($vendorId, $title, $description, $price, $activity_status = 'Active') {
    global $pdo;
    $create = $pdo->prepare("INSERT INTO `packages` (VendorID, Title, Description, Price, ActivityStatus) VALUES (?, ?, ?, ?, ?)");
    $create->execute([$vendorId, $title, $description, $price, $activity_status]);
    return $pdo->lastInsertId();
}

function updatePackage($id, $title, $description, $price, $activity_status) {
    global $pdo;
    $update = $pdo->prepare("UPDATE `packages` SET Title = ?, Description = ?, Price = ?, ActivityStatus = ? WHERE PackageID = ?");
    return $update->execute([$title, $description, $price, $activity_status, $id]);
}

function deletePackage($id) {
    global $pdo;
    $delete = $pdo->prepare("DELETE FROM `packages` WHERE PackageID = ?");
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
    return $update->execute([$rating, $comment, $orderId, $userId]);
}
