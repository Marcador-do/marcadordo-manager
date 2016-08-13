<?php
// Register Custom Post Type
function marcador_form_mail_post_type() {

  $labels = array(
    'name'                  => _x( 'Correos', 'Post Type General Name', 'marcadordo' ),
    'singular_name'         => _x( 'Correo', 'Post Type Singular Name', 'marcadordo' ),
    'menu_name'             => __( 'Correos', 'marcadordo' ),
    'name_admin_bar'        => __( 'Correos', 'marcadordo' ),
    'archives'              => __( 'Item Archives', 'marcadordo' ),
    'parent_item_colon'     => __( 'Parent Item:', 'marcadordo' ),
    'all_items'             => __( 'All Items', 'marcadordo' ),
    'add_new_item'          => __( 'Add New Item', 'marcadordo' ),
    'add_new'               => __( 'Add New', 'marcadordo' ),
    'new_item'              => __( 'New Item', 'marcadordo' ),
    'edit_item'             => __( 'Edit Item', 'marcadordo' ),
    'update_item'           => __( 'Update Item', 'marcadordo' ),
    'view_item'             => __( 'View Item', 'marcadordo' ),
    'search_items'          => __( 'Search Item', 'marcadordo' ),
    'not_found'             => __( 'Not found', 'marcadordo' ),
    'not_found_in_trash'    => __( 'Not found in Trash', 'marcadordo' ),
    'featured_image'        => __( 'Featured Image', 'marcadordo' ),
    'set_featured_image'    => __( 'Set featured image', 'marcadordo' ),
    'remove_featured_image' => __( 'Remove featured image', 'marcadordo' ),
    'use_featured_image'    => __( 'Use as featured image', 'marcadordo' ),
    'insert_into_item'      => __( 'Insert into item', 'marcadordo' ),
    'uploaded_to_this_item' => __( 'Uploaded to this item', 'marcadordo' ),
    'items_list'            => __( 'Items list', 'marcadordo' ),
    'items_list_navigation' => __( 'Items list navigation', 'marcadordo' ),
    'filter_items_list'     => __( 'Filter items list', 'marcadordo' ),
  );
  $args = array(
    'label'                 => __( 'Correo', 'marcadordo' ),
    'description'           => __( 'Marcador form mails', 'marcadordo' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'marcador_mail_info' ),
    'taxonomies'            => array( 'marcador_mail_taxonomy' ),
    'hierarchical'          => false,
    'public'                => false,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'show_in_admin_bar'     => false,
    'show_in_nav_menus'     => false,
    'can_export'            => true,
    'has_archive'           => true,    
    'exclude_from_search'   => true,
    'publicly_queryable'    => false,
    'capability_type'       => 'post',
  );
  register_post_type( 'marcador_mail_post', $args );

  require_once(MARCADORDO_PLUGIN_BASE_PATH . 'includes/types/marcador_mail.metabox.php');
}
add_action( 'init', 'marcador_form_mail_post_type', 0 );
