<?php
/*
Plugin Name: BioBox
Description: A plugin that adds a BioBox with a widget, shortcode, and metabox.
Version: 1.0
Author: Your Name
*/

// Register widget
class BioBox_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'biobox_widget',
            __( 'BioBox - About Me', 'biobox' ),
            [ 'description' => __( 'This should display an image, title text, and paragraph text.', 'biobox' ) ]
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        ?>
        <div class="biobox-widget">
            <?php if ( ! empty( $instance['image'] ) ) : ?>
                <img src="<?php echo esc_url( $instance['image'] ); ?>" alt="<?php echo esc_attr( $instance['title'] ); ?>" class="biobox-image">
            <?php endif; ?>
            <?php if ( ! empty( $instance['text'] ) ) : ?>
                <p><?php echo esc_html( $instance['text'] ); ?></p>
            <?php endif; ?>
        </div>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $image = ! empty( $instance['image'] ) ? $instance['image'] : '';
        $text  = ! empty( $instance['text'] ) ? $instance['text'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'biobox' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_attr_e( 'Image URL:', 'biobox' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="text" value="<?php echo esc_url( $image ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_attr_e( 'Text:', 'biobox' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
        </p>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['image'] = ( ! empty( $new_instance['image'] ) ) ? esc_url_raw( $new_instance['image'] ) : '';
        $instance['text']  = ( ! empty( $new_instance['text'] ) ) ? sanitize_textarea_field( $new_instance['text'] ) : '';
        return $instance;
    }
}

function biobox_register_widget() {
    register_widget( 'BioBox_Widget' );
}
add_action( 'widgets_init', 'biobox_register_widget' );

// Shortcode
function biobox_shortcode( $atts ) {
    $user_id = get_the_author_meta( 'ID' );
    $avatar  = get_avatar_url( $user_id );
    $name    = get_the_author_meta( 'display_name' );
    $desc    = get_the_author_meta( 'description' );
    $twitter = get_the_author_meta( 'twitter' );
    $facebook = get_the_author_meta( 'facebook' );
    $linkedin = get_the_author_meta( 'linkedin' );
    $instagram = get_the_author_meta( 'instagram' );

    ob_start();
    ?>
    <div class="biobox-shortcode">
        <img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="biobox-avatar">
        <div class="biobox-content">
            <h3><?php echo esc_html( $name ); ?></h3>
            <p><?php echo esc_html( $desc ); ?></p>
            <div class="biobox-social">
                <?php if ( $twitter ) : ?>
                    <a href="<?php echo esc_url( $twitter ); ?>" target="_blank"><?php esc_html_e( 'Twitter', 'biobox' ); ?></a>
                <?php endif; ?>
                <?php if ( $facebook ) : ?>
                    <a href="<?php echo esc_url( $facebook ); ?>" target="_blank"><?php esc_html_e( 'Facebook', 'biobox' ); ?></a>
                <?php endif; ?>
                <?php if ( $linkedin ) : ?>
                    <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank"><?php esc_html_e( 'LinkedIn', 'biobox' ); ?></a>
                <?php endif; ?>
                <?php if ( $instagram ) : ?>
                    <a href="<?php echo esc_url( $instagram ); ?>" target="_blank"><?php esc_html_e( 'Instagram', 'biobox' ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'biobox', 'biobox_shortcode' );

// Enqueue styles
function biobox_enqueue_styles() {
    ?>
    <style>
        .biobox-widget img,
        .biobox-shortcode img {
            max-width: 100%;
            height: auto;
            border-radius: 50%;
        }
        .biobox-shortcode {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
        .biobox-content {
            margin-left: 15px;
        }
        .biobox-social a {
            margin-right: 10px;
            text-decoration: none;
            color: #0073aa;
        }
        .biobox-social a:hover {
            color: #005177;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'biobox_enqueue_styles' );

// Add meta box for disabling shortcode
function biobox_add_meta_box() {
    add_meta_box(
        'biobox_meta_box',
        __( 'BioBox Settings', 'biobox' ),
        'biobox_meta_box_callback',
        'post',
        'side'
    );
}
add_action( 'add_meta_boxes', 'biobox_add_meta_box' );

/**
 * Meta box callback
 *
 * @param WP_Post $post
 */
function biobox_meta_box_callback( $post ) {
    wp_nonce_field( 'biobox_save_meta_box_data', 'biobox_meta_box_nonce' );
    $value = get_post_meta( $post->ID, '_biobox_disable', true );
    ?>
    <p>
        <label for="biobox_disable">
            <input type="checkbox" id="biobox_disable" name="biobox_disable" value="1" <?php checked( $value, '1' ); ?>>
            <?php esc_html_e( 'Disable BioBox shortcode', 'biobox' ); ?>
        </label>
    </p>
    <?php
}

/**
 * Save meta box data
 *
 * @param int $post_id
 */
function biobox_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['biobox_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['biobox_meta_box_nonce'], 'biobox_save_meta_box_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $disable = isset( $_POST['biobox_disable'] ) ? '1' : '';
    update_post_meta( $post_id, '_biobox_disable', $disable );
}
add_action( 'save_post', 'biobox_save_meta_box_data' );

// Append shortcode to post content
function biobox_append_shortcode( $content ) {
    if ( is_single() && ! get_post_meta( get_the_ID(), '_biobox_disable', true ) ) {
        $content .= do_shortcode( '[biobox]' );
    }
    return $content;
}
add_filter( 'the_content', 'biobox_append_shortcode' );
