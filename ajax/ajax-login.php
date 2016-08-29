<?php
add_action('wp_ajax_marcador_logout', 'marcador_logout_callback');
add_action('wp_ajax_nopriv_marcador_login', 'marcador_login_callback');
add_action('wp_ajax_nopriv_marcador_register', 'marcador_register_callback');

/**
 * Logout Ajax callback
 */
function marcador_logout_callback() {
  if ( !valid_logout_post_fields() ) send_error_response("Invalid fields");

  wp_logout();
  $body           = new stdClass;
  $body->message  = 'User logout!';
  $body->valid    = TRUE;
  send_response( json_encode($body) );
}


/**
 * Login form Ajax callback.
 */
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


/**
 * Register form Ajax callback.
 */
function marcador_register_callback() {
  if ( !valid_register_post_fields() ) 
    send_error_response( "Email or Username taken, Invalid credentials" );

  $username  = $_POST['username'];
  $password  = $_POST['password'];
  $email     = $_POST['email'];

  // Create user
  $user_id = wp_create_user( $username, $password, $email );

  // Adds user meta
  add_user_meta( 
    $user_id,
    $meta_key = 'marcador_favorites',
    $meta_value = maybe_serialize( $favorites = array() ),
    $unique = TRUE
  );

  // Set Role
  $user = new WP_User( $user_id );
  $user->set_role( 'marcador_contributor' );

  // Email User
  try {
    $email_sent = marcadordo_send_mail(
      $email,
      $subject  = "Registration",
      $html     = "<h2>Bienvenido!!</h2><p>Tus credenciales son:<br /><strong>Usuario: </strong>{$username}<br /><strong>Contraseña: </strong>{$password}</p><p>Gracias por formar parte de nuestra comunidad!</p>",
      $text     = "Bienvenido!!\nTus credenciales son:\nUsuario: {$username}\Contraseña: {$password}\nGracias por formar parte de nuestra comunidad!\n"
    );
  } catch (Exception $e) {
      $message = $e->getMessage();
      $email_sent = false;
  }

  $body                     = new stdClass;
  $body->userID             = $userId;
  $body->email_confirmation = $email_sent;
  $body->valid              = TRUE;
  if (isset($message)) $body->message = $message;
  send_response( json_encode($body) );
}


/**
 * Others
 */
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

function valid_register_post_fields() {
  $valid = check_ajax_referer( 'marcador_ajax_register', FALSE, FALSE);
  $valid = $valid && isset($_POST['email']) && strlen($_POST['email'])>0;
  $valid = $valid && isset($_POST['username']) && strlen($_POST['username'])>0;
  $valid = $valid && isset($_POST['password']) && strlen($_POST['password'])>0;
  $userId = username_exists($_POST['username']) || 
            email_exists($_POST['email']);
  $valid = $valid && ( $userId === FALSE );

  return $valid;
}

function valid_credentials($data) {
  $user_id =  username_exists( $data->user_login ) || 
              email_exists( $data->user_login );
  if ( !$user_id ) return FALSE;

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