<?php
add_action('wp_ajax_cintillo', 'cintillo_games_summary_callback');
add_action('wp_ajax_nopriv_cintillo', 'cintillo_games_summary_callback');

function cintillo_games_summary_callback($date = '2016.07.01') {
  $date = '2016.07.01';
  if (isset($_POST['date'])) $date = $_POST['date'];

  $base = 'http://api.sportradar.us/';
  $endpoint = $base . 'mlb-t5/games/' . str_replace('.', '/', $date) . 
              '/summary.json';
  $filename = hash('sha256', str_replace('/', '.', substr($endpoint, strlen($base))));
  $filename_raw = hash('sha256', str_replace('summary', 'summary_raw', str_replace('/', '.', substr($endpoint, strlen($base)))));

  if (file_exists(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename)) {
    $json = file_get_contents(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename);
    $obj = json_decode($json);
    if( $not_expired = !((time() - $obj->last) >= 900) ) {
      header('Content-Type:application/json; charset=UTF-8');
      header("X-Marcador-Cached: json");
      echo $json;
      wp_die();
    }
  }

  // TODO: Move real call to golang backend
  $api_key = 'zdbbt9a5dmqu98r4gncudytr'; // temp
  $response = wp_remote_get( $endpoint . '?api_key=' . $api_key );
  if( is_array($response) ) {
    $header = $response['headers']; // array of http header lines
    $body_raw = $response['body'];
    $body = json_decode($body_raw); // use the content
    file_put_contents(MARCADORDO_PLUGIN_BASE_PATH. 'storage/' . $filename_raw, $body_raw);
    $response = array();
    $games = $body->league->games;
    foreach ($games as $game) {
      // Builds Response Object
      $status = $game->game->status;
      $current                = new stdClass;
      $current->game_id       = $game->game->id;
      $current->status        = ("scheduled" != $status) ? $status : date('h:i a- D', strtotime($game->game->{$status}));
      $current->home          = new stdClass;
      $current->home->abbr    = $game->game->home->abbr;
      $current->home->runs    = $game->game->home->runs;
      $current->away          = new stdClass;
      $current->away->abbr    = $game->game->away->abbr;
      $current->away->runs    = $game->game->away->runs;
      array_push($response, $current);

      // Crea borrador "Resumen" de partido
      save_partido_post()
    }
    $body = array('cintillo' => $response, 'last' => time());
  } else {
    $body = new stdClass;
    $body->error = 'Error!';
  }
  
  $body_json = json_encode($body);
  file_put_contents(MARCADORDO_PLUGIN_BASE_PATH . 'storage/' . $filename, $body_json);
  header('Content-Type:application/json; charset=UTF-8');
  echo $body_json;
  wp_die( );
}

function save_partido_post() {
  // TODO
}