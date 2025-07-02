<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/*
 * Escape Tags & Slashes
 * Handles escapping the slashes and tags
 */
function demotheme_escape_attr($data) {
    return !empty( $data ) ? esc_attr( stripslashes( $data ) ) : '';
}

/*
 * Strip Slashes From Array
 */
function demotheme_escape_slashes_deep($data = array(),$flag=true) {
    if($flag != true) {
         $data = demotheme_nohtml_kses($data);
    }
    $data = stripslashes_deep($data);
    return $data;
}

/*
 * Strip Html Tags 
 * 
 * It will sanitize text input (strip html tags, and escape characters)
 */
function demotheme_nohtml_kses($data = array()) {
    if ( is_array($data) ) {
        $data = array_map(array($this,'demotheme_nohtml_kses'), $data);
    } elseif ( is_string( $data ) ) {
        $data = wp_filter_nohtml_kses($data);
    }
   return $data;
}

/*
 * Display Short Content By Character
 */
function demotheme_excerpt_char( $content, $length = 40 ) {
    $text = '';
    if( !empty( $content ) ) {
        $text = strip_shortcodes( $content );
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);
        $excerpt_more = apply_filters('excerpt_more', ' ' . ' ...');
        $text = substr($text, 0, $length);
        $text = $text . $excerpt_more;
    }
    return $text;
}

/*
 * search in posts and pages
 */
function demotheme_filter_search( $query ) {
    if( !is_admin() && $query->is_search ) {
        $query->set( 'post_type', array( DEMOTHEME_POST_POST_TYPE, DEMOTHEME_PAGE_POST_TYPE ) );
    }
    return $query;
}
add_filter( 'pre_get_posts', 'demotheme_filter_search' );


/*
 * Remove wp logo from admin bar
 */
function demotheme_remove_wp_logo() {
    global $wp_admin_bar;

    if( class_exists('acf') ) {
        $wp_help  = get_field( 'demotheme_options_wp_help', 'option' );
        if( empty( $wp_help ) ) {
            $wp_admin_bar->remove_menu('wp-logo');
        }
    }
}
add_action( 'wp_before_admin_bar_render', 'demotheme_remove_wp_logo' );

/*
 * Custom login logo
 */
