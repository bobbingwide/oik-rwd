<?php // (C) Copyright Bobbing Wide 2013, 2014

/**
 * Dynamically generate the CSS class that is suggested by the class name
 * 
 * We use PCRE - Perl Compatible Regular Expressions to define the class names that we expect to find
 * 
 * Format is: 
 * <pre>
 *  - single character (w=width/h=height)
 *  - Numeric width or height (depth)
 *  - single character ( p=padding left and right/l=padding-left/r=padding-right/t=padding-top/b=padding-bottom (h) or padding-both (w) )
 *    should we support margin as well as padding **?**
 *  - Numeric width or height ( depth ) for the padding. 
 *  - single character ( as for previous )
 *  - Numeric width or height ( depth) for the margin-right or margin-bottom
 * </pre>
 * 
 * Note: When 'p' then the same padding is applied left and right
 * When 'm' then the margin is only applied to the right or bottom
 * 
   int preg_match ( string $pattern , string $subject [, array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )

 */  
function oik_rwd_dynamic_css_class( $class=null ) { 
  // $pattern = "/(^[who])([0-9]+)([plrtb])([0-9]+)/";
  $pattern = "/(^[wh])([0-9]+)([plrtbm])([0-9]+)([plrtbm]*)([0-9]*)/";
  $matches = null;
  $count = preg_match( $pattern, $class, $matches );  
  //echo $count;
  //print_r( $matches );
  return( $matches );
} 

/**
 * Set a new width allowing for left and right padding 
 * 
 * Does this also work for height? 
 * 
 * @param integer $width
 * @param integer $right
 * @param integer $left
 * @return integer ( ( $width - $right) - left ) 
 */
function oik_rwd_dynamic_adjust_width( $width, $right, $left ) {
  $new_width = $width;
  $new_width -= $right;
  $new_width -= $left;
  return( $new_width );
}

/** 
 * Return a CSS declaration - property: value pair
 * 
 * @param string $property - the property name e.g. color
 * @param string $value - the property value e.g. red
 * @return string suitable for concatenating to other declarations in the declaration block
 */
function oik_rwd_dynamic_css_pv( $property, $value ) {
  return( "$property: $value; " );
}  

/** 
 * Dynamically generate a CSS class definition for width
 * 
 */
function oik_rwd_dynamic_css_generate_width( $class, $width, $pright, $pleft, $mright, $mleft ) {
  $kvs = oik_rwd_dynamic_css_pv( "float", "left" );
  $kvs .= oik_rwd_dynamic_css_pv( "display", "block" );
  $kvs .= oik_rwd_dynamic_css_pv( "width", "$width%" ); 
  $kvs .= oik_rwd_dynamic_css_pv( "padding-right", "$pright%" );
  $kvs .= oik_rwd_dynamic_css_pv( "padding-left", "$pleft%" );
  $kvs .= oik_rwd_dynamic_css_pv( "margin-right", "$mright%" );
  $kvs .= oik_rwd_dynamic_css_pv( "margin-left", "$mleft%" );
  $css = ".$class { $kvs}"; 
  e( $css );
  return( $css );
}

/**
 * Dynamically generate a CSS class for min-height 
 *
 * What are the best units for this: percentages, pixels, em or otherwise?
 *
 */
function oik_rwd_dynamic_css_generate_height( $class, $height, $ptop, $pbottom, $mtop, $mbottom ) {
  $kvs = oik_rwd_dynamic_css_pv( "min-height", "$height%" ); 
  if ( $ptop )
    $kvs .= oik_rwd_dynamic_css_pv( "padding-top", "$ptop%" );
  if ( $pbottom )   
    $kvs .= oik_rwd_dynamic_css_pv( "padding-bottom", "$pbottom%" );
  if ( $mtop )  
    $kvs .= oik_rwd_dynamic_css_pv( "margin-top", "$mtop%" );
  if ( $mbottom )  
    $kvs .= oik_rwd_dynamic_css_pv( "margin-bottom", "$mbottom%" );
  $css = ".$class { $kvs}"; 
  e( $css );
  return( $css );
}

