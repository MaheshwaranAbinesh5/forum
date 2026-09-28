<?php
include "includes/header.php";

// Check if user is logged in
requireLogin();

// Check if category ID is specified
if (!isset($_GET["category_id"]) || empty($_GET["category_id"])) {
    header("location: index.php");
    exit;
}

$category_id = $_GET["category_id"];

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

// Define variables and initialize with empty values
$subject = $content = "";
$subject_err = $content_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate subject
    if (empty(trim($_POST["subject"]))) {
        $subject_err = "Please enter a subject.";
    } elseif (strlen(trim($_POST["subject"])) > 255) {
        $subject_err = "Subject cannot exceed 255 characters.";
    } else {
        $subject = trim($_POST["subject"]);
    }
    
    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter thread content.";
    } else {
        $content = trim($_POST["content"]);
    }
    
    // Check input errors before inserting in database
    if (empty($subject_err) && empty($content_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO threads (subject, content, user_id, category_id) VALUES (?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssii", $param_subject, $param_content, $param_user_id, $param_category_id);
            
            // Set parameters
            $param_subject = $subject;
            $param_content = $content;
            $param_user_id = $_SESSION["user_id"];
            $param_category_id = $category_id;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Get the thread ID
                $thread_id = mysqli_insert_id($conn);
                
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
                <li class="breadcrumb-item"><a href="category.php?id=<?php echo $category_id; ?>"><?php echo escapeOutput($category['name']); ?></a></li>
                <li class="breadcrumb-item active">Create New Thread</li>
            </ol>
        </nav>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="card-title">Create New Thread in <?php echo escapeOutput($category['name']); ?></h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?category_id=" . $category_id; ?>" method="post">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>" maxlength="255">
                        <span class="invalid-feedback"><?php echo $subject_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control character-count <?php echo (!empty($content_err)) ? 'is-invalid' : ''; ?>" rows="10" maxlength="5000" data-counter="counter"><?php echo $content; ?></textarea>
                        <div id="counter" class="form-text text-end">0/5000</div>
                        <span class="invalid-feedback"><?php echo $content_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Create Thread</button>
                        <a href="category.php?id=<?php echo $category_id; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
