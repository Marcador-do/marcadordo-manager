<?php
// Register Custom Taxonomy
function marcador_liga_taxonomy_function() {

    $labels = array(
        'name'                       => _x( 'Ligas', 'Taxonomy General Name', 'marcadordo' ),
        'singular_name'              => _x( 'Liga', 'Taxonomy Singular Name', 'marcadordo' ),
        'menu_name'                  => __( 'Marcador Disciplina', 'marcadordo' ),
        'all_items'                  => __( 'Marcador Disciplinas', 'marcadordo' ),
        'parent_item'                => __( '', 'marcadordo' ),
        'parent_item_colon'          => __( '', 'marcadordo' ),
        'new_item_name'              => __( 'New Item Name', 'marcadordo' ),
        'add_new_item'               => __( 'Add New Item', 'marcadordo' ),
        'edit_item'                  => __( 'Edit Item', 'marcadordo' ),
        'update_item'                => __( 'Update Item', 'marcadordo' ),
        'view_item'                  => __( 'View Item', 'marcadordo' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'marcadordo' ),
        'add_or_remove_items'        => __( 'Add or remove items', 'marcadordo' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'marcadordo' ),
        'popular_items'              => __( 'Popular Items', 'marcadordo' ),
        'search_items'               => __( 'Search Items', 'marcadordo' ),
        'not_found'                  => __( 'Not Found', 'marcadordo' ),
        'no_terms'                   => __( 'No items', 'marcadordo' ),
        'items_list'                 => __( 'Items list', 'marcadordo' ),
        'items_list_navigation'      => __( 'Items list navigation', 'marcadordo' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'marcador_liga_tax', array( 'marcador_liga' ), $args );

}
add_action( 'init', 'marcador_liga_taxonomy_function', 0 );