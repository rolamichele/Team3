<?php
require_once __DIR__ . "/../repos/PackageRepos.php";
require_once __DIR__ . "/../helper/response.php";
require_once __DIR__ . "/../config/cache.php";
require_once __DIR__ . "/../helper/Jwt.php";

function GET_PACKAGES()
{
    global $redis;
    $page  = $_GET['page']  ?? 1;
    $limit = $_GET['limit'] ?? 10;
    $cacheKey = "packages_page_{$page}_limit_{$limit}";
    $cached = $redis->get($cacheKey);
    if ($cached) {
        response(200, "Packages retrieved from cache", json_decode($cached, true));
        return;
    }
    $packages = getAllPackages($page, $limit);
    if ($packages) {
        $redis->setex($cacheKey, 60, json_encode($packages));
        response(200, "Packages retrieved successfully", $packages);
    } else {
        response(404, "No packages found");
    }
}

function GET_PACKAGE_BY_ID()
{
    global $redis;
    $id = $_GET['id'];
    $cacheKey = "package_{$id}";
    $cached = $redis->get($cacheKey);
    if ($cached) {
        response(200, "Package retrieved from cache", json_decode($cached, true));
        return;
    }
    $package = getPackageById($id);
    if ($package) {
        $redis->setex($cacheKey, 60, json_encode($package));
        response(200, "Package retrieved successfully", $package);
    } else {
        response(404, "Package not found");
    }
}

function CREATE_PACKAGE()
{
    global $redis;
    $token = VerifyToken();
    require_vendor($token);

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        response(400, "All fields are required");
        return;
    }
    $packageId = createPackage(
        $data['vendorId'],
        $data['title'],
        $data['description'],
        $data['price'],
        $data['activityStatus'] ?? 'Active'
    );
    foreach ($redis->keys("packages_page_*") as $key) {
        $redis->del($key);
    }
    response(201, "Package created successfully", [
        "PackageID" => $packageId
    ]);
}

function UPDATE_PACKAGE()
{
    global $redis;
    $token = VerifyToken();
    require_vendor($token);

    $id   = $_GET['id'];
    $data = json_decode(file_get_contents("php://input"), true);
    $package = getPackageById($id);
    if (!$package) {
        response(404, "Package not found");
        return;
    }
    updatePackage(
        $id,
        $data['title']         ?? $package['Title'],
        $data['description']   ?? $package['Description'],
        $data['price']         ?? $package['Price'],
        $data['activityStatus'] ?? $package['ActivityStatus']
    );
    $redis->del("package_{$id}");
    foreach ($redis->keys("packages_page_*") as $key) {
        $redis->del($key);
    }
    response(200, "Package updated successfully");
}

function DELETE_PACKAGE()
{
    global $redis;
    $token = VerifyToken();
    require_vendor($token);

    $id = $_GET['id'];
    deletePackage($id);
    $redis->del("package_{$id}");
    foreach ($redis->keys("packages_page_*") as $key) {
        $redis->del($key);
    }
    response(200, "Package deleted successfully");
}

function ADD_REVIEW()
{
    $token = VerifyToken();

    if ($token->role !== 'Client') {
        response(403, "Access denied. Only users can review.");
        return;
    }

    $data   = json_decode(file_get_contents("php://input"), true);
    $result = addReview($_GET['id'], $token->user_id, $data['rating'], $data['comment']);

    if ($result) {
        response(200, "Review added successfully");
    } else {
        response(400, "You can only review a completed order that hasn't been reviewed yet.");
    }
}
