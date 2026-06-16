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
