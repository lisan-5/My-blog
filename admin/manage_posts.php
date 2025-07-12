<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    include_once __DIR__ . '/../config/config.php';
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';
include '../config/db.php';
include '../includes/header.php';

$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id";
$result = $conn->query($sql);
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>Manage Posts</h2>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td><?php echo htmlspecialchars($post['username']); ?></td>
                <td>
                    <a href="../dashboard/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="../dashboard/delete.php? id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
