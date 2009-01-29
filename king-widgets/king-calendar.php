<?php
/*
Plugin Name: King_Calendar_Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-calendar/
Description: Calendar and Event calendar 3.0 widget -> only usefull if events plugin is installed
Author: Georg Leciejewski
Version: 0.62
Author URI: http://www.blog.mediaprojekte.de
*/
define("KINGCALENDARVERSION",  "062");
/*  Copyright 2006  georg leciejewski  (email : georg@mediaprojekte.de)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
* @desc init Put functions into one big function we'll call at the plugins_loaded action.
* This ensures that all required plugin functions are defined.
* @author Georg Leciejewski
*/
function widget_king_calendar_init() {

	# Check for the required plugin functions. This will prevent fatal
	# errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the list_cats function call
	* @author Georg Leciejewski
	*/
	function widget_king_calendar($args, $number = 1) {

		# $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.
			extract($args,EXTR_PREFIX_ALL,"default");
			$options 			= get_option('widget_king_calendar');
			$title 				= $options[$number]['title'];
			$use_events			= $options[$number]['use_events'] ? 1 : 0;
			$show_category		= $options[$number]['show_category'] ? 1 : 0;
			$category_id		= $options[$number]['category_id'];
			$show_on_site_area	= $options[$number]['show_on_site_area'] ? 1 : 0;
			$site_area_id		= $options[$number]['site_area_id'];
			$site_area			= $options[$number]['site_area'];

			$before_widget		= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
			$before_widget_title= empty($options[$number]['before_widget_title']) ? $default_before_title : stripslashes($options[$number]['before_widget_title']);
			$after_widget_title = empty($options[$number]['after_widget_title'] ) ? $default_after_title : stripslashes($options[$number]['after_widget_title']) ;
			$after_widget 		= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;


	# These lines generate our output.
		if( !empty($use_events) )
		{
		#if events calendar mode
			if( !empty($category_id) )
			{
				#if specific category is selected
				$post = $wp_query->post;
				if ( in_category($category_id) )
				{
					echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title ."\n";
					echo $after_widget_title."\n";
					ec3_get_calendar();
					echo $after_widget."\n";

				} #else{}

            }elseif(!empty($show_on_site_area)){
            	#if sitearea is selected
				if ( $site_area($site_area_id) )
				{
                  	echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title ."\n";
					echo $after_widget_title."\n";
					ec3_get_calendar();
					echo $after_widget."\n";

				} #else{}

			}else{
				#no category id selected
                echo $before_widget."\n";
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				ec3_get_calendar();
				echo $after_widget."\n";
		}


		}else{
		#if normal calendar Mode
			if( !empty($category_id) )
			{
				#if specific category is selected
				$post = $wp_query->post;
				if ( in_category($category_id) )
				{
					echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title;
					echo $after_widget_title."\n";
					get_calendar();
					echo $after_widget."\n";

				} #else{}

            }elseif(!empty($show_on_site_area)){
            	#if sitearea is selected
				if ( $site_area($site_area_id) )
				{
                    echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title;
					echo $after_widget_title."\n";
					get_calendar();
					echo $after_widget."\n";
				} #else{}

			}else{
			#no category id selected

					echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title;
					echo $after_widget_title."\n";
					get_calendar();
					echo $after_widget."\n";

			}
		}#endif normal calendar or events

	}#end function widget_king_calendar

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_calendar_control($number=1) {


		# Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_calendar');

		if ( $_POST["king_calendar_submit_$number"] )
		{
			#if defaults are choosen
			if ( isset($_POST["king_calendar_defaults_$number"]) )
			{
				$newoptions[$number]['title']			= "Calendar";
				/*
				$newoptions[$number]['before_widget']		= "<ul>";
				$newoptions[$number]['after_widget']		= addslashes("</li></ul>");
				$newoptions[$number]['before_widget_title'] = "<h2>";
				$newoptions[$number]['after_widget_title']	= addslashes("</h2><li>");
				*/
			}else{# insert new form values

				$newoptions[$number]['title']				= strip_tags(stripslashes($_POST["king_calendar_title_$number"]));
				$newoptions[$number]['show_category']		= isset($_POST["king_calendar_showcategory_$number"]);
				$newoptions[$number]['use_events']			= isset($_POST["king_calendar_use_events_$number"]);
				$newoptions[$number]['category_id']			= $_POST["king_calendar_category_id_$number"];

				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_calendar_show_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_calendar_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_calendar_site_area_id_$number"];

				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_calendar_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_calendar_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_calendar_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_calendar_widget_title_$number"]);

			}
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_calendar', $options);
		}

		$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);

		$show_category		= $options[$number]['show_category'] ? 'checked' : '';
		$use_events			= $options[$number]['use_events'] ? 'checked' : '';
		$category_id		= $options[$number]['category_id'];
        $show_on_site_area	= $options[$number]['show_on_site_area'] ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));
		$before_widget_title= stripslashes(htmlentities($options[$number]['before_widget_title']));
		$after_widget_title = stripslashes(htmlentities($options[$number]['after_widget_title']));

	# Here is the form segment. Notice that I have outsourced the form elements to be a little cleaner
		echo king_get_tab_start('calendar'.$number, array(
								__('Basic Features', 'widgetKing'),
								__('Show', 'widgetKing'),
								__('HTML', 'widgetKing'),
								));
		# show title
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"king_calendar_title_$number",
				'Description' 	=> __('Title', 'widgetKing'),
				'Label_Title' 	=> __('The title above your Calendar', 'widgetKing'),
				'Value' 			=> $title,
				'Size' 			=>'20',
				'Max' 			=>'50'));

		#show use events instead of normal
		echo king_get_checkbox_p(array(
				'Label_Id_Name' 	=>"king_calendar_use_events_$number",
				'Description' 	=> __('Use Events instead of normal Calendar', 'widgetKing'),
				'Label_Title' 	=>  __('Check this box if you have the eventsCalendar Plugin installed. There can only be one Events Calendar on a Page. Dont forget to set your Events Calendar options', 'widgetKing'),
				'Value' 			=>$use_events));
		# devider
		echo king_get_tab_section('calendar'.$number.'-1');
		# show only in category
		widget_king_where_to_show('calendar',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);


		#devider
		echo king_get_tab_section('calendar'.$number.'-2');

		widget_king_htmloptions('calendar',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);

		echo king_get_hidden("king_calendar_submit_$number",'1',"king_calendar_submit_$number");
		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
