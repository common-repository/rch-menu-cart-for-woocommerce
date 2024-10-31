<?php
/*
Plugin Name: RCH Menu Cart for WooCommerce
Description: A plugin for show wooCommerce products on single page and customers can quick order from mobile.
Version: 1.0.0
Author: woomenucart
WC requires at least: 3.2.0
WC tested up to: 4.2
Tested up to: 5.4
*/
define("RCODELLC_BASE_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));
include ('includes/rcodellc-admin.php');
include ('includes/rcodellc-front-view.php');
include ('includes/rcodellc-init.php');
function rcodellc_init(){
  wp_enqueue_style('rcodellc-css',RCODELLC_BASE_URL.'/assets/css/rch-rcodellc.css');
  wp_enqueue_style('colorbox-css',RCODELLC_BASE_URL.'/assets/css/rch-colorbox.css'); 
  wp_enqueue_style('ddaccordion-css',RCODELLC_BASE_URL.'/assets/css/rch-ddaccordion.css');
  wp_enqueue_style('rcodellc-custom-js-css',RCODELLC_BASE_URL.'/assets/custom-js-css/custom-css.css'); 
  
  wp_enqueue_script('jquery');
  wp_enqueue_script('rcodellc-jscolor', plugins_url( 'assets/js/colorpicker/jscolor.js', __FILE__ ));
  wp_enqueue_script('rcodellc-tooltip', plugins_url( 'assets/js/rcodellc_tooltip.js', __FILE__ ));    
  wp_enqueue_script('jquery.colorbox', plugins_url( 'assets/js/jquery.colorbox.js', __FILE__ ));
  
  wp_enqueue_script('rcodellc-ddaccordion', plugins_url( '/assets/js/rch-ddaccordion.js', __FILE__ ));
  wp_enqueue_script('rcodellc-js-popup', plugins_url( '/assets/custom-js-css/jquery.custom-js.js', __FILE__ ));
}
do_action( 'woocommerce_set_cart_cookies', true );

add_action('init','rcodellc_init');
register_activation_hook( __FILE__, 'rcodellc_install');
register_deactivation_hook( __FILE__, 'rcodellc_uninstall');

add_filter( 'body_class', function( $classes ) {
    return array_merge( $classes, array( 'woocommerce' ) );
});
?>