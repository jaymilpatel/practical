<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Register Custom Post Types
 */
function demotheme_register_post_types() {
    $custompost_labels = array(
                            'name'               => _x( 'Custom Post', 'custom_post', 'demotheme' ),
                            'singular_name'      => _x( 'Custom Post', 'custom_post', 'demotheme' ),
                            'menu_name'          => _x( 'Custom Post', 'custom_post', 'demotheme' ),
                            'name_admin_bar'     => _x( 'Custom Post', 'custom_post', 'demotheme' ),
                            'add_new'            => _x( 'Add New', 'custom_post', 'demotheme' ),
                            'add_new_item'       => __( 'Add New Custom Post', 'demotheme' ),
                            'new_item'           => __( 'New Custom Post', 'demotheme' ),
                            'edit_item'          => __( 'Edit Custom Post', 'demotheme' ),
                            'view_item'          => __( 'View Custom Post', 'demotheme' ),
                            'all_items'          => __( 'All Custom Post', 'demotheme' ),
                            'search_items'       => __( 'Search Custom Post', 'demotheme' ),
                            'parent_item_colon'  => __( 'Parent Custom Post:', 'demotheme' ),
                            'not_found'          => __( 'No Custom Post Found.', 'demotheme' ),
                            'not_found_in_trash' => __( 'No Custom Post Found In Trash.', 'demotheme' ),
                        );

    $custompost_args = array(
                            'labels'             => $custompost_labels,
                            'public'             => true,
                            'publicly_queryable' => true,
                            'show_ui'            => true,
                            'show_in_menu'       => true,
                            'query_var'          => true,
                            'rewrite'            => array( 'slug'=> 'custompost', 'with_front' => false ),
                            'capability_type'    => 'post',
                            'has_archive'        => false,
                            'hierarchical'       => false,
                            'menu_position'      => null,
                            'menu_icon'          => 'dashicons-pressthis',
                            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' )
                        );

    register_post_type( DEMOTHEME_CUSTOM_POST_POST_TYPE, $custompost_args );
    
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
                    'name'              => _x( 'Categories', 'taxonomy general name', 'demotheme'),
                    'singular_name'     => _x( 'Category', 'taxonomy singular name','demotheme' ),
                    'search_items'      => __( 'Search Categories','demotheme' ),
                    'all_items'         => __( 'All Categories','demotheme' ),
                    'parent_item'       => __( 'Parent Category','demotheme' ),
                    'parent_item_colon' => __( 'Parent Category:','demotheme' ),
                    'edit_item'         => __( 'Edit Category' ,'demotheme'), 
                    'update_item'       => __( 'Update Category' ,'demotheme'),
                    'add_new_item'      => __( 'Add New Category' ,'demotheme'),
                    'new_item_name'     => __( 'New Category Name' ,'demotheme'),
                    'menu_name'         => __( 'Categories' ,'demotheme')
                );

    $args = array(
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'rewrite'           => array( 'slug'=> 'custom_tax' )
                );
	
    register_taxonomy( DEMOTHEME_CUSTOM_POST_POST_TAX, DEMOTHEME_CUSTOM_POST_POST_TYPE, $args );
    //flush rewrite rules
    flush_rewrite_rules();
}
//add action to create custom post type
add_action( 'init', 'demotheme_register_post_types' );