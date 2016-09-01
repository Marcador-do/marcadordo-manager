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

//Constants
define("MARCADORDO_PLUGIN_BASE_PATH", plugin_dir_path(__FILE__));
date_default_timezone_set ( 'America/Santo_Domingo' );

//Global Required
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'libraries/autoload.php' );
include_once(MARCADORDO_PLUGIN_BASE_PATH . "helpers/general.php");

require_once( MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_mail.post_type.php' );
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_concurso.taxonomy.php' );
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_concurso.post_type.php' );
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_liga.post_type.php' );
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_liga.taxonomy.php' );
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_partido.post_type.php' );

require_once( MARCADORDO_PLUGIN_BASE_PATH . 'ajax/ajax-marcador.php' );
require_once( MARCADORDO_PLUGIN_BASE_PATH . 'ajax/ajax-login.php' );

//MarcadorDO admin.
if(is_admin()) {
  include_once(MARCADORDO_PLUGIN_BASE_PATH . "admin/marcador-admin.php");
}
//MarcadorDO Front.
elseif (!is_admin()) {
  //Hook that handels Contact and Work with Us
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
    $form         = new stdClass();
    $email_to_option  = FALSE;

    if (is_page('contacto')) {
      $form->submitted    = $_POST['submitted'];
      $form->name         = $_POST['message_name'];
      $form->email        = $_POST['message_email'];
      $form->phone        = $_POST['message_phone'];
      $form->enterprise   = $_POST['message_enterprise'];
      $form->asunto       = $_POST['message_asunto'];
      $form->message      = $_POST['message_text'];
      $form->recapcha     = $_POST['g-recaptcha-response'];
      $email_to_option    = 'marcadordo_contact_form_email';
      $validate_function  = 'validate_contact_form';
    } elseif (is_page('trabaja-con-nosotros')) {
      $form->submitted    = $_POST['submitted'];
      $form->name         = $_POST['message_name'];
      $form->email        = $_POST['message_email'];
      $form->phone        = $_POST['message_phone'];
      $form->enterprise   = 'N/A';
      $form->message      = $_POST['message_text'];
      $form->recapcha     = $_POST['g-recaptcha-response'];
      $form->asunto       = 'integrarse';
      $email_to_option    = 'marcadordo_workwithus_form_email';
      $validate_function  = 'validate_workwithus_form';
    } else { return; } // No other form is procesed yet

    if ( !$validate_function($form) ) {
      $type = "error";
      $response = "<div class=\"{$type}\">VALIDATION ERROR</div>";
      return;
    }

    // Send Mail
    if ( marcadordo_form_send_mail($email_to_option, $form) === TRUE ) {
      $type = "success"; $message = $message_sent;
      // Save on Database
      marcadordo_save_mail($form);
    } else { 
      $type = "error"; $message = $message_unsent;
    }

    // Response
    $response = "<div class=\"{$type}\">{$message}</div>";
  }

  // Adds action to handle user activation
  add_action('marcador_activate_user', 'marcador_activate_user_action');
  function marcador_activate_user_action () {
    $key = $_GET['k'];
    $args = array(
      'role'         => 'marcador_contributor',
      'meta_key'     => 'marcador_key',
      'meta_value'   => $key,
      'meta_compare' => '=',
    );
    $users = get_users( $args );

    // If no user found, ignore and send to home
    if ( count($users) < 1 ) wp_redirect( home_url( '/' ) );

    $user = $users[0];
    $is_verified = (bool) get_user_meta($user->ID, 'marcador_verified', TRUE);

    // If user is verified, ignore and send to home
    if ( $is_verified === TRUE ) wp_redirect( home_url( '/' ) );
    
    update_user_meta( $user->ID, 'marcador_verified', TRUE, FALSE );
  }
}
