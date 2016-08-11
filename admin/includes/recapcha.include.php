<?php
if (!isset($section)) $section  = new stdClass();
if (!isset($field))   $field    = new stdClass();

// reCapcha Section and Fields
$section->id        = 'marcador_recapcha_setting_section';
$section->title     = 'reCapcha settings';
$section->callback  = 'marcador_recapcha_setting_section_callback_function';
$section->page      = 'marcadordo-general-settings';
marcador_add_settings_section( $section );

$field->id          = 'marcadordo_recapcha_sitekey';
$field->title       = 'Site Key';
$field->callback    = 'marcador_recapcha_sitekey_setting_callback_function';
marcador_add_settings_field( $section, $field );

$field->id          = 'marcadordo_recapcha_key';
$field->title       = 'Secret';
$field->callback    = 'marcador_recapcha_secret_setting_callback_function';
marcador_add_settings_field( $section, $field );

function marcador_recapcha_setting_section_callback_function() {
  echo '<p>Set your reCapcha key</p>';
}

function marcador_recapcha_sitekey_setting_callback_function() {
  echo '<input name="marcadordo_recapcha_sitekey" type="text" value="' . esc_attr( get_option('marcadordo_recapcha_sitekey') ) . '" class="regular-text" />';
}

function marcador_recapcha_secret_setting_callback_function() {
  echo '<input name="marcadordo_recapcha_key" type="text" value="' . esc_attr( get_option('marcadordo_recapcha_key') ) . '" class="regular-text" />';
}