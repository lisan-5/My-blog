<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    include_once __DIR__ . '/../config/config.php';
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';
include '../includes/header.php';
?>

<h1>Admin Dashboard</h1>
<p>Welcome, Admin!</p>
<ul>
    <li><a href="manage_posts.php">Manage Posts</a></li>
    <li><a href="manage_users.php">Manage Users</a></li>
</ul>

<?php include '../includes/footer.php'; ?>