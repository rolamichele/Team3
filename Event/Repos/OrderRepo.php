<?php

require_once '../Config/DB.php';

function GetAllOrders(){
    global $connection;
    $query = $connection->prepare("SELECT * FROM orders");
    $query->execute();
    return $orders = $query->fetchAll();
}

function GetOrderById($id){
    global $connection;
    $query = $connection->prepare("SELECT * FROM orders WHERE OrderID = :id");
    $query->execute(['id'=>$id]);
    return $orders = $query->fetch();
}

function CreateOrder($userId, $vendorId, $startTime, $endTime){
    global $connection;
    $query = $connection->prepare("INSERT INTO orders (UserID, VendorID, Status, StartTime, EndTime)
    VALUES (:userId, :vendorId, :status, :StartTime, :endTime)");
    return $query->execute([
        'userId' => $userId,
        'vendorId' => $vendorId,
        'status' => 'pending',
        'StartTime' => $startTime,
        'endTime' => $endTime
    ]);
}

function GetOrderByDate($date){
    global $connection;
    $query = $connection->prepare("SELECT * FROM orders WHERE StartTime = :StartTime");
    $query->execute(['StartTime'=>$date]);
    return $orderDate = $query->fetch();
}

function UpdateOrder($orderid,$time,$status="pending"){
    global $connection;
    $query = $connection->prepare("UPDATE orders 
    SET status= :status, StartTime = :StartTime
    WHERE OrderID = :orderid");
    return $query-> execute(['StartTime'=>$time,'orderid'=>$orderid,'status'=>$status]);
}

function CancelOrder($id){
    global $connection;
    $query = $connection->prepare("DELETE FROM orders WHERE OrderID = :id");
    return $query->execute(['id'=>$id]);
}

function UpdateStatus($orderid,$status){
    global $connection;
    $query = $connection->prepare("UPDATE orders SET Status = :status 
    WHERE OrderID = :orderid");
    return $query->execute(['orderid'=>$orderid,'status'=>$status]);
}

function GetOrderPage($page,$limit){
    global $connection;
    $page = (int)$page;
    $limit = (int)$limit;
    $offset = ($page - 1) * $limit;
    $query = $connection->prepare("SELECT * FROM orders LIMIT $limit OFFSET $offset");
    $query->execute();
    return $query->fetchAll();
}

function CalcPrice($vendorId){
    global $connection;
    $query = $connection->prepare("SELECT SUM(CASE WHEN orders.Status = 'pending' 
    THEN items.Price ELSE 0 END) AS PendingAmount,
    SUM(CASE WHEN orders.Status = 'completed' 
    THEN items.Price ELSE 0 END) AS CompletedAmount FROM orders INNER JOIN items
    ON orders.OrderID = items.OrderID
    WHERE orders.VendorID = :vendorId");
    $query->execute(['vendorId' => $vendorId]);
    return $query->fetch();
}


?>