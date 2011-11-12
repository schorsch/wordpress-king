<?php
/*
Plugin Name: King Pages Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-pages/
Description: Adds a sidebar Pages widget and lets users configure every aspect of the page menu.
Author: Georg Leciejewski
Version: 2.0
Author URI: http://www.salesking.eu
*/

/*  Copyright 2006-2012  Georg Leciejewski

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
include_once (ABSPATH . 'wp-content/plugins/king-framework/lib/class-widget-form.php');

/**
 * Pages menu widget
 *
 * @since 3.0.0
 */
class WP_Widget_King_Pages extends WP_Widget {

  function WP_Widget_King_Pages() {
    $widget_ops = array( 'classname' => 'widget_king_pages',
                         'description' => __( "Your Page menus on dope - define where to show, set widget html, import/export" ) );
    $this->WP_Widget('king_pages', __('KingPages'), $widget_ops);
  }

  /**
   * Output of the widget
   * @param <type> $args is an array of strings that help widgets to conform to
   *  the active theme: before_widget, before_title, after_widget,
   *  and after_title are the array keys. Default tags: li and h2.
   * @param <type> $opts
   */
  function widget( $args, $opts ) {
    global $wp_query;
    WidgetForm::do_show($this, $args, $opts);
  }

  /**
  * Output the widgets settings form
  */
  function form( $opts ) {
    //get default settings
    $opts = wp_parse_args( (array) $opts, $this->defaults() );
    $f = new WidgetForm();
    echo '<div>';
    # show title
    echo $f->text (array(
        'name'  => $this->get_field_name('title'),
        'id'    => $this->get_field_id('title'),
        'descr' => __('Title', 'widgetKing'),
        'title' => __('The title above this widget', 'widgetKing'),
        'val'   => esc_html($opts['title']) ));

    #sort Column
    echo '<p>';
    echo $f->label_tag(  $this->get_field_id('orderby'), __('Sort by', 'widgetKing'),
                      __('Sort the choosen column ASCending or DESCending.', 'widgetKing') );
    echo '<br/>';
    echo $f->select_tag( $this->get_field_name('orderby'), $opts['orderby'],
                      array('post_title','menu_order','post_date', 'post_modified','ID','post_author','post_name'),
                      $this->get_field_id('orderby') );

    echo $f->select_tag( $this->get_field_name('order'), $opts['order'],
                      array('asc', 'desc'), $this->get_field_id('order') );
    echo '</p>';

    # devider
    echo '</div> <h3><a href="#">'. __('Advanced', 'widgetKing') .'</a></h3> <div>';
    #exclude
    echo $f->text(array(
      'name'  => $this->get_field_name('exclude'),
      'id'    => $this->get_field_id('exclude'),
      'descr' => __('Exlude Pages with IDs (1,2,3)', 'widgetKing'),
      'title' => __('Comma separated list of numeric IDs to be excluded from the list. E.g: 10, 20, 30', 'widgetKing'),
      'val'   => $opts['exclude']));

    #show child_of
    echo $f->text(array(
      'name'  => $this->get_field_name('child_of'),
      'id'    => $this->get_field_id('child_of'),
      'descr' =>__('Show children of page', 'widgetKing'),
      'title' =>__('Show only subpages of the given page id.', 'widgetKing'),
      'val'   => $opts['child_of'] ));

    #show depth
    echo $f->text(array(
      'name'  => $this->get_field_name('depth'),
      'id'    => $this->get_field_id('depth'),
      'descr' => __('Page tree depth', 'widgetKing'),
      'title' =>__('Numeric value for how many levels of hierarchy (sub-pages) to display. Defaults to 0 - display all pages', 'widgetKing'),
      'val'   => $opts['depth'] ));
/*
		#show_date
    echo king_select_p(array(
        'name'  => $this->get_field_name('show_date'),
        'id'    => $this->get_field_id('show_date'),
        'descr' => __('Show Date', 'widgetKing'),
        'title' =>  __('Display creation or last modified date next to each Page. if Empty -> Display no date. modified -> Display the date last modified. post_date -> Date Page was first created.', 'widgetKing'),
        'options'=> array('', 'modified','post_date'),
        'val'   => $opts['show_date'] ));

		#date_format
    echo king_text_p(array(
      'name'  => $this->get_field_name('date_format'),
      'id'    => $this->get_field_id('date_format'),
      'descr' => __('Date format', 'widgetKing'),
      'title' => __('Defaults to the date format configured in your WordPress options. Custom values need to be in php Time Format. You can google on that..can you?', 'widgetKing'),
      'val'   => $opts['date_format']));
*/
    echo '</div> <h3><a href="#">'. __('Show', 'widgetKing') .'</a></h3> <div>';
    # Where To Show Options Panel
    $f->where_to_show($this, $opts );
    echo '</div> <h3><a href="#">'. __('HTML', 'widgetKing') .'</a></h3> <div>';
    # show html options
    $f->html_opts($this, $opts );
    echo '</div> <h3><a href="#">'. __('Import / Export', 'widgetKing') .'</a></h3> <div>';
    #import
    $f->export_opts($this, $opts);
    echo '</div>';

  }#form

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
    # use setting from json import if availsable
    $new_opts = !empty($new_opts["import"]) ? king_import_json($new_opts["import"]) : $new_opts;
    # save new form values
    $opts['orderby']            = $new_opts["orderby"];
    $opts['order']              = $new_opts["order"];
    $opts['depth']              = $new_opts["depth"];
    $opts['exclude']            = stripslashes($new_opts["exclude"]);
    $opts['show_date']          = $new_opts["show_date"];
    $opts['show_date']          = $new_opts["date_format"];
    $opts['child_of']           = $new_opts["child_of"];

    WidgetForm::clean_default_opts($opts, $new_opts);

    return $opts;
  }

  /**
  * Default options for the widget
  *
  */
  function defaults() {
    return array(
      'title_li' =>'',
      'orderby' =>'post_title',
      'order' =>'ASC',
      'show_date' => '',
      'date_format' => '',
      'child_of' => 0,
      'exclude' => '',
      'depth' => 0,
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
      );
  }
  /**
  * @desc the actual output of the widget
  * @param array $data - widget options
  */
  function output($data) {
    echo '<!-- Start King Pages ' .$this->id_base . ' -->'."\n";
    echo $data['before_widget']."\n";
    echo $data['before_widget_title']."\n";
    echo $data['title'] ."\n";
    echo $data['after_widget_title']."\n";

    $args = array(
      'child_of'		=> $data['child_of'],
      'sort_column' => $data['order'],
      'sort_order' 	=> $data['orderby'],
      'exclude' 		=> $data['exclude'],
      'depth'       => $data['depth'],
      'show_date' 	=> $data['show_date'],
      'date_format' => $data['date_format'],
      'title_li' 		=> ''
        );
    wp_list_pages($args);
    echo $data['after_widget']."\n";
    echo '<!-- End Pages ' . $this->id_base . ' -->'."\n";
    return;
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
function king_pg_widget_init() {
  if ( !is_blog_installed() )
    return;
  register_widget('WP_Widget_King_Pages');
  do_action('widgets_init');
}

add_action('init', 'king_pg_widget_init', 1);

?>