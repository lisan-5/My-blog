<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../config/config.php';
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';
include '../config/db.php';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM posts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>My Posts</h2>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td>
                    <a href="../dashboard/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="../dashboard/delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
