<?php
/*
Plugin Name: King_Framework
Plugin URI: http://www.blog.mediaprojekte.de/
Description: You dont need to activate this Plugin since it contains common Functions, Language, Javascripts used by all King Widgets + King Plugins.
Author: Georg Leciejewski
Version: 0.73
Author URI: http://www.blog.mediaprojekte.de
*/
/*  Copyright 2006  georg leciejewski  (email : georg@mediaprojekte.de)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
define("KINGFRAMEWORKVERSION", "073");
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');

/**
* @desc Admin Menu
* @author georg leciejewski
*/
function king_framework_admin_menu()
{
  if(function_exists('add_options_page'))
  {//($page_title, $menu_title, $access_level, $file, $function = '')
    add_options_page('King Plugin Options','King Plugin Options', 'manage_options', basename(__FILE__),'king_framework_options' );
  }

	$king_options = get_option('king_framework');

	#$GLOBALS['freakmode'] = $king_options['freakmode'];
}
add_action('admin_menu', 'king_framework_admin_menu');

/**
* @desc Admin Options Page
* @author georg leciejewski
*/
function king_framework_options()
{

	$options = $newoptions = get_option('king_framework');
	if ( $_POST["king_framework_submit"] )
	{
			//if defaults are choosen
		if ( isset($_POST["king_framework_defaults"]) )
		{
			//no defaults atm
		}
		else
		{// insert new form values

			$newoptions['widgets_number']	= (int)$_POST["king_framework_widgets_number"];
			$newoptions['freakmode']		= $_POST["king_framework_freakmode"];
		}
	}
	if ( $options != $newoptions )
	{
		$options = $newoptions;
		update_option('king_framework', $options);
	}


	$widgets_number = $options['widgets_number'];
	$freakmode = $options['freakmode'];

	#echo king_get_start_form('wrap','','','post');

	echo '<div class="wrap"><h2>'. __('King Framework', 'widgetKing') .'</h2>' ;
		_e('These Options are global for all King Widgets(if they support the functions). There will be more Options in the future.','widgetKing');
	echo ' <p> <a target="_blank" href="../wp-content/plugins/king-framework/changelog.txt">'.__('Check out the Changelog File','widgetKing').'</a></p> ';
	echo '</div>'; 
}

/**
* @desc Version Check Heading
*/
function king_framework_version() {
	king_version_head('King_Framework',KINGFRAMEWORKVERSION);
}
add_action('admin_head','king_framework_version');

add_action( "admin_print_scripts-widgets.php", 'my_admin_scripts' );
 
function my_admin_scripts() {
  $plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );      
  //add the javascript to widgets admin page                           
  wp_enqueue_script('king_widget_script', $plugin_url.'/js/king_widget.js', array('jquery'));
  //add widget css containing tab styles
  echo '<link rel="stylesheet" href="'.$plugin_url.'/css/king_widget.css" type="text/css" />';       
}

?>
