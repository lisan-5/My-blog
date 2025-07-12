<?php
session_start();
include 'config/config.php';
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$post_id = $_GET['id'];

// Fetch the post
$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}
// Restrict access to drafts
if ($post['status'] !== 'published') {
    // Allow authors or admins to view drafts
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_id'] != $post['user_id'] && ($_SESSION['role'] ?? '') !== 'admin')) {
        die("Post not found.");
    }
}

// Fetch comments
$sql_comments = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY comments.created_at DESC";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("i", $post_id);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();
$comments = $result_comments->fetch_all(MYSQLI_ASSOC);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth/login.php");
        exit;
    }
    $comment_content = $_POST['comment_content'];
    $user_id = $_SESSION['user_id'];

    $sql_insert_comment = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt_insert_comment = $conn->prepare($sql_insert_comment);
    $stmt_insert_comment->bind_param("iis", $post_id, $user_id, $comment_content);
    $stmt_insert_comment->execute();
    header("Location: view_post.php?id=" . $post_id);
    exit;
}

// Fetch like count and user liked status
$sql_likes = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
$stmt_likes = $conn->prepare($sql_likes);
$stmt_likes->bind_param("i", $post_id);
$stmt_likes->execute();
$result_likes = $stmt_likes->get_result();
$like_data = $result_likes->fetch_assoc();
$like_count = $like_data['like_count'];

$user_liked = false;
if (isset($_SESSION['user_id'])) {
    $sql_user_liked = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt_user_liked = $conn->prepare($sql_user_liked);
    $stmt_user_liked->bind_param("ii", $post_id, $_SESSION['user_id']);
    $stmt_user_liked->execute();
    $result_user_liked = $stmt_user_liked->get_result();
    $user_liked = $result_user_liked->num_rows > 0;
}

// Handle like/unlike submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth/login.php");
        exit;
    }
    $user_id = $_SESSION['user_id'];
    if ($_POST['like'] === 'like') {
        $sql_like = "INSERT IGNORE INTO likes (post_id, user_id) VALUES (?, ?)";
        $stmt_like = $conn->prepare($sql_like);
        $stmt_like->bind_param("ii", $post_id, $user_id);
        $stmt_like->execute();
    } else {
        $sql_unlike = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt_unlike = $conn->prepare($sql_unlike);
        $stmt_unlike->bind_param("ii", $post_id, $user_id);
        $stmt_unlike->execute();
    }
    header("Location: view_post.php?id=" . $post_id);
    exit;
}

?>

<div class="card">
    <div class="card-body">
        <h1 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <p class="card-text">
            <small class="text-muted">
                Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?>
            </small>
        </p>
        <!-- Like button and count -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" class="like-form">
                <button type="submit" name="like" value="<?php echo $user_liked ? 'unlike' : 'like'; ?>" class="btn <?php echo $user_liked ? 'btn-secondary' : 'btn-outline-secondary'; ?> like-btn">
                    <?php echo $user_liked ? 'Unlike' : 'Like'; ?>
                </button>
                <span class="like-count"><?php echo $like_count; ?> Likes</span>
            </form>
        <?php else: ?>
            <p><a href="<?php echo BASE_URL; ?>auth/login.php">Log in to like</a></p>
        <?php endif; ?>

        <hr>

        <h3>Comments</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <div class="form-group">
                    <textarea name="comment_content" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit" name="comment" class="btn btn-primary">Post Comment</button>
            </form>
            <hr>
        <?php else: ?>
            <p><a href="<?php echo BASE_URL; ?>auth/login.php">Log in</a> to post a comment.</p>
        <?php endif; ?>

        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        <p class="card-text">
                            <small class="text-muted">
                                Comment by <?php echo htmlspecialchars($comment['username']); ?> on <?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?>
                            </small>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
