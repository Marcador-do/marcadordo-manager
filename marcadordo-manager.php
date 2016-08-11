<?php
/**
 * @package MarcadorDO_Manager
 * @version 1.0
 */
/*
Plugin Name: MarcadorDO Manager
Plugin URI:  http://marcador.do/
Description: MarcadorDO plugin for administration.
Author:      Ronnie A. Baez Sesto
Version:     1.0
Author URI:  http://rabs.com.do/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define("MARCADORDO_PLUGIN_BASE_PATH", plugin_dir_path(__FILE__));
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'libraries/autoload.php' );
use Mailgun\Mailgun;

/**
 * MarcadorDO Front.
 */
if (!is_admin()) {
  /**
   * Hook that handels Contact and Work with Us
   */
  add_action('marcador_form', 'marcadordo_form_generate_response');
  function marcadordo_form_generate_response() {
    global $response;

    //response messages
    $not_human       = "Human verification incorrect.";
    $missing_content = "Please supply all information.";
    $email_invalid   = "Email Address Invalid.";
    $phone_invalid   = "Phone Number Invalid.";
    $message_unsent  = "Message was not sent. Try Again.";
    $message_sent    = "Thanks! Your message has been sent.";
     
    //user posted variables
    $submitted   = $_POST['submitted'];
    $name        = $_POST['message_name'];
    $email       = $_POST['message_email'];
    $phone       = $_POST['message_phone'];
    $enterprise  = $_POST['message_enterprise'];
    $message     = $_POST['message_text'];
    $recapcha    = $_POST['g-recaptcha-response'];

    //php mailer variables
    //$to       = get_option('admin_email');
    //$subject  = "Someone sent a message from ".get_bloginfo('name');
    //$headers  = 'From: '. $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";


    /*if ( $submitted ) {
      if( TRUE != marcadordo_verify_recapcha( $recapcha ) ) {
        $type = "error"; $message = $not_human;
      } else {
        $mailgun = new Mailgun(get_option('marcadordo_mailgun_key'));
        $domain = get_option('marcadordo_mailgun_domain');
        # Make the call to the client.
        $result = $mailgun->sendMessage(
          "$domain",
          array(
            'from'    => 'Mailgun Sandbox <postmaster@sandbox86bf3378ab684a5a8fa457e4337575a5.mailgun.org>',
            'to'      => 'Ronnie A. Baez Sesto <ronnie.baez@gmail.com>',
            'subject' => 'Hello Ronnie A. Baez Sesto',
            'text'    => 'Congratulations Ronnie A. Baez Sesto, you just sent an email with Mailgun!  You are truly awesome!  You can see a record of this email in your logs: https://mailgun.com/cp/log .  You can send up to 300 emails/day from this sandbox server.  Next, you should add your own domain so you can send 10,000 emails/month for free.'));

        $type = "success"; $message = $message_sent;
      }

      if ( $type == "success" )
        $response = "<div class='success'>{$message}</div>";
      else $response = "<div class='error'>{$message}</div>";
    }*/
      $response = "<div>
      Submitted: {$submitted}, 
      Name: {$name}, 
      Email: {$email}, 
      Phone: {$phone}, 
      Enterprise: {$enterprise}, 
      Message: {$message}, 
      reCapcha: {$recapcha}
      </div>";
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
}

/**
 * MarcadorDO admin.
 */
if(is_admin()) {
  include_once(MARCADORDO_PLUGIN_BASE_PATH . "admin/marcador-admin.php");
}