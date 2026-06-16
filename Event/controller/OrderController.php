<?php

require_once "../OrderRepo/OrderRepo.php";
require_once "../OrderHelper/OrderHelper.php";
require_once "../Config/DB.php";

function orders(){
    $orders = GetAllOrders();
    response(200,"All orders are retrieved.", $orders);
}

function OrderID($id){
    $orderID = GetOrderById($id);
    if (!$orderID){
        response (404,"Order doesn't exist.");
    }
    response(200,"Order retrieved",$orderID);
}

function AddOrder($data){
    $existingOrder = GetOrderByDate($data['StartTime']);
    if($existingOrder){
        response(400, "Date is already booked.");
    }
    CreateOrder($data['UserID'],$data['VendorID'],$data['StartTime'],$data['EndTime']);
    response(201,"Order booked successfully.");
}

function OrderDel($id){
    $order = GetOrderById($id);
    if (!$order){
        response (404,"Order doesn't exist.");
    }
    if (strtotime($order['StartTime'])-time()>=86400){
        response(400, "Can't cancel order within 24hrs of event.");
    }
    CancelOrder($id);
    response (200, "Order deleted successfully");
}

function EditOrder($id,$time,$status="pending"){
    $order = GetOrderById($id);
    if (!$order){
        response (404,"Order doesn't exist.");
    }
    if (strtotime($order['StartTime'])-time()>=86400){
        response(400, "Can't edit order within 24hrs of event.");
    }
    UpdateOrder($id,$time,$status);
    response(200,"Order edited successfully.");
}





?>