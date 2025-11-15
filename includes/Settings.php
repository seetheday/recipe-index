<?php
/**
Settings for Visual Recipe Index
Author : Simon Austin (simon@kremental.com)
Inspired by the Category Grid View Plugin by Anshul Sharma
 */

//Default Plugin Settings
function riview_default_settings(){
        $defaults = array(
    'default_image' => VRI_PLUGIN_URL . 'includes/default.jpg',
        'custom_image' => VRI_PLUGIN_URL  .'includes/default.jpg',
    'credits' => 1,
    'color_scheme' => 'light',
    'image_source' => 'featured',
	'lightbox_width' => '700',
	'lightbox_height' => '400',
	'load_comments' => 0);
  return $defaults;
}

 
function riview_verify_options(){
  $current_settings = riview_get_options();
  update_option('riview' , $current_settings);
      do_action('riview_verify_options');
}

function riview_setup_options() {
 
  riview_remove_options();
  $default_settings = riview_default_settings();
  update_option('riview' , $default_settings);
  do_action('riview_setup_options');
}


function riview_remove_options() {
  delete_option('riview');
  do_action('riview_remove_options');
}

function riview_get_options() {
  $current_settings = get_option('riview');
  if ( ! is_array( $current_settings ) ) {
        $current_settings = array();
  }

  return wp_parse_args( $current_settings, riview_default_settings() );
}

function get_riview_option($option) {
  $get_riview_options = riview_get_options();
  return isset( $get_riview_options[$option] ) ? $get_riview_options[$option] : '';
}


function print_riview_option($option) {
  $get_riview_options = riview_get_options();
  if ( isset( $get_riview_options[$option] ) ) {
        echo $get_riview_options[$option];
  }
}


