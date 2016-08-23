<?php
class Marcador_Partido_Metabox {

  public function __construct() {
    if ( is_admin() ) {
      add_action( 'load-post.php', array( $this, 'init_metabox') );
      //add_action( 'load-post-new.php', array( $this, 'init_metabox') );
    }
  }

  public function init_metabox() {
    add_action( 'add_meta_boxes', array($this, 'add_metabox') );
    //add_action( 'save_post', array($this, 'save_metabox'), 10, 2 );
  }

  public function add_metabox() {
    add_meta_box(
      'marcador_partido_meta',
      __( 'Partido Info', 'marcadordo' ), 
      array($this, 'render_metabox'),
      'marcador_partido', 
      'advanced', 
      'default'
    );
  }

  public function render_metabox( $post ) {
    wp_nonce_field( 'partido_nonce_action', 'partido_nonce' );

    $game_id     = get_post_meta( $post->ID, 'marcador_sp_game_id', true );
    $game_data   = get_post_meta( $post->ID, 'marcador_sp_game_data', true );
    $game_status = get_post_meta( $post->ID, 'marcador_sp_game_status', true );

    if( empty( $game_id ) ) $game_id = '';
    if( empty( $game_data ) ) $game_data = '';
    if( empty( $game_status ) ) $game_status = '';

    // Form fields.
    include(MARCADORDO_PLUGIN_BASE_PATH . 'admin/views/metaboxes/marcador_partido.metabox.php');
  }

  public function save_metabox($post_id, $post) {
    $none_name = $_POST['partido_nonce'];
    $nonce_action = 'partido_nonce_action';

    // Check if a nonce is set.
    if ( ! isset( $nonce_name ) ) return;

    // Check if a nonce is valid.
    if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) return;

    // Check if the user has permissions to save data.
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Check if it's not an autosave.
    if ( wp_is_post_autosave( $post_id ) ) return;

    // Check if it's not a revision.
    if ( wp_is_post_revision( $post_id ) ) return;

    // Sanitize user input.
    $game_id = isset( $_POST[ 'marcador_sp_game_id' ] ) ? sanitize_text_field( $_POST[ 'marcador_sp_game_id' ] ) : '';
    $game_data = isset( $_POST[ 'marcador_sp_game_data' ] ) ? sanitize_text_field( $_POST[ 'marcador_sp_game_data' ] ) : '';
    $game_status = isset( $_POST[ 'marcador_sp_game_status' ] ) ? sanitize_text_field( $_POST[ 'marcador_sp_game_status' ] ) : '';

    // Update the meta field in the database.
    update_post_meta( $post_id, 'marcador_sp_game_id', $game_id );
    update_post_meta( $post_id, 'marcador_sp_game_data', $game_data );
    update_post_meta( $post_id, 'marcador_sp_game_status', $game_status );
  }
}

new Marcador_Partido_Metabox;
