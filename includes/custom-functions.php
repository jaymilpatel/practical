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
// add_filter( 'pre_get_posts', 'demotheme_filter_search' );


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
    $price = get_post_meta($post->ID, '_event_price', true);
    $venue_type = get_post_meta($post->ID, '_venue_type', true) ?: 'offline';
    $online_url = get_post_meta($post->ID, '_online_url', true);
    $offline_address = get_post_meta($post->ID, '_offline_address', true);

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

    <label>Venue Type:</label>
    <select name="venue_type" id="venue_type">
        <option value="offline" <?= selected($venue_type, 'offline') ?>>Offline</option>
        <option value="online" <?= selected($venue_type, 'online') ?>>Online</option>
    </select>
    <div class="error-msg" id="error-venue_type" style="color:red;"></div>
    <br><br>

    <div id="offline_fields" style="display: <?= $venue_type === 'offline' ? 'block' : 'none' ?>;">
        <label for="offline_address">Offline Venue Address:</label>
        <input type="text" name="offline_address" id="offline_address" value="<?= esc_attr($offline_address) ?>">
        <div class="error-msg" id="error-offline_address" style="color:red;"></div>
    </div>

    <div id="online_fields" style="display: <?= $venue_type === 'online' ? 'block' : 'none' ?>;">
        <label for="online_url">Online Event URL:</label>
        <input type="url" name="online_url" id="online_url" value="<?= esc_attr($online_url) ?>">
        <div class="error-msg" id="error-online_url" style="color:red;"></div>
    </div>

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
    $venue_type = sanitize_text_field($_POST['venue_type']);
    update_post_meta($post_id, '_venue_type', $venue_type);

    if ($venue_type === 'online') {
        update_post_meta($post_id, '_online_url', esc_url_raw($_POST['online_url']));
        delete_post_meta($post_id, '_offline_address');
    } else {
        update_post_meta($post_id, '_offline_address', sanitize_text_field($_POST['offline_address']));
        delete_post_meta($post_id, '_online_url');
    }
}
add_action('save_post', 'save_event_meta_data');


/* 
 *  Create a shotcode for event submit by front end side
 */



function event_submission_form() {
    ob_start(); ?>
    <style>
/* Layout */
#eventForm {
    max-width: 600px;
    margin: 0 auto;
    padding: 24px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Input styles */
#eventForm input,
#eventForm select {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    margin-bottom: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

#eventForm input:focus,
#eventForm select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.2);
}

/* Error messages */
.error {
    color: red;
    font-size: 0.9em;
    margin-top: -10px;
    margin-bottom: 10px;
}

/* Labels */
#eventForm label {
    font-weight: 600;
    display: block;
    margin-top: 12px;
}

/* Button */
#eventForm button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#eventForm button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Hide honeypot */
#eventForm input[name="honeypot"] {
    display: none;
}
</style>

    <form id="eventForm" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Event Title"  /><br>

        <input type="datetime-local" name="start" id="start"  /><br>
        <div class="error" id="error-start"></div>

        <input type="datetime-local" name="end" id="end"  /><br>
        <div class="error" id="error-end"></div>

        <input type="text" name="organizer_name" id="organizer_name" placeholder="Organizer Name"  /><br>
        <div class="error" id="error-organizer_name"></div>

        <input type="email" name="organizer_email" id="organizer_email" placeholder="Organizer Email"  /><br>
        <div class="error" id="error-organizer_email"></div>

        <input type="text" name="organizer_phone" id="organizer_phone" placeholder="Organizer Phone (+91-XXX-XXX-XXXX)"  /><br>
        <div class="error" id="error-organizer_phone"></div>

        <label for="venue_type">Venue Type:</label><br>
        <select name="venue_type" id="venue_type" >
            <option value="">-- Select Venue Type --</option>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select>
        <div class="error" id="error-venue_type"></div><br>

        <div id="online_input" style="display:none;">
            <input type="url" name="online_url" placeholder="Event URL" />
            <div class="error" id="error-online_url"></div>
        </div>

        <div id="offline_input" style="display:none;">
            <input type="text" name="offline_address" placeholder="Physical Address" />
            <div class="error" id="error-offline_address"></div>
        </div>

        <input type="number" name="price" placeholder="Price" step="0.01"  /><br>
        <div class="error" id="error-price"></div>

        <input type="file" name="image" accept="image/png, image/jpeg"  /><br>
        <div class="error" id="error-image"></div>

        <input type="text" name="honeypot" style="display:none;" />
        <button type="submit">Submit</button>
    </form>

    <style>
        .error { color: red; font-size: 0.9em; }
    </style>

    <?php
    return ob_get_clean();
}
add_shortcode('event_form', 'event_submission_form');


