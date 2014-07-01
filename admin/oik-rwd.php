<?php // (C) Copyright Bobbing Wide 2014

/**
 * Implement "oik_admin_menu" filter 
 *
 * Lazy implementation of a settings page for oik-rwd
 * 
 */
function oik_rwd_lazy_admin_menu() {
  register_setting( 'bw_rwd', 'bw_rwd', 'oik_options_validate' );
  add_submenu_page( 'oik_menu', 'oik RWD', 'Responsive Web Design', 'manage_options', 'oik_options_rwd', 'oik_rwd_do_page');
}


/** 
 * Draw the oik RWD option page
 *
 * Settings for oik-rwd are saved in the bw_rwd options field
 *
 */
function oik_rwd_do_page() {
  oik_menu_header( "Responsive Web Design" );
  oik_box( null, null, "Options", "oik_rwd_options" );
  //oik_box( null, null, "Media queries", "oik_rwd_mq" );
  oik_menu_footer();
  bw_flush();
}

/**
 * Display the options fields
 */ 
function oik_rwd_options() {
  $option = "bw_rwd";
  $options = bw_form_start( $option, $option );
  bw_checkbox_arr( $option, "Intercept classes", $options, 'class_intercept');
  etag( "table" );
  p( isubmit( "ok", "Save changes", null, "button-primary" ) );
  etag( "form" );
  bw_flush();

}

/**
 * Display the table of media query rule definitions
 *
 * @TODO tbc
 */
function oik_rwd_mq() {
  p( "Table of media queries." );
  oik_require( "admin/oik-rwd-mq.php", "oik-rwd" );
  oik_rwd_lazy_mq();
}
