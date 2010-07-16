<?php
/*
Plugin Name: King_Categories_Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress/wordpress-widget-king-categories/
Description: Adds a sidebar Categorie widget and lets users configure EVERY aspect of the category style.
Author: Georg Leciejewski
Version: 1.01
Author URI: http://www.mediaprojekte.de
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
define("KINGCATEGORIESVERSION",  "200");

include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');


/**
 * Categories widget class
 *
 * @since 2.8.0
 */
class WP_Widget_King_Categories extends WP_Widget {

	function WP_Widget_King_Categories() {
		$widget_ops = array( 'classname' => 'widget_king_categories', 'description' => __( "A list or dropdown of categories" ) );
		$this->WP_Widget('king_categories', __('KingCategories'), $widget_ops);
	}

  /**
   * Output of the widget
   * @param <type> $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.
   * @param <type> $instance
   */

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base);

//    print_r($instance);
    //merge defaults
//		$instance = wp_parse_args( (array) $instance, $this->defaults() );
    //take care of some escaped fields
    $instance['feed_image']         = stripslashes($instance['feed_image']);
    $instance['before_widget']      = empty($instance['before_widget']) ? $before_widget : stripslashes($instance['before_widget']);
    $instance['before_widget_title']= empty($instance['before_widget_title']) ? $before_title : stripslashes($instance['before_widget_title']);
    $instance['after_widget_title'] = empty($instance['after_widget_title'] ) ? $after_title : stripslashes($instance['after_widget_title']) ;
    $instance['after_widget']       = empty($instance['after_widget']) ? $after_widget : stripslashes($instance['after_widget']) ;

