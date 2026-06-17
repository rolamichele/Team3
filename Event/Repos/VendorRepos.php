<?php
require_once "../config/DB.php";
function getVendorByEmail($email)
{
    global $connection;
    $select=$connection->prepare("select * from vendors where Email = ?");
            $select->execute([$email]);
            return $select->fetch(PDO::FETCH_ASSOC);
}
function getVendorById($id)
{
    global $connection;
    $select=$connection->prepare("select VendorID, CategoryID, Name, Email, PhoneNumber,
                Description, ActivityStatus, Role, Review from vendors where VendorID = ?");
            $select->execute([$id]);
            return $select->fetch(PDO::FETCH_ASSOC);
}
function createVendor($data)
{
    global $connection;
    $insert = "INSERT INTO vendors (CategoryID, Name, Email, Password, PhoneNumber, Description, ActivityStatus, Role)
     VALUES (?, ?, ?, ?, ?, ?, 'Inactive', 'Vendor')";
    $query = $connection->prepare($insert);
    return $query->execute([
        $data['CategoryID'],
        $data['Name'],
        $data['Email'],
        password_hash($data['Password'], PASSWORD_DEFAULT),
        $data['PhoneNumber'],
        $data['Description']
    ]);
}

function getVendors($search, $categoryId, $location, $limit, $offset)
{
    global $connection;

    $query = "
        SELECT DISTINCT v.*
        FROM vendors v
        LEFT JOIN location l ON v.VendorID = l.VendorID
        WHERE 1=1
    ";

    $params = [];

    if (!empty($search)) {
        $query .= " AND v.Name LIKE :search";
        $params['search'] = "%$search%";
    }

    if (!empty($categoryId)) {
        $query .= " AND v.CategoryID = :categoryId";
        $params['categoryId'] = $categoryId;
    }

    if (!empty($location)) {
        $query .= "
            AND (
                l.City LIKE :location
                OR l.Governorate LIKE :location
                OR l.Country LIKE :location
            )
        ";
        $params['location'] = "%$location%";
    }

    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = $connection->prepare($query);

    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function countVendors($search, $categoryId, $location)
{
    global $connection;

    $query = "
        SELECT COUNT(*) as total
        FROM vendors v
        WHERE 1=1
    ";

    if ($search) {
        $query .= " AND v.Name LIKE '%$search%'";
    }

    if ($categoryId) {
        $query .= " AND v.CategoryID = $categoryId";
    }

    
    if ($location) {
        $query .= "
            AND v.VendorID IN (
                SELECT VendorID
                FROM location
                WHERE City LIKE '%$location%'
                OR Governorate LIKE '%$location%'
                OR Country LIKE '%$location%'
            )
        ";
    }

    $stmt = $connection->prepare($query);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int)$result['total'];
}
function ActivateVendor($connection, $id)
{
    // 1. get current status
    $query = "SELECT ActivityStatus FROM vendors WHERE VendorID = :id";
    
    $stmt = $connection->prepare($query);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendor) {
        return false;
    }

    $currentStatus = $vendor['ActivityStatus'];

    
    $newStatus = ($currentStatus === 'Active') ? 'Inactive' : 'Active';

    // 3. update
    $updateQuery = "UPDATE vendors SET ActivityStatus = :status WHERE VendorID = :id";

    $updateStmt = $connection->prepare($updateQuery);

    $updateStmt->bindValue(":status", $newStatus, PDO::PARAM_STR);
    $updateStmt->bindValue(":id", $id, PDO::PARAM_INT);

    return $updateStmt->execute();
}