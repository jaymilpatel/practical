<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//Include define variable file
require get_stylesheet_directory(). '/includes/custom-define.php';

if( class_exists('acf') ){
    //Include define variable file
    require get_stylesheet_directory(). '/includes/custom-acf.php';
}
//Include custom function file
require get_stylesheet_directory(). '/includes/custom-functions.php';

// Include custom post types & taxonomies
require get_stylesheet_directory(). '/includes/custom-posttypes.php';

//include custom scripts file
require get_stylesheet_directory(). '/includes/custom-scripts.php';

//include custom widgets file
require get_stylesheet_directory(). '/widgets/class-custom-followus-widget.php';