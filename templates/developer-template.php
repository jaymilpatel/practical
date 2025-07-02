<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/*
 * Template Name: Developer Test Template
 *
 * @package WordPress
 * @subpackage demotheme
 * @since demotheme 1.0
 */
get_header(); ?>
<?php
echo "<pre>";
print_r( "Hello Developer!!!!" );
echo "</pre>";
?>
<?php get_footer(); ?>