*/
	function widget_king_calendar_setup()
	{
		$options = $newoptions = get_option('widget_king_calendar');

		if ( isset($_POST['king_calendar_number_submit']) )
		{
			$number = (int) $_POST['king_calendar_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_calendar', $options);
			widget_king_calendar_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of calendars
	* @author Georg Leciejewski
	*/
	function widget_king_calendar_page()
	{

		$options = $newoptions = get_option('widget_king_calendar');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King Calendar', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Calendars would you like?', 'widgetKing');
		echo king_get_select("king_calendar_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9'), 'king_calendar_number' );
		echo king_get_submit('king_calendar_number_submit','','king_calendar_number_submit');
		echo king_get_end_p();
		echo '<p>';
		_e('If using Multiple Calendars show only one on a site by using the Category Option. This makes sence if you have Events installed  and want to show the Events Calendar on a special Category.', 'widgetKing');
		echo king_get_end_p();
		echo king_get_end_form ();

	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_calendar_register()
	{
		include_once('widgets.php');
		$options = get_option('widget_king_calendar');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++)
		{
			$name = array('King Calendar %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_calendar' :  '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_calendar_control' :  '', 450, 400, $i);
		}
		add_action('sidebar_admin_setup', 'widget_king_calendar_setup');
		add_action('sidebar_admin_page', 'widget_king_calendar_page');


	}
widget_king_calendar_register();
#register_sidebar_widget('Events', 'widget_king_calendar');
#register_widget_control('Events', 'widget_king_calendar_control', 450, 350);
include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');

}# end init function
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');

add_action('plugins_loaded', 'widget_king_calendar_init');

/**
* @desc Version Check Heading
*/
function widget_king_calendar_version() {
	king_version_head('King_Calendar_Widget',KINGCALENDARVERSION);
}
add_action('admin_head','widget_king_calendar_version');

?>
