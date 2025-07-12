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

$post_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];

    $sql = "UPDATE posts SET title = ?, content = ?, status = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $title, $content, $status, $post_id, $_SESSION['user_id']);
    
    try {
        $stmt->execute();
        header("Location: " . BASE_URL . "dashboard/my_posts.php");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if (!$post) {
        die("Post not found or you don't have permission to edit it.");
    }
}
?>

<h2>Edit Post</h2>
<form method="POST">
    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    </div>
    <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="published" <?php if ($post['status'] == 'published') echo 'selected'; ?>>Publish</option>
            <option value="draft" <?php if ($post['status'] == 'draft') echo 'selected'; ?>>Save as Draft</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>

<?php include '../includes/footer.php'; ?>
