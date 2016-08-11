<?php
function marcador_add_settings_section( &$section) {
  add_settings_section(
    $section->id, $section->title, $section->callback, $section->page
  );
}

function marcador_add_settings_field( &$section, &$field ) {
  add_settings_field(
    $field->id, $field->title, $field->callback, $section->page, $section->id
    /*, $args*/
  );
  register_setting( $section->page, $field->id );
}