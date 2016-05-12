<?php
/*
 * Plugin Name: Simple Worpress Gallery Slider
 * Version: 1.1
 * Plugin URI: https://github.com/flegfleg/simple-wp-gallery-slider/
 * Description: Turns all wordpress galleries into sliders.
 * Author: Florian Egermann
 * Author URI: http://www.fleg.de/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: simple-wp-gallery-slider
 * Requires at least: 4.0
 * Tested up to: 4.5
 *
 *
 * @package WordPress
 * @author Florian Egermann
 * @since 1.0.0
 */

/* * * * * * * * * * * * * *
 * Defaults
 * * * * * * * * * * * * * *
 */
  $GLOBALS['counter'] = 0;
  $arguments = array();

function slider_defaults() {

  $slider_defaults = array (
    // GENERAL
    'mode'=> 'horizontal',
    'slideSelector'=> '',
    'infiniteLoop'=> true,
    'hideControlOnEnd'=> false,
    'speed'=> 500,
    'easing'=> null,
    'slideMargin'=> 0,
    'startSlide'=> 0,
    'randomStart'=> false,
    'captions'=> false,
    'ticker'=> false,
    'tickerHover'=> false,
    'adaptiveHeight'=> false,
    'adaptiveHeightSpeed'=> 500,
    'video'=> false,
    'useCSS'=> true,
    'preloadImages'=> 'visible',
    'responsive'=> true,
    'slideZIndex'=> 50,
    'wrapperClass'=> 'bx-wrapper',

    // TOUCH
    'touchEnabled'=> true,
    'swipeThreshold'=> 50,
    'oneToOneTouch'=> true,
    'preventDefaultSwipeX'=> true,
    'preventDefaultSwipeY'=> false,

    // PAGER
    'pager'=> true,
    'pagerType'=> 'full',
    'pagerShortSeparator'=> ' / ',
    'pagerSelector'=> null,
    'buildPager'=> null,
    'pagerCustom'=> null,

    // CONTROLS
    'controls'=> true,
    'nextText'=> 'Next',
    'prevText'=> 'Prev',
    'nextSelector'=> null,
    'prevSelector'=> null,
    'autoControls'=> false,
    'startText'=> 'Start',
    'stopText'=> 'Stop',
    'autoControlsCombine'=> false,
    'autoControlsSelector'=> null,

    // AUTO
    'auto'=> false,
    'pause'=> 4000,
    'autoStart'=> true,
    'autoDirection'=> 'next',
    'autoHover'=> false,
    'autoDelay'=> 0,
    'autoSlideForOnePage'=> false,

    // CAROUSEL
    'minSlides'=> 1,
    'maxSlides'=> 1,
    'moveSlides'=> 0,
    'slideWidth'=> 0
    );
  return $slider_defaults;
}


/* * * * * * * * * * * * * *
 * Localization
 * * * * * * * * * * * * * *
 */

  function get_slider_defaults() {
    return $slider_defaults;
  }

/* * * * * * * * * * * * * *
 * Add scripts & styles
 * * * * * * * * * * * * * *
 */

function swpgs_scripts_styles() {
  // liquidslider library
  wp_enqueue_script( 'bxslider-js', plugins_url() . '/simple-wp-gallery-slider/vendor/jquery.bxslider/jquery.bxslider.min.js', array( 'jquery' ), '', true );
  wp_enqueue_style( 'bxslider-css', plugins_url() . '/simple-wp-gallery-slider/slider.css', array(), '' );
  // start the slider
  wp_enqueue_script( 'swpgs_start', plugins_url() . '/simple-wp-gallery-slider/slider.js', array('jquery'), '', true );

}

add_action( 'wp_enqueue_scripts', 'swpgs_scripts_styles' );


/* * * * * * * * * * * * * *
 * Filter the gallery output
 * * * * * * * * * * * * * *
 */


add_filter( 'post_gallery','swpgs_gallery', 10,2 );

function swpgs_gallery( $string, $attr ){

  // prevent gallery overwrite when using 'slider="false" in the gallery shortcode'
  if ( ( isset( $attr[ 'slider' ] ) ) && ( $attr[ 'slider' ] == 'false' ) ) {

    return $string;

  } else {

    $js_args = merge_defaults( $attr );
    wp_localize_script( 'swpgs_start', 'slider_' . $GLOBALS['counter'] . '_args', $js_args );

    $output = '<ul class="swpgs-slider" id="slider_' . $GLOBALS['counter'] .'">';
    
    $attachment_list = get_posts( array('include' => $attr['ids'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'post__in') );


      foreach( $attachment_list as $attachment ){

        $image = wp_get_attachment_image_src($attachment->ID, array( 1024, 512)); 

        $orientation = ($image[1] > $image[2] ? 'landscape' : 'portrait'); 
        $output .= '<li class="gallery-item '. $orientation . '">';
        $output .= '<img src="' . $image[0].'" width="' . $image[1] . '" height="' . $image[2] . '" title="' . $attachment->post_title . '">';
        $output .= '</li>';
      }
    $output .= "</ul>";
    return $output;
  }
}

function merge_defaults( $args ) {
  $def = slider_defaults();

  $allowed_args = array_intersect_key ( $args, $def );

  foreach ($allowed_args as $key => $value) {
    if ( $value == "true" ) { $value = (bool) true; } elseif ( $value == "false" ) { $value = (bool) false; } // arguments from wp_localize are strings. convert them to booleans. see: http://wordpress.stackexchange.com/questions/186155/how-do-you-pass-a-boolean-value-to-wp-localize-script/186191#186191
    $def[$key] = $value;
  }
  // var_dump( $GLOBALS['counter'] );
  // $arguments[ $GLOBALS['counter'] ] = $def ;
  // // var_dump( $def );
  $GLOBALS['counter'] ++;
  return array( $def ); 
}



 ?>