<?php
include "includes/header.php";

// Check if category ID is specified
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: index.php");
    exit;
}

$category_id = $_GET["id"];

// Get category details
$sql = "SELECT * FROM categories WHERE category_id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $category = mysqli_fetch_assoc($result);
        } else {
            // Category not found
            header("location: index.php");
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active"><?php echo escapeOutput($category['name']); ?></li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo escapeOutput($category['name']); ?></h1>
            <?php if (isLoggedIn()): ?>
                <a href="create_thread.php?category_id=<?php echo $category_id; ?>" class="btn btn-success">Create New Thread</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary">Login to Create Thread</a>
            <?php endif; ?>
        </div>
        
        <p class="lead"><?php echo escapeOutput($category['description']); ?></p>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="card-title h5">Discussion Threads</h2>
            </div>
            <div class="card-body">
                <?php
                // Get all threads in this category
                $sql = "SELECT t.*, u.username, COUNT(r.reply_id) as reply_count 
                       FROM threads t 
                       JOIN users u ON t.user_id = u.user_id 
                       LEFT JOIN replies r ON t.thread_id = r.thread_id 
                       WHERE t.category_id = ? 
                       GROUP BY t.thread_id 
                       ORDER BY t.created_at DESC";
                
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $category_id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="list-group">';
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<a href="thread.php?id=' . $row['thread_id'] . '" class="list-group-item list-group-item-action thread-item" style="padding:10px">';
                                echo '<div class="d-flex w-100 justify-content-between">';
                                echo '<h5 class="mb-1">' . escapeOutput($row['subject']) . '</h5>';
                                echo '<small>' . formatDate($row['created_at']) . '</small>';
                                echo '</div>';
                                echo '<p class="mb-1">' . mb_substr(escapeOutput($row['content']), 0, 150) . (strlen($row['content']) > 150 ? '...' : '') . '</p>';
                                echo '<div class="thread-meta">';
                                echo '<small>By: ' . escapeOutput($row['username']) . ' | Replies: ' . $row['reply_count'] . '</small>';
                                echo '</div>';
                                echo '</a>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-info">No threads found in this category. Be the first to create one!</div>';
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    
                    mysqli_stmt_close($stmt);
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include "includes/footer.php";
?>
