<?php
include_once ( MARCADORDO_PLUGIN_BASE_PATH . "admin/helpers/general.php" );

// TODO: Refactor this
add_action ( 'admin_init' , 'marcador_settings_api_init' );
function marcador_settings_api_init ()
{
    // reCapcha Section and Fields
    include_once ( MARCADORDO_PLUGIN_BASE_PATH . "admin/includes/recapcha.include.php" );

    // Forms Section and Fields
    include_once ( MARCADORDO_PLUGIN_BASE_PATH . "admin/includes/forms.include.php" );

    // MailGun Section and Fields
    include_once ( MARCADORDO_PLUGIN_BASE_PATH . "admin/includes/mailgun.include.php" );

    // MailGun Section and Fields
    include_once ( MARCADORDO_PLUGIN_BASE_PATH . "admin/includes/facebook.include.php" );
}


add_action ( 'admin_menu' , 'marcadordo_plugin_menu' );
function marcadordo_plugin_menu ()
{
    include_once ( MARCADORDO_PLUGIN_BASE_PATH . "admin/includes/plugin_menu.include.php" );
}