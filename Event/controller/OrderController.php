<?php

require_once "../Repos/OrderRepo.php";
require_once "../OrderHelper/OrderHelper.php";
require_once "../Config/DB.php";
require_once '../vendor/autoload.php';
require_once '../Repos/userRepo.php';
require_once '../Config/mail.php';


function orders(){
    if(isset($_GET['page']) && isset($_GET['limit'])){
        $page = (int)$_GET['page'];
        $limit = (int)$_GET['limit'];
        $orders = GetOrderPage($page, $limit);
    }else {
        $orders = GetAllOrders();
    }
        foreach ($orders as $order) {
        if (time() >= strtotime($order['EndTime'])) {
            UpdateStatus($order['OrderID'], "completed");
            $order['Status'] = "completed";
        }
    }
    response(200, "All orders are retrieved.", $orders);
}

function OrderID($id){
    $order = GetOrderById($id);
    if (!$order){
        response(404,"Order doesn't exist.");
    }
    if (time() >= strtotime($order['EndTime'])) {
        UpdateStatus($order['OrderID'], "completed");
        $order['Status'] = "completed";
    }
    response(200,"Order retrieved",$order);
}

function AddOrder($data){
    $existingOrder = GetOrderByDate($data['StartTime']);
    if($existingOrder){
        response(400, "Date is already booked.");
    }
    CreateOrder($data['UserID'],$data['VendorID'],$data['StartTime'],$data['EndTime']);
    $user = getUserById($data['UserID']);
    sendMail($user['Email'],
    "Order Confirmation",
    "Your order has been created successfully");
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

function CompletedStatus($order){
    $currentTime = time();
    if ($currentTime >= strtotime($order['EndTime'])) {
        UpdateStatus($order['OrderID'], "completed");
        return "completed";
    }
    return $order['Status'];
}

function Wallet($vendorId){
    $data = CalcPrice($vendorId);
    response(200, "Total price retrieved.", [
        'Amount to be received' => $data['PendingAmount'],
        'Received Amount' => $data['CompletedAmount']
    ]);
}

?>