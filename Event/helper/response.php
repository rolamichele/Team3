<?php
function response($code , $message , $data=null){
    header("Content-Type: application/json");
    http_response_code($code);
    echo json_encode(["message" => $message , "data" => $data]);
    exit;
}
function getRequestBody() {
    return json_decode(file_get_contents("php://input"), true);

}