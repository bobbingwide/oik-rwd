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
  oik_menu_header( "Responsive Web Design", "w100pc" );
  oik_box( null, null, "Options", "oik_rwd_options" );
  //oik_box( null, null, "Media queries", "oik_rwd_mq" );
  //oik_box( null, null, "Test area", "oik_rwd_test_area" );
  oik_menu_footer();
  bw_flush();
}

/**
 * Display the options fields
 * 
 * Rather than attempt to calculate the Window width breakpoint ourselves we let the user define it.
 * In the short term this is a lot easier.
 * But we may still have to take into account the context in which the RWD classes are being used.  
 * which may lead to implementing the code in jQuery rather than server side generated CSS Media Queries
 * 
 */ 
function oik_rwd_options() {
  $option = "bw_rwd";
  $options = bw_form_start( $option, $option );
  bw_checkbox_arr( $option, "Intercept classes", $options, 'class_intercept');
  //bw_textfield_arr( $option,"Sidebar width (px)/percentage (%)", $options, 'sidebar', 6 );
  //bw_textfield_arr( $option,"Main body margins width (px)/percentage (%)", $options, 'margins', 6 );
  //bw_textfield_arr( $option, "Breakpoint px", $options, 'breakpoint', 6 );
  bw_textfield_arr( $option, "Window width breakpoint (px)", $options, 'max_width', 6 );
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

/**
 * Display the test area using an iframe of the home page
 * 
 * @TODO - Decide whether or not to implement this and if we should allow a test page to be specified.
 */
function oik_rwd_test_area() {
  oik_require( "shortcodes/oik-iframe.php" );
  oik_require( "shortcodes/oik-rwd.php", "oik-rwd" );
  $atts = array( "src" =>  site_url()
               , "width" => oik_rwd_adjusted_max_width_for_context()
               , "height" => 640
               , "frameborder" => "1" 
               , "scrolling" => "yes"
               );
  e( bw_iframe( $atts ) );
}

 