add_action('rest_api_init', function() {
    register_rest_route('event/v1', '/submit', [
        'methods' => 'POST',
        'callback' => 'handle_event_submission',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('v1', '/events', [
        'methods' => 'GET',
        'callback' => 'get_filtered_events',
        'permission_callback' => '__return_true',
    ]);
});

function handle_event_submission(WP_REST_Request $request) {
    $params = $request->get_file_params() + $request->get_params();
    if (!empty($params['honeypot'])) return new WP_Error('bot', 'Bot detected');

    $post_id = wp_insert_post([
        'post_type' => 'event',
        'post_title' => sanitize_text_field($params['title']),
        'post_status' => 'pending'
    ]);

    if (!$post_id) return new WP_Error('fail', 'Could not create post');

    update_post_meta($post_id, '_event_start', sanitize_text_field($params['start']));
    update_post_meta($post_id, '_event_end', sanitize_text_field($params['end']));
    update_post_meta($post_id, '_organizer_name', sanitize_text_field($params['organizer_name']));
    update_post_meta($post_id, '_organizer_email', sanitize_email($params['organizer_email']));
    update_post_meta($post_id, '_organizer_phone', sanitize_text_field($params['organizer_phone']));
    $venue_type = sanitize_text_field($params['venue_type']);
    update_post_meta($post_id, '_venue_type', $venue_type);

    if ($venue_type === 'online') {
        update_post_meta($post_id, '_online_url', esc_url_raw($params['online_url']));
        delete_post_meta($post_id, '_offline_address');
    } else {
        update_post_meta($post_id, '_offline_address', sanitize_text_field($params['offline_address']));
        delete_post_meta($post_id, '_online_url');
    }
    update_post_meta($post_id, '_event_price', floatval($params['price']));

    if (!empty($_FILES['image']) && !$_FILES['image']['error']) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($_FILES['image'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            $attachment = [
                'post_mime_type' => $upload['type'],
                'post_title' => basename($upload['file']),
                'post_content' => '',
                'post_status' => 'inherit'
            ];
            $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);
            set_post_thumbnail($post_id, $attach_id);
        }
    }

    return ['success' => true, 'post_id' => $post_id];
}

/*
 * Show upcoming event listing 
 */

function get_filtered_events($request) {
    global $wpdb;

    // Basic IP rate limiting (next section)
    if (!check_ip_rate_limit()) {
        return new WP_REST_Response(['error' => 'Rate limit exceeded'], 429);
    }

    $city = sanitize_text_field($request['city'] ?? '');
    $type = sanitize_text_field($request['type'] ?? '');

    $meta_query = [
        [
            'key'     => '_event_start',
            'value'   => current_time('Y-m-d H:i:s'),
            'compare' => '>=',
            'type'    => 'DATETIME',
        ],
    ];

    $tax_query = [];

    if ($city) {
        $tax_query[] = [
            'taxonomy' => 'city',
            'field'    => 'slug',
            'terms'    => $city,
        ];
    }

    if ($type) {
        $tax_query[] = [
            'taxonomy' => 'event_type',
            'field'    => 'slug',
            'terms'    => $type,
        ];
    }

    $args = [
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'meta_query'     => $meta_query,
        'tax_query'      => $tax_query,
        'posts_per_page' => 10,
    ];

    $query = new WP_Query($args);
    $events = [];

    foreach ($query->posts as $post) {
        $events[] = [
            'id'    => $post->ID,
            'title' => $post->post_title,
            'url'   => get_permalink($post),
        ];
    }

    return rest_ensure_response($events);
}


/* 
 * Register custom post status
 */


function register_custom_event_statuses() {
    register_post_status('pending_review', [
        'label'                     => _x('Pending Review', 'post'),
        'public'                    => true,
        'label_count'               => _n_noop('Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>'),
        'post_type'                 => ['event'],
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
    ]);

    register_post_status('scheduled', [
        'label'                     => _x('Scheduled', 'post'),
        'public'                    => true,
        'label_count'               => _n_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>'),
        'post_type'                 => ['event'],
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
    ]);

    register_post_status('rejected', [
        'label'                     => _x('Rejected', 'post'),
        'public'                    => false,
        'label_count'               => _n_noop('Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>'),
        'post_type'                 => ['event'],
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
    ]);
}
add_action('init', 'register_custom_event_statuses');


/*
 * Add Status Options to the Post Editor
 */
function add_event_custom_status_dropdown() {
    global $post;
    if ($post->post_type !== 'event') return;

    $statuses = [
        'pending_review' => 'Pending Review',
        'scheduled' => 'Scheduled',
        'rejected' => 'Rejected'
    ];
    ?>
    <script>
        jQuery(document).ready(function($) {
            var select = $("select#post_status");
            <?php foreach ($statuses as $key => $label): ?>
                select.append("<option value='<?php echo esc_attr($key); ?>' <?php selected($post->post_status, $key); ?>><?php echo esc_html($label); ?></option>");
            <?php endforeach; ?>
        });
    </script>
    <?php
}
add_action('post_submitbox_misc_actions', 'add_event_custom_status_dropdown');

/*
 * Display Custom Status Badges in Admin List
 */
function add_event_custom_status_badges($column, $post_id) {
    if ($column == 'title') {
        $status = get_post_status($post_id);
        $colors = [
            'pending_review' => 'orange',
            'scheduled'      => 'blue',
            'rejected'       => 'red'
        ];

        // if (array_key_exists($status, $colors)) {
            echo '<span abc style="background: ' . $colors[$status] . '; color: white; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
        // }
    }
}
add_action('manage_event_posts_custom_column', 'add_event_custom_status_badges', 10, 2);

/*
 * Filter by Event Date (past/future)
 */

function add_event_date_filter() {
    global $typenow;
    if ($typenow === 'event') {
        $selected = $_GET['event_time'] ?? '';
        ?>
        <select name="event_time">
            <option value="">All Dates</option>
            <option value="future" <?php selected($selected, 'future'); ?>>Future Events</option>
            <option value="past" <?php selected($selected, 'past'); ?>>Past Events</option>
        </select>
        <?php
    }
}
add_action('restrict_manage_posts', 'add_event_date_filter');

function filter_event_query_by_date($query) {
    global $pagenow;
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'event' && $pagenow === 'edit.php') {
        $filter = $_GET['event_time'] ?? '';
        if ($filter === 'future') {
            $query->set('meta_query', [[
                'key'     => '_event_start',
                'value'   => current_time('Y-m-d H:i'),
                'compare' => '>=',
                'type'    => 'DATETIME'
            ]]);
        } elseif ($filter === 'past') {
            $query->set('meta_query', [[
                'key'     => '_event_start',
                'value'   => current_time('Y-m-d H:i'),
                'compare' => '<',
                'type'    => 'DATETIME'
            ]]);
        }
    }
}
// add_action('pre_get_posts', 'filter_event_query_by_date');

