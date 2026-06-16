<?php

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

