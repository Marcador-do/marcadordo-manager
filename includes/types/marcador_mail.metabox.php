<?php
class Marcador_Mail_Info_Metabox {

  public function __construct() {
    if ( is_admin() ) {
      add_action( 'load-post.php', array( $this, 'init_metabox') );
      add_action( 'load-post-new.php', array( $this, 'init_metabox') );
    }
  }

  public function init_metabox() {
    add_action( 'add_meta_boxes', array($this, 'add_metabox') );
    //add_action( 'save_post', array($this, 'save_metabox'), 10, 2 );
  }

  public function add_metabox() {
    add_meta_box(
      'marcador_mail_info',
      __( 'Mail Info', 'marcadordo' ), 
      array($this, 'render_metabox'),
      'marcador_mail_post', 
      'advanced', 
      'default'
    );
  }

  public function render_metabox( $post ) {
    wp_nonce_field( 'mail_nonce_action', 'mail_nonce' );

    $mail_name   = get_post_meta( $post->ID, 'marcador_mail_name', true );
    $mail_email  = get_post_meta( $post->ID, 'marcador_mail_email', true );
    $mail_phone  = get_post_meta( $post->ID, 'marcador_mail_phone', true );
    $mail_asunto = get_post_meta( $post->ID, 'marcador_mail_asunto', true );

    if( empty( $mail_name ) ) $mail_name = '';
    if( empty( $mail_email ) ) $mail_email = '';
    if( empty( $mail_phone ) ) $mail_phone = '';
    if( empty( $mail_asunto ) ) $mail_asunto = '';

    // Form fields.
    include(MARCADORDO_PLUGIN_BASE_PATH . 'admin/views/metaboxes/marcador_mail.metabox.php');
  }

  public function save_metabox($post_id, $post) {
    $none_name = $_POST['mail_nonce'];
    $nonce_action = 'mail_nonce_action';

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
    $mail_new_name = isset( $_POST[ 'marcador_mail_name' ] ) ? sanitize_text_field( $_POST[ 'marcador_mail_name' ] ) : '';
    $mail_new_email = isset( $_POST[ 'marcador_mail_email' ] ) ? sanitize_text_field( $_POST[ 'marcador_mail_email' ] ) : '';
    $mail_new_phone = isset( $_POST[ 'marcador_mail_phone' ] ) ? sanitize_text_field( $_POST[ 'marcador_mail_phone' ] ) : '';
    $mail_new_asunto = isset( $_POST[ 'marcador_mail_asunto' ] ) ? sanitize_text_field( $_POST[ 'marcador_mail_asunto' ] ) : '';

    // Update the meta field in the database.
    update_post_meta( $post_id, 'marcador_mail_name', $mail_new_name );
    update_post_meta( $post_id, 'marcador_mail_email', $mail_new_email );
    update_post_meta( $post_id, 'marcador_mail_phone', $mail_new_phone );
    update_post_meta( $post_id, 'marcador_mail_asunto', $mail_new_asunto );
  }
}

new Marcador_Mail_Info_Metabox;
