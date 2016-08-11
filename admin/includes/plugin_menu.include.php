<?php
add_menu_page('MarcadorDO Plugin General Options', 'MarcadorDO', 
              'manage_options', 'marcadordo-admin', 
              'marcadordo_plugin_menu_page'/* $icon_url, $position */);

/*add_submenu_page('marcadordo-admin', 'reCapcha Settings', 'reCapcha', 
                  'manage_options', 'marcadordo-recapcha',
                  'marcadordo_plugin_submenu_recapcha');
add_submenu_page('marcadordo-admin', 'reCapcha', 'reCapcha', 
                  'manage_options', 'marcadordo-recapcha',
                  'marcadordo_plugin_options');
add_submenu_page('marcadordo-admin', 'reCapcha', 'reCapcha', 
                  'manage_options', 'marcadordo-recapcha',
                  'marcadordo_plugin_options');
add_submenu_page('marcadordo-admin', 'reCapcha', 'reCapcha', 
                  'manage_options', 'marcadordo-recapcha',
                  'marcadordo_plugin_options');*/

function marcadordo_plugin_menu_page() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  include_once(MARCADORDO_PLUGIN_BASE_PATH . "admin/views/general-settings-view.php");
}

/*function marcadordo_plugin_submenu_recapcha() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  $recapcha_current_key = get_option('marcadordo_recapcha_key');
  include_once(plugin_dir_path(__FILE__) . "admin/views/recapcha-view.php");
}*/