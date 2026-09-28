<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // Change to your database username
define('DB_PASSWORD', '');      // Change to your database password
define('DB_NAME', 'forum_db');

// Attempt to connect to MySQL database
$conn = mysqli_connect('localhost', 'root', '', 'forum_db');

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>