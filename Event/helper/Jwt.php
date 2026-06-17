<?php
require_once '../helper/response.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
function GenerateToken($user) {
    global $secret_key;

   $payload = [
        "iat"     => time(),
        "exp"     => time() + 3600,
        "user_id" => $user['UserID'],
        "role"    => $user['Role']
    ];
 return JWT::encode($payload, $secret_key, "HS256");
}
function VerifyToken() {
    global $secret_key;
    $headers = getallheaders();
    $token   = $headers['Authentication'] ?? '';
    if (!$token) {
        response(401, 'Token is required.');
    }
    $token = str_replace("Bearer ", "", $token);
    try {
        $decoded = JWT::decode($token, new Key($secret_key, "HS256"));
        return $decoded;
    } catch (Exception $e) {
        response(401, 'Invalid token.');
    }
}
function require_vendor($verifiedToken) {
    if($verifiedToken->role !=="vendor"){
        response(403, 'Access denied. vendor privileges required.');
    }
}
