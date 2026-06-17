<?php
require_once "../helper/response.php";
require_once "../Repos/vendorRepos.php";
require_once "../config/cache.php";
require_once "../helper/jwt.php";

function vendorRegister($data)
{
    $name= $data['Name'];
    $email = $data['Email'];
    $password = $data['Password' ];
    $phone = $data['PhoneNumber'];
    $categoryId = $data['CategoryID'] ?? null;
    $description = $data['Description'];
    if (!$name || !$email || !$password) {
        response(400, "Name, email, and password are required");
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        response(400,"email format is wrong");
    }
    if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$&*+])[A-Za-z\d!@#$&*+]{8,64}$/",$password)){
    echo "password must include uppercase";}
    if (!$categoryId) {
        response(400, "CategoryID is required");
    }
    $vendorId = createVendor([
    'Name' => $name,
    'Email' => $email,
    'Password' => $password,
    'PhoneNumber' => $phone,
    'CategoryID' => $categoryId,
    'Description' => $description
]);

    if (!$vendorId) {
        response(409, "Email already in use");
    }
 
    response(201, "Vendor registered successfully");
}

function vendorLogin($data){
        $email=$data['Email'];
        $password=$data['Password'];
        if (!$email || !$password)
            {
                response(400, "Email and password are required");
            }
        if(!filter_var($email,FILTER_VALIDATE_EMAIL))
            {
                response(400,['Invalid email format']);
            }
        $vendor=getVendorByEmail($email);
        if(!$vendor)
            {
                response(404,['vendor not found']);
            }
        if(!password_verify($password,$vendor['Password']))
            {
                response(401,['Incorrect password']);
            }
            if ($vendor['ActivityStatus'] !== 'Active') {
        response(403, "Your account is not active yet. Please wait for admin approval.");}
        $token=GenerateToken([
        'id'   => $vendor['VendorID'],
        'role' => 'Vendor'
    ]);
        response(200,"logged in",["token" => $token]);
}
function getVendorMe() {
    $decoded = verifyToken();
    if ($decoded->role !== 'Vendor') {
        response(403, "Access restricted to vendors only");
    }
    $vendor = getVendorById($decoded->user_id);
    if (!$vendor) {
        response(404, "Vendor not found");
    }
 
    response(200, "Vendor fetched", $vendor);
}

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
