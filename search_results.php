<?php
session_start();
include 'config/config.php';
include 'config/db.php';
include 'includes/header.php';
// Prepare current user liked posts
$user_id = $_SESSION['user_id'] ?? null;
$user_likes = [];
if ($user_id) {
    $stmt_ul = $conn->prepare("SELECT post_id FROM likes WHERE user_id = ?");
    $stmt_ul->bind_param("i", $user_id);
    $stmt_ul->execute();
    $res_ul = $stmt_ul->get_result();
    while ($row_ul = $res_ul->fetch_assoc()) {
        $user_likes[] = $row_ul['post_id'];
    }
}

if (!isset($_GET['query'])) {
    header("Location: index.php");
    exit;
}

$query = $_GET['query'];
$search_query = "%" . $query . "%";

 $sql = "SELECT posts.*, users.username, 
               (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.status = 'published' AND (posts.title LIKE ? OR users.username LIKE ?)
        ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_query, $search_query);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
<hr>

<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                <div class="post-content">
                    <?php
                        $content = htmlspecialchars($post['content']);
                        if (strlen($content) > 200) {
                            echo substr($content, 0, 200) . '...';
                            echo ' <a href="view_post.php?id=' . $post['id'] . '">Read More</a>';
                        } else {
                            echo $content;
                        }
                    ?>
                </div>
                <p class="card-text mt-3">
                    <small class="text-muted">
                        Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?>
                    </small>
                </p>
                <p class="card-text">
                    <small class="text-muted">
                        <?php echo $post['like_count']; ?> Likes
                    </small>
                </p>
                <?php if ($user_id): ?>
                    <?php $liked = in_array($post['id'], $user_likes); ?>
                    <a href="like.php?post_id=<?php echo $post['id']; ?>&action=<?php echo $liked ? 'unlike' : 'like'; ?>" class="btn <?php echo $liked ? 'btn-secondary' : 'btn-outline-secondary'; ?> mb-2">
                        <?php echo $liked ? 'Unlike' : 'Like'; ?>
                    </a>
                <?php else: ?>
                    <p><a href="<?php echo BASE_URL; ?>auth/login.php">Log in to like</a></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No posts found matching your search.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
