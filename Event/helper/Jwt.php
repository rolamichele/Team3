<?php
require_once '../helper/response.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
function GenerateToken($user)
{
        $payload=[
            "iat"=>time(),
            "exp"=>time()+ 3600,
            "user_id"=>$user['id'],
            "role" =>$user['role']  
            ];
     return JWT::encode($payload,"B0RN0Jx6muUoyGJGmahlRiQJ6mpNXEDQShyHT8bCbYp", 'HS256');
}
function verifyToken(){
    $headers=getallheaders();
    $token=$headers['Authorization']??'';
    if (!$token){
        response(401,"token is required");
    }
    $token=str_replace("Bearer ","",$token);
    try{
        $decoded=JWT::decode($token,new Key("B0RN0Jx6muUoyGJGmahlRiQJ6mpNXEDQShyHT8bCbYp",'HS256'));
        return $decoded; 
    }catch(Exception $e)
    {
        response(401, $e->getMessage() );
    }
}
function require_admin($decodedToken){
    if ($decodedToken->role!=="admin"){
        response(403,"you are not authorized to access this resource");
    }
}
?>
