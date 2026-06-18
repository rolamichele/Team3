<?php
require_once "../config/DB.php";
function getUserByEmail($email)
{
    global $connection;
    $select=$connection->prepare("select * from users where Email = ?");
            $select->execute([$email]);
            return $select->fetch();
}
function getUserById($id)
{
    global $connection;
    $select=$connection->prepare("select * from users where UserID = ?");
            $select->execute([$id]);
            return $select->fetch(PDO::FETCH_ASSOC);
}
function getAllUsers()
{
    global $connection;
    $select=$connection->prepare("select * from users");
            $select->execute();
            return $select->fetchAll();
}
function updateUser($id, $data)
{
    global $connection;
    $update = "UPDATE users SET Name = :name, Email = :email, PhoneNumber = :phone WHERE UserID = :id";
    $query = $connection->prepare($update);
    return $query->execute([
        ':name' => $data['Name'],
        ':email' => $data['Email'],
        ':phone' => $data['PhoneNumber'],
        ':id' => $id
    ]);
}
function deleteUser($id)
{
    global $connection;
    $delete = "DELETE FROM users WHERE UserID = :id";
    $query = $connection->prepare($delete);
    return $query->execute([':id' => $id]);
}
function createUser($data)
{
    global $connection;
    $insert = "INSERT INTO users (Name, Email, Password, PhoneNumber, Role) VALUES (:name, :email, :password, :phone, :role)";
    $query = $connection->prepare($insert);
    return $query->execute([
        ':name' => $data['Name'],
        ':email' => $data['Email'],
        ':password' => password_hash($data['Password'], PASSWORD_DEFAULT),
        ':phone' => $data['PhoneNumber'],
        ':role' => $data['Role']
    ]);
}
?>