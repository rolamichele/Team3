<?php

require_once "../helper/response.php";
require_once "../helper/Jwt.php";
require_once "../Repos/vendorRepos.php";
require_once "../config/cache.php";

function getAllVendors()
{
    global $redis;

    
    $search = $_GET['search'] ?? null;
    $categoryId = $_GET['categoryId'] ?? null;
    $location = $_GET['location'] ?? null;

   
    $day = $_GET['day'] ?? null;
    $startTime = $_GET['time'] ?? null; 
   
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;

    $page = max(1, $page);
    $pageSize = max(1, min(100, $pageSize));

    $offset = ($page - 1) * $pageSize;


    $filterArray = [
        'search'     => $search,
        'categoryId' => $categoryId,
        'location'   => $location,
        'day'        => $day,
        'startTime'  => $startTime,
        'page'       => $page,
        'pageSize'   => $pageSize
    ];
    $cacheKey = "vendors:" . md5(json_encode($filterArray));

   
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

   
    $vendors = getVendors(
        $search,
        $categoryId,
        $location,
        $day,
        $startTime,
        $pageSize,
        $offset
    );


    $totalRecords = countVendors(
        $search,
        $categoryId,
        $location,
        $day,
        $startTime
    );

    $totalPages = ceil($totalRecords / $pageSize);

    $meta = [
        "page"         => $page,
        "pageSize"     => $pageSize,
        "totalRecords" => $totalRecords,
        "totalPages"   => $totalPages,
        "hasNext"      => $page < $totalPages,
        "hasPrev"      => $page > 1
    ];

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
function getVendorById($vendorId)
{
    global $redis;

    if (!$vendorId) {
        return response(400, "Vendor ID is required");
    }

    
    $cacheKey = "vendor_detail:" . $vendorId;

    $cachedData = $redis->get($cacheKey);

    if ($cachedData) {
        $vendor = json_decode($cachedData, true);

        return response(
            200,
            "Vendor detailed data fetched successfully (from cache)",
            $vendor
        );
    }


    $vendor = getVendorId($vendorId);

   
    if (!$vendor) {
        return response(404, "Vendor not found");
    }

  
    $redis->setex(
        $cacheKey,
        3600,
        json_encode($vendor)
    );

    return response(
        200,
        "Vendor detailed data fetched successfully",
        $vendor
    );
}
function getActiveVendors()
{
    global $redis;

    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;
    
    $page = max(1, $page);
    $pageSize = max(1, min(100, $pageSize));
    $offset = ($page - 1) * $pageSize;

  
    $cacheKey = "vendors:active_only:page:" . $page . ":size:" . $pageSize;

   
    $cachedData = $redis->get($cacheKey);
    if ($cachedData) {
        $cachedData = json_decode($cachedData, true);
        return response(200, "Active Vendors fetched successfully (from cache)", $cachedData["data"], $cachedData["meta"]);
    }

    $vendors =  getActiveVendorsOnly($pageSize, $offset);
    $totalRecords = countAllActiveVendorsOnly();

    $totalPages = ceil($totalRecords / $pageSize);
    $meta = [
        "page" => $page,
        "pageSize" => $pageSize,
        "totalRecords" => $totalRecords,
        "totalPages" => $totalPages,
        "hasNext" => $page < $totalPages,
        "hasPrev" => $page > 1
    ];

    $responseData = ["data" => $vendors, "meta" => $meta];

    $redis->setex($cacheKey, 3600, json_encode($responseData));

    return response(200, "Active Vendors fetched successfully", $vendors, $meta);
}
function VendorStatus($connection)
{
    //  $verifiedToken = verifyToken();
    //  require_vendor($verifiedToken);
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
function updateVendor()
{
    // $verifiedToken = verifyToken();
    // require_vendor($verifiedToken);

    $vendorId = $_GET['vendor_id'] ?? null;

    if (!$vendorId) {
        return response(400, "vendor_id is required");
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        return response(400, "Invalid JSON body");
    }


    if (empty($data['name']) || empty($data['categoryId'])) {
        return response(400, "Missing required fields");
    }

    try {

        $result = updateVendorData($vendorId, $data);

        if ($result) {
            return response(200, "Vendor updated successfully");
        }

        return response(500, "Failed to update vendor");

    } catch (Exception $e) {

        return response(500, $e->getMessage());
    }
}
function getTopRatedVendors()
{
    // global $redis;

    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;
    
    $page = max(1, $page);
    $pageSize = max(1, min(100, $pageSize));
    $offset = ($page - 1) * $pageSize;

   
    // $cacheKey = "vendors:all_top_rated_absolute" . $page . ":size:" . $pageSize;

    
    // $cachedData = $redis->get($cacheKey);
    // if ($cachedData) {
    //     $cachedData = json_decode($cachedData, true);
    //     return response(200, "Top rated vendors fetched successfully (from cache)", $cachedData["data"], $cachedData["meta"]);
    // }

    
    $vendors = getTopRated($pageSize, $offset);
    $totalRecords = countAllVendorsAbsolute();    

    $totalPages = ceil($totalRecords / $pageSize);
    $meta = [
        "page" => $page,
        "pageSize" => $pageSize,
        "totalRecords" => $totalRecords,
        "totalPages" => $totalPages,
        "hasNext" => $page < $totalPages,
        "hasPrev" => $page > 1
    ];

    // $responseData = ["data" => $vendors, "meta" => $meta];

    
    // $redis->setex($cacheKey, 3600, json_encode($responseData));

    return response(200, "Top rated vendors fetched successfully", $vendors, $meta);
}
