<?php

include_once(plugin_dir_path( __FILE__ ) . 'log.php');
if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

function withData($ds, $f) {
  foreach($ds as $hubspot_id => $d) {
    $f($d, $hubspot_id);
  }
}

function idForHubspotId($hubspot_id, $from, $calledFrom) {
  hlsm_log($calledFrom . " idForHubspotId hubspot_id: " . $hubspot_id);
  return $from[$hubspot_id];
}

// Authors

$hlsm_uploaded_authors = array();

function hlsm_upload_author($userdata, $hubspot_id) {
  global $hlsm_uploaded_authors, $hlsm_uploaded_author_ids;
  $id = wp_insert_user( $userdata ) ;  // returns id or WP_Error
  hlsm_log_result($id, "User upload");
  hlsm_log($id);
  // If the user exists, we grab his id and put it in the map
  if (is_wp_error($id)) {
    $users = get_users();
    foreach ($users as $user) {
      if ($user->user_login === $userdata['user_login']) {
        $id = $user->ID;
        hlsm_log($id);
        break;
      }
    }  
  } else {
    // Record the image id, for deletion
    array_push($hlsm_uploaded_author_ids, $id);
  }
  $hlsm_uploaded_authors[$hubspot_id] = $id;
}

// Topics (categories)

$hlsm_uploaded_topics = array();

function hlsm_upload_topic($name, $hubspot_id) {
  global $hlsm_uploaded_topics, $hlsm_uploaded_category_ids;
  $id = wp_create_category($name);  // returns 0 of failure, id on success
  hlsm_log_result($id, "Category upload");
  if ($id !== 0) {
    array_push($hlsm_uploaded_category_ids, $id);
  }
  $hlsm_uploaded_topics[$hubspot_id] = $id;
}

// Images

$hlsm_uploaded_images = array();
$hlsm_attach_ids_first_images = array(); // Used for featuring images
function hlsm_upload_images($images, $pid) {
  global $hlsm_uploaded_images, $hlsm_attach_ids_first_images, $hlsm_uploaded_image_ids;

  $index = 0;
  foreach ($images as $hubspot_url => $filename) {
    // Upload the file
    $result = upload_bits(plugin_dir_path( __FILE__ ) . 'images/' . $filename);
    $attachment = create_attachment($filename, $result);
    // Insert the attachment into the media library
    $attach_id = wp_insert_attachment($attachment, $result['file'], $pid);
    attach_media($result, $attach_id);
    array_push($hlsm_uploaded_image_ids, $attach_id);
    if ($index === 0) {
      hlsm_log("Flagging first image for: " . $pid);
      $hlsm_attach_ids_first_images[$pid] = $attach_id;
    }
    $hlsm_uploaded_images[$hubspot_url] = $result['url'];
    $index++;
  }
}

function upload_bits($file) {
    // Upload the file
    hlsm_log("Attempting to upload: " . $file);
    $filename = basename($file);
    $result = wp_upload_bits($filename, null, file_get_contents($file));
    hlsm_log(array('wp_upload_bits results' => $result));
    return $result;
}

function create_attachment($filename, $result) {
    // Check the type of tile. We'll use this as the 'post_mime_type'.
    // $filetype = wp_check_filetype( basename( $filename ), null );
    $filetype = wp_check_filetype( $filename, null );
    hlsm_log(array("File type: " => $filetype));
    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();
    // Prepare an array of post data for the attachment.
    $attachment = array(
      'guid'           => $wp_upload_dir['url'] . '/' . basename( $result['file']), 
      'post_mime_type' => $filetype['type'],
      'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $result['file'])),
      'post_content'   => '',
      'post_status'    => 'inherit'
    );
    return $attachment;
}

