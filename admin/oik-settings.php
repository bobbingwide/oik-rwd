<?php // (C) Copyright Bobbing Wide 2014

/**
 * OIK_actions class
 *
 * Defines the action names, labels and callback functions to implement them
 *
 * Standard actions   Labels
 * ----------------   ---------------
 * add                Add
 * add_this           Add 
 * delete             Delete
 * edit               Edit
 * list
 * preview            Preview
 *
 *
 */               
class OIK_action {

  var $action;
  var $action_label;
  var $action_cb;
  
  function __construct( $action=null, $action_label=null, $action_cb=null ) {
    $this->action = $action;
    if ( $action_label ) {
      $this->action_label = $action_label;
    } else {
      $this->action_label = $action; 
    }
    
    if( $action_cb ) {
      $this->action_cb = $action_cb;
    } else {
      $this->action_cb = "${action}_cb"; 
    }  
  }
}

/**
 * OIK_settings_field class
 */  
class OIK_settings_field {
  var $field;
  var $label;
  var $field_type;
  var $value;
  function __construct( $field=null, $label=null, $field_type=null ) {
    $this->field = $field;
    if ( $label ) {
      $this->label = $label; 
    } else {
      $this->label = $field;
    }
    if ( $field_type ) {
      $this->field_type = $field_type;
    } else {
      $this->field_type = "text";
      
    } 
  }
  
  function get_value( $field ) {
    return( $this->value );
  }
  
  function render() {
    p( $this->field );
    p( $this->label );
  }
   
}

/**
 * OIK_settings class
 * 
 * options management for oik plugins settings
 *
 * 
 * - Displays a list of settings values for a given option field.
 * - Each row supports actions such as "Edit", "Delete", "Preview"
 * - "Add" new action button displayed below the list
 * - When "Add" new chosen then a new box is displayed above the list
 * - With an "Add this" button
 * - When "Edit" chosen then the box is displayed in edit mode, with an "Edit" button
 * - "Delete" deletes the entry
 * - "Preview" displays the entry
 *
 *
 * Processing depends on the button that was pressed. There should only be one!
 * 
 * Selection                       Validate? Perform action        Display preview Display add  Display edit Display select list
 * ------------------------------- --------  -------------------   --------------- ------------ ------------ -------------------
 * preview_$option                    No        n/a                   Yes             -            -            -
 * delete_$option                     No        delete selected       -               -            -            Yes
 * edit_$option                       No        n/a                   -               -            Yes          Yes
 *
 * _oik_settings_edit_$option         Yes       update selected type  -               -            Yes          Yes
 * _oik_settings_add_this_$option     Yes       add the new option    -               -            Yes          Yes
 * _oik_settings_add_$option          No        display add           -               Yes          -            Yes
 *
 * The format for the settings stored in the options table is as serialised array with a key field, which is expected to be unique and multiple data fields. 
 * e.g. For the options field "bw_rwd_mq" ( media queries settings )
 * the key is mq ( abbreviation for media query ) and the data fields are:
 * - max-width ( a set of width pairs )
 * - padding ( as max-width  )
 * 
 * 
 */
class OIK_settings {

  var $page = null;
  var $option = null;

  var $buttons = array();
  var $menu_label = null;
  var $list_label = "List";
  var $list_types_cb = null; 
  var $option_add_label = "Add";
  
  var $fields = array();
  var $actions = array();
  
  /**
   * Display the table headings for the fields
   */ 
  function field_headings() {
    $field_headings = array();
    foreach ( $this->fields as $field => $field_obj ) {
      $field_headings[] = $field_obj->label;
    } 
    $field_headings[] = "Action";
  
    return( $field_headings );
  }
  
  /**
   * Display the table of option data and links
   * 
   */
  function field_rows() {
    $option = get_option( $this->option );
    if ( is_array( $option ) && count( $option )) {
      foreach ( $option as $key => $data ) {
        //$type = bw_array_get( $bw_type, "type", null );
        //_oik_cpt_type_row( $type, $data );
        $this->field_row( $key, data );
      }
    } else {
      p( "Nothing to show" );
    }  
  } 
  
    
  /** 
   * Display a single row of data
   *
   */  
  function field_row( $key, $data ) {
    /*
    bw_trace2();
    $row = array();
    $row[] = $type;
    $args = $data['args'];
    $fields = bw_array_get( $data, 'fields', null );
    $row[] = esc_html( stripslashes( $args['label'] ) ) . "&nbsp";
    $singular_name = bw_return_singular_name( $args );
    $row[] = esc_html( stripslashes( $singular_name ) ) . "&nbsp";
    $row[] = esc_html( stripslashes( $args['description'] ) ) . "&nbsp";  
    $row[] = icheckbox( "hierarchical[$type]", $args['hierarchical'], true );
    */
  
    $row = fields( $key, $data );
    $row[] = links( $key, $data );
  
    bw_tablerow( $row );
   
  }
  
  function get_action( $action ) {
    $action_obj = bw_array_get( $this->actions, $action, null );
    return( $action_obj );
  }
  
  function get_action_label( $action ) {
    $action_obj = $this->get_action( $action );
    bw_trace2( $action_obj, "axction_obj" );
    return( $action_obj->action_label );
  }
  
