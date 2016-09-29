<?php
/**
 * Resultados
 */
add_action('wp_ajax_resultados', 'resultados_callback');
add_action('wp_ajax_nopriv_resultados', 'resultados_callback');
function resultados_callback () {
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

  $filename = build_cache_filename ('[resultados]'.$endpoint, $is_raw = false);
  $filename_raw = build_cache_filename ( $endpoint, $is_raw = true );

  $body_json = get_cached($filename);
  if ($body_json !== false) send_cached_response($body_json);

  $response = wp_remote_get( get_sportradar_endpoint_url ( $endpoint ) );
  if( is_array($response) ) {
    $header = $response['headers'];
    $body_raw = $response['body'];

    $body = json_decode($body_raw); // use the content
    $response = array();
    $games = $body->league->games;
    foreach ($games as $game) {
      $current                = new stdClass;
      $current->game_id       = $game->game->id;
      $current->status        = $game->game->status;
      $current->scheduled     = $game->game->scheduled;
      $current->link          = get_post_permalink(get_partidoId_from_gameId($game->game->id));
      $current->home          = new stdClass;
      $current->home->abbr    = $game->game->home->abbr;
      $current->home->runs    = $game->game->home->runs;
      $current->home->name    = $game->game->home->name;
      $current->home->market =  $game->game->home->market;
      $current->away          = new stdClass;
      $current->away->abbr    = $game->game->away->abbr;
      $current->away->runs    = $game->game->away->runs;
      $current->away->name    = $game->game->away->name;
      $current->away->market =  $game->game->away->market;

      array_push($response, $current);
    }
    $body = array('resultados' => $response, 'last' => time());
    $body_json = json_encode($body);

    set_cache($filename_raw, $body_raw);
    set_cache($filename, $body_json);
  } else {
    send_error_response ("No response from API");
  }

  send_response($body_json);
}


/**
 * Calendario
 */
add_action('wp_ajax_calendario', 'calendario_callback');
add_action('wp_ajax_nopriv_calendario', 'calendario_callback');
function calendario_callback () {
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

  $filename = build_cache_filename ('[calendario]'.$endpoint, $is_raw = false);
  $filename_raw = build_cache_filename ( $endpoint, $is_raw = true );

  $body_json = get_cached($filename);
  if ($body_json !== false) send_cached_response($body_json);

  $response = wp_remote_get( get_sportradar_endpoint_url ( $endpoint ) );
  if( is_array($response) ) {
    $header = $response['headers'];
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
      $current->scheduled     = $game->game->scheduled;
      $current->home          = new stdClass;
      $current->home->abbr    = $game->game->home->abbr;
      $current->home->name    = $game->game->home->name;
      $current->home->pitcher =  $game->game->home->probable_pitcher->first_name . " " . $game->game->home->probable_pitcher->last_name;
      $current->away          = new stdClass;
      $current->away->abbr    = $game->game->away->abbr;
      $current->away->name    = $game->game->away->name;
      $current->away->pitcher =  $game->game->away->probable_pitcher->first_name . " " . $game->game->away->probable_pitcher->last_name;

      array_push($response, $current);
    }
    $body = array('calendario' => $response, 'last' => time());
    $body_json = json_encode($body);

    set_cache($filename_raw, $body_raw);
    set_cache($filename, $body_json);

  } else {
    send_error_response ("No response from API");
  }

  send_response($body_json);
}


/**
 * Posiciones
 */
add_action('wp_ajax_posiciones', 'posiciones_callback');
add_action('wp_ajax_nopriv_posiciones', 'posiciones_callback');
function posiciones_callback () {
  if (isset($_POST['date'])) $date = date("Y", strtotime($_POST['date']));
  else $date = date('Y');
  if (isset($_POST['season'])) $season = $_POST['season'];
  else $season = 'REG';

  $args = array(
    'league'        => 'mlb',
    'access_level'  => 't',
    'version'       => 5,
    'objects'       => 'seasontd',
    'date'          => $date,
    'season'        => $season, // PRE, REG, PST
    'type'          => 'standings',
    'format'        => 'json',
  );
  $endpoint = get_sportradar_api_endpoint ( $args );

  $filename_raw = build_cache_filename ( $endpoint, $is_raw = true );

  $body_json = get_cached_raw($filename_raw);
  if ($body_json !== false) send_cached_response($body_json);

  $response = wp_remote_get( get_sportradar_endpoint_url ( $endpoint ) );
  if( is_array($response) ) {
    $header = $response['headers'];
    $body_raw = $response['body'];
    $body_json = $body_raw;
    set_cache($filename_raw, $body_raw);
  } else {
    send_error_response ("No response from API");
  }

  send_response($body_json);
}


/**
 * Estadisticas
 */
add_action('wp_ajax_estadisticas', 'estadisticas_callback');
add_action('wp_ajax_nopriv_estadisticas', 'estadisticas_callback');
function estadisticas_callback () {
  if (isset($_POST['date'])) $date = date("Y", strtotime($_POST['date']));
  else $date = date('Y');
  if (isset($_POST['season'])) $season = strtoupper($_POST['season']);
  else $season = 'REG';

  $args = array(
    'league'        => 'mlb',
    'access_level'  => 't',
    'version'       => 5,
    'objects'       => 'seasontd',
    'date'          => $date,
    'season'       => $season,
    'char'          => 'leaders',
    'type'          => 'statistics',
    'format'        => 'json',
  );
  $endpoint = get_sportradar_api_endpoint ( $args );

  $filename = build_cache_filename ('[estadisticas]'.$endpoint, $is_raw = false);
  $filename_raw = build_cache_filename ( $endpoint, $is_raw = true );

  $body_json = get_cached($filename);
  if ($body_json !== false) send_cached_response($body_json);

  $response = wp_remote_get( get_sportradar_endpoint_url ( $endpoint ) );
  if( is_array($response) ) {
    $header = $response['headers'];
    $body_raw = $response['body'];

    $body = json_decode($body_raw); // use the content
    $response = array();
    $leagues = $body->leagues;
    foreach ($leagues as $league) {
      if ($league->alias === 'AL' || $league->alias === 'NL') {
        $current                                = new stdClass;
        $current->id                            = $league->id;
        $current->name                          = $league->name;
        $current->alias                         = $league->alias;
        $current->hitting                       = new stdClass;
        $current->hitting->batting_average      = $league->hitting->batting_average->players;
        $current->hitting->home_runs            = $league->hitting->home_runs->players;
        $current->hitting->runs_batted_in       = $league->hitting->runs_batted_in->players;
        $current->hitting->hits                 = $league->hitting->hits->players;
        $current->hitting->stolen_bases         = $league->hitting->stolen_bases->players;
        $current->pitching                      = new stdClass;
        $current->pitching->earned_run_average  = $league->pitching->earned_run_average->players;
        $current->pitching->games_won           = $league->pitching->games_won->players;
        $current->pitching->strikeouts          = $league->pitching->strikeouts->players;
        $current->pitching->games_saved         = $league->pitching->games_saved->players;
        $current->pitching->games_completed     = $league->pitching->games_completed->players;

        array_push($response, $current);
      }
    }
    $body = array('estadisticas' => $response, 'last' => time());
    $body_json = json_encode($body);

    set_cache($filename_raw, $body_raw);
    set_cache($filename, $body_json);

  } else {
    send_error_response ("No response from API");
  }

  send_response($body_json);
}