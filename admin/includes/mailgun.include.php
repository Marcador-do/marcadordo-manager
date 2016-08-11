<?php
if (!isset($section)) $section  = new stdClass();
if (!isset($field))   $field    = new stdClass();

$section->id        = 'marcador_mailgun_setting_section';
$section->title     = 'MailGun settings';
$section->callback  = 'marcador_mailgun_setting_section_callback_function';
$section->page      = 'marcadordo-general-settings';
marcador_add_settings_section( $section );

$field->id        = 'marcadordo_mailgun_domain';
$field->title     = 'MailGun Domain';
$field->callback  = 'marcador_mailgun_domain_setting_callback_function';
marcador_add_settings_field( $section, $field );

$field->id        = 'marcadordo_mailgun_key';
$field->title     = 'MailGun Key';
$field->callback  = 'marcador_mailgun_key_setting_callback_function';
marcador_add_settings_field( $section, $field );

function marcador_mailgun_setting_section_callback_function() {
  echo '<p>Set your MailGun key and domain.</p>';
}

function marcador_mailgun_domain_setting_callback_function() {
  echo '<input name="marcadordo_mailgun_domain" type="text" value="' . esc_attr( get_option('marcadordo_mailgun_domain') ) . '" class="regular-text" />';
}

function marcador_mailgun_key_setting_callback_function() {
  echo '<input name="marcadordo_mailgun_key" type="text" value="' . esc_attr( get_option('marcadordo_mailgun_key') ) . '" class="regular-text" />';
}