/*
 * Filter by City Taxonomy
 */
function add_city_taxonomy_filter() {
    global $typenow;
    if ($typenow === 'event') {
        wp_dropdown_categories([
            'show_option_all' => 'All Cities',
            'taxonomy'        => 'city',
            'name'            => 'city',
            'orderby'         => 'name',
            'selected'        => $_GET['city'] ?? '',
            'hierarchical'    => true,
            'depth'           => 3,
            'show_count'      => true,
            'hide_empty'      => true,
            'value_field'     => 'name'
        ]);
    }
}
add_action('restrict_manage_posts', 'add_city_taxonomy_filter');
function filter_events_by_city_in_admin($query) {
    global $pagenow;

    if (
        is_admin() &&
        $query->is_main_query() &&
        $pagenow === 'edit.php' &&
        isset($_GET['post_type']) && $_GET['post_type'] === 'event' &&
        isset($_GET['city']) && is_numeric($_GET['city']) && $_GET['city'] != 0
    ) {
       
         $taxquery = array(
                        array(
                            'taxonomy' => 'city',
                            'field' => 'term_id',
                            'terms' => array( $_GET['city']),
                            'operator'=> 'IN'
                        )
                    );
                        $query->set( 'tax_query', $taxquery );
    }
}
// add_action('pre_get_posts', 'filter_events_by_city_in_admin');



