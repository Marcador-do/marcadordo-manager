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

    $login_function = "marcador";
    if (!isset( $_POST[ 'auth' ] ) && !isset( $_POST[ 'auth_type' ] )) {
        $login_function .= "_login";
    } else if (isset( $_POST[ 'auth_type' ] ) && isset( $_POST[ 'auth_type' ] )) {
        $login_function .= "_" . $_POST[ 'auth_type' ] . "_login"; // should be "google" or facebook
    } else send_error_response ( "Invalid credentials" );

    try {
        $data = $login_function ();
        $body = new stdClass;
        $body->data = "Hello " . $data->user_login . "!";
        $body->valid = TRUE;
        send_response ( json_encode ( $body ) );
    } catch (Exception $e) {
        send_error_response ( $e->getMessage() );
    }
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

    if (!valid_google_credentials ( $data )) send_error_response ( "Invalid credentials" );
    return $data;
}

function marcador_facebook_login ()
{
    $data = new stdClass;
    $data->user_login = $_POST[ 'username' ];

    $user_id = username_exists ( $data->user_login )
               | email_exists ( $data->user_login );
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
 * @return bool|false|int
 */
function valid_login_post_fields ()
{
    $valid = check_ajax_referer ( 'marcador_ajax_login' , FALSE , FALSE );
    $valid = $valid && isset( $_POST[ 'username' ] ) && strlen ( $_POST[ 'username' ] ) > 0;
    if (!isset( $_POST[ 'auth' ] ) && !isset( $_POST[ 'auth_type' ] ))
        $valid = $valid && isset( $_POST[ 'password' ] ) && strlen ( $_POST[ 'password' ] ) > 0;

    return $valid;
}


/**
 * @param $credentials
 * @return bool
 */
function valid_credentials ($credentials)
{
    $user_id = username_exists ( $credentials->user_login ) | email_exists ( $credentials->user_login );
    if (FALSE === $user_id || !is_marcador_collaborator ( $user_id )) return FALSE;

    $is_active = get_user_meta ( $user_id , 'marcador_verified' , TRUE );
    if ($is_active === "false") return FALSE;

    $user = wp_signon ( (array)$credentials , FALSE );
    if (is_wp_error ( $user )) return FALSE;

    return TRUE;
}


/**
 * @param $credentials
 * @return bool
 */
function valid_google_credentials ($credentials)
{
    $user_id = username_exists ( $credentials->user_login ) | email_exists ( $credentials->user_login );
    if (FALSE === $user_id || !is_marcador_collaborator ( $user_id )) return FALSE;

    $is_active = get_user_meta ( $user_id , 'marcador_verified' , TRUE );
    if ($is_active === "false") return FALSE;

    $id_token = $_POST[ 'auth' ];
    $google_id = is_valid_google_token ($credentials->user_login , $id_token);

    $marcador_google_id = get_user_meta ( $user_id , 'marcador_google_id' , TRUE );
    if ($marcador_google_id !== $google_id) return FALSE;

    wp_set_auth_cookie ( $user_id , FALSE );
    return FALSE;
}