<?php
add_action('wp_ajax_marcador_logout', 'marcador_logout_callback');
add_action('wp_ajax_nopriv_marcador_login', 'marcador_login_callback');

function marcador_logout_callback() {
  if ( !valid_logout_post_fields() ) send_error_response("Invalid fields");

  $body = new stdClass;
  $body->message = 'Valid logout!';
  $body->valid = TRUE;
  send_response( json_encode($body) );
}

function marcador_login_callback() {
  if ( !valid_login_post_fields() ) send_error_response("Invalid fields");

  $body = new stdClass;
  $body->message = 'Valid login!';
  $body->valid = TRUE;
  send_response( json_encode($body) );
}

function valid_logout_post_fields() {
  if (isset($_POST['date'])) $date = $_POST['date'];
  return TRUE;
}

function valid_login_post_fields() {
  if (isset($_POST['date'])) $date = $_POST['date'];
  return TRUE;
}

function send_error_response($error) {
  $body = new stdClass;
  $body->error = $error;
  send_response( json_encode($body) );
}

function send_response($json) {
  header('Content-Type:application/json; charset=UTF-8');
  echo $json;
  wp_die();
}