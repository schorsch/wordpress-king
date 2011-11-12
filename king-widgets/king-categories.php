<?php
/*
Plugin Name: King_Categories_Widget
Plugin URI: http://www.mediaprojekte.de/cms-systeme/wordpress/wordpress-widget-king-categories/
Description: Category list widget - Configure EVERY aspect of the category list.
Author: Georg Leciejewski
Version: 2.00
Author URI: http://www.mediaprojekte.de
*/

/*
    Copyright 2006-2012  Georg Leciejewski
 
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
 * @since 3.0.0
 */
class WP_Widget_King_Categories extends WP_Widget {

  function WP_Widget_King_Categories() {
    $widget_ops = array( 'classname' => 'widget_king_categories', 'description' => __( "List categories using all wp options, define where to show, set widget html, import/export " ) );
    $this->WP_Widget('king_categories', __('KingCategories'), $widget_ops);
  }

  /**
   * Output of the widget
   * @param <type> $args is an array of strings that help widgets to conform to
   * the active theme: before_widget, before_title, after_widget,
   * and after_title are the array keys. Default tags: li and h2.
   * @param <type> $opts
   */
  function widget( $args, $opts ) {
    global $wp_query;
    extract( $args );
    $title = apply_filters('widget_title', empty( $opts['title'] ) ? __( 'Categories' ) : $opts['title'], $opts, $this->id_base);

    //take care of some escaped fields
    $opts['feed_image']         = stripslashes($opts['feed_image']);
    $opts['before_widget']      = empty($opts['before_widget']) ? $before_widget : stripslashes($opts['before_widget']);
    $opts['before_widget_title']= empty($opts['before_widget_title']) ? $before_title : stripslashes($opts['before_widget_title']);
    $opts['after_widget_title'] = empty($opts['after_widget_title'] ) ? $after_title : stripslashes($opts['after_widget_title']) ;
    $opts['after_widget']       = empty($opts['after_widget']) ? $after_widget : stripslashes($opts['after_widget']) ;

    $already_out = false;
    # Show only in category
    if( !empty($opts['show_category']) ) {
      $post = $wp_query->post;
      if ( king_in_category($opts['cat_ids']) )  {
        $this->output($opts);
        $already_out = true;
      }
    }
    # sitearea Output
    if( !empty($opts['show_on_site_area']) ) {
      if ( king_in_site_area($opts['site_area'], $opts['site_area_id']) && !$already_out) {
        # in the site area
        $this->output($opts);
      }
    } elseif(!empty($opts['show_not_on_site_area'])) {
      if (!king_in_site_area($opts['site_area'], $opts['site_area_id']) && !$already_out ) {
        #not in the site area
        $this->output($opts);
      }
    }
    # always show
    if( empty($opts['show_not_on_site_area']) && empty($opts['show_on_site_area']) && empty($opts['show_category']) ) {
      $this->output($opts);
    }
  }

   /** Update a particular instance.
   *
   * This function should check that $new_opts is set correctly.
   * The newly calculated value of $opts should be returned.
   * If "false" is returned, the instance won't be saved/updated.
   *
   * @param array $new_opts New settings for this instance as input by the user via form()
   * @param array $old_opts Old settings for this instance
   * @return array Settings to save or bool false to cancel saving
   */
  function update( $new_opts, $old_opts ) {
    $opts = $old_opts;
    # use setting from json import if available
    $new_opts = !empty($new_opts["import"]) ? king_import_json($new_opts["import"]) : $new_opts;
    # save new form values
    $opts['title']              = strip_tags(stripslashes($new_opts["title"]));
    $opts['orderby']            = $new_opts["orderby"];
    $opts['order']              = $new_opts["order"];
    $opts['style']              = isset( $new_opts["style"]) ? 'list': 'none' ;
    $opts['show_last_update']   = $new_opts["show_last_update"];
    $opts['show_count']         = $new_opts["show_count"];
    $opts['hide_empty']         = $new_opts["hide_empty"];
    $opts['use_desc_for_title'] = $new_opts["use_desc_for_title"];
    $opts['depth']              = $new_opts["depth"];
    $opts['child_of']           = strip_tags(stripslashes($new_opts["child_of"]));
    $opts['feed']               = strip_tags(stripslashes($new_opts["feed"]));
    $opts['feed_image']         = addslashes($new_opts["feed_image"]);
    $opts['exclude']            = stripslashes($new_opts["exclude"]);
    $opts['hierarchical']       = $new_opts["hierarchical"];
    $opts['before_widget']      = html_entity_decode($new_opts["before_widget"]);
    $opts['after_widget']       = html_entity_decode($new_opts["after_widget"]);
    $opts['before_widget_title']= html_entity_decode($new_opts["before_widget_title"]);
    $opts['after_widget_title'] = html_entity_decode($new_opts["after_widget_title"]);

    $opts['show_category']      = isset($new_opts["show_category"]);
    $opts['cat_ids']            = $new_opts["cat_ids"];
    $opts['show_on_site_area']  = $new_opts["show_on_site_area"];
    $opts['show_not_on_site_area']= $new_opts["show_not_on_site_area"];
    $opts['site_area']          = $new_opts["site_area"];
    $opts['site_area_id']       = $new_opts["site_area_id"];

    return $opts;
  } #end update

