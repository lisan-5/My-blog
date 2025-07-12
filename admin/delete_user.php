<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    include_once __DIR__ . '/../config/config.php';
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';
include '../config/db.php';

$user_id = $_GET['id'];

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

try {
    $stmt->execute();
    header("Location: manage_users.php");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
