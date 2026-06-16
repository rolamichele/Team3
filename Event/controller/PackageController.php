<?php
require __DIR__ . "/../repos/PackageRepo.php";
require __DIR__ . "/../helper/response.php";

function GetPackages()
{   $packages = getAllPackages();
    if ($packages) {
        response(200, "Packages retrieved successfully", $packages);
    } else {
        response(404, "No packages found");
    }
}
function GetPackageById()
{  $id = $_GET['id'];
    $package = getPackageById($id);
    if ($package == null) {
        response(404, "Package not found");
    }
    response(200, "Package retrieved successfully", $package);
}
function CreatePackage()
{
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) {
        response(400, "All fields are required");
    }

    $title = $data['title'];
    $description = $data['description'];
    $price = $data['price'];
    $activityStat = $data['activityStat'] ?? 1;

    $packageId = createPackage($title,$description,$price,$activityStat);
    if ($packageId) {
        response(201, "Package created successfully", [
            "PackageID" => $packageId
        ]);
    } else {
        response(500, "Package could not be created");
    }
}
function UpdatePackage()
{
    $id = $_GET['id'];
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) {
        response(400, "No data provided");
    }
    $package = getPackageById($id);
    if ($package == null) {
        response(404, "Package not found");
    }

    $title = $data['title'] ?? $package['Title'];
    $description = $data['description'] ?? $package['Description'];
    $price = $data['price'] ?? $package['Price'];
    $activityStat = $data['activityStat'] ?? $package['ActivityStat'];

    $result = updatePackage($id,$title,$description,$price,$activityStat);

    if ($result) {
        response(200, "Package updated successfully");
    } else {
        response(500, "Package could not be updated");
    }
}
function DeletePackage()
{   $id = $_GET['id'];
    $result = deletePackage($id);
    if ($result) {
        response(200, "Package deleted successfully");
    } else {
        response(500, "Package could not be deleted");
    }
}
function AddReview()
{
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) {
        response(400, "All fields are required");
    }
    $orderId = $data['order_id'];
    $userId  = $data['user_id']; 
    $rating  = $data['rating'];
    $comment = $data['comment'];

    if ($rating < 1 || $rating > 5) {
        response(400, "Rating must be between 1 and 5");
    }
    $canReview = canReview($orderId, $userId);
    if (!$canReview) {
        response(403, "You are not allowed to review this order");
    }
    $result = addReview($orderId, $userId, $rating, $comment);
    if ($result) {
        response(201, "Review added successfully");
    } else {
        response(500, "Failed to add review");
    }
}