//    print_r($instance);
    $already_out = false;
    # These lines generate our output. Widgets can be very complex
    # but as you can see here, they can also be very, very simple.
    if( !empty($instance['show_category']) ) {
      $post = $wp_query->post;
      if ( king_in_category($instance['category_id']) )  {
        $this->output($instance);
        $already_out = true;
      }
    }

    # sitearea Output
    if( !empty($instance['show_on_site_area']) ) {
      if ( king_in_site_area($instance['site_area'], $instance['site_area_id']) && !$already_out) {
        # in the site area
        $this->output($instance);
      }
    } elseif(!empty($instance['show_not_on_site_area'])) {
      if (!king_in_site_area($instance['site_area'], $instance['site_area_id']) && !$already_out ) {
        #not in the site area
        $this->output($instance);
      }
    }
    # always show
    if( empty($instance['show_not_on_site_area']) && empty($instance['show_on_site_area']) && empty($instance['show_category']) ) {
      $this->output($instance);
    }

    if (!empty($instance['debug'])){
      $str = "<h2>__('Your Menu Options are:', 'widgetKing')</h2>";
      $str .= print_r($instance);
      echo $str;
    }
	}

  	/** Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
   
//    if( $new_instance["copy] !=='No' && $new_instance["copy"] !=  $this->id_base){
//      # copy settings from another widget
//      $copy = $_POST["king_cat_copy_$number"];
//      $instance = array();
//      foreach($options[$copy] as $key => $val){
//        $instance[$key] = $val;
//      }
//    } elseif( !empty($new_instance["import"]) ){
//      # use setting from dump
//      $instance = king_read_options($_POST["king_cat_dump_$number"]);
//    } else{

      # save new form values
      $instance['title']          = strip_tags(stripslashes($new_instance["title"]));
      $instance['orderby']        = $new_instance["orderby"];
      $instance['order']          = $new_instance["order"];
      $instance['style']          = isset( $new_instance["style"]) ? 'list': 'none' ;
      $instance['show_last_update']	= $new_instance["show_last_update"];
      $instance['show_count']       = $new_instance["show_count"];
      $instance['hide_empty']       = $new_instance["hide_empty"];
      $instance['use_desc_for_title']	= $new_instance["use_desc_for_title"];
      $instance['depth']        = $new_instance["depth"];
      $instance['child_of']     = strip_tags(stripslashes($new_instance["child_of"]));
      $instance['feed']         = strip_tags(stripslashes($new_instance["feed"]));
      $instance['feed_image']		= addslashes($new_instance["feed_image"]);
      $instance['exclude']			= stripslashes($new_instance["exclude"]);
      $instance['hierarchical']	= $new_instance["hierarchical"];
      $instance['debug']        = $new_instance["debug"];
      $instance['before_widget']= html_entity_decode($new_instance["before_widget"]);
      $instance['after_widget']	= html_entity_decode($new_instance["after_widget"]);
      $instance['before_widget_title']  = html_entity_decode($new_instance["before_widget_title"]);
      $instance['after_widget_title']   = html_entity_decode($new_instance["after_widget_title"]);

      $instance['show_category']      = isset($new_instance["showcategory"]);
      $instance['category_id']        = $new_instance["category_id"];
      $instance['show_on_site_area']	= $new_instance["show_on_site_area"];
      $instance['show_not_on_site_area']= $new_instance["show_not_on_site_area"];
      $instance['site_area']			= $new_instance["site_area"];
      $instance['site_area_id']		= $new_instance["site_area_id"];
//  }
		return $instance;
	}

	function form( $instance ) {
//    echo print_r($instance);
		//Defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults() );

    echo '<h3><a href="#">'. __('Basic', 'widgetKing').'</a></h3> <div>';
		# show title
		echo king_text_p(array(
				'name' => $this->get_field_name('title'),
				'id' => $this->get_field_id('title'),
				'descr' 	=> __('Title', 'widgetKing'),
				'title' 	=> __('The title above your category menu', 'widgetKing'),
				'val' 		=> esc_html($instance['title']) ));

		#sort Column
    echo '<p>';
    echo king_label(  $this->get_field_id('orderby'), __('Sort by', 'widgetKing'),
                      __('Sort Categories ascending or descending depending on choosen sort column.', 'widgetKing') );
    echo '<br/>';
    echo king_select( $this->get_field_name('orderby'), $instance['orderby'],
                      array('name', 'ID', 'count', 'term_group', 'slug'),
                      $this->get_field_id('orderby') );

    echo king_select( $this->get_field_name('order'), $instance['order'],
                      array('asc', 'desc'), $this->get_field_id('order') );
    echo '</p>';
 		#show category Count
		echo king_checkbox_p(array(
				'name' => $this->get_field_name('show_count'),
				'id' => $this->get_field_id('show_count'),
				'descr' 	=> __('Show post counts', 'widgetKing'),
				'title' 	=> __('Show number of posts in category', 'widgetKing'),
				'val' 		=> $instance['show_count'] ));
		
		#show empty
		echo king_checkbox_p(array(
				'name' => $this->get_field_name('hide_empty'),
        'id'    => $this->get_field_id('hide_empty'),
				'descr' 	=> __('Hide Empty Categories', 'widgetKing'),
				'title' 	=> __('Categories without articles are not shown.', 'widgetKing'),
				'val' 		=> $instance['hide_empty'] ));

		# devider
    echo '</div> <h3><a href="#">'. __('Advanced', 'widgetKing') .'</a></h3> <div>';
    		#exclude categories
		echo king_text_p(array(
				'name' => $this->get_field_name('exclude'),
      'id'    => $this->get_field_id('exclude'),
				'descr' 	=> __('Exclude Categories (1,2,3)', 'widgetKing'),
				'title' 	=> __('Sets the Categories to be excluded. This must be in the form of an array (ex: 1, 2, 3).', 'widgetKing'),
				'val' 		=> $instance['exclude']));

		#show child_of
		echo king_text_p(array(
				'name' => $this->get_field_name('child_of'),
      'id'    => $this->get_field_id('child_of'),
				'descr' 	=>__('Show Children of Category', 'widgetKing'),
				'title' 	=>__('Show only children of this category(id).', 'widgetKing'),
				'val' 		=> $instance['child_of'] ));
    #show cat depth
		echo king_text_p(array(
				'name' => $this->get_field_name('depth'),
        'id'    => $this->get_field_id('depth'),
				'descr' 	=> __('Category tree depth', 'widgetKing'),
				'title' 	=>__('Descend to depth(number) into the category tree: 0 = All, -1 = All Flat(no indent), 1 = only top-level, n = number/levels to descend', 'widgetKing'),
        'val' 		=> $instance['depth'] ));
//		#insert feed text
		echo king_text_p(array(
      'name' => $this->get_field_name('feed'),
      'id'    => $this->get_field_id('feed'),
      'descr' 	=>__('Show Category Feed Text', 'widgetKing'),
      'title' 	=> __('Text to display for the link to each Categorys RSS2 feed. Default is no text, and no feed displayed.', 'widgetKing'),
      'val' 		=> $instance['feed']));
//		#name of feed image  Path/filename
		echo king_text_p(array(
      'name' => $this->get_field_name('feed_image'),
      'id'    => $this->get_field_id('feed_image'),
      'descr' 	=>__('Show Category Feed Image', 'widgetKing'),
      'title' 	=> __('URL Path/filename for a graphic to act as a link to each Categories RSS2 feed.Overrides the feed parameter.', 'widgetKing'),
      'val' 		=> $instance['feed_image']));
    #show show_last_update
		echo king_checkbox_p(array(
				'name' => $this->get_field_name('show_last_update'),
      'id'    => $this->get_field_id('show_last_update'),
				'descr' 	=>__('Date of the last post', 'widgetKing'),
				'title' 	=> __('Sets whether to display the date of the last post in each Category.', 'widgetKing'),
				'val' 		=> $instance['show_last_update'] ));
		#description as title
		echo king_checkbox_p(array(
				'name' => $this->get_field_name('use_desc_for_title'),
      'id'    => $this->get_field_id('use_desc_for_title'),
				'descr' 	=>__('Use Description as Title','widgetKing'),
				'title' 	=>__('Sets whether to display the Category Description in the links title tag.', 'widgetKing'),
				'val' 		=> $instance['use_desc_for_title']));	
		    #list style
		echo king_checkbox_p(array(
				'name' => $this->get_field_name('style'),
        'id'    => $this->get_field_id('style'),
				'descr' 	=> __('Show as List (li)', 'widgetKing'),
				'title' 	=> __('Sets whether the Categories are enclosed by style points ->li', 'widgetKing'),
				'val' 		=> $instance['style']));
    #show hirachical
		echo king_checkbox_p(array(
      'name'  => $this->get_field_name('hierarchical'),
      'id'    => $this->get_field_id('hierarchical'),
      'descr' => __('Show hierarchical', 'widgetKing'),
      'title' =>__('Shows Categories hierarchical with sub-categories indented -> Depending on your CSS', 'widgetKing'),
      'val' 	=> $instance['hierarchical']));
    		
		#devider
    echo '</div> <h3><a href="#">'. __('Show', 'widgetKing') .'</a></h3> <div>';
		# Where To Show Options Panel
		where_to_show_widget($this,
                        $instance['show_category'],
                        $instance['category_id'],
                        $instance['show_on_site_area'],
                        $instance['show_not_on_site_area'],
                        $instance['site_area'],
                        $instance['site_area_id'] );
		# devider
    echo '</div> <h3><a href="#">'. __('HTML', 'widgetKing') .'</a></h3> <div>';
		widget_king_htmloptions($this, 
              stripslashes(htmlentities($instance['before_widget'])),
              stripslashes(htmlentities($instance['before_widget_title'])),
              stripslashes(htmlentities($instance['after_widget_title'])),
              stripslashes(htmlentities($instance['after_widget'])) );
		# show debug output
//		echo king_checkbox_p(array(
//				'name' =>"debug",
//				'descr' 	=> __('Show Debug Output', 'widgetKing'),
//				'title' 	=>  __('Shows all set options in Frontend to check what you have entered. The list_cats() is pretty bitchy so you might want to know whats going on.', 'widgetKing'),
//				'val' 		=> $debug));
//
//    echo '</div> <h3><a href="#">'. __('Export', 'widgetKing') .'</a></h3> <div>';
////		king_get_dump_options('cat',$number,'widget_king_categories');
//    #copy
//		echo king_select_p(array(
//			'name' => $this->get_field_name('copy'),
//			'descr' 	=> __('Copy Settings from Widget No.', 'widgetKing'),
//			'title' 	=> __('Choose a Widget Number from which you want to copy the settings into this one. Make sure to choose the right widget, with some Options in it!', 'widgetKing'),
//			'select_options'=> array('No','1', '2', '3', '4', '5', '6', '7', '8', '9')));
    echo '</div>';
	}



  /**
  * @desc the actual output of the category menu
  * @param array $data - holding the switches
  * @param int $number - the current widget number
  */
  function output($data) {
    echo '<!-- Start King Cat ' .$this->id_base . ' -->'."\n";
    echo $data['before_widget']."\n";
    echo $data['before_widget_title']."\n";
    echo $data['title'] ."\n";
    echo $data['after_widget_title']."\n";

    $args =	array(
//      'show_option_all' => '',
//      'show_option_none' => __('No categories'),
//      'exclude_tree' => '',
//      'current_category' => 0,
//      'title_li' => __( 'Categories' ),
//      'echo' => 1,
//      'taxonomy' => 'category'
      'orderby' => $data['orderby'],
      'order' => $data['order'],
      'show_last_update' => $data['show_last_update'], # 0
      'style' => $data['style'],
      'show_count' => $data['show_count'], #0
      'hide_empty' => $data['hide_empty'], # 1
      'use_desc_for_title' => $data['use_desc_for_title'], # 1
      'child_of' => $data['child_of'], # 0
      'feed' => $data['feed'],
      'feed_type' => '',
      'feed_image' => $data['feed_image'],
      'exclude' => $data['exclude'],
      'hierarchical' => $data['hierarchical'], # true
      'depth' => $data['depth'] );
    wp_list_categories($args);
    echo $data['after_widget']."\n";
    echo '<!-- End Cat ' . $this->id_base . ' -->'."\n";
    return;
  }

  /**
  * Default options for the widget
  *
  */
  function defaults() {
    return array(
      //WP_LISTCATS OPTIONS
//      'show_option_all' => '',
//      'show_option_none' => __('No categories'),
//      'exclude_tree' => '',
//      'current_category' => 0,
//      'title_li' => __( 'Categories' ),
//      'echo' => 1,
//      'taxonomy' => 'category'
      'orderby' =>'name',
      'order' =>'asc',
      'show_last_update' => 0,
      'style' =>'list',
      'show_count' => 0,
      'hide_empty' => 1,
      'use_desc_for_title' => 1,
      'child_of' => 0,
      'feed' => '',
      'feed_type' => '',
      'feed_image' => '',
      'exclude' => '',
      'hierarchical' =>  true,
      'depth' => 1,
      //widget options
      'title' => '',
      'before_widget' => "<li>",
      'after_widget'	=> addslashes("</ul></li>"),
      'before_widget_title' => "<h2>",
      'after_widget_title'	=> addslashes("</h2><ul>"),
      //show options
      'show_category'	=> '',
      'category_id'   => '',
      'show_on_site_area' => '',
      'show_not_on_site_area'	=>  '',
      'site_area_id'	=> '',
      'site_area'		=> ''
      );
  }
}

/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @since 2.2.0
 */
function king_cat_widget_init() {
	if ( !is_blog_installed() )
		return;
  register_widget('WP_Widget_King_Categories');
	do_action('widgets_init');
}

add_action('init', 'king_cat_widget_init', 1);