/** 
 * Return the value for the padding/margin field when the $padding_pos matches a value in $match
 */
function oik_rwd_padding( $match, $padding_pos, $padding ) {
  if ( !$padding_pos || FALSE === strpos( $match, $padding_pos ) ) {
    $value = 0;
  } else {
    $value = $padding;
  } 
  return( $value );
}

/**
 * Dynamically generate a CSS class for width or height
 *
 * @param string $class - the dynamic class name
 * @param array $dynaclass - 
 */
function oik_rwd_dynamic_css_generate( $class, $dynaclass, $mapping=null, $padding_map=null ) {
  list( $class, $type, $width, $padding_pos, $padding, $margin_pos, $margin ) = $dynaclass;
  if ( $type == "w" ) {
    if ( $padding_pos == "m" ) {
      $mleft = 0; // oik_rwd_padding("m", $padding_pos, $padding );
      $mright = oik_rwd_padding( "m", $padding_pos, $padding );
      $pleft = oik_rwd_padding( "lb", $margin_pos, $margin );
      $pright = oik_rwd_padding( "prb", $margin_pos, $margin );
    } else {
      $pleft = oik_rwd_padding( "lb", $padding_pos, $padding );
      $pright = oik_rwd_padding( "prb", $padding_pos, $padding );
      $mleft = 0; // oik_rwd_padding( "m", $margin_pos, $margin );
      $mright = oik_rwd_padding( "m", $margin_pos, $margin );
    }  
    // Apply width mapping 
    if ( $mapping ) {
      $width = oik_rwd_apply_width_mapping( $width, $mapping );
    }
    // apply padding and margin mapping as well **?** @TODO 2013/09/07
    if ( $padding_map ) {
      $mleft = oik_rwd_apply_width_mapping( $mleft, $padding_map );
      $mright = oik_rwd_apply_width_mapping( $mright, $padding_map );
      $pleft = oik_rwd_apply_width_mapping( $pleft, $padding_map );
      $pright = oik_rwd_apply_width_mapping( $pright, $padding_map );
    }  
    $width = oik_rwd_dynamic_adjust_width( $width, $pleft, $pright );
    $width = oik_rwd_dynamic_adjust_width( $width, $mleft, $mright );
    $width = oik_rwd_dynamic_css_generate_width( $class, $width, $pright, $pleft, $mright, $mleft );
  } elseif ( $type == "h" ) {
    if ( null == $mapping ) {
      if ( $padding_pos == "m" ) {
        $mtop = 0; // oik_rwd_padding("m", $padding_pos, $padding );
        $mbottom = oik_rwd_padding( "m", $padding_pos, $padding );
        $ptop = oik_rwd_padding( "t", $margin_pos, $margin );
        $pbottom = oik_rwd_padding( "b", $margin_pos, $margin );
      } else {
        $ptop = oik_rwd_padding( "pt", $padding_pos, $padding );
        $pbottom = oik_rwd_padding( "pb", $padding_pos, $padding );
        $mtop = 0; // oik_rwd_padding( "m", $margin_pos, $margin );
        $mbottom = oik_rwd_padding( "m", $margin_pos, $margin );
      }
      $width = oik_rwd_dynamic_css_generate_height( $class, $width, $ptop, $pbottom, $mtop, $mbottom );
    }
  } else {
    bw_trace2(); // Type "o" not yet supported
  }
}

/**
 * Maintain a list of "oik responsive-web-design" classes to generate 
 *
 * The CSS for each class only needs to be generated once
 * There's no need to even attempt to generate the CSS if no classes are defined
 *
 * @TODO - check if this works for admin pages as well
 *
 */
