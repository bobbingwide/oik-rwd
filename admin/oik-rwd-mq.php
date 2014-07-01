<?php // (C) Copyright Bobbing Wide 2014

 

if ( !class_exists( "OIK_settings" ) ) {
  oik_require( "admin/oik-settings.php", "oik-rwd" );
}

/**
 * Edit the media queries settings for oik-rwd
 *
 */
function oik_rwd_lazy_mq() {
  $page = new OIK_rwd_mq_settings(); 
  $page->doPage();

}


/**
 * 
 * OIK_rwd_mq_settings class
 * 
 * Media query settings for oik-rwd
 */ 
class OIK_rwd_mq_settings extends OIK_settings {

  function init() {
    parent::init();
    $this->page = "oik_options_rwd";                      
    $this->option = "bw_rwd_mq";
    $this->option_add_label = "Add media query";
    
    $actions['preview'] = new OIK_action( "preview", "Preview" );
    $actions['edit'] = new OIK_action( "edit", "Edit" );
    $actions['delete'] = new OIK_action( "delete", "Delete" );
    $actions['add'] = new OIK_action( "add", "Add" );
    
    $this->actions = $actions; ///bw_as_array( "preview,edit,delete,add" );
    
    $this->action_labels = bw_as_array( "Preview,Edit,Delete,Add" );
    
    $fields['mq'] = new OIK_settings_field( "mq", "Media query" );
    $fields['max-width'] = new OIK_settings_field( "max-width" );
    $fields['padding'] = new OIK_settings_field( "padding" );
    
    $this->fields = $fields; // array( "mq" => "Media query", "max-width" => "Max width", "padding" => "Padding" );
    bw_trace2( $this, "this" );                     
  }

  function list_types() {
    p( "List" );
    parent::list_types();
    p( "acvter");
  }
  
  function preview_cb() {
    p( "Preview" );
  }
  
  function edit_cb() {
  }
  
  

}



