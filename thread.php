<?php
include "includes/header.php";

// Check if thread ID is specified
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: index.php");
    exit;
}

$thread_id = $_GET["id"];

// Get thread details
$sql = "SELECT t.*, u.username, c.name as category_name, c.category_id 
        FROM threads t 
        JOIN users u ON t.user_id = u.user_id 
        JOIN categories c ON t.category_id = c.category_id 
        WHERE t.thread_id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $thread_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $thread = mysqli_fetch_assoc($result);
        } else {
            // Thread not found
            header("location: index.php");
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    
    mysqli_stmt_close($stmt);
}

// Process reply form if submitted
$reply_content = $reply_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn()) {
    // Validate reply content
    if (empty(trim($_POST["content"]))) {
        $reply_err = "Please enter a reply.";
    } else {
        $reply_content = trim($_POST["content"]);
    }
    
    // Check input errors before inserting in database
    if (empty($reply_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO replies (content, user_id, thread_id) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sii", $param_content, $param_user_id, $param_thread_id);
            
            // Set parameters
            $param_content = $reply_content;
            $param_user_id = $_SESSION["user_id"];
            $param_thread_id = $thread_id;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to refresh the page
                header("location: thread.php?id=" . $thread_id);
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="category.php?id=<?php echo $thread['category_id']; ?>"><?php echo escapeOutput($thread['category_name']); ?></a></li>
                <li class="breadcrumb-item active"><?php echo escapeOutput($thread['subject']); ?></li>
            </ol>
        </nav>
        
        <div class="card thread-card mb-4">
            <div class="card-header">
                <h1 class="card-title h4"><?php echo escapeOutput($thread['subject']); ?></h1>
                <div class="text-muted small">
                    Posted by <?php echo escapeOutput($thread['username']); ?> on <?php echo formatDate($thread['created_at']); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="thread-content mb-3">
                    <?php echo nl2br(escapeOutput($thread['content'])); ?>
                </div>
            </div>
        </div>
        
        <h2 class="h4 mb-3">Replies</h2>
        
        <?php
        // Get all replies for this thread
        $sql = "SELECT r.*, u.username 
                FROM replies r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.thread_id = ? 
                ORDER BY r.created_at ASC";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $thread_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $reply_count = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $reply_count++;
                        echo '<div class="card thread-card mb-3">';
                        echo '<div class="card-header d-flex justify-content-between">';
                        echo '<span class="reply-meta">Reply #' . $reply_count . ' by ' . escapeOutput($row['username']) . '</span>';
                        echo '<span class="reply-meta">' . formatDate($row['created_at']) . '</span>';
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<div class="reply-content">' . nl2br(escapeOutput($row['content'])) . '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-info">No replies yet. Be the first to reply!</div>';
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
        ?>
        
        <?php if (isLoggedIn()): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="h5">Post a Reply</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $thread_id; ?>" method="post">
                        <div class="mb-3">
                            <label for="content" class="form-label">Your Reply</label>
                            <textarea name="content" id="content" class="form-control character-count <?php echo (!empty($reply_err)) ? 'is-invalid' : ''; ?>" rows="5" maxlength="1000" data-counter="counter"><?php echo $reply_content; ?></textarea>
                            <div id="counter" class="form-text text-end">0/1000</div>
                            <span class="invalid-feedback"><?php echo $reply_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Post Reply</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Please <a href="login.php">login</a> to post a reply.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include "includes/footer.php";
?>