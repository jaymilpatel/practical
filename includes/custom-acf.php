<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/*
 * Custom Theme Options
 */
if( function_exists('acf_add_options_page') ) {
    // Demo Theme General Settings
    $general_settings   = array(
                                'page_title' 	=> __( 'Demo Theme Settings', 'demotheme' ),
                                'menu_title'	=> __( 'Demo Theme Settings', 'demotheme' ),
                                'menu_slug' 	=> 'demotheme-general-settings',
                                'capability'	=> 'edit_posts',
                                'redirect'      => false,
                                'icon_url'      => 'dashicons-admin-customizer'
                            );
    acf_add_options_page( $general_settings );
}