function add_event_admin_columns($columns) {

    $columns['_organizer_name'] = 'Organization Name';
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['event_status'] = 'Status';
            $new_columns['event_date'] = 'Start Date';
        }
    }
    return $new_columns;
}
add_filter('manage_event_posts_columns', 'add_event_admin_columns');


function show_event_column_data($column, $post_id) {
    if ($column === '_organizer_name') {
        $value = get_post_meta($post_id, '_organizer_name', true);
        echo esc_html($value);
    }
    if ($column === 'event_status') {
        $status = get_post_status($post_id);
        $labels = [
            'pending_review' => 'Pending Review',
            'scheduled' => 'Scheduled',
            'rejected' => 'Rejected',
            'publish' => 'Published',
            'draft' => 'Draft'
        ];

        $colors = [
            'pending_review' => 'orange',
            'scheduled' => 'blue',
            'rejected' => 'red',
            'publish' => 'green',
            'draft' => '#ccc'
        ];

        $label = $labels[$status] ?? $status;
        $color = $colors[$status] ?? '#999';

        echo "<span style='background:$color; color:white; padding:2px 6px; border-radius:3px;'>$label</span>";
    }

    if ($column === 'event_date') {
        $start = get_post_meta($post_id, '_event_start', true);
        echo $start ? date('M d, Y H:i', strtotime($start)) : '—';
    }
}
add_action('manage_event_posts_custom_column', 'show_event_column_data', 10, 2);

function make_event_columns_sortable($columns) {
    $columns['_organizer_name'] = '_organizer_name';
    $columns['event_date'] = 'event_date';
    return $columns;
}
add_filter('manage_edit-event_sortable_columns', 'make_event_columns_sortable');

function sort_events_by_organization_name($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if (
        is_admin() &&
        isset($_GET['post_type']) && $_GET['post_type'] === 'event' &&
        $query->is_main_query()
    ) {
        if( !isset($_GET['post_status']) && $_GET['post_status'] == '')
        $query->set('post_status', ['publish', 'draft', 'pending', 'private', 'scheduled', 'pending_review', 'rejected']);
    }
    if (
        isset($_GET['orderby']) && $_GET['orderby'] === '_organizer_name'
    ) {
        $query->set('meta_key', '_organizer_name');
        $query->set('orderby', 'meta_value'); // or meta_value_num if it's numeric
    }
    if ($query->get('orderby') === 'event_date') {
        $query->set('meta_key', '_event_start');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'sort_events_by_organization_name');


/* 
 * Add custom Role & Capability Management
 */ 



function add_custom_event_roles() {
    // Event Submitter
   add_role('event_submitter', 'Event Submitter', [
        'read' => true,
        'edit_events' => true,
        'edit_event' => true,
        'delete_event' => false,
        'publish_events' => false,
    ]);

    // Moderator (can review & publish others’ events)
    add_role('event_moderator', 'Event Moderator', [
        'read' => true,
        'edit_events' => true,
        'edit_others_events' => true,
        'publish_events' => true,
        'delete_events' => true,
        'delete_others_events' => true,
    ]);

    // Admin with full access
    add_role('event_admin', 'Event Admin', [
        'read' => true,
        'edit_events' => true,
        'edit_others_events' => true,
        'publish_events' => true,
        'delete_events' => true,
        'delete_others_events' => true,
        'read_private_events' => true,
        'edit_private_events' => true,
        'edit_published_events' => true,
        'delete_private_events' => true,
        'delete_published_events' => true,
    ]);
}
add_action('init', 'add_custom_event_roles');
function add_event_caps_to_admin() {
    $roles = ['administrator']; // you can add other roles here, like 'editor', 'limited_author'

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if (!$role) continue;

        $caps = [
            'edit_event',
            'read_event',
            'delete_event',
            'edit_events',
            'edit_others_events',
            'publish_events',
            'read_private_events',
            'delete_events',
            'delete_private_events',
            'delete_published_events',
            'delete_others_events',
            'edit_private_events',
            'edit_published_events',
        ];

        foreach ($caps as $cap) {
            $role->add_cap($cap);
        }
    }
}
add_action('admin_init', 'add_event_caps_to_admin');

