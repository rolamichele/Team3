<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "test2";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $err) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $err->getMessage()]);
    exit;
}
