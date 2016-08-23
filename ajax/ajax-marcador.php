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

  // Return cached response if valid timeout
  get_cached($filename);

  // TODO: Move real call to golang backend
  $api_key = 'zdbbt9a5dmqu98r4gncudytr'; // temp
  $response = wp_remote_get( $endpoint . '?api_key=' . $api_key );
  if( is_array($response) ) {
    $header = $response['headers']; // array of http header lines
    $body_raw = $response['body'];
    $body = json_decode($body_raw); // use the content
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
      save_partido_post($game->game);
    }
    $body = array('cintillo' => $response, 'last' => time());
    $body_json = json_encode($body);

    set_cache($filename_raw, $body_raw);
    set_cache($filename, $body_json);
  } else {
    $body = new stdClass;
    $body->error = 'Error!';
    $body_json = json_encode($body);
  }
  
  header('Content-Type:application/json; charset=UTF-8');
  echo $body_json;
  wp_die( );
}

function get_cached($filename) {
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
}

function set_cache($filename, $data) {
    file_put_contents(MARCADORDO_PLUGIN_BASE_PATH. 'storage/' . $filename, $data);
}

function get_partidoId_from_gameId($gameId) {
  $args = array(
    'post_type'   => 'marcador_partido',
    'meta_key'       => 'marcador_sp_game_id',
    'meta_value'     => $gameId,
    'meta_compare'   => '=',
    'post_per_page'  => 1
  );
  
  $query = new WP_Query( $args );
  if ($query->post_count < 1) return 0;
  $posts = $query->get_posts();
  $post_id = $posts[0]->ID;
  return $post_id;
}

function save_partido_post($game) {

  $data               = new stdClass;
  $data->home         = new stdClass;
  $data->away         = new stdClass;
  $data->date         = $game->scheduled;
  $data->home->id     = $game->home->id;
  $data->home->name   = $game->home->name;
  $data->home->abbr   = $game->home->abbr;
  $data->home->runs   = $game->home->runs;
  $data->away->id     = $game->away->id;
  $data->away->name   = $game->away->name;
  $data->away->abbr   = $game->away->abbr;
  $data->away->runs   = $game->away->runs;

  $current_partido = get_partidoId_from_gameId($game->id);
  $wp_error = FALSE;
  if (0 === $current_partido) {
    $postarr = array(
      'post_id' => $current_partido,
      'post_title'  => date('Y-m-d', strtotime($game->scheduled)) . " " . $game->home->name . " VS " .  $game->away->name,
      'post_type'   => 'marcador_partido',
      'post_author' => 1,
      'post_status' => 'draft',
      'meta_input'  => array(
        'marcador_sp_game_id'       => $game->id,
        'marcador_sp_game_data'     => json_encode($data),
        'marcador_sp_game_status'   => $game->status,
      )
    );
  } else {
    $postarr = array(
      'post_id' => $current_partido,
      'post_type'   => 'marcador_partido',
      'meta_input'  => array(
        'marcador_sp_game_data'     => json_encode($data),
      )
    );
  }

  return wp_insert_post( $postarr, $wp_error );
}
