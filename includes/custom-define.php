<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !defined( 'DEMOTHEME_POST_POST_TYPE' ) ) {
    define( 'DEMOTHEME_POST_POST_TYPE', 'post' );
}
if( !defined( 'DEMOTHEME_PAGE_POST_TYPE' ) ) {
    define( 'DEMOTHEME_PAGE_POST_TYPE', 'page' );
}
if( !defined( 'DEMOTHEME_CUSTOM_POST_POST_TYPE' ) ) {
    define( 'DEMOTHEME_CUSTOM_POST_POST_TYPE', 'custom_post' );
}
if( !defined( 'DEMOTHEME_CUSTOM_POST_POST_TAX' ) ) {
    define( 'DEMOTHEME_CUSTOM_POST_POST_TAX', 'custom_post_tax' );
}
if( !defined( 'DEMOTHEME_META_PREFIX' ) ) {
    define( 'DEMOTHEME_META_PREFIX', '_demotheme_' );
}