<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/*
 * Enqueue scripts and styles for the back end.
 */
function demotheme_admin_scripts() {
    global $wp_version;
    // Load our admin stylesheet.
    wp_enqueue_style( 'demotheme-admin-style', get_template_directory_uri() . '/css/admin-style.css' );
    // wp_enqueue_style( 'demotheme-tailwindcss-style','https://cdn.tailwindcss.com/3.4.16' );
    // wp_enqueue_script( 'demotheme-tailwindcss-script',  'https://cdn.tailwindcss.com', array(),time());
    // Load our admin script.
    wp_enqueue_script( 'demotheme-admin-script', get_template_directory_uri() . '/js/admin-script.js', array(),time());
    //localize admin script
    wp_localize_script( 'demotheme-admin-script', 'DEMOTHEMEADMIN', array(
                                                                    'ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http')),
                                                                ));
    wp_enqueue_media();
}
/*
 * Enqueue scripts and styles for the front end.
 */
function demotheme_public_scripts() {
    // Load our main stylesheet.
    wp_enqueue_style( 'demotheme-style', get_stylesheet_uri(), array(), NULL );
    // Load our public style stylesheet.
    wp_enqueue_style( 'demotheme-public-style', get_template_directory_uri() . '/css/public-style.css', array(), NULL );
    // Load main jquery
    // wp_enqueue_script( 'jquery', array(), NULL );
    // Load public script
    wp_enqueue_script( 'demotheme-public-script', get_template_directory_uri() . '/js/public-script.js', array(), NULL );
    //localize public script
    wp_localize_script( 'demotheme-public-script', 'DEMOTHEMEPUBLIC', array(
                                                                    'ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http')),
                                                                ));
}
/*
 * Enqueue scripts and styles for the admin login screen.
 */
function demotheme_login_stylesheet() {
    wp_enqueue_style( 'demotheme-login-style', get_stylesheet_directory_uri() . '/css/login-style.css' );
}
//add action to load scripts and styles for the back end
add_action( 'admin_enqueue_scripts', 'demotheme_admin_scripts' );
//add action load scripts and styles for the front end
add_action( 'wp_enqueue_scripts', 'demotheme_public_scripts' );
//add action load scripts and styles for admin login screen
add_action( 'login_enqueue_scripts', 'demotheme_login_stylesheet' );