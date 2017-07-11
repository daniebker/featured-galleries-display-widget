<?php
/*
Plugin Name: Featured Galleries Display Widget
Plugin URI: 
Description: A simple widget for displaying featured galleries.
Version: 0.1.0
Author: daniel baker
Author URI: http://daniebker.co.uk
License: MIT
*/

add_action( 'admin_init', 'plugin_has_parent_plugin' );
function plugin_has_parent_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'featured-galleries/featured-galleries.php' ) ) {
        add_action( 'admin_notices', 'plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function plugin_notice(){
    ?><div class="error"><p>Sorry, but Featured Galleries Display Widget Plugin requires the Featured Galleries plugin to be installed and active.</p></div><?php
}

// Creating the widget 
class featured_galleries_display_widget extends WP_Widget {

    function __construct() {
        parent::__construct(

        'featured_galleries_display_widget', 

        __('Featured Galleries Display Widget', 'featured_galleries_display_widget_domain'), 

        array( 'description' => __( 'A widget to quickly display a featured gallery on any page or widget area.', 'featured_galleries_display_widget_domain' ), ) 
        );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $queried_object = get_queried_object();

        if ($queried_object) {
            $post_id = $queried_object->ID;

            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if(is_plugin_active('featured-galleries/featured-galleries.php')){
                $galleryArray = get_post_gallery_ids($post_id);

                echo ' <!-- GALLERY --> <div class="' . $instance['galleryClass'] . '">';
                foreach ($galleryArray as $id) {
                    echo '<div class="' . $instance['imageWrapperClass'] . '">';
                    echo '<a href="' . wp_get_attachment_url( $id ) . '" data-lightbox="gallery">';
                    echo '<img src="'. wp_get_attachment_thumb_url( $id ) . '" class="' . $instance['imageClass'] . '"></a></div>';
                }
                echo '</div> <!-- END GALLERY -->';

          }

        }

        echo $args['after_widget'];
    }
            
    public function form( $instance ) {
      // Check values
        if( $instance) {
            $title = esc_attr($instance['title']);
            $galleryClass = esc_attr($instance['galleryClass']);
            $imageWrapperClass = esc_attr($instance['imageWrapperClass']);
            $imageClass = esc_attr($instance['imageClass']);
        } else {
            $title = 'Gallery';
            $galleryClass = 'row';
            $imageWrapperClass = 'col-lg-2 col-sm-4 col-xs-6';
            $imageClass = 'thumbnail img-responsive';
        }
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('galleryClass'); ?>"><?php _e('Gallery Class', 'wp_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('galleryClass'); ?>" name="<?php echo $this->get_field_name('galleryClass'); ?>" type="text" value="<?php echo $galleryClass; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('imageWrapperClass'); ?>"><?php _e('Image Wrapper Class', 'wp_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('imageWrapperClass'); ?>" name="<?php echo $this->get_field_name('imageWrapperClass'); ?>" type="text" value="<?php echo $imageWrapperClass; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('imageClass'); ?>"><?php _e('Image Class', 'wp_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('imageClass'); ?>" name="<?php echo $this->get_field_name('imageClass'); ?>" type="text" value="<?php echo $imageClass; ?>" />
        </p>
       <?php
    }
        
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['galleryClass'] = strip_tags($new_instance['galleryClass']);
        $instance['imageWrapperClass'] = strip_tags($new_instance['imageWrapperClass']);
        $instance['imageClass'] = strip_tags($new_instance['imageClass']);
        return $instance;
    }
} 
	
function enqueue_scripts()
{
    wp_register_script( 'light-box2', plugins_url( 'js/lightbox.js', __FILE__ ), array( 'jquery', 'jquery-ui-core' ), '20120208', true );
    
    wp_enqueue_script( 'light-box2' );
    wp_enqueue_style('lightbox_css', plugins_url( 'css/lightbox.css', __FILE__ ) );

}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

function wpb_load_widget() {
	register_widget( 'featured_galleries_display_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

?>
