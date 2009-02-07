<?php
/*
Plugin Name: King_Pages_Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-pages/
Description: Adds a sidebar Pages widget and lets users configure every Aspect of the Pages Navigation list.
Author: Georg Leciejewski
Version: 0.55
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
define("KINGPAGESVERSION",  "055");
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');
/**
* @desc init Put functions into one big function we'll call at the plugins_loaded action.
* This ensures that all required plugin functions are defined.
* @author Georg Leciejewski
*/
function widget_king_pages_init() {

	# Check for the required plugin functions. This will prevent fatal
	# errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of widget composing the list_pages function call
	* 		this function is called for every pages widget
	* @param array $args - aditional arguments passed to the wp_list_pages filter by some other plugins
	* @param int $number - the widget identifier
	* @author Georg Leciejewski
	*/
	function widget_king_pages($args, $number = 1) {

		# $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.
		$data = array();
		extract($args,EXTR_PREFIX_ALL,"default");
		$options 			= get_option('widget_king_pages');
		$data['title'] 		= $options[$number]['title'];
		$data['child_of']	= $options[$number]['child_of'] ? $options[$number]['child_of'] : 0;
		$data['sort_column']= $options[$number]['sort_column'];
		$data['sort_order']	= $options[$number]['sort_order'];
		$data['exclude'] 	= $options[$number]['exclude'];
		$data['depth'] 		= $options[$number]['depth'];
		$data['show_date']	= $options[$number]['show_date'];
		$data['date_format']= $options[$number]['date_format'];
		$data['sort_order']	= $options[$number]['sort_order'];
		$data['title_li'] 	= $options[$number]['title_li'];
		$data['foldlist'] 	= $options[$number]['foldlist'];

        $data['show_category'] 			= $options[$number]['show_category'] ? 1 : 0;
		$data['category_id'] 			= $options[$number]['category_id'];
		$data['show_on_site_area'] 		= $options[$number]['show_on_site_area'] ? 1 : 0;
		$data['show_not_on_site_area'] 	= $options[$number]['show_not_on_site_area'] ? 1 : 0;
		$data['site_area_id'] 			= $options[$number]['site_area_id'];
		$data['site_area'] 				= $options[$number]['site_area'];
        $data['before_widget'] 			= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
		$data['before_widget_title'] 	= empty($options[$number]['before_widget_title']) ? $default_before_title : stripslashes($options[$number]['before_widget_title']);
		$data['after_widget_title']  	= empty($options[$number]['after_widget_title'] ) ? $default_after_title : stripslashes($options[$number]['after_widget_title']) ;
		$data['after_widget']  			= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;

		$already_out = null;

		if( !empty($data['show_category']) )
		{ # if in category
			$post = $wp_query->post;
			if ( king_in_category($data['category_id']) )
			{
                king_pages_output($data, $number);
				$already_out = 1;
			}
		}

		if( !empty($data['show_on_site_area']) )
		{ # display widget on special sitearea

			if ( king_in_site_area($data['site_area'], $data['site_area_id']) && $already_out != 1)
			{
                king_pages_output($data, $number);
				$already_out = 1;
			}
		}
		elseif(!empty($data['show_not_on_site_area']))
		{ # display widget everwhere but in this sitearea
            if (!king_in_site_area($data['site_area'], $data['site_area_id']) && $already_out != 1)
            {
                king_pages_output($data, $number);
			}
		}

		if(empty($data['show_not_on_site_area']) && empty($data['show_on_site_area']) && empty($data['show_category']))
		{# always show
			king_pages_output($data, $number);
		}

		#debug output
		if (!empty($options[$number]['debug']))
			echo "<h2>__('Your Menu Options are:', 'widgetKing')</h2>
				child_of=$child_of <br>
				sort_column=$sort_column <br>
				sort_order=$sort_order <br>
				exclude=$exclude <br>
				depth=$depth <br>
				show_date=$show_date <br>
				date_format=$date_format <br>
				title_li=title_li <br>";

	}#End Frontend Output

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_pages_control($number) {

		# Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_pages');

		if ( $_POST["king_pages_submit_$number"] )
		{
			if ( isset($_POST["king_pages_defaults_$number"]) )
			{# if defaults are choosen
            	$newoptions[$number]['title']			= "Pages Menu $number";
				$newoptions[$number]['child_of']		= '0';
				$newoptions[$number]['sort_column']		= 'post_title';
				$newoptions[$number]['sort_order']		= 'ASC';
				$newoptions[$number]['exclude']			= '';
				$newoptions[$number]['depth']			= '0';
				$newoptions[$number]['show_date']		= '';
				$newoptions[$number]['date_format']		= '';
				$newoptions[$number]['debug']			= '';
				$newoptions[$number]['before_widget']		= "<ul>";
				$newoptions[$number]['after_widget']		= addslashes("</ul>");
				$newoptions[$number]['before_widget_title'] = "<h2>";
				$newoptions[$number]['after_widget_title']	= addslashes("</h2>");
			}
            elseif( !empty($_POST["king_pages_dump_$number"]) && isset($_POST["king_pages_usedump_$number"]))
			{
				$newoptions[$number] = king_read_options($_POST["king_pages_dump_$number"]);
			}
			else
			{# insert new form values
				$newoptions[$number]['title']			= strip_tags(stripslashes($_POST["king_pages_title_$number"]));
				$newoptions[$number]['child_of']		= $_POST["king_pages_child_of_$number"];
				$newoptions[$number]['sort_column']		= $_POST["king_pages_sort_column_$number"];
				$newoptions[$number]['sort_order']		= $_POST["king_pages_sort_order_$number"];
				$newoptions[$number]['exclude']			= $_POST["king_pages_exclude_$number"];
				$newoptions[$number]['depth']			= $_POST["king_pages_depth_$number"];
				$newoptions[$number]['show_date']		= $_POST["king_pages_show_date_$number"];
				$newoptions[$number]['date_format']		= $_POST["king_pages_date_format_$number"];
				$newoptions[$number]['debug']			= isset($_POST["king_pages_debug_$number"]);

                $newoptions[$number]['category_id']			= $_POST["king_pages_category_id_$number"];
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_pages_show_on_site_area_$number"]);
				$newoptions[$number]['show_not_on_site_area'] = isset($_POST["king_pages_show_not_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_pages_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_pages_site_area_id_$number"];

				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_pages_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_pages_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_pages_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_pages_widget_title_$number"]);
				$newoptions[$number]['foldlist']			= isset($_POST["king_pages_foldlist_$number"]);
			}
		}
		if ( $options != $newoptions )
		{# save it
			$options = $newoptions;
			update_option('widget_king_pages', $options);
		}
		$title				= wp_specialchars($options[$number]['title']);
		$child_of			= $options[$number]['child_of'];
		$sort_column		= $options[$number]['sort_column'];
		$sort_order			= $options[$number]['sort_order'];
		$exclude			= $options[$number]['exclude'];
		$depth 				= $options[$number]['depth'];
		$show_date			= $options[$number]['show_date'];
		$date_format		= $options[$number]['date_format'];
		$debug				= $options[$number]['debug'] ? 'checked' : '';

		$show_category		= !empty($options[$number]['show_category']) ? 'checked' : '';
		$category_id		= $options[$number]['category_id'];
		$show_on_site_area	= !empty($options[$number]['show_on_site_area']) ? 'checked' : '';
		$show_not_on_site_area = !empty($options[$number]['show_not_on_site_area']) ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));
		$before_widget_title= stripslashes(htmlentities($options[$number]['before_widget_title']));
		$after_widget_title = stripslashes(htmlentities($options[$number]['after_widget_title']));
		$foldlist			= $options[$number]['foldlist'] ? 'checked' : '';

		# Here is the form segment. Notice that I have outsourced the form elements to be a little cleaner
        echo king_get_tab_start('pages'.$number, array(
								__('Basic', 'widgetKing'),
								__('Advanced', 'widgetKing'),
								__('Show', 'widgetKing'),
								__('HTML', 'widgetKing'),
								__('Export', 'widgetKing'),
								));
		# show title
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_pages_title_$number",
				'Description' 	=> __('Title', 'widgetKing'),
				'Label_Title' 	=> __('The title above your pages menu', 'widgetKing'),
				'Value' 		=> $title,
				'Max' 			=>'50'));
		# show child pages
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_pages_child_of_$number",
				'Description' 	=>  __('List Child Pages of (ID)', 'widgetKing'),
				'Label_Title' 	=> __('Show only subpages of the given PagesID', 'widgetKing'),
				'Value' 		=> $child_of,
				'Class' 		=>'small',
				'Max' 			=>'3'));
		#sort Order
		echo king_get_select_p(array(
				'Label_Id_Name' =>"king_pages_sort_column_$number",
				'Description' 	=> __('Sort by', 'widgetKing'),
				'Label_Title' 	=> __('Sort Pages by choosen sort column.', 'widgetKing'),
				'select_options'=>array('post_title', 'menu_order','post_date','post_modified','ID','post_author','post_name'),
				'Value' 		=>$sort_column));
		#sort Order
		echo king_get_select_p(array(
				'Label_Id_Name' =>"king_pages_sort_order_$number",
				'Description' 	=> __('Sort order', 'widgetKing'),
				'Label_Title' 	=> __('Sort Categories ascending or descending depending on choosen sort column.', 'widgetKing'),
				'select_options'=>array('asc', 'desc'),
				'Value' 		=>$sort_order));

		#  exlude pages
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_pages_exclude_$number",
				'Description' 	=>  __('Exlude Pages with (ID)', 'widgetKing'),
				'Label_Title' 	=> __('Comma separated list of Page numeric IDs to be excluded from the list (example:10, 20, 30)', 'widgetKing'),
				'Value' 		=> $exclude,
				'Max' 			=>'150'));
		# show depth
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_pages_depth_$number",
				'Description' 	=>  __('Show till Sub-Level', 'widgetKing'),
				'Label_Title' 	=> __('Numeric value for how many levels of hierarchy (sub-pages) to display. Defaults to 0 (display all pages)', 'widgetKing'),
				'Value' 		=> $depth,
				'Size' 			=>'3',
				'Max' 			=>'3'));
		echo king_get_tab_section('pages'.$number.'-1');
		#show_date
		echo king_get_select_p(array(
				'Label_Id_Name' =>"king_pages_show_date_$number",
				'Description' 	=> __('Show Date', 'widgetKing'),
				'Label_Title' 	=> __('Display creation or last modified date next to each Page. if Empty -> Display no date. modified -> Display the date last modified. post_date -> Date Page was first created.', 'widgetKing'),
				'select_options'=>array('', 'modified','post_date'),
				'Value' 		=>$show_date));
		#date_format
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_pages_date_format_$number",
				'Description' 	=>  __('Format of date to display', 'widgetKing'),
				'Label_Title' 	=> __('Defaults to the date format configured in your WordPress options. Custom values need to be in php Time Format. You can google on that..can you?', 'widgetKing'),
				'Value' 		=> $date_format,
				'Max' 			=>'50'));
        	#show foldlist
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_pages_foldlist_$number",
				'Description' 	=> __('Use Foldable Pages Navigation', 'widgetKing'),
				'Label_Title' 	=> __('If you have the Fold Pages List Plugin installed you can set this option to use the wswwpx_fold_page_list() call.', 'widgetKing'),
				'Value' 		=>$foldlist));
		#set to defaults
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_pages_defaults_$number",
				'Description' 	=>  __('Insert default Options', 'widgetKing'),
				'Label_Title' 	=> __('Set Menu Options to Wordpress Defaults','widgetKing')
				));
		# devider
		echo king_get_tab_section('pages'.$number.'-2');
        widget_king_where_to_show('pages',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);
		# devider
		echo king_get_tab_section('pages'.$number.'-3');
		# widget surrounding HTML
		widget_king_htmloptions('pages',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);
		# show debug output
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_pages_debug_$number",
				'Description' 	=> __('Show Debug Output', 'widgetKing'),
				'Label_Title' 	=>  __('Shows all set options in Frontend to check what you have entered. The list_cats() is pretty bitchy so you might want to know whats going on.', 'widgetKing'),
				'Value' 		=>$debug));
        # devider
		echo king_get_tab_section('pages'.$number.'-4');
		king_get_dump_options('pages',$number,'widget_king_pages');
		echo king_get_hidden("king_pages_submit_$number",'1',"king_pages_submit_$number");
		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function widget_king_pages_setup()
	{
		$options = $newoptions = get_option('widget_king_pages');

		if ( isset($_POST['king_pages_number_submit']) )
		{
			$number = (int) $_POST['king_pages_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_pages', $options);
			widget_king_pages_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of categories
	* @author Georg Leciejewski
	*/
	function widget_king_pages_page()
	{
		$options = $newoptions = get_option('widget_king_pages');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King Pages Menu', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Pages Menus would you like? ', 'widgetKing');
		echo king_get_select("king_pages_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9'), 'king_pages_number' );
		echo king_get_submit('king_pages_number_submit','','king_pages_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_pages_register()
	{
		$options = get_option('widget_king_pages');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++)
		{
			$name = array('King Pages %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_pages' : /* unregister */ '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_pages_control' : /* unregister */ '', 400, 350, $i);
		}
		add_action('sidebar_admin_setup', 'widget_king_pages_setup');
		add_action('sidebar_admin_page', 'widget_king_pages_page');
	}
	widget_king_pages_register();
	include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');

}# end init function

add_action('plugins_loaded', 'widget_king_pages_init');

/**
*@desc the output of the pages menu
* @param array $data - holding the switches
* @param int $number - the current widget number
*/
function king_pages_output($data,$number)
{
	echo '<!-- Start King Pages ' . $number . ' -->'."\n";
	echo $data['before_widget']."\n";
	echo $data['before_widget_title']."\n";
	echo $data['title'] ."\n";
	echo $data['after_widget_title']."\n";

	if(function_exists('wswwpx_fold_page_list') && !empty($data['foldlist']))
	{# to be optimised
		wswwpx_fold_page_list(array(
						'child_of'		=>$data['child_of'],
						'sort_column' 	=>$data['sort_column'],
						'sort_order' 	=>$data['sort_order'],
						'exclude' 		=>$data['exclude'],
						'depth' 		=>$data['depth'],
						'show_date' 	=>$data['show_date'],
						'date_format' 	=>$data['date_format'],
						'title_li' 		=>$data['title_li'],
						));
	}
	else
	{
		wp_list_pages(array(
						'child_of'		=>$data['child_of'],
						'sort_column' 	=>$data['sort_column'],
						'sort_order' 	=>$data['sort_order'],
						'exclude' 		=>$data['exclude'],
						'depth' 		=>$data['depth'],
						'show_date' 	=>$data['show_date'],
						'date_format' 	=>$data['date_format'],
						'title_li' 		=>$data['title_li'],
						));

	}
	echo  $data['after_widget']."\n";
	echo '<!-- End King Pages  ' . $number . ' -->'."\n";

    return;
}
/**
* @desc Version Check Heading
*/
function widget_king_pages_version()
{
	king_version_head('King_Pages_Widget',KINGPAGESVERSION);
}
add_action('admin_head','widget_king_pages_version');
?>
