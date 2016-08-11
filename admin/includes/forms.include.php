<?php
if (!isset($section)) $section  = new stdClass();
if (!isset($field))   $field    = new stdClass();

$section->id        = 'marcador_forms_setting_section';
$section->title     = 'Forms settings';
$section->callback  = 'marcador_forms_setting_section_callback_function';
$section->page      = 'marcadordo-general-settings';
marcador_add_settings_section( $section );

$field->id        = 'marcadordo_contact_form_email';
$field->title     = 'Contact Email';
$field->callback  = 'marcador_forms_contact_setting_callback_function';
marcador_add_settings_field( $section, $field );

$field->id        = 'marcadordo_workwithus_form_email';
$field->title     = 'Work with us Email';
$field->callback  = 'marcador_forms_workwithus_setting_callback_function';
marcador_add_settings_field( $section, $field );

function marcador_forms_setting_section_callback_function() {
  echo '<p>Set your forms Email key</p>';
}

function marcador_forms_contact_setting_callback_function() {
  echo '<input name="marcadordo_contact_form_email" type="text" value="' . esc_attr( get_option('marcadordo_contact_form_email') ) . '" class="regular-text" />';
}

function marcador_forms_workwithus_setting_callback_function() {
  echo '<input name="marcadordo_workwithus_form_email" type="text" value="' . esc_attr( get_option('marcadordo_workwithus_form_email') ) . '" class="regular-text" />';
}