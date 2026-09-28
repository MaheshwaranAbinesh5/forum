<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    return false;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Get username by ID
function getUsernameById($conn, $userId) {
    $sql = "SELECT username FROM users WHERE user_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $userId);
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt, $username);
                mysqli_stmt_fetch($stmt);
                return $username;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    return "Unknown User";
}

// Format timestamp to readable date
function formatDate($timestamp) {
    return date("F j, Y, g:i a", strtotime($timestamp));
}

// Get category name by ID
function getCategoryNameById($conn, $categoryId) {
    $sql = "SELECT name FROM categories WHERE category_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt, $name);
                mysqli_stmt_fetch($stmt);
                return $name;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    return "Unknown Category";
}

// Escape user input for output
function escapeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>