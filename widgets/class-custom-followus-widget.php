<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

add_action( 'widgets_init', 'demotheme_followus_widget' );

/**
 * Register the Follow Us Widget
 */
function demotheme_followus_widget() {
    register_widget( 'Followus' );
}

class Followus extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct(){
        parent::__construct(
                'followus', // Base ID
                __( 'Demo Theme - Follow Us', 'demotheme' ), // Name
                array('description' => __( 'Custom follow us widget', 'demotheme' )) // Args
        );
    }

    /**
     * Front end display
     * @param type $args
     * @param type $instance
     */
    public function widget($args, $instance) {

        extract( $args );

        echo $before_widget;

        $title = apply_filters( 'widget_title', $instance['title'] );

        if( !empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }
        
        if( demotheme_check_acf_activation() ) {
            $fb_url     = get_field( 'fb_url', 'option' );
            $fb_url     = !empty( $fb_url ) ? $fb_url : '#';

            $tw_url     = get_field( 'tw_url', 'option' );
            $tw_url     = !empty( $tw_url ) ? $tw_url : '#';

            $insta_url  = get_field( 'insta_url', 'option' );
            $insta_url  = !empty( $insta_url ) ? $insta_url : '#';
?>
        <?php if( !empty($fb_url) && !empty($tw_url) && !empty($insta_url) ) { ?>
            <ul class="social-icon">
                <?php if( !empty($fb_url) ) { ?>
                    <li>
                        <a href="<?php echo $fb_url; ?>" target="_blank">facebook</a>
                    </li>
                <?php } ?>
                <?php if( !empty($tw_url) ) { ?>
                    <li>
                        <a href="<?php echo $tw_url; ?>" target="_blank">twitter</a>
                    </li>
                <?php } ?>
                <?php if( !empty($insta_url) ) { ?>
                    <li>
                        <a href="<?php echo $insta_url; ?>" target="_blank">instagram</a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
<?php
        }
        echo $after_widget;
    }

    /**
     * Update widget values
     * @param type $new_instance
     * @param type $old_instance
     * @return type
     */
    public function update($new_instance, $old_instance) {
        $instance            = array();
        $instance['title']   = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';        

        return $instance;
    }

    /*
     * Displays the widget form in the admin panel
     */
    function form($instance) {
        $title  = !empty( $instance['title'] ) ? $instance['title'] : '';        
    ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'demotheme' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
            </p>
    <?php
    }
}
?>