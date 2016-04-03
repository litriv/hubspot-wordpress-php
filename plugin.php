<?php
/**
 * Plugin Name: Migration from HubSpot
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Migration of blog from HubSpot to WordPress
 * Version: The Plugin's Version Number, e.g.: 1.0
 * Author: Jaco Esterhuizen
 * Author URI: http://URI_Of_The_Plugin_Author
 * License: A "Slug" license name e.g. GPL2
 */

// $hlsm_test = true;
$hlsm_test = false;

// $hlsm_delete = true;
$hlsm_delete = false;

$hlsm_upload = true;
// $hlsm_upload = false;

include_once(plugin_dir_path( __FILE__ ) . 'upload.php');
include_once(plugin_dir_path( __FILE__ ) . 'delete.php');
include_once(plugin_dir_path( __FILE__ ) . 'log.php');

include_once(plugin_dir_path( __FILE__ ) . 'authors.php');
include_once(plugin_dir_path( __FILE__ ) . 'testauthors.php');

include_once(plugin_dir_path( __FILE__ ) . 'topics.php');
include_once(plugin_dir_path( __FILE__ ) . 'testtopics.php');

include_once(plugin_dir_path( __FILE__ ) . 'posts.php');
include_once(plugin_dir_path( __FILE__ ) . 'testposts.php');

include_once(plugin_dir_path( __FILE__ ) . 'postsimages.php');

include_once(plugin_dir_path( __FILE__ ) . 'postsfeaturedimages.php');

include_once(plugin_dir_path( __FILE__ ) . 'postsurls.php');

include_once(plugin_dir_path( __FILE__ ) . 'postscontent.php');
include_once(plugin_dir_path( __FILE__ ) . 'testpostscontent.php');

include_once(plugin_dir_path( __FILE__ ) . 'postsmetadesc.php');

set_time_limit(300);

$hlsm_flags = file_get_contents(plugin_dir_path( __FILE__ ) . "flags");
$hlsm_delete = ($hlsm_flags === "10\n" || $hlsm_flags === "11\n");
$hlsm_upload = ($hlsm_flags === "01\n" || $hlsm_flags === "11\n");
$hlsm_uploaded_author_ids = array();
$hlsm_uploaded_post_ids = array();
$hlsm_uploaded_category_ids = array();
$hlsm_uploaded_image_ids = array();

function hlsm_detect_plugin_deactivation(  $plugin, $network_activation ) {
  global 
    $hlsm_flags,
    $hlsm_delete, 
    $hlsm_upload, 
    $hlsm_uploaded_author_ids,
    $hlsm_uploaded_post_ids,
    $hlsm_uploaded_category_ids,
    $hlsm_uploaded_image_ids;
  
  hlsm_log(array('plugin' => $plugin));
  if ($plugin !== "southerlymigrate/southerlymigrate.php") {
    return; 
  }
  hlsm_log("Flags");
  hlsm_log($hlsm_flags);
  
  if ($hlsm_delete) {
    // Delete authors
    hlsm_delete_authors();

    // Delete posts (most will get deleted by deleting authors, but a few remains with author 'admin')
    hlsm_delete_posts();
    
    // Delete categories (topics)
    hlsm_delete_categories();

    // Delete images
    hlsm_delete_images();
  }

  if ($hlsm_upload) {
    // Upload author
    withData(hlsm_data_factory("authors"), 'hlsm_upload_author');
    hlsm_log("Author ids:");
    hlsm_log($hlsm_uploaded_author_ids);
    $r = file_put_contents(plugin_dir_path( __FILE__ ) . "uploaded_author_ids", serialize($hlsm_uploaded_author_ids));
    if ($r === false) {
      hlsm_log("Writing file failed.");
    }

    // Upload topics
    withData(hlsm_data_factory("topics"), 'hlsm_upload_topic');
    hlsm_log("Category ids:");
    hlsm_log($hlsm_uploaded_category_ids);
    $r = file_put_contents(plugin_dir_path( __FILE__ ) . "uploaded_category_ids", serialize($hlsm_uploaded_category_ids));
    if ($r === false) {
      hlsm_log("Writing file failed.");
    }

    // Upload posts
    withData(hlsm_data_factory("posts"), 'hlsm_upload_post');
    hlsm_log("Post ids:");
    hlsm_log($hlsm_uploaded_post_ids);
    $r = file_put_contents(plugin_dir_path( __FILE__ ) . "uploaded_post_ids", serialize($hlsm_uploaded_post_ids));
    if ($r === false) {
      hlsm_log("Writing file failed.");
    }
    
    // Upload images
    withData(hlsm_data_factory("posts_images"), 'hlsm_upload_images');
    hlsm_log("Images ids:");
    hlsm_log($hlsm_uploaded_image_ids);
    $r = file_put_contents(plugin_dir_path( __FILE__ ) . "uploaded_image_ids", serialize($hlsm_uploaded_image_ids));
    if ($r === false) {
      hlsm_log("Writing file failed.");
    }
    withData(hlsm_data_factory("posts_featured_images"), 'hlsm_feature_image');

    // create_cta_links();
    // create_cta_img_srcs();

    withData(hlsm_data_factory("posts_urls"), 'hlsm_set_url_for_hubspot_url');
    withData(hlsm_data_factory("posts_content"), 'hlsm_update_post');
    withData(hlsm_data_factory("posts_metadesc"), 'hlsm_upload_post_metadesc');
    // withData(hlsm_data_factory("pages"), 'hlsm_upload_page');
  } 

  // Set flags to 00
  $r = file_put_contents(plugin_dir_path( __FILE__ ) . "flags", serialize("00"));
  if ($r === false) {
    hlsm_log("Resetting flags failed.");
  }
}

function hlsm_data_factory($data_type) {
  global $hlsm_test;
  $f = "hlsm_" . ($hlsm_test ? "test_" : "") . $data_type;
  return $f();
}

add_action( 'deactivated_plugin', 'hlsm_detect_plugin_deactivation', 10, 2 );

?>
