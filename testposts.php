<?php

include_once(plugin_dir_path( __FILE__ ) . 'upload.php');

function hlsm_test_posts() {
    global $hlsm_uploaded_authors, $hlsm_uploaded_topics;
    return array(
        0 => array(
            //'post_content'   => 'Test post content', // The full text of the post.
            'post_name'      => 'test-post', // The name (slug) for your post
            'post_title'     => 'Test post', // The title of your post.
            'post_status'    => 'draft', // | 'publish' | 'pending'| 'future' | 'private' | custom registered status ] // Default 'draft'.
            'post_type'      => 'post', // Default 'post'.
            'post_author'    => idForHubspotId(0, $hlsm_uploaded_authors), // The user ID number of the author. Default is the current user ID.
            //'ping_status'    => [ 'closed' | 'open' ] // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
            //'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
            //'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
            //'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
            //'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
            //'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
            //'guid'           => // Skip this and let Wordpress handle it, usually.
            //'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
            //'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
            'post_date'      => date('2010-01-01 12:00:00'), // [ Y-m-d H:i:s ] // The time post was made.
            //'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
            //'comment_status' => [ 'closed' | 'open' ] // Default is the option 'default_comment_status', or 'closed'.
            'post_category'  => array(idForHubspotId(1, $hlsm_uploaded_topics), idForHubspotId(2, $hlsm_uploaded_topics)), // Default empty.
            //'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
            //'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
            //'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
        ),
        1 => array(
            //'post_content'   => 'Test post2 content', // The full text of the post.
            'post_name'      => 'test-post2', // The name (slug) for your post
            'post_title'     => 'Test post2', // The title of your post.
            'post_status'    => 'publish', // | 'publish' | 'pending'| 'future' | 'private' | custom registered status ] // Default 'draft'.
            'post_type'      => 'post', // Default 'post'.
            'post_author'    => idForHubspotId(1, $hlsm_uploaded_authors), // The user ID number of the author. Default is the current user ID.
            //'ping_status'    => [ 'closed' | 'open' ] // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
            //'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
            //'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
            //'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
            //'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
            //'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
            //'guid'           => // Skip this and let Wordpress handle it, usually.
            //'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
            //'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
            'post_date'      => date('2012-02-02 14:02:02'), // [ Y-m-d H:i:s ] // The time post was made.
            //'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
            //'comment_status' => [ 'closed' | 'open' ] // Default is the option 'default_comment_status', or 'closed'.
            'post_category'  => array(1), // Default empty.
            //'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
            //'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
            //'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
        ));
}

?>
