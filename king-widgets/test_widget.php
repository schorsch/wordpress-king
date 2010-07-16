<?php
/*
Plugin Name: testing_Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress/wordpress-widget-king-categories/
Description: Adds a sidebar Categorie widget and lets users configure EVERY aspect of the category list. 
Author: Georg Leciejewski
Version: 1.01
Author URI: http://www.mediaprojekte.de
*/
/**
 * Search widget class
 *
 * @since 2.8.0
 */
class WP_Widget_Testing extends WP_Widget {

	function WP_Widget_Testing() {
		$widget_ops = array('classname' => 'widget_testing', 'description' => __( "a test widget ") );
		$this->WP_Widget('testing', __('testing Search'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		// Use current theme search form if it exists
		get_search_form();

		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

}