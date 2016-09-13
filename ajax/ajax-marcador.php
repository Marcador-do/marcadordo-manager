<?php
add_action('wp_ajax_cintillo', 'cintillo_games_summary_callback');
add_action('wp_ajax_nopriv_cintillo', 'cintillo_games_summary_callback');

function cintillo_games_summary_callback() {
  if (isset($_POST['date'])) $date = date("Y/m/d", strtotime($_POST['date']));
  else $date = date('Y/m/d');

  $args = array(
    'league'        => 'mlb',
    'access_level'  => 't',
    'version'       => 5,
    'objects'       => 'games',
    'date'          => $date,
    'type'          => 'summary',
    'format'        => 'json',
  );
  $endpoint = get_sportradar_api_endpoint ( $args );

  $filename = build_cache_filename ( '[cintillo]'.$endpoint, $is_raw = false );
  $filename_raw = build_cache_filename ( $endpoint, $is_raw = true );

  // Return cached response if valid timeout
  $body_json = get_cached($filename);
  if ($body_json !== false) send_cached_response($body_json);

  $response = wp_remote_get( get_sportradar_endpoint_url ( $endpoint ) );
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
      // Crea borrador "Resumen" de partido
      $borrador_id            = save_partido_post($game->game);
      $current->link          = get_post_permalink($borrador_id);
      array_push($response, $current);
    }
    $body = array('cintillo' => $response, 'last' => time());
    $body_json = json_encode($body);

    set_cache($filename_raw, $body_raw);
    set_cache($filename, $body_json);
  } else {
    send_error_response ('Error!');
  }
  
  send_response($body_json);
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
  $inserted_id = wp_insert_post( $postarr, $wp_error );
  $current_partido = (0 === $inserted_id) ? $current_partido : $inserted_id;
  return $current_partido;
}
