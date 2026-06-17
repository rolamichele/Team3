<?php
require_once '../helper/response.php';
require_once '../helper/jwt.php';
require_once '../Repos/userRepo.php';

function signup($data)
{
    $name= $data['Name'];
    $email = $data['Email'];
    $password = $data['Password' ];
    $phone = $data['PhoneNumber'];
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
    'PhoneNumber' => $phone
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
?>