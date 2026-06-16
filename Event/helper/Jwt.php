<?php
<<<<<<< HEAD

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


function generateToken($user)
{
    $payload = [
        "iat" => time(),
        "exp" => time() + 3600,
        "user_id" => $user['id'],
        "role" => $user['role']
    ];

    return JWT::encode(
        $payload,
         "B0RN0Jx6muUoyGJGmah1RiQJ6mpNXEDQShyHT8bCbYp",
        "HS256"
    );
}

function VerifyToken()
{
    $headers = getallheaders();
    $token = $headers['Authentication'] ?? '';

    if (!$token) {
        response(401, "token is requird");
    }

    $token = str_replace("Bearer ", "", $token);

    try {
        $decoded = JWT::decode(
            $token,
            new Key("YOUR_SECRET_KEY", "HS256")
        );

        return $decoded;
    } catch (Exception $e) {
        response(401, "invalid token");
    }
}
=======
require_once '../helper/response.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
function GenerateToken($user) {
    global $secret_key;
>>>>>>> 0a3f366d58d2ab304b870c3a05135a62d1654014

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
