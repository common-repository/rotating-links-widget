<?php
/*
Plugin Name: Rotating Links Widget
Plugin URI: http://www.techforum.sk/
Description: Widget displays random links
Version: 0.1
Author: Ján Bočínec
Author URI: http://www.johnnypea.wp.sk/
License: GPL2
*/

/**
 * Rotating Links Widget class
 */
class Rotating_Links_Widget extends WP_Widget {

	function Rotating_Links_Widget() {
		$widget_ops = array('description' => __('Rotating Links Widget') );
		$this->WP_Widget('rotate_links', __('Rotating Links'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args, EXTR_SKIP);
		
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$show_description = isset($instance['description']) ? $instance['description'] : false;
		$show_name = isset($instance['name']) ? $instance['name'] : false;
		$show_rating = isset($instance['rating']) ? $instance['rating'] : false;
		$show_images = isset($instance['images']) ? $instance['images'] : true;
		$category = isset($instance['category']) ? $instance['category'] : false;
		$limit = isset($instance['limit']) ? $instance['limit'] : 1;

		$before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
		wp_list_bookmarks( array(
			'title_before' => $before_title, 
			'title_after' => $after_title,
			'category_before' => $before_widget, 
			'category_after' => $after_widget,
			'show_images' => $show_images, 
			'show_description' => $show_description,
			'show_name' => $show_name, 
			'show_rating' => $show_rating,
			'category' => $category,
			'title_li' => $title, 
			'class' => 'rotlinkcat widget',
			'orderby' => 'rand',
			'limit' => $limit,
			'categorize' => '0',
		));
	}

	function update( $new_instance, $old_instance ) {
		$new_instance = (array) $new_instance;
		$instance = array( 'images' => 0, 'name' => 0, 'description' => 0, 'rating' => 0 );
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = 1;
		}
		$instance['category'] = intval($new_instance['category']);
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['limit'] = ( isset($new_instance['limit']) && !empty($new_instance['limit']) ) ? absint($new_instance['limit']) : 1;

		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'images' => true, 'name' => true, 'description' => false, 'rating' => false, 'category' => false ) );
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Rotating Links');
		$limit = isset($instance['limit']) ? absint($instance['limit']) : 1;			
		$link_cats = get_terms( 'link_category');
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of links to show:'); ?></label> <input size="3" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" /></p>
		<p>
		<label for="<?php echo $this->get_field_id('category'); ?>" class="screen-reader-text"><?php _e('Select Link Category'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
		<option value=""><?php _e('All Links'); ?></option>
		<?php
		foreach ( $link_cats as $link_cat ) {
			echo '<option value="' . intval($link_cat->term_id) . '"'
				. ( $link_cat->term_id == $instance['category'] ? ' selected="selected"' : '' )
				. '>' . $link_cat->name . "</option>\n";
		}
		?>
		</select></p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['images'], true) ?> id="<?php echo $this->get_field_id('images'); ?>" name="<?php echo $this->get_field_name('images'); ?>" />
		<label for="<?php echo $this->get_field_id('images'); ?>"><?php _e('Show Link Image'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['name'], true) ?> id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" />
		<label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Show Link Name'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['description'], true) ?> id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" />
		<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Show Link Description'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['rating'], true) ?> id="<?php echo $this->get_field_id('rating'); ?>" name="<?php echo $this->get_field_name('rating'); ?>" />
		<label for="<?php echo $this->get_field_id('rating'); ?>"><?php _e('Show Link Rating'); ?></label>
		</p>
<?php
	}
}

/**
 * Register widget.
 */

add_action('widgets_init', create_function('', 'return register_widget("Rotating_Links_Widget");'));
