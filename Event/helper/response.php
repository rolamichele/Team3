<?php
<<<<<<< HEAD

function response($code,$message,$data=null)
{
    header("Content-Type: application/json");

    http_response_code($code);

    echo json_encode([
        "message"=>$message,
        "data"=>$data
    ]);

    exit;
}
=======
function response($code , $message , $data=null){
    header("Content-Type: application/json");
    http_response_code($code);
    echo json_encode(["message" => $message , "data" => $data]);
    exit;
}
function getRequestBody() {
    return json_decode(file_get_contents("php://input"), true);
}
>>>>>>> 0a3f366d58d2ab304b870c3a05135a62d1654014

