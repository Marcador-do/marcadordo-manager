<?php
use Respect\Validation\Validator as v;
use Mailgun\Mailgun;

function validate_contact_form($form) {
  $out = FALSE;
  $validator = v::attribute('submitted',  v::intVal()->notEmpty()->equals(1))
                ->attribute('name',       v::stringType()->notEmpty()->length(null, 50))
                ->attribute('email',      v::email()->notEmpty())
                ->attribute('phone',      v::phone()->notEmpty())
                ->attribute('enterprise', v::stringType()->length(null, 75))
                ->attribute('message',    v::stringType()->notEmpty()->length(null, 144))
                ->attribute('asunto',     v::stringType()->notBlank()->in(array('anunciarse', 'propuesta', 'sugerencia', 'integrarse')));
  $out =  $validator->validate($form) && 
          marcadordo_verify_recapcha($form->recapcha);
  return $out;
}

function validate_workwithus_form($form) {
  $out = FALSE;
  $validator = v::attribute('submitted',  v::intVal()->notEmpty()->equals(1))
                ->attribute('name',       v::stringType()->notEmpty()->length(null, 50))
                ->attribute('email',      v::email()->notEmpty())
                ->attribute('phone',      v::phone()->notEmpty())
                ->attribute('message',    v::stringType()->notEmpty()->length(null, 144));
                //->attribute('recapcha',   v::intVal()->equals(1);
  $out =  $validator->validate($form) && 
          marcadordo_verify_recapcha($form->recapcha);
  return $out;
}


function marcadordo_verify_recapcha($recaptcha) {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ip = $_SERVER['REMOTE_ADDR'];
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
  curl_setopt($ch, CURLOPT_POST, 1);
  $payload = array(
    'secret'    => get_option('marcadordo_recapcha_key'),
    'response'  => $recaptcha,
    'remoteip'  => $ip
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
  // receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = json_decode( curl_exec ($ch) );
  curl_close ($ch);

  // further processing ....
  if ($server_output->success == TRUE) {
    return TRUE;
  } else { 
    return FALSE;
  }
}


/**
 * Email
 */
function marcadordo_form_send_mail($email_to_option, $form) {
  return marcadordo_send_mail(
    $email_to = get_option($email_to_option),
    $subject  = 'Formulario',
    $html     = "<strong>Name:</strong> {$form->name}<br /><strong>Email:</strong> {$form->email}<br /><strong>Telefono:</strong> {$form->phone}<br /><strong>Compañia:</strong> {$form->enterprise}<br /><strong>Asunto:</strong> {$form->asunto}<br /><strong>Mensaje:</strong><br />{$form->message}<br />",
    $text     = "Name:\t{$form->name}\n<strong>Email:</strong>\t{$form->email}\n<strong>Telefono:</strong>\t{$form->phone}\n<strong>Compañia:</strong>\t{$form->enterprise}\n<strong>Asunto:</strong>\t{$form->asunto}\n<strong>Mensaje:</strong>\n{$form->message}\n"
  );
}

function marcadordo_send_mail($email_to, $subject, $html, $text) {
  $mailgun = new Mailgun(get_option('marcadordo_mailgun_key'));
  $domain = get_option('marcadordo_mailgun_domain');

  // Make the call to the client.
  $result = $mailgun->sendMessage(
    "$domain",
    array(
      'from'    => "Mailgun Marcador <postmaster@{$domain}>",
      'to'      => " <{$email_to}>",
      'subject' => "[MARCADOR] {$subject}",
      'html'    => $html,
      'text'    => $text
    )
  );

  $response = $result->http_response_body;
  if($response->message === 'Queued. Thank you.') return TRUE;
  return FALSE;
}

function marcadordo_save_mail($form) {
  $wp_error = FALSE;
  $postarr = array(
    'post_title'  => $form->name . " - " . $form->email,
    'post_type'   => 'marcador_mail_post',
    'tax_input'   => array( 
      'marcador_mail_taxonomy' => $form->asunto,
    ),
    'post_author' => 1,
    'post_status' => 'publish',
    'meta_input'  => array(
      'marcador_mail_name'     => $form->name,
      'marcador_mail_email'    => $form->email,
      'marcador_mail_phone'    => $form->phone,
      'marcador_mail_asunto'   => $form->asunto,
    )
  );
  return wp_insert_post( $postarr, $wp_error );
}

function send_verification_email ( $email, $username, $verification_key ) {
  $out              = new stdClass;
  $out->email_sent  = false;
  $link             = home_url( '/' ) . "activate?k={$verification_key}";
  $esc_link         = esc_url( $link );

  try {
    ob_start();
    include(MARCADORDO_PLUGIN_BASE_PATH . "includes/emails/correo-confirmacion.php");
    $template = ob_get_contents();
    ob_end_clean();

    $out->email_sent = marcadordo_send_mail(
      $email,
      $subject  = "Confirmación de cuenta",
      //$html     = "<h2>Bienvenido {$username}!!</h2><p>Debes activar tu cuenta haciendo click en el enlace <a href=\"{$link}\">{$link}</a><br /><br />O pudes copiar el enlace y pegarlo en tu navegador.</p><p>El equipo de Marcador te espera!</p>",
      $html     = $template,
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
 * Ajax
 */
function send_error_response ($error) {
  $body                 = new stdClass;
  $body->error          = new stdClass;
  $body->error->value   = TRUE;
  $body->error->message = $error;
  send_response( json_encode($body) );
}

function send_cached_response($json) {
  header("X-Marcador-Cached: json");
  send_response($json);
}

function send_response($json) {
  header('Content-Type:application/json; charset=UTF-8');
  echo $json;
  wp_die();
}


/**
 * Json File Cache
 */
function build_cache_filename ( $filename, $is_raw = false ) {
  $hash = str_replace('/', '.', $filename);
  if ($is_raw)  $hash = str_replace('.json', '_raw.json', $hash);
  return hash('sha256', $hash);
}

function get_cached($filename) {
  if (file_exists(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename)) {
    $json = file_get_contents(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename);
    $obj = json_decode($json);
    if( $not_expired = !((time() - $obj->last) >= 900) ) {
      return $json;
    }
  }
  return false;
}

function get_cached_raw($filename) {
  if (file_exists(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename)) {
    $json = file_get_contents(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename);
    return $json;
  }
  return false;
}

function set_cache($filename, $data) {
    file_put_contents(MARCADORDO_PLUGIN_BASE_PATH. 'storage/' . $filename, $data);
}


/**
 * API endpoints
 */
function get_sportradar_api_endpoint ( $args ) {
  $endpoint = "";
  $endpoint .= $args['league']; // Example: mlb
  $endpoint .= "-" . $args['access_level']; // Example: t (Test)
  $endpoint .= $args['version']; // Example: 5
  $endpoint .= "/" . $args['objects']; // Example: games
  if (isset($args['date'])) $endpoint .= "/" . str_replace(array('.', '-'), '/', $args['date']); // Example: 2016-04-12
  if (isset($args['event_id'])) $endpoint .= "/" . $args['event_id'];
  if (isset($args['season'])) $endpoint .= "/" . $args['season'];
  if (isset($args['char'])) $endpoint .= "/" . $args['char'];
  $endpoint .= "/" . $args['type']; // Example: summary
  $endpoint .= "." . $args['format']; // Example: json

  return $endpoint;
}

function get_sportradar_endpoint_url ( $endpoint ) {
  $url = 'http://api.sportradar.us/';
  $url .= $endpoint;
  $url .= '?api_key=zdbbt9a5dmqu98r4gncudytr';

  return $url;
}