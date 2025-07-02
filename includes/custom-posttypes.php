<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Register Custom Post Types
 */
function demotheme_register_post_types() {
    $eventpost_labels = array(
                            'name'               => _x( 'Events', 'custom_post', 'demotheme' ),
                            'singular_name'      => _x( 'Events', 'custom_post', 'demotheme' ),
                            'menu_name'          => _x( 'Events', 'custom_post', 'demotheme' ),
                            'name_admin_bar'     => _x( 'Events', 'custom_post', 'demotheme' ),
                            'add_new'            => _x( 'Add New', 'custom_post', 'demotheme' ),
                            'add_new_item'       => __( 'Add New Events', 'demotheme' ),
                            'new_item'           => __( 'New Events', 'demotheme' ),
                            'edit_item'          => __( 'Edit Events', 'demotheme' ),
                            'view_item'          => __( 'View Events', 'demotheme' ),
                            'all_items'          => __( 'All Events', 'demotheme' ),
                            'search_items'       => __( 'Search Events', 'demotheme' ),
                            'parent_item_colon'  => __( 'Parent Event,s:', 'demotheme' ),
                            'not_found'          => __( 'No Events Found.', 'demotheme' ),
                            'not_found_in_trash' => __( 'No Events Found In Trash.', 'demotheme' ),
                        );

    $eventpost_args = array(
                            'labels'             => $eventpost_labels,
                            'public'             => true,
                            'publicly_queryable' => true,
                            'show_ui'            => true,
                            'show_in_menu'       => true,
                            'query_var'          => true,
                            'rewrite'            => array('slug' => 'events/%city%', 'with_front' => false ),
                            'capability_type'    => 'post',
                            'has_archive'        => true,
                            'hierarchical'       => true,
                            'menu_position'      => null,
                            'menu_icon'          => 'dashicons-pressthis',
                            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
                            'show_in_rest'       => true,
                        );

    register_post_type( DEMOTHEME_EVENT_POST_TYPE, $eventpost_args );
    
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
                    'rewrite'           => array( 'slug' => 'city' )
                );
	
    register_taxonomy( DEMOTHEME_CITY_POST_TAX, DEMOTHEME_EVENT_POST_TYPE, $args );
    //flush rewrite rules
    flush_rewrite_rules();
}
//add action to create custom post type
add_action( 'init', 'demotheme_register_post_types' );


function filter_event_permalink($post_link, $post) {
    if ($post->post_type !== 'event') return $post_link;

    $terms = get_the_terms($post->ID, 'city');
    if (!$terms || is_wp_error($terms)) {
        return str_replace('%city%', 'no-city', $post_link);
    }

    $term = $terms[0]; // Use first term
    $slug_path = $term->slug;

    // Walk up the hierarchy
    while ($term->parent != 0) {
        $term = get_term($term->parent, 'city');
        $slug_path = $term->slug . '/' . $slug_path;
    }

    return str_replace('%city%', $slug_path, $post_link);
}
add_filter('post_type_link', 'filter_event_permalink', 10, 2);

function add_event_rewrite_rules() {
    add_rewrite_rule(
        '^events/([^/]+)/([^/]+)/?$',
        'index.php?event=$matches[2]',
        'top'
    );

    add_rewrite_rule(
        '^events/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?event=$matches[3]',
        'top'
    );

    add_rewrite_rule(
        '^events/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?event=$matches[4]',
        'top'
    );
}
add_action('init', 'add_event_rewrite_rules');
