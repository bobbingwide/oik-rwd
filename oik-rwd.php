<?php 
/*
Plugin Name: oik-rwd
Plugin URI: https://oik-plugins.com/oik-plugins/oik-rwd
Description: Dynamically generate responsive CSS classes for width and height (with margins and/or padding) using the [bw_rwd] shortcode
Version: 0.5.3  
Author: bobbingwide
Author URI: https://bobbingwide.com/about-bobbing-wide
License: GPL2


    Copyright 2013-2017, 2023 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Implement "oik_loaded" action for oik-rwd
 * 
 * Determine if class interception should be performed for oik-rwd
 * If so, then define our implementation of the "oik_shortcode_atts" action hook
 * Note: If the option is not set we can use [bw_rwd] with no parameters to enable it at a later stage
 */
function oik_rwd_init() {
  $class_intercept = bw_get_option( "class_intercept", "bw_rwd" );
  if ( $class_intercept ) {
    add_action( "oik_shortcode_atts", "oik_rwd_shortcode_atts", 10, 3 );
  }  
}

/**
 * Implement "oik_add_shortcodes" for oik-rwd
 */    
function oik_rwd_add_shortcodes() {
  bw_add_shortcode( "bw_rwd", "bw_rwd", oik_path( "shortcodes/oik-rwd.php", "oik-rwd" ), false ); 
}

/**
 * Implement "oik_shortcode_atts" filter for oik-rwd 
 *
 * The oik-rwd plugin looks for the class= parameter. 
 * If found it may generate dynamic class definitions even when the [bw_rwd] shortcode is not present.
 * 
 * @param array $atts - shortcode parameters
 * @param string $content - may be null
 * @param string $tag - the name of the shortcode
 *
 */
function oik_rwd_shortcode_atts( $atts, $content, $tag ) {
  $class = bw_array_get( $atts, "class", null );
  if ( $class ) {
    oik_rwd_oik_class_intercept( $class );
  }
  return( $atts );
}

/**
 * Implement "oik_class_intercept" action
 *
 * @TODO "oik_class_intercept" is not yet implemented by oik
 */
function oik_rwd_oik_class_intercept( $class ) {
  if ( $class ) {
    oik_require( "shortcodes/oik-rwd.php", "oik-rwd" );
    oik_rwd_class( $class );
  }  
} 

/**
 * Implement "oik_responsive_column_class" filter for oik-rwd
 * 
 * @TODO - Implement some more advanced logic...
 * Given a class name that looks a bit like a wnnpc 
 * transform it into a responsive class with an appropriate amount of padding and margin
 * e.g. w33pc will become w33p0m2
 *      w50pc will become w50p0m2
 * We can do this based on the number of columns and the current column
 *
 * Note: this is a filter hook with a side effect of calling oik_rwd_class()
 *
 * @param string $class - the class name we want to responsify. e.g. w50pc
 * @param integer $columns - the number of columns we're taking about
 * @param integer $column - the current column - future use
 * @param array $atts - shortcode parameters
 * @return string - the new class name
 *
 */
function oik_rwd_oik_responsive_column_class( $class, $columns, $column, $atts=null ) {
  oik_require( "shortcodes/oik-rwd.php", "oik-rwd" );
  $padding = bw_array_get( $atts, "padding", "p0" );
  $margin = bw_array_get( $atts, "margin", "m2" );
  $class = str_replace( "pc", "$padding$margin", $class );
  oik_rwd_class( $class );
  return( $class );
}

/** 
 * Implement "oik_admin_menu" action for oik-rwd
 * 
 * @TODO - this version only supports "class interception"
 * 
 */ 
function oik_rwd_admin_menu() {
  oik_require( "admin/oik-rwd.php", "oik-rwd" );
  oik_rwd_lazy_admin_menu();
}

/**
 * Set the plugin server. Not necessary for a plugin on WordPress.org
 */
// function oik_rwd_admin_menu() {
//  oik_register_plugin_server( __FILE__ );
//}

/**
 * Implement "admin_notices" for oik-rwd to check plugin dependency
 *
 * Now dependent upon oik v3.0.0 or higher
 */ 
function oik_rwd_activation() {
  static $plugin_basename = null;
  if ( !$plugin_basename ) {
    $plugin_basename = plugin_basename(__FILE__);
    add_action( "after_plugin_row_oik-rwd/oik-rwd.php", "oik_rwd_activation" ); 
    if ( !function_exists( "oik_plugin_lazy_activation" ) ) { 
      require_once( "admin/oik-activation.php" );
    }
  }  
  $depends = "oik:3.0.0";
  oik_plugin_lazy_activation( __FILE__, $depends, "oik_plugin_plugin_inactive" );
}

/**
 * Function to run when the plugin file is loaded 
 */
function oik_rwd_plugin_loaded() {
  add_action( "admin_notices", "oik_rwd_activation" );
  //add_action( "oik_admin_menu", "oik_rwd_admin_menu" );
  add_action( "oik_loaded", "oik_rwd_init" );
  add_action( "oik_add_shortcodes", "oik_rwd_add_shortcodes" );
  add_action( "oik_admin_menu", "oik_rwd_admin_menu" );
  add_filter( "oik_responsive_column_class", "oik_rwd_oik_responsive_column_class", 10, 4 );
}

oik_rwd_plugin_loaded();
