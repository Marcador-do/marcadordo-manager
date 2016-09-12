<?php
/**
 * Resultados
 */
add_action('wp_ajax_resultados', 'resultados_callback');
add_action('wp_ajax_nopriv_resultados', 'resultados_callback');
function resultados_callback () {
  if (isset($_POST['date'])) $date = $_POST['date'];
  else $date = date('Y/m/d');
  $base = 'http://api.sportradar.us/';
  $endpoint = $base . 'mlb-t5/games/' . str_replace('.', '/', $date) . 
              '/summary.json';

  $filename_raw = hash('sha256', str_replace('summary', 'summary_raw', str_replace('/', '.', substr($endpoint, strlen($base)))));

  get_cached_raw($filename_raw);

  $api_key = 'zdbbt9a5dmqu98r4gncudytr'; // temp
  $response = wp_remote_get( $endpoint . '?api_key=' . $api_key );
  if( is_array($response) ) {
    $header = $response['headers'];
    $body_raw = $response['body'];
    //$body = json_decode($body_raw); // use the content
    //$body_json = json_encode($body);
    $body_json = $body_raw;
  } else {
    $body = new stdClass;
    $body->error = 'Error!';
    $body_json = json_encode($body);
  }

  header('Content-Type:application/json; charset=UTF-8');
  echo $body_json;
  wp_die( );
}


/**
 * Calendario
 */
add_action('wp_ajax_calendario', 'calendario_callback');
add_action('wp_ajax_nopriv_calendario', 'calendario_callback');


/**
 * Posiciones
 */
add_action('wp_ajax_posiciones', 'posiciones_callback');
add_action('wp_ajax_nopriv_posiciones', 'posiciones_callback');


/**
 * Estadisticas
 */
add_action('wp_ajax_estadisticas', 'estadisticas_callback');
add_action('wp_ajax_nopriv_estadisticas', 'estadisticas_callback');