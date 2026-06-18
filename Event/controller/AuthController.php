<?php
require_once '../helper/response.php';
require_once '../helper/jwt.php';
require_once '../Repos/userRepo.php';
require_once '../Repos/VendorRepos.php';

function signup($data)
{
    $name= $data['Name'];
    $email = $data['Email'];
    $password = $data['Password' ];
    $phone = $data['PhoneNumber'];
    $role = $data['Role'] ?? 'Client';
    if (!in_array($role, ['Client', 'Admin'])) {
    response(400, 'Invalid role');
}
    if (!$name || !$email || !$password) {
        response(400, "Name, email, and password are required");
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        response(400,"email format is wrong");
    }
    if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$&*+])[A-Za-z\d!@#$&*+]{8,64}$/",$password)){
    echo "password must include uppercase";}
    $userId = createUser([
    'Name' => $name,
    'Email' => $email,
    'Password' => $password,
    'PhoneNumber' => $phone,
    'Role' => $role
]);

    if (!$userId) {
        response(409, "Email already in use");
    }
 
    response(201, "User registered successfully");
}
function login($data){
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
        $user=getUserByEmail($email);
        if(!$user)
            {
                response(404,['user not found']);
            }
        if(!password_verify($password,$user['Password']))
            {
                response(401,['Incorrect password']);
            }
        $token=GenerateToken($user);
        response(200,"logged in",["token" => $token]);
}
function getMe() {
    $decoded = verifyToken();
    $user = getUserById($decoded->user_id);
    if (!$user) {
        response(404, "User not found");
    }
 
    response(200, "User fetched", $user);
}

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
        $token=GenerateToken($vendor);
        response(200,"logged in",["token" => $token]);
}
function getVendorMe()
{
    $decoded = verifyToken();
    require_vendor($decoded);

    $vendor = getVendorId($decoded->user_id);
    if (!$vendor) {
        response(404, "Vendor not found");
    }
    response(200, "Vendor fetched", $vendor);
}
function toggleVendorStatus($vendorId) {
    $decoded = verifyToken();
 
    if ($decoded->role !== 'admin') {
        response(403, "Access restricted to admins only");
    }
 
    if (!$vendorId) {
        response(400, "Vendor ID is required");
    }
 
    $vendor = getVendorId($vendorId);
 
    if (!$vendor) {
        response(404, "Vendor not found");
    }
 
    $newStatus = $vendor['AcctivatedByAdmin'] === 'Active' ? 'Inactive' : 'Active';
 
    $result = updateVendorStatus($vendorId, $newStatus);
 
    if (!$result) {
        response(500, "Failed to update vendor status");
    }
 
    response(200, "Vendor status changed to '$newStatus'");
}
function changePassword($data) {
    $decoded = verifyToken();
 
    $currentPassword = $data['CurrentPassword'] ?? '';
    $newPassword     = $data['NewPassword']     ?? '';
    $confirmPassword = $data['ConfirmPassword'] ?? '';
 
    if (!$currentPassword || !$newPassword || !$confirmPassword) {
        response(400, "CurrentPassword, NewPassword, and ConfirmPassword are required");
    }
 
    if ($newPassword !== $confirmPassword) {
        response(400, "NewPassword and ConfirmPassword do not match");
    }
 
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$&*+])[A-Za-z\d!@#$&*+]{8,64}$/", $newPassword)) {
        response(400, "Password must be 8+ chars with uppercase, lowercase, number and special character (!@#$&*+)");
    }
    $row = getUserPasswordById($decoded->user_id);
 
    if (!$row) {
        response(404, "User not found");
    }
 
    if (!password_verify($currentPassword, $row['Password'])) {
        response(401, "Current password is incorrect");
    }
 
    if (password_verify($newPassword, $row['Password'])) {
        response(400, "New password must be different from current password");
    }
 
    $updated = updatePassword($decoded->user_id, $newPassword);
 
    if (!$updated) {
        response(500, "Failed to update password");
    }
 
    response(200, "Password changed successfully");
}
?>