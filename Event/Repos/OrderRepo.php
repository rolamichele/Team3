<?php

require_once '../Config/DB.php';

function GetAllOrders(){
    global $connection;
    $query = $connection->prepare("SELECT orders.OrderID,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items', sum(price) as 'Price', orders.StartTime,orders.EndTime FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID");
    $query->execute();
    return $orders = $query->fetchAll(PDO::FETCH_ASSOC);
}

function GetOrderById($id){
    global $connection;
    $query = $connection->prepare("SELECT orders.*,items.*,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items' FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID 
    WHERE orders.OrderID = :id");
    $query->execute(['id'=>$id]);
    return $orders = $query->fetch(PDO::FETCH_ASSOC);
    }
    
    function CreateOrder($userId, $vendorId, $startTime, $endTime,$packageId){
    global $connection;
    $qm = implode(",",array_fill(0,count($packageId),"?"));
    $packageQuery = $connection->prepare("SELECT PackageID,Title,Price FROM packages 
    where PackageID in($qm) AND ActivityStatus = 'Active' AND VendorID = ?");
    $executeParams = array_merge($packageId,[$vendorId]);
    $packageQuery->execute($executeParams);
    $result = $packageQuery->fetchAll(PDO::FETCH_ASSOC);
    if(count($result)<1){
        return response(400,"Invalid Data");
    }
    $orderQuery = $connection->prepare("INSERT INTO orders (UserID, VendorID, Status, StartTime, EndTime)
    VALUES (:userId, :vendorId, :status, :StartTime, :endTime)");
    $orderQuery->execute([
        'userId' => $userId,
        'vendorId' => $vendorId,
        'status' => 'pending',
        'StartTime' => $startTime,
        'endTime' => $endTime
    ]);
    $query = "INSERT INTO items (Name,Price,OrderID) VALUES  ";
    $arrValues = [] ;
    $orderID = $connection->lastInsertId();
    foreach($result as $item){
        $query .= "(?,?,?),";
        array_push($arrValues,$item['Title'],$item['Price'],$orderID); 
    }
    $query = rtrim($query,",");
    $itemQuery = $connection->prepare($query);
    $itemQuery->execute($arrValues);
}

function GetOrderByDate($date){
    global $connection;
    $query = $connection->prepare("SELECT * FROM orders WHERE StartTime = :StartTime");
    $query->execute(['StartTime'=>$date]);
    return $orderDate = $query->fetch();
}

function UpdateOrder($orderid,$packageId,$time,$endtime,$status="pending"){
    global $connection;
    $item = $connection->prepare("DELETE FROM items WHERE OrderID = :orderid");
    $item->execute(['orderid'=>$orderid]);
        $qm = implode(",",array_fill(0,count($packageId),"?"));
    $packageQuery = $connection->prepare("SELECT PackageID,Title,Price FROM packages 
    where PackageID in($qm) AND ActivityStatus = 'Active'");
    $packageQuery->execute($packageId);
    $result = $packageQuery->fetchAll(PDO::FETCH_ASSOC);
    if(count($result)<1){
        return response(400,"Invalid Data");
    }
     $query = "INSERT INTO items (Name,Price,OrderID) VALUES  ";
    $arrValues = [] ;
    
    foreach($result as $item){
        $query .= "(?,?,?),";
        array_push($arrValues,$item['Title'],$item['Price'],$orderid); 
    }
    $query = rtrim($query,",");
    $itemQuery = $connection->prepare($query);
    $itemQuery->execute($arrValues);
    $query = $connection->prepare("UPDATE orders 
    SET status= :status, StartTime = :StartTime, EndTime= :EndTime
    WHERE OrderID = :orderid");
    return $query-> execute(['StartTime'=>$time,'orderid'=>$orderid,'status'=>$status, 'EndTime'=>$endtime]);
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
    $query = $connection->prepare("SELECT orders.OrderID,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items', sum(price) as 'Price', orders.StartTime,orders.EndTime FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID
    LIMIT $limit OFFSET $offset");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
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

function GetUserOrders($id){
    global $connection;
    $query = $connection->prepare("SELECT orders.OrderID,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items', sum(price) as 'Price', orders.StartTime,orders.EndTime FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID
    where orders.UserID=?");
    $query->execute([$id]);
    return $orders = $query->fetchAll(PDO::FETCH_ASSOC);
}

function GetUserOrderById($id,$userid){
    global $connection;
    $query = $connection->prepare("SELECT orders.*,items.*,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items' FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID 
    WHERE orders.OrderID = :id AND orders.UserID=:userid");
    $query->execute(['id'=>$id,
                    'userid'=>$userid]);
    return $orders = $query->fetch(PDO::FETCH_ASSOC);
    }


function GetVendorOrders($id){
    global $connection;
    $query = $connection->prepare("SELECT orders.OrderID,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items', sum(price) as 'Price', orders.StartTime,orders.EndTime FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID
    where orders.VendorID=?");
    $query->execute([$id]);
    return $orders = $query->fetchAll(PDO::FETCH_ASSOC);
}

function GetVendorOrderById($id,$Vendorid){
    global $connection;
    $query = $connection->prepare("SELECT orders.*,items.*,vendors.Name,users.Name,count(items.OrderID) 
    as 'Total Items' FROM orders 
    Join items on orders.OrderID = items.OrderID
    join users on orders.UserID = users.UserID
    join vendors on orders.VendorID = vendors.VendorID 
    WHERE orders.OrderID = :id AND orders.UserID=:vendorid");
    $query->execute(['id'=>$id,
                    'vendorid'=>$Vendorid]);
    return $orders = $query->fetch(PDO::FETCH_ASSOC);
    }


?>