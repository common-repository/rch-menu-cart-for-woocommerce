<?php
function rcodellc_admin_menue(){
  $rcodellc_icon_url = RCODELLC_BASE_URL . '/assets/images/logo.png';
  add_menu_page('Menu Cart', 'Menu Cart', 'edit_theme_options', __FILE__, 'rcodellc_setting',$rcodellc_icon_url, 24);
    add_submenu_page( __FILE__, 'Menu Cart','Menu Cart', 'edit_theme_options', __FILE__,'rcodellc_setting');  
}
function rcodellc_install(){
  if (get_option('rcodellc_display_mini_cart')){
  }else{
    rcodellc_setting_reset();
  }
}
function rcodellc_uninstall(){}
add_action('admin_menu', 'rcodellc_admin_menue');
/**
 * Add settings link to plugin actions
 *
 * @param  array  $plugin_actions
 * @param  string $plugin_file
 * @since  1.0
 * @return array
 */
function rcodellc_add_plugin_link( $plugin_actions, $plugin_file ) {
    $new_actions = array();
    if ( $plugin_file=='rch-woo-menu-cart/rch-woo-menu-cart.php' ) {
        $new_actions['cl_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'rch-woo-menu-cart' ), esc_url( admin_url( '?page=rch-woo-menu-cart%2Fincludes%2Frcodellc-init.php' ) ) );
    }
    return array_merge( $new_actions, $plugin_actions );
}
add_filter( 'plugin_action_links', 'rcodellc_add_plugin_link', 10, 2 );
?>