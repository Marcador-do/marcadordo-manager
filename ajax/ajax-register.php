<?php
add_action ( 'wp_ajax_nopriv_marcador_register' , 'marcador_register_callback' );
add_action ( 'wp_ajax_nopriv_marcador_google_register' , 'marcador_register_callback' );
add_action ( 'wp_ajax_nopriv_marcador_facebook_register' , 'marcador_register_callback' );


/**
 * Register form Ajax callback.
 */
function marcador_register_callback ()
{
    if (!valid_register_post_fields ())
        send_error_response ( "Email or Username taken, Invalid credentials" );

    $username = $_POST[ 'username' ];
    $password = $_POST[ 'password' ];
    $email = $_POST[ 'email' ];
    $verification_key = generate_verification_key ( $username , $email );

// Create user
    $user_id = wp_create_user ( $username , $password , $email );

// Adds user meta
    add_user_meta ( // List of favorites
        $user_id ,
        $meta_key = 'marcador_favorites' ,
        $meta_value = maybe_serialize ( $favorites = array() ) ,
        $unique = TRUE
    );
    add_user_meta ( // Verified status
        $user_id ,
        $meta_key = 'marcador_verified' ,
        $meta_value = '0' ,
        $unique = TRUE
    );
    add_user_meta ( // Activation & pass reset key field
        $user_id ,
        $meta_key = 'marcador_key' ,
        $meta_value = $verification_key ,
        $unique = TRUE
    );
    update_user_meta (
        $user_id ,
        'show_admin_bar_front' ,
        'false' ,
        'true'
    );

// Set Role
    $user = new WP_User( $user_id );
    $user->set_role ( 'marcador_contributor' );

// Email User
    $out = send_verification_email ( $email , $username , $verification_key );

    $body = new stdClass;
    $body->userID = $userId;
    $body->email_confirmation = $out->email_sent;
    $body->valid = TRUE;
    if (isset( $out->message )) $body->message = $out->message;
    send_response ( json_encode ( $body ) );
}

function valid_register_post_fields ()
{
    $valid = check_ajax_referer ( 'marcador_ajax_register' , FALSE , FALSE );
    $valid = $valid && isset( $_POST[ 'email' ] ) && strlen ( $_POST[ 'email' ] ) > 0;
    $valid = $valid && isset( $_POST[ 'username' ] ) && strlen ( $_POST[ 'username' ] ) > 0;
    $valid = $valid && isset( $_POST[ 'password' ] ) && strlen ( $_POST[ 'password' ] ) > 0;
    $userId = username_exists ( $_POST[ 'username' ] )
              || email_exists ( $_POST[ 'email' ] );
    $valid = $valid && ( $userId === FALSE );

    return $valid;
}