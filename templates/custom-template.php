<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Template Name: Custom Post Template
 *
 * @package WordPress
 * @subpackage demotheme
 * @since demotheme 1.0
 */

get_header(); ?>
<?php
if(have_posts() ){
    while( have_posts() ){
        the_post();
        the_content();
    }
}
?>
<?php get_footer(); ?>