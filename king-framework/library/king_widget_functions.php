<?php
/*
King Widgets common used functions and includes. Mostly for the Widgets Admin and Options Area.
Author: Georg Leciejewski
Version: 0.70
Author URI: http://www.blog.mediaprojekte.de
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

require_once( ABSPATH . WPINC . '/class-json.php' );

/**
* @desc Output of king widget css and js in admin head area. Is included in every king widget
* @author Georg Leciejewski         
*/
 
function widget_admin_head() {
  #$plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );  
  $js_dir = get_settings('siteurl').'/wp-content/plugins/king-framework/js/';
  $css_dir = get_settings('siteurl').'/wp-content/plugins/king-framework/css/';    
  //add the javascript to widgets admin page                           
  wp_enqueue_script('king_widget_script', $js_dir.'king_widget.js', array('jquery', 'jquery-ui-tabs'));
  //add widget css containing tab styles
  echo '<link rel="stylesheet" href="'.$css_dir.'/king_widget.css" type="text/css" />';     
  // the translations
  load_plugin_textdomain('widgetKing','/wp-content/plugins/king-framework/lang');

}
add_action( "admin_print_scripts-widgets.php", 'widget_admin_head' );

/**
* @desc Read Plugin Options from String back into Array
* @author Georg Leciejewski
* @param string $input 	- String from textarea with new options
* @return array for the update_option call .. its not beeing validated
*/
function king_import_json($input) {
   $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
   return $json->decode($input);
}
/**
* @desc Output Plugin Options as json
* @author Georg Leciejewski
* @param array $args 	- array with VALID widget options
* @return string json
*/
function king_export_json($args) {  
   $json = new Services_JSON();
   return $json->encodeUnsafe($args);
}

/**
* @desc check in which area we are to show widget or not
* @param string $site_area - WP definition of siteareas like is_page, is_home
* @param string $site_area_id - can be a post slug / post id
* @return boolean true/false
*/
function king_in_site_area($site_area,$site_area_id)
{
	$site_area_ids = explode(',', $site_area_id);
	foreach ($site_area_ids as $key => $val)
	{
		if ( $site_area($val) )
		{
			return true;
		}
	}
}

/**
* @desc check in which category we are
* @param string $category - WP definition of siteareas like is_page, is_home
* @return boolean true/false
*/
function king_in_category($categories) {
	global $category_cache, $post;
	$cat_ids = explode(',', $categories);
	foreach($cat_ids as $cat_id){
    if (in_category($cat_id) ){
			return true;
		}
	}
}

/**
*@desc prints out array in a readable way
*/
function debug($var = false, $showHtml = false)
{

		print "\n<pre class=\"debug\">\n";
		ob_start();
		print_r($var);
		$var = ob_get_clean();

		if ($showHtml) {
			$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
		}
		print "{$var}\n</pre>\n";

}
?>