function demotheme_custom_login_logo() {
    if( class_exists('acf') ) {
        $wp_login_logo      = get_field( 'demotheme_options_login_logo', 'option' );
        $wp_login_w         = get_field( 'demotheme_options_login_logo_w', 'option' );
        $wp_login_h         = get_field( 'demotheme_options_login_logo_h', 'option' );
        $wp_login_bg        = get_field( 'demotheme_options_login_bg', 'option' );
        $wp_login_btn_c     = get_field( 'demotheme_options_login_btn_color', 'option' );
        $wp_login_btn_c_h   = get_field( 'demotheme_options_login_btn_color_h', 'option' );
        if( !empty( $wp_login_logo ) ) {
?>
        <style type="text/css">
            .login h1 a {
                background-image: url('<?php echo $wp_login_logo; ?>') !important;
                background-size: <?php echo $wp_login_w.'px'; ?> auto !important;
                height: <?php echo $wp_login_h.'px'; ?> !important;
                width: <?php echo $wp_login_w.'px'; ?> !important;
            }
        </style>
<?php
        }
        if( !empty( $wp_login_bg ) ){
?>
        <style type="text/css">
            body.login{ background: #133759 url("<?php echo $wp_login_bg; ?>") no-repeat center; background-size: cover;}
            body.login div#login form#loginform input#wp-submit {background-color: <?php echo $wp_login_btn_c; ?> !important;}
            body.login div#login form#loginform input#wp-submit:hover {background-color: <?php echo $wp_login_btn_c_h; ?> !important;}
        </style>
<?php
        }
    }
}
add_action( 'login_enqueue_scripts', 'demotheme_custom_login_logo' );

/*
 * Change custom login page url
 */
function demotheme_loginpage_custom_link() {
    $site_url = esc_url( home_url( '/' ) );
    return $site_url;
}
add_filter( 'login_headerurl', 'demotheme_loginpage_custom_link' );

/*
 * Change title on logo
 */
function demotheme_change_title_on_logo() {
    $site_title = get_bloginfo( 'name' );
    return $site_title;
}
add_filter( 'login_headertitle', 'demotheme_change_title_on_logo' );

/*
 * Change admin your favicon
 */
function demotheme_admin_favicon() {
    if( class_exists('acf') ) {
        $favicon_url        = get_field( 'demotheme_options_wp_favicon', 'option' );
        if( !empty( $favicon_url ) ){
            echo '<link rel="icon" type="image/x-icon" href="' . $favicon_url . '" />';
        }
    }
}
add_action('login_head', 'demotheme_admin_favicon');
add_action('admin_head', 'demotheme_admin_favicon');

/*
 * add filter to add shortcode in widget
 */
add_filter( 'widget_text', 'do_shortcode' );

/*
 * Disable Gunturnburg Editor
 */

add_filter('use_block_editor_for_post', '__return_false');

/* 
 * Create meta box for event post type
 */ 
function event_custom_meta_boxes() {
    add_meta_box('event_details', 'Event Details', 'render_event_meta_box', 'event', 'normal', 'default');
}
add_action('add_meta_boxes', 'event_custom_meta_boxes');

function render_event_meta_box($post) {
    wp_nonce_field('save_event_meta', 'event_meta_nonce');
    $start = get_post_meta($post->ID, '_event_start', true);
    $end = get_post_meta($post->ID, '_event_end', true);
    $name  = get_post_meta($post->ID, '_organizer_name', true);
    $email = get_post_meta($post->ID, '_organizer_email', true);
    $phone = get_post_meta($post->ID, '_organizer_phone', true);
    $venue = get_post_meta($post->ID, '_event_venue', true);
    $price = get_post_meta($post->ID, '_event_price', true);

    ?>
    <label>Start Date/Time:
        <input type="datetime-local" name="event_start" id="event_start" value="<?= esc_attr($start) ?>" />
        <div class="error-msg" id="error-event_start" style="color:red;"></div>
    </label><br>

    <label>End Date/Time:
        <input type="datetime-local" name="event_end" id="event_end" value="<?= esc_attr($end) ?>" />
        <div class="error-msg" id="error-event_end" style="color:red;"></div>
    </label><br>

    <h3>Organizer Details</h3>

    <label for="organizer_name">Name:</label><br>
    <input type="text" name="organizer_name" id="organizer_name" value="<?= esc_attr($name); ?>"><br>
    <div class="error-msg" id="error-organizer_name" style="color:red;"></div><br>

    <label for="organizer_email">Email:</label><br>
    <input type="email" name="organizer_email" id="organizer_email" value="<?= esc_attr($email); ?>"><br>
    <div class="error-msg" id="error-organizer_email" style="color:red;"></div><br>

    <label for="organizer_phone">Phone (format: +91-XXX-XXX-XXXX):</label><br>
    <input type="text" name="organizer_phone" id="organizer_phone" value="<?= esc_attr($phone); ?>"><br>
    <div class="error-msg" id="error-organizer_phone" style="color:red;"></div><br>

    <label>Venue + Coordinates:
        <input type="text" name="event_venue" id="event_venue" value="<?= esc_attr($venue) ?>" />
        <div class="error-msg" id="error-event_venue" style="color:red;"></div>
    </label><br>

    <label>Ticket Price:
        <input type="number" name="event_price" id="event_price" value="<?= esc_attr($price) ?>" step="0.01" />
        <div class="error-msg" id="error-event_price" style="color:red;"></div>
    </label>

    <?php
}

// Save Meta box field for event post type
function save_event_meta_data($post_id) {
    if (!isset($_POST['event_meta_nonce']) || !wp_verify_nonce($_POST['event_meta_nonce'], 'save_event_meta')) return;

    update_post_meta($post_id, '_event_start', sanitize_text_field($_POST['event_start']));
    update_post_meta($post_id, '_event_end', sanitize_text_field($_POST['event_end']));
    if (isset($_POST['organizer_name'])) {
        update_post_meta($post_id, '_organizer_name', sanitize_text_field($_POST['organizer_name']));
    }
    if (isset($_POST['organizer_email'])) {
        update_post_meta($post_id, '_organizer_email', sanitize_email($_POST['organizer_email']));
    }
    if (isset($_POST['organizer_phone'])) {
        update_post_meta($post_id, '_organizer_phone', sanitize_text_field($_POST['organizer_phone']));
    }
    update_post_meta($post_id, '_event_venue', sanitize_text_field($_POST['event_venue']));
    update_post_meta($post_id, '_event_price', floatval($_POST['event_price']));
}
add_action('save_post', 'save_event_meta_data');
