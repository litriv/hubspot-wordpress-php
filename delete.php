<?php

function hlsm_delete_authors() {
  // $authors = get_users(array('role' => 'contributor'));
  // hlsm_log(array('Existing authors' => $authors));
  $file_contents = file_get_contents(plugin_dir_path( __FILE__ ) . "uploaded_author_ids");
  hlsm_log($file_contents);
  $previous = unserialize($file_contents);
  // if (is_array($authors)) {
    // foreach ($authors as $a) {
      foreach ($previous as $k => $v) {
        // if ($a->ID == $v) {
          $r = wp_delete_user($v);
          if ($r) {
            hlsm_log("User deleted: " . $v  );
          } else {
            hlsm_log("User deletion failed: " . $v  );
          }
        // }
      }
    // }
  // }
}

function hlsm_delete_categories() {
  // $categories = get_categories(array('hide_empty' => false));
  // hlsm_log(array('Existing categories: ' => $categories));
  $file_contents = file_get_contents(plugin_dir_path( __FILE__ ) . "uploaded_category_ids");
  hlsm_log($file_contents);
  $previous = unserialize($file_contents);
  foreach ($previous as $k => $v) {
  // foreach ($categories as $c) {
    $r = wp_delete_category($v);
    if ($r === false) {
      // This shouldn't happen
      hlsm_log("Category delete attempt failed - category does not exist: " . $v  );
    } else if ($r === true) {
      hlsm_log("Deleted category: " . $v  );
    } else if ($r === 0) {
      hlsm_log("Attempted to delete default category: " . $v  );
    } else {
      hlsm_log_result($r, "Category deletion");
    }
  // }
  }
}

function hlsm_delete_images() {
  hlsm_log("Deleting images...");
  // $posts = get_posts( array(
  //   'posts_per_page' => 10000,
  //   'post_type' => 'attachment')
  // );
  $file_contents = file_get_contents(plugin_dir_path( __FILE__ ) . "uploaded_image_ids");
  hlsm_log($file_contents);
  $previous = unserialize($file_contents);
  hlsm_log("Previous images:");
  hlsm_log($previous);
  // foreach ($posts as $p) {
    foreach ($previous as $k => $v) {
      // if ($p->ID == $v) {
        $r = wp_delete_attachment($v);
        if (0 === $r) {
          hlsm_log("Failed to delete image attachment: " . $v);
        } else {
          hlsm_log("Deleted: " . $v);
        }
      // }
    } 
  // }
}

function hlsm_delete_posts() {
  $force = true;
  // $ps = get_posts(array('posts_per_page' => 10000, 'post_status' => 'any'));
  $file_contents = file_get_contents(plugin_dir_path( __FILE__ ) . "uploaded_post_ids");
  hlsm_log($file_contents);
  $previous = unserialize($file_contents);
  foreach ($previous as $k => $v) {
  // foreach ($ps as $p) {
    // foreach (hlsm_data_factory("posts") as $k => $v) {
    // if ($v['post_title'] == $p->post_title) {
    $r = wp_delete_post($v, $force);
    if ($r !== false) {
      hlsm_log("Deleted post: ". $v);
    } else {
      hlsm_log("Failed to delete post: " . $v);
    }
    // }
    // }
  // }
  }
}

?>
