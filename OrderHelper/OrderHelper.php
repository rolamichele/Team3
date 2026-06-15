<?php

function response ($code, $message, $data=null){

    header ("content-type: Application/Json");
    http_response_code($code);
    echo json_encode(["message"=>$message, "data"=> $data]);
    exit;
}


?>