<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../config/config.php';
    header("Location: ../auth/login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';
include '../config/db.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $status = $_POST['status'];

    $sql = "INSERT INTO posts (title, content, user_id, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $content, $user_id, $status);
    
    try {
        $stmt->execute();
        header("Location: my_posts.php");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Create Post</h2>
<form method="POST">
    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" placeholder = "Type your title" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="5" placeholder="Type your blog" required></textarea>
    </div>
    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="published">Publish</option>
            <option value="draft">Save as Draft</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Create</button>
</form>

<?php include '../includes/footer.php'; ?>
