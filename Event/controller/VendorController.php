<?php
require_once "../helper/response.php";
require_once "../Repos/vendorRepos.php";
require_once "../config/cache.php";

function getAllVendors()
{
    global $redis;

    $search = $_GET['search'] ?? null;
    $categoryId = $_GET['categoryId'] ?? null;
    $location = $_GET['location'] ?? null;

    // Pagination
    $page = (int)($_GET['page'] ?? 1);
    $pageSize = (int)($_GET['pageSize'] ?? 10);

    $page = max(1, $page);
    $pageSize = max(1, min(100, $pageSize));

    $offset = ($page - 1) * $pageSize;

    // Cache Key
    $cacheKey = "vendors:" . md5(json_encode($_GET));

    // Check Cache
    $cachedData = $redis->get($cacheKey);

    if ($cachedData) {

        $cachedData = json_decode($cachedData, true);

        return response(
            200,
            "Vendors fetched successfully (from cache)",
            $cachedData["data"],
            $cachedData["meta"]
        );
    }

    // DB
    $vendors = getVendors(
        $search,
        $categoryId,
        $location,
        $pageSize,
        $offset
    );

    $totalRecords = countVendors($search, $categoryId, $location);
    $totalPages = ceil($totalRecords / $pageSize);

    $meta = [
        "page" => $page,
        "pageSize" => $pageSize,
        "totalRecords" => $totalRecords,
        "totalPages" => $totalPages,
        "hasNext" => $page < $totalPages,
        "hasPrev" => $page > 1
    ];

    // Store FULL response (data + meta)
    $responseData = [
        "data" => $vendors,
        "meta" => $meta
    ];

    $redis->setex(
        $cacheKey,
        3600,
        json_encode($responseData)
    );

    return response(
        200,
        "Vendors fetched successfully",
        $vendors,
        $meta
    );
}
function VendorStatus($connection)
{
    $vendorId = $_GET['vendor_id'] ?? null;

    if (!$vendorId) {
        return response(400, "Vendor ID is required");
    }

    $result = ActivateVendor($connection, $vendorId);

    if ($result) {
        return response(200, "Vendor status change successfully");
    }

    return response(500, "Failed to change vendor status");
}
