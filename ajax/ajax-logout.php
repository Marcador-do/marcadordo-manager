<?php
add_action ( 'wp_ajax_marcador_logout' , 'marcador_logout_callback' );


function marcador_logout_callback ()
{
    if (!valid_logout_post_fields ()) send_error_response ( "Invalid fields" );

    wp_clear_auth_cookie();
    $body = new stdClass;
    $body->message = 'User logout!';
    $body->valid = TRUE;
    send_response ( json_encode ( $body ) );
}


function valid_logout_post_fields ()
{
    return TRUE;
}