function oik_rwd_class( $class=null ) {
  static $omq_classes;
  $classes = bw_as_array( $class ); 
  foreach ( $classes as $class ) { 
    $dynaclass = oik_rwd_dynamic_css_class( $class );
    if ( $dynaclass ) {
      if ( !isset( $omq_classes ) ) {
        $omq_classes = array();
        add_action( "wp_footer", "oik_rwd_wp_footer", 25 );
      }
      $omq_classes[$class] = $dynaclass;
    }  
  }  
  return( $omq_classes );
}

/** 
 * Apply a mapping to the width to adjust for different devices
 *
 * Note: Added test on $i vs count( $map ) to fix infinite loop when $width exceeded the largest figure in the mapping.
 *  
 * @param integer $width - original width 
 * @param mixed - mapping array or string representation
 * @return integer - new width
 */
function oik_rwd_apply_width_mapping( $width, $map ) {
  $map = bw_as_array( $map );
  $i = 0;
  $new_width = null;
  while ( $new_width === null  && $i < count( $map ) ) {
    if ( $width <= $map[$i] ) {
      $new_width = $map[$i+1];
    }  
    $i++;
    $i++;
  }
  if ( $new_width === null ) {
    $new_width = $width;
  }  
  return( $new_width );
}  

/**
 * We're trying to create a set of media queries with graceful degradation as the window width narrows
 * So we need to know min/max width at which a transition occurs
 * and how to cater for different percentages.
 *
 * Then we adjust the generated width and left and right margins and padding accordingly
 * 
 *
 * <code> 
  .w33p3 { float: left; display: block; width: 97%; padding-right: 3%; }
 
    @media screen and ( max-width: 768px ) {
     .w33p3 { float: left; display: block; width: 97%; padding-right: 3% }  
    }

    @media screen and ( max-width: 480px ) {
     .w33p3 { float: left; display: block; width: 98%; padding-right: 2% }  
    }

    @media screen and ( max-width: 320px ) {
     .w33p3 { float: left; display: block; width: 99%; padding-right: 1%; }
    }
    </code>
 */
function oik_rwd_default_media_rules() {
  // $mr[] = array( "mq" => "max-width: 768px", "mapping" => "25,50,75,50,100,50", "padding" => "2,1,5,2,10,3" );
  $max_width = oik_rwd_adjusted_max_width_for_context();
  $mr[] = array( "mq" => "max-width: ${max_width}px", "mapping" => "100,100", "padding" => "2,1,5,2,10,3" );
  $mr[] = array( "mq" => "max-width: 480px", "mapping" => "33,100,66,50,100,100", "padding" => "10,2" );
  $mr[] = array( "mq" => "max-width: 320px", "mapping" => "100,100", "padding" => "10,1" );
  return( $mr ); 
}

/**
 * Return the max-width in pixels taking into account the context
 *
 * I started writing these comments
 * then realised it would be just as easy to get the user to type in a single number
 * rather than mess about with calculations. 
 
 * Here's some documentation explaining what would have needed to have been done...
 * 
 * The RWD classes are intended for use within the main content
 * which may be next to some sidebars and then further padded out by left and right margins. 
 * 
 * +--------+---------------------------------+--------+
 * | left   |  body                           | right  |
 * | margin |                                 | margin |
 * |        +-----------------------+---------+        |
 * |        |   main content        | sidebar |        |
 * |        |   breakpoint          |         |        |
 * +--------+-----------------------+---------+--------+ 
 * So the CSS we might have in the stylesheet is
 *  body { width: 80%; margin: 0 auto; }
 *  sidebar { width: 30%; }
 * 
 * The "breakpoint" is the minimum width we allow the main content to reduce to before
 * we start applying media query logic to adjust the div widths, padding and margins
 *
 * As you can see this figure is nowhere near the max-width.
 * We have to calculate what this max-width would be given the constraints:
 * - breakpoint - min width in pixels before the main content breaks  
 * - sidebar - %age to allow for sidebars 
 * - margins - %age to allow for margins
 *
 * Calculation:
 * 
 * body_width = breakpoint + ( breakpoint x ( sidebar% / ( 100 - sidebar% ) ) )
 * max_width = body_width + ( body_width x ( margins% / ( 100 - margins% ) ) )
 *          
 */                                              
