<?php
add_action('wp_ajax_marcador_logout', 'marcador_logout_callback');

//add_action('wp_ajax_marcador_login', 'marcador_login_callback');
add_action('wp_ajax_nopriv_marcador_login', 'marcador_login_callback');

function marcador_logout_callback() {
  if ( !valid_logout_post_fields() ) send_error_response("Invalid fields");

  $body           = new stdClass;
  $body->message  = 'Valid logout!';
  $body->valid    = TRUE;
  send_response( json_encode($body) );
}

function marcador_login_callback() {
  if ( !valid_login_post_fields() ) send_error_response("All fields required");

  $data                 = new stdClass;
  $data->user_login     = $_POST['username'];
  $data->user_password  = $_POST['password'];
  $data->remember       = FALSE;
  if ( !valid_credentials($data) ) send_error_response("Invalid credentials");

  $body         = new stdClass;
  $body->data   = "Hello " . $data->user_login . "!";
  $body->valid  = TRUE;
  send_response( json_encode($body) );
}

function valid_logout_post_fields() {
  if (isset($_POST['date'])) $date = $_POST['date'];
  return TRUE;
}

function valid_login_post_fields() {
  $valid = check_ajax_referer( 'marcador_ajax_login', FALSE, FALSE);
  $valid = $valid && isset($_POST['username']) && strlen($_POST['username'])>0;
  $valid = $valid && isset($_POST['password']) && strlen($_POST['password'])>0;

  return $valid;
}

function valid_credentials($data) {
  $user_id =  username_exists( $data->user_login ) || 
              email_exists( $data->user_login );
  if (!$user_id ) return FALSE;

  $user = wp_signon( (array) $data, FALSE );
  if (is_wp_error( $user )) return FALSE;

  $marcador_user_role = 'marcador_contributor';
  $is_colaborator = array_search( $marcador_user_role, $user->roles, FALSE );
  if ( $is_colaborator === FALSE || is_null($is_colaborator) ) { 
    wp_logout(); 
    return FALSE;
  }

  return TRUE;
}

function send_error_response($error) {
  $body                 = new stdClass;
  $body->error          = new stdClass;
  $body->error->value   = TRUE;
  $body->error->message = $error;
  send_response( json_encode($body) );
}

function send_response($json) {
  header('Content-Type:application/json; charset=UTF-8');
  echo $json;
  wp_die();
}