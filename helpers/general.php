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

function marcadordo_send_mail($email_to_option, $form) {
  $mailgun = new Mailgun(get_option('marcadordo_mailgun_key'));
  $domain = get_option('marcadordo_mailgun_domain');
  $email_to = get_option($email_to_option);

  // Make the call to the client.
  $result = $mailgun->sendMessage(
    "$domain",
    array('from'    => "Mailgun Marcador <postmaster@{$domain}>",
          'to'      => " <{$email_to}>",
          'subject' => '[MARCADOR] Formulario',
          'html'    => "<strong>Name:</strong> {$form->name}<br /><strong>Email:</strong> {$form->email}<br /><strong>Telefono:</strong> {$form->phone}<br /><strong>Compañia:</strong> {$form->enterprise}<br /><strong>Asunto:</strong> {$form->asunto}<br /><strong>Mensaje:</strong><br />{$form->message}<br />",
          'text'    => "Name:\t{$form->name}\n<strong>Email:</strong>\t{$form->email}\n<strong>Telefono:</strong>\t{$form->phone}\n<strong>Compañia:</strong>\t{$form->enterprise}\n<strong>Asunto:</strong>\t{$form->asunto}\n<strong>Mensaje:</strong>\n{$form->message}\n"));

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