<?php
add_action ( 'wp_ajax_marcador_logout' , 'marcador_logout_callback' );


function marcador_logout_callback ()
{
    if (!valid_logout_post_fields ()) send_error_response ( "Invalid fields" );

    wp_logout ();
    $body = new stdClass;
    $body->message = 'User logout!';
    $body->valid = TRUE;
    send_response ( json_encode ( $body ) );
}


function valid_logout_post_fields ()
{
    if (isset( $_POST[ 'date' ] )) $date = $_POST[ 'date' ];
    return TRUE;
}