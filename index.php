<?php
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Forum Categories</h1>
        
        <?php
        // Get all categories
        $sql = "SELECT * FROM categories ORDER BY name";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
            while ($row = mysqli_fetch_assoc($result)) {
                // Count threads in this category
                $thread_sql = "SELECT COUNT(*) as thread_count FROM threads WHERE category_id = " . $row['category_id'];
                $thread_result = mysqli_query($conn, $thread_sql);
                $thread_count = mysqli_fetch_assoc($thread_result)['thread_count'];
                
                echo '<div class="col">';
                echo '<div class="card category-card h-100">';
                echo '<div class="card-header">';
                echo '<h5 class="card-title mb-0">' . escapeOutput($row['name']) . '</h5>';
                echo '</div>';
                echo '<div class="card-body">';
                echo '<p class="card-text">' . escapeOutput($row['description']) . '</p>';
                echo '<p class="card-text"><small class="text-muted">' . $thread_count . ' thread(s)</small></p>';
                echo '<a href="category.php?id=' . $row['category_id'] . '" class="btn btn-primary">View Discussions</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">No categories found.</div>';
        }
        ?>
    </div>
</div>

<?php
include "includes/footer.php";
?>