function attach_media($upload_result, $attach_id) {
  hlsm_log_result($attach_id, "Insert attachment");
  // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
  require_once( ABSPATH . 'wp-admin/includes/image.php' );
  // Generate the metadata for the attachment, and update the database record.
  $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_result['file']);
  $update_metadata_result = wp_update_attachment_metadata( $attach_id, $attach_data );
  if ( false === $update_metadata_result) {
    hlsm_log("File upload failed");
  } else {
    hlsm_log("File uploaded and attached to media library");
  }
}

function hlsm_feature_image($pid, $_) {
  global $hlsm_attach_ids_first_images;
  hlsm_log("Featuring image for:" . $pid . ", " . $hlsm_attach_ids_first_images[$pid]);
  // set featured image
  add_post_meta($pid, '_thumbnail_id', $hlsm_attach_ids_first_images[$pid]);
}

// Posts

$hlsm_uploaded_posts = array();

function hlsm_upload_post($postdata, $hubspot_id) {
  global $hlsm_uploaded_posts, $hlsm_uploaded_post_ids;
  $shouldReturnWP_ErrorOnError = true;
  $id = wp_insert_post($postdata, $shouldReturnWP_ErrorOnError);
  hlsm_log_result($id, "Post upload");
  array_push($hlsm_uploaded_post_ids, $id);
  $hlsm_uploaded_posts[$hubspot_id] = $id;
}

// Stores the post url for the hubspot url
$hlsm_uploaded_posts_by_url = array();

function hlsm_set_url_for_hubspot_url($hubspot_url, $hubspot_id) {
  global $hlsm_uploaded_posts_by_url, $hlsm_uploaded_posts;
  hlsm_log("Setting url for hubspot url: " . $hubspot_url);
  $hlsm_uploaded_posts_by_url[$hubspot_url] = get_permalink($hlsm_uploaded_posts[$hubspot_id]);
}

function hlsm_update_post($postcontent) {
  $shouldReturnWP_ErrorOnError = true;
  $id = wp_update_post($postcontent, $shouldReturnWP_ErrorOnError);
  hlsm_log_result($id, "Post update");
}

function hlsm_upload_post_metadesc($metacontent) {
  $r = add_post_meta($metacontent['ID'], '_yoast_wpseo_metadesc', $metacontent['desc'], true);
  if ($r === true) {
    hlsm_log(array("Yoast SEO meta description added" => $metacontent['ID']));
  } else {
    hlsm_log(array("Yoast SEO meta description already exists" => $metacontent['ID']));
    $r = update_post_meta($metacontent['ID'], '_yoast_wpseo_metadesc', $metacontent['desc']);
    // TODO do some error checking and reporting here
  }
}

$hlsm_landing_page_titles = array(
  "how-to-run-a-content-marketing-strategy-workshop",
);
$hlsm_cta_links = array();
function create_cta_links() {
  global $hlsm_cta_links, $hlsm_landing_page_titles;
  foreach ( get_pages() as $p ) {
    if (
      $p->post_title === $hlsm_landing_page_titles[0] || 
      $p->post_title === $hlsm_landing_page_titles[1] || 
      $p->post_title === $hlsm_landing_page_titles[2] || 
      $p->post_title === $hlsm_landing_page_titles[3]
    ) {
      $hlsm_cta_links[$p->post_title] = $get_page_link($p->ID);
    }
  }
}

$hlsm_cta_img_srcs = array();
function create_cta_img_srcs() {
  global $hlsm_cta_img_srcs, $hlsm_landing_page_titles;
  $toUpload = array(
    $hlsm_landing_page_titles[0] => 'name of file to upload',
  );
  foreach ($toUpload as $title => $filename) {
    $result = upload_bits(plugin_dir_path( __FILE__ ) . 'ctaimages/' . $filename);
    $attachment = create_attachment($filename, $result);
    // Insert the attachment into the media library
    $attach_id = wp_insert_attachment($attachment, $result['file'], $pid);
    attach_media($result, $attach_id);
    $hlsm_cta_img_src[$title] = $result['url'];
  }
}
?>
