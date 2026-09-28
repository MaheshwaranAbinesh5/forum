<?php
include "includes/header.php";

// Check if user is logged in
requireLogin();

// Check if thread ID is specified
if (!isset($_GET["thread_id"]) || empty($_GET["thread_id"])) {
    header("location: index.php");
    exit;
}

$thread_id = $_GET["thread_id"];

// Get thread details
$sql = "SELECT t.*, c.name as category_name, c.category_id 
        FROM threads t 
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

// Define variables and initialize with empty values
$content = "";
$content_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter reply content!!";
    } else {
        $content = trim($_POST["content"]);
    }
    
    // Check input errors before inserting in database
    if (empty($content_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO replies (content, user_id, thread_id) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sii", $param_content, $param_user_id, $param_thread_id);
            
            // Set parameters
            $param_content = $content;
            $param_user_id = $_SESSION["user_id"];
            $param_thread_id = $thread_id;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the thread page
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
    <div class="col-md-10 offset-md-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="category.php?id=<?php echo $thread['category_id']; ?>"><?php echo escapeOutput($thread['category_name']); ?></a></li>
                <li class="breadcrumb-item"><a href="thread.php?id=<?php echo $thread_id; ?>"><?php echo escapeOutput($thread['subject']); ?></a></li>
                <li class="breadcrumb-item active">Post Reply</li>
            </ol>
        </nav>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="card-title">Post Reply to "<?php echo escapeOutput($thread['subject']); ?>"</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?thread_id=" . $thread_id; ?>" method="post">
                    <div class="mb-3">
                        <label for="content" class="form-label">Your Reply</label>
                        <textarea name="content" id="content" class="form-control character-count <?php echo (!empty($content_err)) ? 'is-invalid' : ''; ?>" rows="7" maxlength="1000" data-counter="counter"><?php echo $content; ?></textarea>
                        <div id="counter" class="form-text text-end">0/1000</div>
                        <span class="invalid-feedback"><?php echo $content_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Post Reply</button>
                        <a href="thread.php?id=<?php echo $thread_id; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