function oik_rwd_adjusted_max_width_for_context( ) {
  static $max_width;
  if ( is_null( $max_width ) ) {
    $max_width = bw_get_option( "max_width", "bw_rwd" );
    if ( $max_width && is_numeric( $max_width ) ) {
      // Good - we'll use this - let's hope it's greater than or equal to 768.  
    } else {
      $max_width = 768;
    }    
  }
  return( $max_width );
}
/**
 * Dynamically generate the CSS classes and media queries
 *
 * Dynamically generate the CSS class definitions specified
 * then, for each of the media queries generate adjusted versions 
 * applying the given mapping rules for the width and padding values
 *
 */
function oik_rwd_wp_footer() {
  $classes = oik_rwd_class();
  $mrs = oik_rwd_default_media_rules();
  $mrs = apply_filters( "oik_rwd_media_rules", $mrs );
  stag( "style", null, null, kv( "type", "text/css" ) . kv( "media", "screen" ) );
  foreach ( $classes as $class => $dynaclass ) {
    oik_rwd_dynamic_css_generate( $class, $dynaclass );
  }
  foreach ( $mrs as $mr ) {
    $mq = bw_array_get( $mr, "mq", "max-width: 480px" );
    $mapping = bw_as_array( bw_array_get( $mr, "mapping", "30,100,70,50,100,100" ) );
    $padding_map = bw_as_array( bw_array_get( $mr, "padding", "10,2" ) );
    //e( "\n" ); 
    e( "@media screen and ( $mq ) {" ); 
    foreach ( $classes as $class => $dynaclass ) {
      oik_rwd_dynamic_css_generate( $class, $dynaclass, $mapping, $padding_map );
    }
    e( "}" );
  }    
  etag( "style" );
  bw_flush(); 
}

/** 
 * Dynamically generate oik responsive web design class definitions for the class names given
 *
 * If classes are given then we create responsive definitions for these
 * else we enable class interception for any future shortcode.
 * Note: In this case there's no need to do anything else for this shortcode instance.
 * 
 * @param array $atts - expected to either contain "class" or uses the index 0 value(s)
 * @param string $content - not expected
 * @param string $tag - the shortcode used
 * @return string - nothing is generated directly by this shortcode
 */
function bw_rwd( $atts=null, $content=null, $tag=null )  {
  $class = bw_array_get_from( $atts, "class,0", null );
  if ( $class ) {
    $classes = oik_rwd_class( $class );
  } else {
    add_action( "oik_shortcode_atts", "oik_rwd_shortcode_atts", 10, 3 );
  }  
  return( bw_ret() );
}

/**
 * Help hook for "bw_rwd" shortcode
 */
function bw_rwd__help( $shortcode="bw_rwd" ) {
  return( "Dynamically generate oik responsive web design CSS classes" );
}

/**
 * Syntax hook for "bw_rwd" shortcode
 * 
 * "class" is the list of CSS classes to generate
 * 
 * optionally add @TODO **?** 2013/09/08
 *
 * "mq" is a new media query to add to the existing ones
 * "mapping" is the width mapping rules for this media query
 * "padding" is the margin and padding mapping rules for this media query
 * 
 */
function bw_rwd__syntax( $shortcode="bw_rwd" ) {
  $syntax = array( "class" => bw_skv( null, "<i>class list</i>", "CSV list of classes to generate" ) 
                 );
  return( $syntax );
}  

/**
 * For examples we could simply create a set of divs like in the oik lazy smart shortcodes presentation
 * Then for snippets we could produce the output from oik_rwd_wp_footer()
 *
 */
 
  



