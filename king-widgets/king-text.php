<?php
/*
Plugin Name: King Text Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-text/
Description: Adds a Text widget Options are: in which category or Site Area to show + php/Html output + the html before and after the Widget.
Author: Georg Leciejewski
Version: 0.72
Author URI: http://www.blog.mediaprojekte.de
*/

/*  Copyright 2006 - 2012  georg leciejewski
License:
NOT OPEN SOURCE
This Software is free for private, charity use.
Further you can use it free if you are developing Wordpress Plugins and have those available on your site.
If your are using this in any commercial enviroment please contact me for the realy little licence fee.. I promise.
I?m also available for further support and development in such a case!
You are not allowed to redistribute this code without my permission.
The license may change in the future without prior notice.
Georg Leciejewski
georg@mediaprojekte.de
*/
define("KINGTEXTVERSION","200");

include_once (ABSPATH . 'wp-content/plugins/king-framework/lib/class-widget-form.php');
require_once(ABSPATH . 'wp-content/plugins/king-framework/lib/king_widget_functions.php');


/**
 * Categories widget class
 *
 * @since 3.0.0
 */
class WP_Widget_King_Text extends WP_Widget {

  function WP_Widget_King_Text() {
    $widget_ops = array( 'classname' => 'widget_king_text', 'description' => __( "Better text widget with settings for php, where to show, widget html, import/export." ) );
    $this->WP_Widget('king_text', __('KingText'), $widget_ops);
  }

  /**
  * Output of the widget
  * @param <Array> $args is an array of strings that help widgets to conform to
  *  the active theme: before_widget, before_title, after_widget,
  *  and after_title are the array keys. Default tags: li and h2.
  * @param <Array> $opts
  */
  function widget( $args, $opts ) {
    global $wp_query;
    extract( $args );
    $title = apply_filters('widget_title', empty( $opts['title'] ) ? '' : $opts['title'], $opts, $this->id_base);

    //take care of some escaped fields
    $opts['before_widget']      = empty($opts['before_widget']) ? $before_widget : stripslashes($opts['before_widget']);
    $opts['before_widget_title']= empty($opts['before_widget_title']) ? $before_title : stripslashes($opts['before_widget_title']);
    $opts['after_widget_title'] = empty($opts['after_widget_title'] ) ? $after_title : stripslashes($opts['after_widget_title']) ;
    $opts['after_widget']       = empty($opts['after_widget']) ? $after_widget : stripslashes($opts['after_widget']) ;

    $already_out = false;

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

		echo $f->checkbox(array(
				'name'  => $this->get_field_name('use_php'),
				'id'    => $this->get_field_id('use_php'),
				'descr' => __('Use PHP in Text', 'widgetKing'),
				'title' => __('If checked the inserted code is evaluated as php. PHP Code MUST be enclosed in &lt;?php and ?&gt; tags! You can also insert Wordpress Code if you have not found a Widget for it yet.', 'widgetKing'),
				'val'  =>  $opts['use_php']) );
//    if ( !current_user_can('unfiltered_html') )
//					$newoptions[$number]['text'] = stripslashes(wp_filter_post_kses($newoptions[$number]['text']));
		echo $f->textarea(array(
				'name'  => $this->get_field_name('text'),
				'id'    => $this->get_field_id('text'),
				'descr' =>  __('Text or HTML', 'widgetKing'),
				'title' => __('Insert your Text Freely. This can be bannercode, images or whatever you like. The HTML gets stripped if you do not have the right to insert unfiltered html.', 'widgetKing'),
				'val' 	=> $opts['text']
				));

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
    $use_php = $data['use_php'] ? 1 : 0;

		$textparts = explode('<!--more-->', $data['text']);
		$partno = mt_rand(0, sizeof($textparts) - 1);

    if( !empty($use_php) ) {
      eval('?>'.$textparts[$partno]);
    }else {
      echo $textparts[$partno];
    }

    echo $data['after_widget']."\n";
    echo '<!-- End Pages ' . $this->id_base . ' -->'."\n";
    return;
  }

    /**
  * Default options for the widget
  *
  */
  function defaults() {
    return array(
      'use_php' => '',
      //widget options
      'title' => '',
      'text' => '',
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
      'site_area'    => '' );
  }
}

function king_text_widget_init() {
  if ( !is_blog_installed() )
    return;
  register_widget('WP_Widget_King_Text');
  do_action('widgets_init');
}

add_action('init', 'king_text_widget_init', 1);

?>
