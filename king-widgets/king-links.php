<?php
/*

Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-links/
Description: Up to 10 sidebar Link widgets and lets you configure every Aspect of the Link list.
Author: Georg Leciejewski
Version: 0.65
Author URI: http://www.blog.mediaprojekte.de
*/
define("KINGLINKSVERSION",  "065");
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
function widget_king_links_init() {

	# Check for the required plugin functions. This will prevent fatal
	# errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the get_links function call
	* @author Georg Leciejewski
	*/
	function widget_king_links($args, $number = 1) {
		
		# $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.

			extract($args,EXTR_PREFIX_ALL,"default");
			$options 			= get_option('widget_king_links');
			$title 				= $options[$number]['title'];
			$category			= empty($options[$number]['category']) ? -1 : $options[$number]['category'];
			$show_category		= $options[$number]['show_category'] ? 1 : 0;
			$category_id		= $options[$number]['category_id'];
			$show_on_site_area	= $options[$number]['show_on_site_area'] ? 1 : 0;
			$site_area_id		= $options[$number]['site_area_id'];
			$site_area			= $options[$number]['site_area'];
			$before				= stripslashes($options[$number]['before']);
			$after				= stripslashes($options[$number]['after']);
			$between			= stripslashes($options[$number]['between']);
			$show_images		= $options[$number]['show_images'] ? 1 : 0;
			$orderby 			= $options[$number]['orderby'];
			$show_description	= $options[$number]['show_description'] ? 1 : 0;
			$show_rating		= $options[$number]['show_rating'] ? 1 : 0;
			$limit				= empty($options[$number]['limit']) ? -1 : $options[$number]['limit'];
			$show_updated		= $options[$number]['show_updated'] ? 1 : 0;
			$before_widget		= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
			$before_widget_title= empty($options[$number]['before_widget_title']) ? $default_before_title : stripslashes($options[$number]['before_widget_title']);
			$after_widget_title = empty($options[$number]['after_widget_title'] ) ? $default_after_title : stripslashes($options[$number]['after_widget_title']) ;
			$after_widget		= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;


		# These lines generate our output. Widgets can be very complex
		# but as you can see here, they can also be very, very simple.


			if( !empty($show_category) ) # show only on specific category
			{
				$post = $wp_query->post;
				if ( in_category($category_id) )
				{
					if (!empty($options[$number]['use_wpgetlinks']))
					{
						echo $before_widget."\n";
						echo $before_widget_title."\n";
						echo $title ."\n";
						echo $after_widget_title."\n";
						wp_get_links($category);
						echo $after_widget."\n";
					}
					else
					{
						echo $before_widget."\n";
						echo $before_widget_title."\n";
						echo $title ."\n";
						echo $after_widget_title."\n";
						get_links(
							$category,
							$before,
							$after,
							$between,
							$show_images,
							$orderby,
							$show_description,
							$show_rating,
							$limit,
							$show_updated,
							$echo=1);
						echo $after_widget."\n";
					}
				}
			}
			elseif(!empty($show_on_site_area))
			{ # show only on specific site area

				if ( $site_area($site_area_id) )
				{

					if (!empty($options[$number]['use_wpgetlinks'])){
						echo $before_widget."\n";
						echo $before_widget_title."\n";
						echo $title ."\n";
						echo $after_widget_title."\n";
						wp_get_links($category);
						echo $after_widget."\n";
					}
					else
					{
						echo $before_widget."\n";
						echo $before_widget_title."\n";
						echo $title ."\n";
						echo $after_widget_title."\n";
						get_links(
							$category,
							$before,
							$after,
							$between,
							$show_images,
							$orderby,
							$show_description,
							$show_rating,
							$limit,
							$show_updated,
							$echo=1);
						echo $after_widget."\n";
					}
				}
			}
			else
			{ # always show
				if (!empty($options[$number]['use_wpgetlinks'])){
					echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title ."\n";
					echo $after_widget_title."\n";
					wp_get_links($category);
					echo $after_widget."\n";
				}
				else
				{
					echo $before_widget."\n";
					echo $before_widget_title."\n";
					echo $title ."\n";
					echo $after_widget_title."\n";
					get_links(
						$category,
						$before,
						$after,
						$between,
						$show_images,
						$orderby,
						$show_description,
						$show_rating,
						$limit,
						$show_updated,
						$echo=1);
						echo $after_widget."\n";
				}
			} #end else if site area


		if (!empty($options[$number]['debug']))
		{
			echo '<h2>'.__('Your Link Options are:', 'widgetKing').'</h2>';
			echo "get_links($category,$before,$after,$between,$show_images,$orderby,$show_description,$show_rating,$limit,$show_updated,$echo)";
		}

	} # end function widget_king_links

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_links_control($number)
	{

		# Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_links');

		if ( $_POST["king_links_submit_$number"] )
		{
			if ( isset($_POST["king_links_defaults_$number"]) )
			{ #if defaults are choosen
				$newoptions[$number]['title']			= "Links $number";
				$newoptions[$number]['category']		= -1;
				$newoptions[$number]['before']			= '';
				$newoptions[$number]['after']			= '<br />';
				$newoptions[$number]['between']			= '<br />';
				$newoptions[$number]['show_images']		= 1;
				$newoptions[$number]['orderby']			= 'name';
				$newoptions[$number]['show_description']= 1;
				$newoptions[$number]['show_rating']		= 0;
				$newoptions[$number]['limit']			= -1;

            }
            elseif( !empty($_POST["king_links_dump_$number"]) && isset($_POST["king_links_usedump_$number"]))
			{
				$newoptions[$number] = king_read_options($_POST["king_links_dump_$number"]);
			}
            elseif($_POST["king_links_copy_$number"] !=='No' && $_POST["king_links_copy_$number"] != $number)
            {# insert choosen values
                $copy = $_POST["king_links_copy_$number"];
				$newoptions[$number]['title']				= $options[$copy]['title'];
				$newoptions[$number]['category']			= $options[$copy]['category'];
				$newoptions[$number]['before']				= $options[$copy]['before'];
				$newoptions[$number]['after']				= $options[$copy]['after'];
				$newoptions[$number]['between']				= $options[$copy]['between'];
				$newoptions[$number]['show_images']			= $options[$copy]['show_images'];
				$newoptions[$number]['orderby']				= $options[$copy]['orderby'];
				$newoptions[$number]['show_description']	= $options[$copy]['show_description'];
				$newoptions[$number]['show_rating']			= $options[$copy]['show_rating'];
				$newoptions[$number]['limit']				= $options[$copy]['limit'];
				$newoptions[$number]['show_category']		= $options[$copy]['show_category'];
				$newoptions[$number]['category_id']			= $options[$copy]['category_id'];
				$newoptions[$number]['show_on_site_area']	= $options[$copy]['show_on_site_area'];
				$newoptions[$number]['site_area']			= $options[$copy]['site_area'];
				$newoptions[$number]['site_area_id']		= $options[$copy]['site_area_id'];
				$newoptions[$number]['debug']				= $options[$copy]['debug'];
				$newoptions[$number]['before_widget']		= $options[$copy]['before_widget'];
				$newoptions[$number]['after_widget']		= $options[$copy]['after_widget'];
				$newoptions[$number]['before_widget_title']	= $options[$copy]['before_widget_title'];
				$newoptions[$number]['after_widget_title']	= $options[$copy]['after_widget_title'];
				$newoptions[$number]['use_wpgetlinks']		= $options[$copy]['use_wpgetlinks'];

			}
			else
			{# insert choosen values
				$newoptions[$number]['title']				= strip_tags(stripslashes($_POST["king_links_title_$number"]));
				$newoptions[$number]['category']			= html_entity_decode($_POST["king_links_category_$number"]);
				$newoptions[$number]['before']				= html_entity_decode($_POST["king_links_before_$number"]);
				$newoptions[$number]['after']				= html_entity_decode($_POST["king_links_after_$number"]);
				$newoptions[$number]['between']				= html_entity_decode($_POST["king_links_between_$number"]);
				$newoptions[$number]['show_images']			= isset($_POST["king_links_show_images_$number"]);
				$newoptions[$number]['orderby']				= $_POST["king_links_orderby_$number"];
				$newoptions[$number]['show_description']	= isset($_POST["king_links_show_description_$number"]);
				$newoptions[$number]['show_rating']			= isset($_POST["king_links_show_rating_$number"]);
				$newoptions[$number]['limit']				= $_POST["king_links_limit_$number"];
				$newoptions[$number]['show_category']		= isset($_POST["king_links_showcategory_$number"]);
				$newoptions[$number]['category_id']			= $_POST["king_links_category_id_$number"];
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_links_show_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_links_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_links_site_area_id_$number"];
				$newoptions[$number]['debug']				= isset($_POST["king_links_debug_$number"]);
				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_links_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_links_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_links_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_links_widget_title_$number"]);
				$newoptions[$number]['use_wpgetlinks']		= isset($_POST["king_links_use_wpgetlinks_$number"]);
			}
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_links', $options);
		}
		$title				= wp_specialchars($options[$number]['title']);
		$category			= $options[$number]['category'];
		$before				= stripslashes(htmlentities($options[$number]['before']));
		$after				= stripslashes(htmlentities($options[$number]['after']));
		$between			= stripslashes(htmlentities($options[$number]['between']));
		$show_images		= $options[$number]['show_images'] ? 'checked' : '';
		$orderby 			= $options[$number]['orderby'];
		$show_description	= $options[$number]['show_description'] ? 'checked' : '';
		$show_rating		= $options[$number]['show_rating'] ? 'checked' : '';
		$limit				= $options[$number]['limit'];
		$show_category		= $options[$number]['show_category'] ? 'checked' : '';
		$category_id		= $options[$number]['category_id'];
		$show_on_site_area	= $options[$number]['show_on_site_area'] ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];
		$debug				= $options[$number]['debug'] ? 'checked' : '';
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));
		$before_widget_title= stripslashes(htmlentities($options[$number]['before_widget_title']));
		$after_widget_title = stripslashes(htmlentities($options[$number]['after_widget_title']));
		$use_wpgetlinks		= $options[$number]['use_wpgetlinks'] ? 'checked' : '';


		echo king_get_tab_start('links'.$number, array(
								__('Basic', 'widgetKing'),
								__('Advanced', 'widgetKing'),
								__('Show', 'widgetKing'),
								__('HTML', 'widgetKing'),
								__('Export', 'widgetKing'),
								));

		# show title
		echo king_get_textbox_p(array(
			'Label_Id_Name' => "king_links_title_$number",
			'Description' 	=> __('Title', 'widgetKing'),
			'Label_Title' 	=> __('The title above your category menu', 'widgetKing'),
			'Value' 		=> $title,
			'Max' 			=> '50'));
		#show category only ID
		echo king_get_textbox_p(array(
			'Label_Id_Name' => "king_links_category_$number",
			'Description' 	=> __('Link Category ID', 'widgetKing'),
			'Label_Title' 	=> __('Show only Links belonging to this category.Defaults to -1 (show all links)', 'widgetKing'),
			'Value' 		=> $category,
			'Max' 			=>'5',
			'Class' 		=>'small'
			));
        #Use wp_get_links
		echo king_get_checkbox_p(array(
			'Label_Id_Name' => "king_links_use_wpgetlinks_$number",
			'Description' 	=> __('Use Options from Linkmanager', 'widgetKing'),
			'Label_Title' 	=> __('When set you only need to provide the Link Category ID! It takes all the Links options from the Links Manager. Dont forget to set the widget HTML. Internally this uses the wp_get_links() Method', 'widgetKing'),
			'Value' 		=> $use_wpgetlinks));
		#Limit to show only xx Links
		echo king_get_textbox_p(array(
			'Label_Id_Name' 	=> "king_links_limit_$number",
			'Description' 	=> __('Limit Shown Links', 'widgetKing'),
			'Label_Title' 	=> __('Show only this Number of Links inside the box. -1 stands for show all. But you can also leave this field empty.', 'widgetKing'),
			'Value' 		=> $limit,
			'Max' 			=> '5',
			'Class' 		=>'small'));
		#show Images
		echo king_get_checkbox_p(array(
			'Label_Id_Name' => "king_links_show_images_$number",
			'Description' 	=> __('Show Link Images', 'widgetKing'),
			'Label_Title' 	=> __('If there are Images associated with a Link, show them', 'widgetKing'),
			'Value' 		=> $show_images));
		#sort Column
		echo king_get_select_p(array(
			'Label_Id_Name' => "king_links_orderby_$number",
			'Description' 	=> __('Sort by', 'widgetKing'),
			'Label_Title' 	=> __('The _ underscore means sorting in reverse order. owner->User who added link through Links Manager./ Rand-> Random / rel-> Link relationship (XFN) / length -> The length of the link name, shortest to longest.', 'widgetKing'),
			'select_options'=> array('id', 'name','url','target','category','description','owner','rating','updated','rel','notes','rss','length','rand','_id', '_name','_url','_target','_category','_description','_owner','_rating','_updated','_rel','_notes','_rss','_length'),
			'Value' 		=> $orderby ));
		#Show the Link Descriptions
		echo king_get_checkbox_p(array(
			'Label_Id_Name' => "king_links_show_description_$number",
			'Description' 	=> __('Link Description', 'widgetKing'),
			'Label_Title' 	=> __('Show the Link Descriptions', 'widgetKing'),
			'Value' 		=> $show_description));
		#Show the Link Rating
		echo king_get_checkbox_p(array(
			'Label_Id_Name' =>"king_links_show_rating_$number",
			'Description' 	=> __('Rating', 'widgetKing'),
			'Label_Title' 	=> __('Show the Link Rating', 'widgetKing'),
			'Value' 		=>$show_rating));
		# Devider
		echo king_get_tab_section('links'.$number.'-1');
		#before Link
		echo king_get_textbox_p(array(
			'Label_Id_Name' => "king_links_before_$number",
			'Description' 	=> __('Before Link', 'widgetKing'),
			'Label_Title' 	=> __('HTML bevor a Link in the List', 'widgetKing'),
			'Value' 		=> $before));
		#after Link
		echo king_get_textbox_p(array(
			'Label_Id_Name' => "king_links_after_$number",
			'Description' 	=> __('After Link', 'widgetKing'),
			'Label_Title' 	=> __('HTML after a Link in the List', 'widgetKing'),
			'Value' 		=> $after));
		#between Link
		echo king_get_textbox_p(array(
			'Label_Id_Name' => "king_links_between_$number",
			'Description' 	=> __('Between Link', 'widgetKing'),
			'Label_Title' 	=> __('Text to place between each link/image and its description. Defaults to a space', 'widgetKing'),
			'Value' 		=> $between));
		#set to defaults
		echo king_get_checkbox_p(array(
			'Label_Id_Name' =>"king_links_defaults_$number",
			'Description' 	=>  __('Insert default Options', 'widgetKing'),
			'Label_Title' 	=> __('Set Menu Options to Wordpress Defaults','widgetKing')
			));
        #copy
		echo king_get_select_p(array(
			'Label_Id_Name' => "king_links_copy_$number",
			'Description' 	=> __('Copy Settings from Widget No.', 'widgetKing'),
			'Label_Title' 	=> __('Choose a Widget Number from which you want to copy the settings into this one. Make sure to choose the right widget, with some Options in it!', 'widgetKing'),
			'select_options'=> array('No','1', '2', '3', '4', '5', '6', '7', '8', '9')));
        #devider
		echo king_get_tab_section('links'.$number.'-2');
        # Where To Show Options Panel
		widget_king_where_to_show('links',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);
		#devider
		echo king_get_tab_section('links'.$number.'-3');

		widget_king_htmloptions('links',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);

		#show debug output
		echo king_get_checkbox_p(array(
			'Label_Id_Name' =>"king_links_debug_$number",
			'Description' 	=> __('Show Debug Output', 'widgetKing'),
			'Label_Title' 	=>  __('Shows all set options in Frontend to check what you have entered.', 'widgetKing'),
			'Value' 		=>$debug));
		echo king_get_tab_section('links'.$number.'-4');
		king_get_dump_options('links',$number,'widget_king_links');
		echo king_get_hidden("king_links_submit_$number",'1',"king_links_submit_$number");
		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function widget_king_links_setup()
	{
		$options = $newoptions = get_option('widget_king_links');

		if ( isset($_POST['king_links_number_submit']) )
		{
			$number = (int) $_POST['king_links_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_links', $options);
			widget_king_links_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of categories
	* @author Georg Leciejewski
	*/
	function widget_king_links_page()
	{
		$options = $newoptions = get_option('widget_king_links');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King Link Menu', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Link Boxes would you like?', 'widgetKing');
		echo king_get_select("king_links_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9'), 'king_links_number' );
		echo king_get_submit('king_links_number_submit','','king_links_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_links_register()
	{
	  
		$options = get_option('widget_king_links');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++) {
			$name = array('King Links %s', null, $i);
	 		register_sidebar_widget($name, $i <= $number ? 'widget_king_links' :  '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_links_control' :  '', 450, 350, $i);
		}
		add_action('sidebar_admin_setup', 'widget_king_links_setup');
		add_action('sidebar_admin_page', 'widget_king_links_page');
	}
	include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');
	widget_king_links_register();
}# end init function
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');
add_action('plugins_loaded', 'widget_king_links_init');
/**
* @desc Version Check Heading
*/
function widget_king_links_version()
{
	king_version_head('King_Links_Widget',KINGLINKSVERSION);
}
add_action('admin_head','widget_king_links_version');
?>
