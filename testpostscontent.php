<?php

include_once(plugin_dir_path( __FILE__ ) . 'upload.php');

function hlsm_test_posts_content() {
    global $hlsm_uploaded_posts;
    return array(
        array(
            'ID' => idForHubspotId(0, $hlsm_uploaded_posts),
            'post_content'   => 'Test post content', // The full text of the post.
        ),
        array(
            'ID' => idForHubspotId(1, $hlsm_uploaded_posts),
            'post_content'   => 'Test post2 content', // The full text of the post.
        ));
}

?>