/*
 * Sidebar Widget with Random Upcoming Event
 */

class Random_Event_Widget extends WP_Widget {
    function __construct() {
        parent::__construct('random_event_widget', 'Random Upcoming Event');
    }

    function widget($args, $instance) {
        $event = get_transient('random_event_widget');

        if (!$event) {
            $query = new WP_Query([
                'post_type' => 'event',
                'posts_per_page' => 1,
                'orderby' => 'rand',
                'meta_query' => [[
                    'key' => '_event_start',
                    'value' => current_time('Y-m-d H:i:s'),
                    'compare' => '>=',
                    'type' => 'DATETIME'
                ]]
            ]);

            if ($query->have_posts()) {
                $event = $query->posts[0];
                set_transient('random_event_widget', $event, 600);
            }
        }

        if ($event) {
            $link = get_permalink($event->ID);
            $nonce = wp_create_nonce('join_event_' . $event->ID);

            echo '<div class="random-event-widget">';
            echo '<h4>' . esc_html($event->post_title) . '</h4>';
            echo '<p><a href="' . esc_url($link) . '">View Event</a></p>';
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="event_id" value="' . esc_attr($event->ID) . '">';
            echo '<input type="hidden" name="nonce" value="' . esc_attr($nonce) . '">';
            echo '<input type="hidden" name="action" value="join_event">';
            echo '<button type="submit">Join Event</button>';
            echo '</form>';
            echo '</div>';
        }
    }
}
add_action('widgets_init', fn() => register_widget('Random_Event_Widget'));


/*
 * Audit Trail System
 */

function create_event_log_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'event_logs';

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        event_id BIGINT NOT NULL,
        user_id BIGINT NOT NULL,
        action VARCHAR(20),
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        changes LONGTEXT
    ) {$wpdb->get_charset_collate()};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'create_event_log_table');


add_action('save_post_event', function ($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!$update) return;

    $old = get_post_meta($post_id, '_event_snapshot', true) ?: [];
    $new = [
        'title' => $post->post_title,
        'start' => get_post_meta($post_id, '_event_start', true),
        'end' => get_post_meta($post_id, '_event_end', true),
        // 'venue' => get_post_meta($post_id, '_event_venue', true),
    ];

    $diff = array_diff_assoc($new, $old);
    if (!empty($diff)) {
        global $wpdb;
        $wpdb->insert("{$wpdb->prefix}event_logs", [
            'event_id' => $post_id,
            'user_id' => get_current_user_id(),
            'action' => 'edit',
            'changes' => wp_json_encode($diff),
        ]);
        update_post_meta($post_id, '_event_snapshot', $new);
    }
}, 10, 3);

/*
 *  QR Code Generation on Event Publish
 */
add_action('publish_event', function ($post_id) {
    if (get_post_meta($post_id, '_event_qr', true)) return;

    $event_url = get_permalink($post_id);
    $upload_dir = wp_upload_dir();
    $filename = 'qr_event_' . $post_id . '.png';
    $filepath = $upload_dir['path'] . '/' . $filename;

    // Generate QR code using Google API (or use a library like PHP QR Code)
    $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($event_url);
    $image = file_get_contents($qr_url);

    if ($image) {
        file_put_contents($filepath, $image);

        $filetype = wp_check_filetype($filename, null);
        $attachment = [
            'guid'           => $upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title'     => 'QR for Event ' . $post_id,
            'post_status'    => 'inherit'
        ];

        $attach_id = wp_insert_attachment($attachment, $filepath, $post_id);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        wp_generate_attachment_metadata($attach_id, $filepath);
        wp_update_attachment_metadata($attach_id, []);

        update_post_meta($post_id, '_event_qr', $attach_id);
    }
});


add_action('add_meta_boxes', function () {
    add_meta_box('event_qr', 'Event QR Code', function ($post) {
        $qr_id = get_post_meta($post->ID, '_event_qr', true);
        if ($qr_id) {
            echo '<div >';
            echo '<img src="'.wp_get_attachment_image_url($qr_id).'" style="max-width:100%">';
            // echo wp_get_attachment_image($qr_id, 'thumbnail');
            echo '</div>';
        } else {
            echo 'QR code will be generated on publish.';
        }
    }, 'event', 'side');
});
