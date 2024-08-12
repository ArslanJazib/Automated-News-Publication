<?php
/**
 * Template Name: Temporary Link Content
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Temp Post</title>
</head>
<body>
<?php
// Get the post ID from the URL parameter
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

// Check if the post ID is valid
if ($post_id ) {

?>

       <?php
            $post_content = get_post_field('post_content', $post_id);
            echo apply_filters('the_content', $post_content);
            ?>



    <?php
} else {
    // Display a message for expired or invalid links
    ?>

    <div>
        <h2>Invalid or Expired Link</h2>
        <p>The link you've accessed is no longer valid.</p>
    </div>

    <?php
}
?>

</body>
</html>