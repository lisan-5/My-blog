<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../config/config.php';
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';
include '../config/db.php';

$post_id = $_GET['id'];

$sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);

try {
    $stmt->execute();
    header("Location: " . BASE_URL . "dashboard/my_posts.php");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
