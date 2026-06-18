<?php
require_once "../config/DB.php";
function getVendorByEmail($email)
{
    global $connection;
    $select=$connection->prepare("select * from vendors where Email = ?");
            $select->execute([$email]);
            return $select->fetch(PDO::FETCH_ASSOC);
}
// function getById($id)
// {
//     global $connection;
//     $select=$connection->prepare("select VendorID, CategoryID, Name, Email, PhoneNumber,
//                 Description, ActivityStatus, Role from vendors where VendorID = ?");
//             $select->execute([$id]);
//             return $select->fetch(PDO::FETCH_ASSOC);
// }

function updateVendorStatus($vendorId, $status) {
    global $connection;
    $update = $connection->prepare("UPDATE vendors SET AcctivatedByAdmin = ? WHERE VendorID = ?");
    $update->execute([$status, $vendorId]);
    return $update->rowCount() > 0;
}
function createVendor($data)
{
    global $connection;
    $insert = "INSERT INTO vendors (CategoryID, Name, Email, Password, PhoneNumber, Description, ActivityStatus, Role)
     VALUES (?, ?, ?, ?, ?, ?, 'Inactive', 'Vendor')";
    $query = $connection->prepare($insert);
    return $query->execute([
        $data['CategoryID'],
        $data['Name'],
        $data['Email'],
        password_hash($data['Password'], PASSWORD_DEFAULT),
        $data['PhoneNumber'],
        $data['Description']
    ]);
}
function getVendors($search, $categoryId, $location, $day, $startTime, $limit, $offset)
{
    global $connection;

    $query = "
        SELECT
            v.*,
            COALESCE(r_stats.AvgRate, 0) AS AvgRate,
            COALESCE(r_stats.TotalReviews, 0) AS TotalReviews
        FROM vendors v
        LEFT JOIN (
            SELECT 
                VendorID,
                ROUND(AVG(Rate), 1) AS AvgRate,
                COUNT(ReviewID) AS TotalReviews
            FROM reviews
            GROUP BY VendorID
        ) r_stats ON v.VendorID = r_stats.VendorID
    ";

   
    if (!empty($location)) {
        $query .= " LEFT JOIN location l ON v.VendorID = l.VendorID";
    }

    if (!empty($day) || !empty($startTime)) {
        $query .= " LEFT JOIN availabletimeslots ats ON v.VendorID = ats.VendorID";
    }

    $query .= " WHERE 1=1";
    $params = [];

    
    if (!empty($search)) {
        $query .= " AND v.Name LIKE :search";
        $params['search'] = "%$search%";
    }

    if (!empty($categoryId)) {
        $query .= " AND v.CategoryID = :categoryId";
        $params['categoryId'] = $categoryId;
    }


    if (!empty($location)) {
        $query .= "
            AND (
                l.City LIKE :location
                OR l.Governorate LIKE :location
                OR l.Country LIKE :location
            )
        ";
        $params['location'] = "%$location%";
    }

    
    if (!empty($day)) {
        $query .= " AND ats.Day = :day AND ats.Status = 'Available'";
        $params['day'] = $day;
    }

    if (!empty($startTime)) {
        $query .= " AND :startTime BETWEEN ats.StartTime AND ats.EndTime AND ats.Status = 'Available'";
        $params['startTime'] = $startTime;
    }

    $query .= " GROUP BY v.VendorID ORDER BY v.VendorID DESC LIMIT :limit OFFSET :offset";

    $stmt = $connection->prepare($query);

   
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
    }

  
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);


    
    foreach ($vendors as &$vendor) {
        $vendorId = $vendor['VendorID'];

     
        $locStmt = $connection->prepare("
            SELECT Country, Governorate, City, Address 
            FROM location 
            WHERE VendorID = :vendorId
        ");
        $locStmt->execute(['vendorId' => $vendorId]);
        $vendor['locations'] = $locStmt->fetchAll(PDO::FETCH_ASSOC);

       
        $slotsStmt = $connection->prepare("
            SELECT Day, StartTime, EndTime, Status 
            FROM availabletimeslots 
            WHERE VendorID = :vendorId AND Status = 'Available'
        ");
        $slotsStmt->execute(['vendorId' => $vendorId]);
        $vendor['time_slots'] = $slotsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $vendors;
}
function getById($vendorId)
{
    global $connection;

    $query = "
        SELECT
            v.*,
            COALESCE(r_stats.AvgRate, 0) AS AvgRate,
            COALESCE(r_stats.TotalReviews, 0) AS TotalReviews
        FROM vendors v
        LEFT JOIN (
            SELECT 
                VendorID,
                ROUND(AVG(Rate), 1) AS AvgRate,
                COUNT(ReviewID) AS TotalReviews
            FROM reviews
            GROUP BY VendorID
        ) r_stats ON v.VendorID = r_stats.VendorID
        WHERE v.VendorID = :vendorId
    ";

    $stmt = $connection->prepare($query);
    $stmt->execute(['vendorId' => $vendorId]);

    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendor) {
        return null; 
    }

  
    $locStmt = $connection->prepare("
        SELECT Country, Governorate, City, Address 
        FROM location 
        WHERE VendorID = :vendorId
    ");
    $locStmt->execute(['vendorId' => $vendorId]);
    
    $vendor['locations'] = $locStmt->fetchAll(PDO::FETCH_ASSOC);
   
    $slotsStmt = $connection->prepare("
        SELECT Day, StartTime, EndTime, Status 
        FROM availabletimeslots 
        WHERE VendorID = :vendorId AND Status = 'Available'
    ");
    $slotsStmt->execute(['vendorId' => $vendorId]);
    
    $vendor['time_slots'] = $slotsStmt->fetchAll(PDO::FETCH_ASSOC);

    return $vendor;
}
// ==========================================
// 1. دالة جلب البيانات النشطة بالصفحات فقط
// ==========================================
function getActiveVendorsOnly($limit, $offset){
   global $connection;
    $query = "SELECT v.*,COALESCE(r_stats.AvgRate, 0) AS AvgRate,
        COALESCE(r_stats.TotalReviews, 0) AS TotalReviews FROM vendors v
        LEFT JOIN (SELECT VendorID,ROUND(AVG(Rate), 1) AS AvgRate,
        COUNT(ReviewID) AS TotalReviews
        FROM reviews GROUP BY VendorID) r_stats ON v.VendorID = r_stats.VendorID
        WHERE v.ActivityStatus = 'Active' ORDER BY v.VendorID DESC
        LIMIT :limit OFFSET :offset";
    $stmt = $connection->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($vendors as &$vendor) {$vendorId = $vendor['VendorID'];
        $locStmt = $connection->prepare("SELECT Country, Governorate, City, Address FROM location WHERE VendorID = :vendorId");
        $locStmt->execute(['vendorId' => $vendorId]);
        $vendor['locations'] = $locStmt->fetchAll(PDO::FETCH_ASSOC);
        $slotsStmt = $connection->prepare("SELECT Day, StartTime, EndTime, Status FROM availabletimeslots WHERE VendorID = :vendorId AND Status = 'Available'");
        $slotsStmt->execute(['vendorId' => $vendorId]);
        $vendor['time_slots'] = $slotsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $vendors;
}
function countAllActiveVendorsOnly(){
    global $connection;
    $query = "SELECT COUNT(DISTINCT v.VendorID) AS total FROM vendors v WHERE v.ActivityStatus = 'Active'";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
}
function countVendors($search, $categoryId, $location, $day, $startTime)
{
    global $connection;
    $query = "SELECT COUNT(DISTINCT v.VendorID) AS total FROM vendors v";
    if (!empty($location)) {
        $query .= " LEFT JOIN location l ON v.VendorID = l.VendorID";
    }
    if (!empty($day) || !empty($startTime)) {
        $query .= " LEFT JOIN availabletimeslots ats ON v.VendorID = ats.VendorID";
    }
    $query .= " WHERE 1=1";
    $params = [];
    if (!empty($search)) {
        $query .= " AND v.Name LIKE :search";
        $params['search'] = "%$search%";
    }
    if (!empty($categoryId)) {
        $query .= " AND v.CategoryID = :categoryId";
        $params['categoryId'] = $categoryId;
    }
    if (!empty($location)) {
        $query .= "AND (l.City LIKE :location OR l.Governorate LIKE :location
        OR l.Country LIKE :location)";
        $params['location'] = "%$location%";
    }
    if (!empty($day)) {
        $query .= " AND ats.Day = :day AND ats.Status = 'Available'";
        $params['day'] = $day;
    }
    if (!empty($startTime)) {
        $query .= " AND :startTime BETWEEN ats.StartTime AND ats.EndTime AND ats.Status = 'Available'";
        $params['startTime'] = $startTime;
    }
    $stmt = $connection->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}
function ActivateVendor($connection, $id)
{
    // 1. get current status
    $query = "SELECT ActivityStatus FROM vendors WHERE VendorID = :id";
    
    $stmt = $connection->prepare($query);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendor) {
        return false;
    }

    $currentStatus = $vendor['ActivityStatus'];

    
    $newStatus = ($currentStatus === 'Active') ? 'Inactive' : 'Active';
    // 3. update
    $updateQuery = "UPDATE vendors SET ActivityStatus = ? WHERE VendorID = ?";

    $updateStmt = $connection->prepare($updateQuery);

    // $updateStmt->bindValue(":status", $newStatus, PDO::PARAM_STR);
    // $updateStmt->bindValue(":id", $id, PDO::PARAM_INT);

    return $updateStmt->execute([$newStatus,$id]);
    
}
function updateVendorData($vendorId, $data)
{
    global $connection;

    try {
        $connection->beginTransaction();

       
        //    1. VENDORS (UPDATE / INSERT)
      
        $checkVendor = $connection->prepare("
            SELECT VendorID FROM vendors WHERE VendorID = :vendorId
        ");
        $checkVendor->execute(['vendorId' => $vendorId]);

        if ($checkVendor->rowCount() > 0) {
            $stmt = $connection->prepare("
                UPDATE vendors
                SET Name = :name,
                    Description = :description,
                    CategoryID = :categoryId
                WHERE VendorID = :vendorId
            ");
        } else {
            $stmt = $connection->prepare("
                INSERT INTO vendors (VendorID, Name, Description, CategoryID)
                VALUES (:vendorId, :name, :description, :categoryId)
            ");
        }

        $stmt->execute([
            'vendorId' => $vendorId,
            'name' => $data['name'],
            'description' => $data['description'],
            'categoryId' => $data['categoryId']
        ]);


       
        //    2. LOCATION 
        
        $oldLocationsStmt = $connection->prepare("
            SELECT * FROM location WHERE VendorID = :vendorId
        ");
        $oldLocationsStmt->execute(['vendorId' => $vendorId]);
        $oldLocations = $oldLocationsStmt->fetchAll(PDO::FETCH_ASSOC);

        $incomingLocations = $data['locations'] ?? [];

        $existingLocKeys = [];
        foreach ($oldLocations as $loc) {
            $existingLocKeys[] = $loc['City'] . '_' . $loc['Governorate'];
        }

        $incomingLocKeys = [];

        foreach ($incomingLocations as $loc) {
            $key = $loc['city'] . '_' . $loc['governorate'];
            $incomingLocKeys[] = $key;

            if (in_array($key, $existingLocKeys)) {
                
                $stmt = $connection->prepare("
                    UPDATE location
                    SET Country = :country,
                        Address = :address
                    WHERE VendorID = :vendorId
                    AND City = :city
                    AND Governorate = :governorate
                ");
            } else {
           
                $stmt = $connection->prepare("
                    INSERT INTO location
                    (VendorID, Country, Governorate, City, Address)
                    VALUES (:vendorId, :country, :governorate, :city, :address)
                ");
            }

            $stmt->execute([
                'vendorId' => $vendorId,
                'country' => $loc['country'],
                'governorate' => $loc['governorate'],
                'city' => $loc['city'],
                'address' => $loc['address'] ?? null // استخدام تلافي الخطأ في حال لم يُرسل العنوان
            ]);
        }

        // DELETE missing locations
        foreach ($oldLocations as $loc) {
            $key = $loc['City'] . '_' . $loc['Governorate'];

            if (!in_array($key, $incomingLocKeys)) {
                $del = $connection->prepare("
                    DELETE FROM location
                    WHERE VendorID = :vendorId
                    AND City = :city
                    AND Governorate = :governorate
                ");

                $del->execute([
                    'vendorId' => $vendorId,
                    'city' => $loc['City'],
                    'governorate' => $loc['Governorate']
                ]);
            }
        }


        
        //    3. TIME SLOTS
      
        $oldSlotsStmt = $connection->prepare("
            SELECT * FROM availabletimeslots WHERE VendorID = :vendorId
        ");
        $oldSlotsStmt->execute(['vendorId' => $vendorId]);
        $oldSlots = $oldSlotsStmt->fetchAll(PDO::FETCH_ASSOC);

        $incomingSlots = $data['time_slots'] ?? [];

        $existingSlotKeys = [];
        foreach ($oldSlots as $slot) {
            $existingSlotKeys[] = $slot['Day'] . '_' . $slot['StartTime'];
        }

        $incomingSlotKeys = [];

        foreach ($incomingSlots as $slot) {
            $key = $slot['day'] . '_' . $slot['startTime'];
            $incomingSlotKeys[] = $key;

            if (in_array($key, $existingSlotKeys)) {
                // UPDATE
                $stmt = $connection->prepare("
                    UPDATE availabletimeslots
                    SET EndTime = :endTime,
                        Status = :status
                    WHERE VendorID = :vendorId
                    AND Day = :day
                    AND StartTime = :startTime
                ");
            } else {
                // INSERT
                $stmt = $connection->prepare("
                    INSERT INTO availabletimeslots
                    (VendorID, Day, StartTime, EndTime, Status)
                    VALUES (:vendorId, :day, :startTime, :endTime, :status)
                ");
            }

            $stmt->execute([
                'vendorId' => $vendorId,
                'day' => $slot['day'],
                'startTime' => $slot['startTime'],
                'endTime' => $slot['endTime'],
                'status' => $slot['status'] ?? 'Available'
            ]);
        }

        // DELETE missing slots
        foreach ($oldSlots as $slot) {
            $key = $slot['Day'] . '_' . $slot['StartTime'];

            if (!in_array($key, $incomingSlotKeys)) {
                $del = $connection->prepare("
                    DELETE FROM availabletimeslots
                    WHERE VendorID = :vendorId
                    AND Day = :day
                    AND StartTime = :startTime
                ");

                $del->execute([
                    'vendorId' => $vendorId,
                    'day' => $slot['Day'],
                    'startTime' => $slot['StartTime']
                ]);
            }
        }

        $connection->commit();
        return true;

    } catch (Exception $e) {
        $connection->rollBack();
        throw $e;
    }
}
function getTopRated($limit, $offset)
{
    global $connection;
    
    $query = "
         SELECT
             v.*,
             COALESCE(r_stats.AvgRate, 0) AS AvgRate,
             COALESCE(r_stats.TotalReviews, 0) AS TotalReviews
         FROM vendors v
         LEFT JOIN (
             SELECT 
                 VendorID,
                 ROUND(AVG(Rate), 1) AS AvgRate,
                 COUNT(ReviewID) AS TotalReviews
             FROM reviews
             GROUP BY VendorID
        ) r_stats ON v.VendorID = r_stats.VendorID
        ORDER BY AvgRate DESC, TotalReviews DESC 
        LIMIT :limit OFFSET :offset 
    ";

    $stmt = $connection->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute(); 
    
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($vendors as &$vendor) {
        $vendorId = $vendor['VendorID'];

        $locStmt = $connection->prepare("SELECT Country, Governorate, City, Address FROM location WHERE VendorID = :vendorId");
        $locStmt->execute(['vendorId' => $vendorId]);
        $vendor['locations'] = $locStmt->fetchAll(PDO::FETCH_ASSOC);
 
        $slotsStmt = $connection->prepare("SELECT Day, StartTime, EndTime, Status FROM availabletimeslots WHERE VendorID = :vendorId AND Status = 'Available'");
        $slotsStmt->execute(['vendorId' => $vendorId]);
        $vendor['time_slots'] = $slotsStmt->fetchAll(PDO::FETCH_ASSOC);

        $reviewsStmt = $connection->prepare("
            SELECT Rate, Comment 
            FROM reviews 
            WHERE VendorID = :vendorId 
            ORDER BY ReviewID DESC  
        ");
        $reviewsStmt->execute(['vendorId' => $vendorId]);
        $vendor['reviews'] = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $vendors;
}
function countAllVendorsAbsolute() {
    global $connection;
    $stmt = $connection->prepare("SELECT COUNT(VendorID) AS total FROM vendors");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}