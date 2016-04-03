<?php

include_once(plugin_dir_path( __FILE__ ) . 'log.php');

function hlsm_log($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

function hlsm_log_result($result, $action) {
    //On success
    if( !is_wp_error($result) ) {
        hlsm_log($action . " : " . $result);
    } else {
        if (is_object($result)) {
            hlsm_log($action . " failed: " . $result->get_error_message());
        } else {
            hlsm_log($action . " failed");
        }
    }
}

?>
