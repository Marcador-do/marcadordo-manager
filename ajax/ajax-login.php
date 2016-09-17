<?php
add_action ( 'wp_ajax_nopriv_marcador_login' , 'marcador_login_callback' );
add_action ( 'wp_ajax_nopriv_marcador_google_login' , 'marcador_login_callback' );
add_action ( 'wp_ajax_nopriv_marcador_facebook_login' , 'marcador_login_callback' );


/**
 * Login form Ajax callback.
 */
function marcador_login_callback ()
{
    if (!valid_login_post_fields ()) send_error_response ( "All fields required" );

    if (!isset( $_POST[ 'auth' ] )) $data = marcador_login ();
    else if (isset( $_POST[ 'auth_type' ] ) && $_POST[ 'auth_type' ] === "google")
        $data = marcador_google_login ();
    else if (isset( $_POST[ 'auth_type' ] ) && $_POST[ 'auth_type' ] === "facebook")
        $data = marcador_facebook_login ();
    else send_error_response ( "Invalid credentials" );

    $body = new stdClass;
    $body->data = "Hello " . $data->user_login . "!";
    $body->valid = TRUE;
    send_response ( json_encode ( $body ) );
}

function marcador_login ()
{
    $data = new stdClass;
    $data->user_login = $_POST[ 'username' ];
    $data->user_password = $_POST[ 'password' ];
    $data->remember = FALSE;
    if (!valid_credentials ( $data )) send_error_response ( "Invalid credentials" );
    return $data;
}

function marcador_google_login ()
{
    $data = new stdClass;
    $data->user_login = $_POST[ 'username' ];

    $user_id = username_exists ( $data->user_login )
               || email_exists ( $data->user_login );
    if (!$user_id) send_error_response ( "Invalid credentials" );

    $id_token = $_POST[ 'auth' ];
    $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" .
           $id_token;
    $response = wp_remote_get ( $url );
    if (is_wp_error ( $response )) send_error_response ( "Couldn't validate" );

    $body = json_decode ( $reponse[ 'body' ] );
    if ($body->email_verified === true && $body->email === $data->user_login)
        return $data;

    send_error_response ( "Couldn't validate" );
}

function marcador_facebook_login ()
{
    $data = new stdClass;
    $data->user_login = $_POST[ 'username' ];

    $user_id = username_exists ( $data->user_login )
               || email_exists ( $data->user_login );
    if (!$user_id) send_error_response ( "Invalid credentials" );

    $id_token = $_POST[ 'auth' ];
    $url = "https://graph.facebook.com/debug_token?input_token={$id_token}&access_token={$id_token}";
    $response = wp_remote_get ( $url );
    if (is_wp_error ( $response )) send_error_response ( "Couldn't validate" );

    $body = json_decode ( $reponse[ 'body' ] );
    if ($body->email_verified === true && $body->email === $data->user_login)
        return $data;

    send_error_response ( "Couldn't validate" );
}


/**
 * Validates proper login fields
 *
 * @param $_POST['_wpnonce'] string
 * @param $_POST['username'] string
 * @param $_POST['password'] string
 * @return bool|false|int
 */
function valid_login_post_fields ()
{
    $valid = check_ajax_referer ( 'marcador_ajax_login' , FALSE , FALSE );
    $valid = $valid && isset( $_POST[ 'username' ] ) && strlen ( $_POST[ 'username' ] ) > 0;
    $valid = $valid && isset( $_POST[ 'password' ] ) && strlen ( $_POST[ 'password' ] ) > 0;

    return $valid;
}


/**
 * @param $credentials
 * @return bool
 */
function valid_credentials ($credentials)
{
    $user_id = username_exists ( $credentials->user_login )
               || email_exists ( $credentials->user_login );
    if (!$user_id) return FALSE;

    $is_active = get_user_meta ( $user_id , 'marcador_verified' , TRUE );
    if ($is_active === "false") return FALSE;

    $user = wp_signon ( (array)$credentials , FALSE );
    if (is_wp_error ( $user )) return FALSE;

    $marcador_user_role = 'marcador_contributor';
    $is_collaborator = array_search ( $marcador_user_role , $user->roles , FALSE );
    if ($is_collaborator === FALSE || is_null ( $is_collaborator )) {
        wp_logout ();
        return FALSE;
    }

    return TRUE;
}