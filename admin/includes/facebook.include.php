<?php
if (!isset( $section )) $section = new stdClass();
if (!isset( $field )) $field = new stdClass();

// reCapcha Section and Fields
$section->id = 'marcadordo_facebook_setting_section';
$section->title = __ ( 'Facebook Settings' , 'marcadordo' );
$section->callback = $section->id . '_callback_function';
$section->page = 'marcadordo-general-settings';
marcador_add_settings_section ( $section );

$field->id = 'marcadordo_facebook_app_id';
$field->title = __ ( 'App Id' , 'marcadordo' );
$field->callback = $field->id . '_setting_callback_function';
marcador_add_settings_field ( $section , $field );

$field->id = 'marcadordo_facebook_app_secret';
$field->title = __ ( 'App Secret' , 'marcadordo' );
$field->callback = $field->id . '_setting_callback_function';
marcador_add_settings_field ( $section , $field );

function marcadordo_facebook_setting_section_callback_function ()
{
    echo '<p>Set your Facebook App Settings here:</p>';
}

function marcadordo_facebook_app_id_setting_callback_function ()
{
    echo '<input name="marcadordo_facebook_app_id" type="text" value="' . esc_attr ( get_option ( 'marcadordo_facebook_app_id' ) ) . '" class="regular-text" />';
}

function marcadordo_facebook_app_secret_setting_callback_function ()
{
    echo '<input name="marcadordo_facebook_app_secret" type="text" value="' . esc_attr ( get_option ( 'marcadordo_facebook_app_secret' ) ) . '" class="regular-text" />';
}