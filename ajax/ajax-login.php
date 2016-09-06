<?php
add_action('wp_ajax_marcador_logout', 'marcador_logout_callback');

add_action('wp_ajax_nopriv_marcador_login', 'marcador_login_callback');
add_action('wp_ajax_nopriv_marcador_register', 'marcador_register_callback');

add_action('wp_ajax_nopriv_marcador_google_login', 'marcador_login_callback');
add_action('wp_ajax_nopriv_marcador_google_register', 'marcador_register_callback');

/*add_action('wp_ajax_nopriv_marcador_facebook_login', 'marcador_login_callback');
add_action('wp_ajax_nopriv_marcador_facebook_register', 'marcador_register_callback');*/

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

  if ( !isset($_POST['auth']) ) $data = marcador_login();
  else if ( isset($_POST['auth_type']) && $_POST['auth_type'] === "google" )
    $data = marcador_google_login();
  else if ( isset($_POST['auth_type']) && $_POST['auth_type'] === "facebook" )
    $data = marcador_facebook_login();
  else send_error_response("Invalid credentials");

  $body         = new stdClass;
  $body->data   = "Hello " . $data->user_login . "!";
  $body->valid  = TRUE;
  send_response( json_encode($body) );
}

function marcador_login() {
  $data                 = new stdClass;
  $data->user_login     = $_POST['username'];
  $data->user_password  = $_POST['password'];
  $data->remember       = FALSE;
  if ( !valid_credentials($data) ) send_error_response("Invalid credentials");
  return $data;
}

function marcador_google_login() {
  $data                 = new stdClass;
  $data->user_login     = $_POST['username'];

  $user_id =  username_exists( $data->user_login ) || 
              email_exists( $data->user_login );
  if ( !$user_id ) send_error_response("Invalid credentials");

  $id_token = $_POST['auth'];
  $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . 
            $id_token;
  $response = wp_remote_get( $url );
  if ( is_wp_error($response) ) send_error_response("Couldn't validate");

  $body = json_decode( $reponse['body'] );
  if ($body->email_verified === true && $body->email === $data->user_login)
    return $data;

  send_error_response("Couldn't validate");
}

function marcador_facebook_login() {
  $data                 = new stdClass;
  $data->user_login     = $_POST['username'];

  $user_id =  username_exists( $data->user_login ) || 
              email_exists( $data->user_login );
  if ( !$user_id ) send_error_response("Invalid credentials");

  $id_token = $_POST['auth'];
  $url = "https://graph.facebook.com/debug_token?input_token={$id_token}&access_token={$id_token}";
  $response = wp_remote_get( $url );
  if ( is_wp_error($response) ) send_error_response("Couldn't validate");

  $body = json_decode( $reponse['body'] );
  if ($body->email_verified === true && $body->email === $data->user_login)
    return $data;

  send_error_response("Couldn't validate");
}


/**
 * Register form Ajax callback.
 */
function marcador_register_callback() {
  if ( !valid_register_post_fields() ) 
    send_error_response( "Email or Username taken, Invalid credentials" );

  $username         = $_POST['username'];
  $password         = $_POST['password'];
  $email            = $_POST['email'];
  $verification_key = generate_verification_key( $username, $email );

  // Create user
  $user_id = wp_create_user( $username, $password, $email );

  // Adds user meta
  add_user_meta( // List of favorites
    $user_id,
    $meta_key   = 'marcador_favorites',
    $meta_value = maybe_serialize( $favorites = array() ),
    $unique     = TRUE
  );
  add_user_meta( // Verified status
    $user_id,
    $meta_key   = 'marcador_verified',
    $meta_value = '0',
    $unique     = TRUE
  );
  add_user_meta( // Activation & pass reset key field
    $user_id,
    $meta_key   = 'marcador_key',
    $meta_value = $verification_key,
    $unique     = TRUE
  );
  update_user_meta(
    $user_id,
    'show_admin_bar_front',
    'false',
    'true'
  );

  // Set Role
  $user = new WP_User( $user_id );
  $user->set_role( 'marcador_contributor' );

  // Email User
  $out = send_verification_email ( $email, $username, $verification_key );

  $body                     = new stdClass;
  $body->userID             = $userId;
  $body->email_confirmation = $out->email_sent;
  $body->valid              = TRUE;
  if (isset($out->message)) $body->message = $out->message;
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

  $is_active = get_user_meta ( $user_id, 'marcador_verified', TRUE );
  if ( $is_active === "false" ) return FALSE;

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

function send_verification_email ( $email, $username, $verification_key ) {
  $out              = new stdClass;
  $out->email_sent  = false;
  $link             = home_url( '/' ) . "activate?k={$verification_key}";
  $esc_link         = esc_url( $link );

  try {
    $out->email_sent = marcadordo_send_mail(
      $email,
      $subject  = "Verificación",
      $html     = "<h2>Bienvenido {$username}!!</h2><p>Debes activar tu cuenta haciendo click en el enlace <a href=\"{$link}\">{$link}</a><br /><br />O pudes copiar el enlace y pegarlo en tu navegador.</p><p>El equipo de Marcador te espera!</p>",
      $text     = "Bienvenido {$username}!!\nDebes activar tu cuenta haciendo copiando y pegando este enlace en tu navegador\n\n{$link}\n\nEl equipo de Marcador te espera!\n"
    );
  } catch (Exception $e) {
      $out->message     = $e->getMessage();
      $out->email_sent  = false;
  }

  return $out;
}

function send_account_active_email ( $email, $username ) {
  $out              = new stdClass;
  $out->email_sent  = false;

  try {
    $out->email_sent = marcadordo_send_mail(
      $email,
      $subject  = "Cuenta activada",
      $html     = "<h2>Gracias {$username}!!</h2><p>Tu cuenta ha sido activada exitosamente.</p>",
      $text     = "Gracias {$username}!!\n\nTu cuenta ha sido activada exitosamente.\n"
    );
  } catch (Exception $e) {
      $out->message     = $e->getMessage();
      $out->email_sent  = false;
  }

  return $out;
}

function generate_verification_key ( $usename, $email ) {
  return generate_key( $username . $email );
}

function generate_key ( $data ) {
  $now  = time();
  $salt = get_option( "security_salt", hash('sha1', (string) $now) );

  return hash('sha256', $data . $now . $salt);
}


/**
 * Output responses functions
 */
function send_error_response ($error) {
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