  /**
  * Output the widgets settings form 
  */
  function form( $opts ) {
    //get default settings
    $opts = wp_parse_args( (array) $opts, $this->defaults() );

    echo '<h3><a href="#">'. __('Basic', 'widgetKing').'</a></h3> <div>';
    # show title
    echo king_text_p(array(
        'name'  => $this->get_field_name('title'),
        'id'    => $this->get_field_id('title'),
        'descr' => __('Title', 'widgetKing'),
        'title' => __('The title above this widget', 'widgetKing'),
        'val'   => esc_html($opts['title']) ));

    #sort Column
    echo '<p>';
    echo king_label(  $this->get_field_id('orderby'), __('Sort by', 'widgetKing'),
                      __('Sort the choosen column ASCending or DESCending.', 'widgetKing') );
    echo '<br/>';
    echo king_select( $this->get_field_name('orderby'), $opts['orderby'],
                      array('name', 'ID', 'count', 'term_group', 'slug'),
                      $this->get_field_id('orderby') );

    echo king_select( $this->get_field_name('order'), $opts['order'],
                      array('asc', 'desc'), $this->get_field_id('order') );
    echo '</p>';
     #show category Count
    echo king_checkbox_p(array(
      'name'  => $this->get_field_name('show_count'),
      'id'    => $this->get_field_id('show_count'),
      'descr' => __('Show post counts', 'widgetKing'),
      'title' => __('Show number of posts in category', 'widgetKing'),
      'val'   => $opts['show_count'] ));
    
    #show empty
    echo king_checkbox_p(array(
      'name'  => $this->get_field_name('hide_empty'),
      'id'    => $this->get_field_id('hide_empty'),
      'descr' => __('Hide Empty Categories', 'widgetKing'),
      'title' => __('Categories without articles are not shown.', 'widgetKing'),
      'val'   => $opts['hide_empty'] ));

    # devider
    echo '</div> <h3><a href="#">'. __('Advanced', 'widgetKing') .'</a></h3> <div>';
    #exclude categories
    echo king_text_p(array(
      'name'  => $this->get_field_name('exclude'),
      'id'    => $this->get_field_id('exclude'),
      'descr' => __('Exclude Categories (1,2,3)', 'widgetKing'),
      'title' => __('Comma separated list of numeric IDs to be excluded from the list. E.g: 10, 20, 30', 'widgetKing'),
      'val'   => $opts['exclude']));

    #show child_of
    echo king_text_p(array(
      'name'  => $this->get_field_name('child_of'),
      'id'    => $this->get_field_id('child_of'),
      'descr' =>__('Show Children of Category', 'widgetKing'),
      'title' =>__('Show only children of this category(id).', 'widgetKing'),
      'val'   => $opts['child_of'] ));
    #show cat depth
    echo king_text_p(array(
      'name'  => $this->get_field_name('depth'),
      'id'    => $this->get_field_id('depth'),
      'descr' => __('Category tree depth', 'widgetKing'),
      'title' =>__('Descend to depth(number) into the category tree: 0 = All, -1 = All Flat(no indent), 1 = only top-level, n = number/levels to descend', 'widgetKing'),
      'val'   => $opts['depth'] ));
//    #insert feed text
    echo king_text_p(array(
      'name'  => $this->get_field_name('feed'),
      'id'    => $this->get_field_id('feed'),
      'descr' =>__('Show Category Feed Text', 'widgetKing'),
      'title' => __('Text to display for the link to each Categorys RSS2 feed. Default is no text, and no feed displayed.', 'widgetKing'),
      'val'   => $opts['feed']));
//    #name of feed image  Path/filename
    echo king_text_p(array(
      'name'  => $this->get_field_name('feed_image'),
      'id'    => $this->get_field_id('feed_image'),
      'descr' =>__('Show Category Feed Image', 'widgetKing'),
      'title' => __('URL Path/filename for a graphic to act as a link to each Categories RSS2 feed.Overrides the feed parameter.', 'widgetKing'),
      'val'   => $opts['feed_image']));
    #show show_last_update
    echo king_checkbox_p(array(
      'name'  => $this->get_field_name('show_last_update'),
      'id'    => $this->get_field_id('show_last_update'),
      'descr' =>__('Date of the last post', 'widgetKing'),
      'title' => __('Sets whether to display the date of the last post in each Category.', 'widgetKing'),
      'val'   => $opts['show_last_update'] ));
    #description as title
    echo king_checkbox_p(array(
      'name'  => $this->get_field_name('use_desc_for_title'),
      'id'    => $this->get_field_id('use_desc_for_title'),
      'descr' =>__('Use Description as Title','widgetKing'),
      'title' =>__('Sets whether to display the Category Description in the links title tag.', 'widgetKing'),
      'val'   => $opts['use_desc_for_title']));
        #list style
    echo king_checkbox_p(array(
        'name'  => $this->get_field_name('style'),
        'id'    => $this->get_field_id('style'),
        'descr' => __('Show as List (li)', 'widgetKing'),
        'title' => __('Sets whether the Categories are enclosed by style points ->li', 'widgetKing'),
        'val'   => $opts['style']));
    #show hirachical
    echo king_checkbox_p(array(
      'name'  => $this->get_field_name('hierarchical'),
      'id'    => $this->get_field_id('hierarchical'),
      'descr' => __('Show hierarchical', 'widgetKing'),
      'title' =>__('Shows Categories hierarchical with sub-categories indented -> Depending on your CSS', 'widgetKing'),
      'val'   => $opts['hierarchical']));

    echo '</div> <h3><a href="#">'. __('Show', 'widgetKing') .'</a></h3> <div>';
    # Where To Show Options Panel
    where_to_show_widget(
      $this,
      $opts['show_category'],
      $opts['cat_ids'],
      $opts['show_on_site_area'],
      $opts['show_not_on_site_area'],
      $opts['site_area'],
      $opts['site_area_id'] );

    echo '</div> <h3><a href="#">'. __('HTML', 'widgetKing') .'</a></h3> <div>';
    # show html options
    widget_king_htmloptions(
      $this, 
      stripslashes(htmlentities($opts['before_widget'])),
      stripslashes(htmlentities($opts['before_widget_title'])),
      stripslashes(htmlentities($opts['after_widget_title'])),
      stripslashes(htmlentities($opts['after_widget'])) 
    );
    echo '</div> <h3><a href="#">'. __('Import / Export', 'widgetKing') .'</a></h3> <div>';
    #import
    echo king_textarea_p(array(
      'name'  => $this->get_field_name('import'),
      'id'    => $this->get_field_id('import'),
      'descr' =>__('Import (JSON)', 'widgetKing'),
      'title' => __('A valid JSON string comming from another category widget', 'widgetKing'),
      'val'   => ''));

    echo king_textarea_p(array(
      'name'  => $this->get_field_name('export'),
      'id'    => $this->get_field_id('export'),
      'descr' =>__('Export(JSON)', 'widgetKing'),
      'title' => __('Copy this json string into another category widget to copy its settings', 'widgetKing'),
      'val'   => king_export_json($opts) ) );
    echo '</div>';

  }#form


  /**
  * @desc the actual output of the category menu
  * @param array $data - widget options
  */
  function output($data) {
    echo '<!-- Start King Cat ' .$this->id_base . ' -->'."\n";
    echo $data['before_widget']."\n";
    echo $data['before_widget_title']."\n";
    echo $data['title'] ."\n";
    echo $data['after_widget_title']."\n";

    $args =  array(
      'title_li' => '',
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
      'title_li' =>'',
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
      'after_widget'  => addslashes("</ul></li>"),
      'before_widget_title' => "<h3 class='widget-title'>",
      'after_widget_title'  => addslashes("</h3><ul>"),
      //show options
      'show_category'  => '',
      'cat_ids'   => '',
      'show_on_site_area' => '',
      'show_not_on_site_area'  =>  '',
      'site_area_id'  => '',
      'site_area'    => ''
//  TODO if needed  WP_LISTCATS OPTIONS
//    'show_option_all' => '',
//    'show_option_none' => __('No categories'),
//    'exclude_tree' => '',
//    'current_category' => 0,
//    'title_li' => __( 'Categories' ),
//    'echo' => 1,
//    'taxonomy' => 'category'
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