  function get_action_cb( $action ) {
    $action_obj = $this->get_action( $action );
    
    return( array( $this, $action_obj->action_cb ) );
  }
  
  /**
   * Return a link to invoke the action if supported
   */
  function link( $action, $key ) {
    static $nbsp = null;
    $link = null;
    $action_obj = $this->get_action( $action );
    if ( $action_obj ) {
      $url = "admin.php?page=";
      $url .= $this->page;
      $url .= "&amp;$action_";
      $url .= $this->option;
      $url .= '"';
      $url .= $key;
      $link .= $nbsp;
      $action_label = $action_obj->action_label;
      $link .= retlink( null, admin_url( $url ), $action_label );
    }
    $nbsp = "&nbsp;";
    return( $link );
  } 
  
  /**
   * Return the set of action links for the row
   * @TODO Allow other actions
   */  
  function links( $key, $data ) {
    $links = null;
    $links .= link( "preview", $key );
    $links .= link( "delete", $key );
    $links .= link( "edit", $key );
    return( $links );
  }
  
  function add_cb() {
    p( "Add cb" );
  }
    
  


  function __construct() {
    $this->init();
    
  }
  
  function init() {
    $this->list_types_cb = array( $this, "list_types" );
    
  
  }
  
    
  /**
   * Generic handling of the settings page
   */  
  function dopage() {
    //oik_menu_header( "types", "w100pc" );
    $this->do_actions();
    
    
    
    oik_box( null, null, $this->list_label, $this->list_types_cb );
    
    //oik_menu_footer();
    bw_flush();
    
  
  }
  
  /**
   * 
   */
  function do_actions() {
    $validated = false;
    $option = $this->option;
    $preview_type = bw_array_get( $_REQUEST, "preview_$option", null );
    $delete_type = bw_array_get( $_REQUEST, "delete_$option", null );
    $edit_type = bw_array_get( $_REQUEST, "edit_$option", null );
  
    /** These codes override the ones from the list... but why do we need to do it? 
     * Do we have to receive the others in the $_REQUEST **?**
     *
    */
    $oik_settings_edit_type = bw_array_get( $_REQUEST, "_oik_settings_edit_$option", null );
    $oik_settings_add_this_type = bw_array_get( $_REQUEST, "_oik_settings_add_this_$option", null );
    $oik_settings_add_type = bw_array_get( $_REQUEST, "_oik_settings_add_$option", null );
    if ( $oik_settings_add_type || $oik_settings_add_this_type ) {
      $preview_type = null;
      $delete_type = null;
      $edit_type = null; 
    }  
  
  
    if ( $preview_type ) {
      //oik_box( NULL, NULL, "Preview", "oik_cpt_preview" );
    } 
  
    if ( $delete_type ) { 
      //_oik_settings_delete_type( $delete_type );
    }  

    if ( $edit_type ) {
      //bw_build_overridden_type( $edit_type );
    }
    if ( $oik_settings_edit_type ) {  
      $validated = _oik_settings_type_validate( false );
    }  
  
    if ( $oik_settings_add_this_type ) {
      $validated = _oik_settings_type_validate( true );
    }
  
    if ( $oik_settings_add_type || ( $oik_settings_add_this_type && !$validated )  ) {
      oik_box( NULL, NULL, $this->get_action_label( "add" ), $this->get_action_cb( "add") );
    }
  
    if ( $edit_type || $oik_settings_edit_type || $validated ) {
      oik_box( null, null, "Edit type", "oik_settings_edit_type" );
    }
  }  
  
  /**
   * Display the table of registered post types 
   * 
   * This table includes the oik custom post types.
   */
  function list_types() {
    p( "" );
    bw_form();
    stag( "table", "widefat" );
    stag( "thead");
    bw_tablerow( $this->field_headings() );
    etag( "thead");
    //_oik_cpt_registered_table();
    $this->field_rows();
    etag( "table" );
    p( isubmit( "_oik_settings_add_" . $this->option, $this->option_add_label, null, "button-primary" ) );
    etag( "form" );
    // bw_flush();
  } 
  
  
/*  
  
  
  
  if ( $preview_type ) {
    oik_box( NULL, NULL, "Preview", "oik_cpt_preview" );
  } 
  
  if ( $delete_type ) { 
    _oik_cpt_delete_type( $delete_type );
  }  

  if ( $edit_type ) {
    bw_build_overridden_type( $edit_type );
  }
  if ( $oik_cpt_edit_type ) {  
    $validated = _oik_cpt_type_validate( false );
  }  
  
  if ( $oik_cpt_add_oik_cpt ) {
    $validated = _oik_cpt_type_validate( true );
  }
  
  if ( $oik_cpt_add_type || ( $oik_cpt_add_oik_cpt && !$validated )  ) {
    oik_box( NULL, NULL, "Add new", "oik_cpt_add_oik_cpt" );
  }
  
  if ( $edit_type || $oik_cpt_edit_type || $validated ) {
    oik_box( null, null, "Edit type", "oik_cpt_edit_type" );
  }
  oik_box( NULL, NULL, "types", "oik_cpt_types" );
  //oik_box( NULL, NULL, "registered types", "oik_cpt_registered_types" );
  oik_box( null, null, "registered types", "oik_cpt_registered_types" );
  oik_menu_footer();
  bw_flush();
}
 
 */ 
  

}
