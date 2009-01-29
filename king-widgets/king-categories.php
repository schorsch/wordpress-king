<?php
/*
Plugin Name: King_Categories_Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress/wordpress-widget-king-categories/
Description: Adds a sidebar Categorie widget and lets users configure EVERY aspect of the category list. 
Author: Georg Leciejewski
Version: 1.01
Author URI: http://www.blog.mediaprojekte.de
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
define("KINGCATEGORIESVERSION",  "101");
#include_once('widgets.php');
include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');
  

/**
* @desc init Put functions into one big function we'll call at the plugins_loaded action.
* This ensures that all required plugin functions are defined.
* @author Georg Leciejewski
*/
function widget_king_categories_init() {

	# Check for the required plugin functions. This will prevent fatal
	# errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the list_cats function call
	* @author Georg Leciejewski
	*/
	function widget_king_categories($args, $number = 1) {

		# $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.
			$data = array();
			extract($args,EXTR_PREFIX_ALL,"default");
			$options					= get_option('widget_king_categories');
			$data['title'] 				= empty($options[$number]['title']) ? __('Categories') : $options[$number]['title'];
			$data['hierarchical']		= $options[$number]['hierarchical'] ? 1 : 0;
			$data['sort_column']		= $options[$number]['sort_column'];
			$data['sort_order']			= $options[$number]['sort_order'];
			$data['file']				= $options[$number]['file'];
			$data['list']				= $options[$number]['list'] ? 1 : 0;
			$data['optiondates'] 		= $options[$number]['optiondates'] ? 1 : 0;
			$data['optioncount']		= $options[$number]['count'] ? 1 : 0;
			$data['hide_empty']			= $options[$number]['empty'] ? 1 : 0;
			$data['count']				= $options[$number]['count'] ? 1 : 0;
			$data['use_desc_for_title'] = $options[$number]['desc_title'] ? 1 : 0;
			$data['children']			= $options[$number]['children'] ? 1 : 0;
			$data['child_of'] 			= empty($options[$number]['child_of']) ? 0 : $options[$number]['child_of'];
			$data['feed'] 				= $options[$number]['feed'];
			$data['feed_image']			= stripslashes($options[$number]['feed_image']);
			$data['exclude'] 			= $options[$number]['exclude'];
			$data['show_category']		= $options[$number]['show_category'] ? 1 : 0;
			$data['category_id']		= $options[$number]['category_id'];
			$data['show_on_site_area']	= $options[$number]['show_on_site_area'] ? 1 : 0;
			$data['show_not_on_site_area']	= $options[$number]['show_not_on_site_area'] ? 1 : 0;
			$data['site_area_id']		= $options[$number]['site_area_id'];
			$data['site_area']			= $options[$number]['site_area'];

			$data['before_widget']		= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
			$data['before_widget_title']= empty($options[$number]['before_widget_title']) ? $default_before_title : stripslashes($options[$number]['before_widget_title']);
			$data['after_widget_title'] = empty($options[$number]['after_widget_title'] ) ? $default_after_title : stripslashes($options[$number]['after_widget_title']) ;
			$data['after_widget'] 		= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;


		# These lines generate our output. Widgets can be very complex
		# but as you can see here, they can also be very, very simple.

		if( !empty($data['show_category']) )
		{
			$post = $wp_query->post;

			if ( king_in_category($data['category_id']) )
			{
				king_cat_output($data,$number);
				$already_out = 1;
			}
		}

		# sitearea Output
		if( !empty($data['show_on_site_area']) )
		{
			if ( king_in_site_area($data['site_area'], $data['site_area_id']) && $already_out != 1)
			{ #if in the site area
				king_cat_output($data,$number);
			}

		}
		elseif(!empty($data['show_not_on_site_area']))
		{
			if (!king_in_site_area($data['site_area'], $data['site_area_id']) && $already_out != 1)
			{#if not in the site area
				king_cat_output($data,$number);
			}#end if not sitearea
		}

		if(empty($data['show_not_on_site_area']) && empty($data['show_on_site_area']) && empty($data['show_category']))
		{# always show
			king_cat_output($data,$number);

		}

		if (!empty($options[$number]['debug']))
			echo "<h2>__('Your Menu Options are:', 'widgetKing')</h2>
				sort_column=$sort_column <br>
				sort_order=$sort_order <br>
				file=$file <br>
				list=$list <br>
				optiondates=$optiondates <br>
				optioncount=$count <br>
				hide_empty=$hide_empty <br>
				use_desc_for_title=$use_desc_for_title <br>
				children=$children <br>
				child_of=$child_of <br>
				feed=$feed <br>
				feed_image=$feed_image <br>
				exclude=$exclude <br>
				hierarchical=$hierarchical";
	}

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_categories_control($number) {

		# Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_categories');

		if ( $_POST["king_cat_submit_$number"] )
		{
			#if defaults are choosen
			if ( isset($_POST["king_cat_defaults_$number"]) )
			{
				$newoptions[$number]['title']			= "Category Menu $number";
				$newoptions[$number]['sort_column']		= 'ID';
				$newoptions[$number]['sort_order']		= 'asc';
				$newoptions[$number]['file']			= '';
				$newoptions[$number]['list']			= 1;
				$newoptions[$number]['optiondates']		= '';
				$newoptions[$number]['count']			= '';
				$newoptions[$number]['empty']			= 1;
				$newoptions[$number]['desc_title']		= 1;
				$newoptions[$number]['children']		= '';
				$newoptions[$number]['child_of']		= '';
				$newoptions[$number]['feed']			= '';
				$newoptions[$number]['feed_image']		= '';
				$newoptions[$number]['exclude']			= '';
				$newoptions[$number]['hierarchical']	= 1;
				$newoptions[$number]['debug']			= '';
				$newoptions[$number]['before_widget']		= "<li>";
				$newoptions[$number]['after_widget']		= addslashes("</ul></li>");
				$newoptions[$number]['before_widget_title'] = "<h2>";
				$newoptions[$number]['after_widget_title']	= addslashes("</h2><ul>");

			}elseif( $_POST["king_cat_copy_$number"] !=='No' && $_POST["king_cat_copy_$number"] != $number){
				$copy = $_POST["king_cat_copy_$number"];
				$newoptions[$number] = array();
				foreach($options[$copy] as $key => $val){
					$newoptions[$number][$key] = $val;
				}
			}elseif( !empty($_POST["king_cat_dump_$number"]) && isset($_POST["king_cat_usedump_$number"])){

				$newoptions[$number] = king_read_options($_POST["king_cat_dump_$number"]);

			}else{# insert new form values

				$newoptions[$number]['title']			= strip_tags(stripslashes($_POST["king_cat_title_$number"]));
				$newoptions[$number]['sort_column']		= $_POST["king_sort_column_$number"];
				$newoptions[$number]['sort_order']		= $_POST["king_sort_order_$number"];
				$newoptions[$number]['file']			= stripslashes($_POST["king_file_$number"]);
				$newoptions[$number]['list']			= isset($_POST["king_list_$number"]);
				$newoptions[$number]['optiondates']		= isset($_POST["king_optiondates_$number"]);
				$newoptions[$number]['count']			= isset($_POST["king_cat_count_$number"]);
				$newoptions[$number]['empty']			= isset($_POST["king_cat_empty_$number"]);
				$newoptions[$number]['desc_title']		= isset($_POST["king_desc_title_$number"]);
				$newoptions[$number]['children']		= isset($_POST["king_children_$number"]);
				$newoptions[$number]['child_of']		= strip_tags(stripslashes($_POST["king_child_of_$number"]));
				$newoptions[$number]['feed']			= strip_tags(stripslashes($_POST["king_feed_$number"]));
				$newoptions[$number]['feed_image']		= addslashes($_POST["king_feed_image_$number"]);
				$newoptions[$number]['exclude']			= stripslashes($_POST["king_exclude_$number"]);
				$newoptions[$number]['hierarchical']	= isset($_POST["king_cat_hierarchical_$number"]);
				$newoptions[$number]['debug']			= isset($_POST["king_cat_debug_$number"]);
				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_cat_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_cat_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_cat_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_cat_widget_title_$number"]);
				$newoptions[$number]['foldlist']		= isset($_POST["king_cat_foldlist_$number"]);

				$newoptions[$number]['show_category']		= isset($_POST["king_cat_showcategory_$number"]);
				$newoptions[$number]['category_id']			= $_POST["king_cat_category_id_$number"];
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_cat_show_on_site_area_$number"]);
				$newoptions[$number]['show_not_on_site_area']= isset($_POST["king_cat_show_not_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_cat_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_cat_site_area_id_$number"];

			}
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_categories', $options);
		}
		$title				= wp_specialchars($options[$number]['title']);
		$hierarchical		= $options[$number]['hierarchical'] ? 'checked' : '';
		$sort_column		= $options[$number]['sort_column'];
		$sort_order			= $options[$number]['sort_order'];
		$file				= $options[$number]['file'];
		$list				= $options[$number]['list'] ? 'checked' : '';
		$optiondates 		= $options[$number]['optiondates']? 'checked' : '';
		$count				= $options[$number]['count'] ? 'checked' : '';
		$empty				= $options[$number]['empty'] ? 'checked' : '';
		$desc_title			= $options[$number]['desc_title'] ? 'checked' : '';
		$children			= $options[$number]['children'] ? 'checked' : '';
		$child_of			= $options[$number]['child_of'];
		$feed				= $options[$number]['feed'];
		$feed_image			= stripslashes($options[$number]['feed_image']);
		$exclude			= $options[$number]['exclude'];
		$hierarchical		= $options[$number]['hierarchical'] ? 'checked' : '';
		$debug				= $options[$number]['debug'] ? 'checked' : '';
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));
		$before_widget_title= stripslashes(htmlentities($options[$number]['before_widget_title']));
		$after_widget_title = stripslashes(htmlentities($options[$number]['after_widget_title']));
		$foldlist			= $options[$number]['foldlist'] ? 'checked' : '';

		$show_category		= $options[$number]['show_category'] ? 'checked' : '';
		$category_id		= $options[$number]['category_id'];
		$show_on_site_area	= $options[$number]['show_on_site_area'] ? 'checked' : '';
		$show_not_on_site_area	= $options[$number]['show_not_on_site_area'] ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];



		echo king_get_tab_start('cat'.$number, array(
								__('Basic', 'widgetKing'),
								__('Advanced', 'widgetKing'),
								__('Show', 'widgetKing'),
								__('HTML', 'widgetKing'),
								__('Export', 'widgetKing'),
								));
		# show title
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_cat_title_$number",
				'Description' 	=> __('Title', 'widgetKing'),
				'Label_Title' 	=> __('The title above your category menu', 'widgetKing'),
				'Value' 		=>$title,
				'Size' 			=>'20',
				'Max' 			=>'50'));
		#show category Count
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_cat_count_$number",
				'Description' 	=> __('Show post counts', 'widgetKing'),
				'Label_Title' 	=> __('Show number of posts in category', 'widgetKing'),
				'Value' 		=>$count));
		#show hirachical
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_cat_hierarchical_$number",
				'Description' 	=> __('Show hierarchical', 'widgetKing'),
				'Label_Title' 	=>__('Shows Categories hierarchical with sub-categories indented -> Depending on your CSS', 'widgetKing'),
				'Value' 		=>$hierarchical));
		#show cat children
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_children_$number",
				'Description' 	=> __('Show Children', 'widgetKing'),
				'Label_Title' 	=>__('Show the children of the categories or just the top level categories.', 'widgetKing'),
				'Value' 		=>$children));
		#show empty
		echo king_get_checkbox_p(array(
				'Label_Id_Name' => "king_cat_empty_$number",
				'Description' 	=> __('Hide Empty Categories', 'widgetKing'),
				'Label_Title' 	=> __('Categories without articles are not shown.', 'widgetKing'),
				'Value' 		=> $empty));
		#sort Column
		echo king_get_select_p(array(
				'Label_Id_Name' =>"king_sort_column_$number",
				'Description' 	=> __('Sort by', 'widgetKing'),
				'select_options'=>array('name', 'id'),
				'Value' 		=>$sort_column));
		#sort Order
		echo king_get_select_p(array(
				'Label_Id_Name' =>"king_sort_order_$number",
				'Description' 	=> __('Sort order', 'widgetKing'),
				'Label_Title' 	=> __('Sort Categories ascending or descending depending on choosen sort column.', 'widgetKing'),
				'select_options'=>array('asc', 'desc'),
				'Value' 		=>$sort_order));
		#list as list
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_list_$number",
				'Description' 	=> __('Show as List (li)', 'widgetKing'),
				'Label_Title' 	=> __('Sets whether the Categories are enclosed by list points ->li', 'widgetKing'),
				'Value' 		=>$list));
		# devider
		echo king_get_tab_section('cat'.$number.'-1');
		#show dates
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_optiondates_$number",
				'Description' 	=>__('Date of the last post', 'widgetKing'),
				'Label_Title' 	=> __('Sets whether to display the date of the last post in each Category.', 'widgetKing'),
				'Value' 		=>$optiondates));
		#description as title
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_desc_title_$number",
				'Description' 	=>__('Use Description as Title','widgetKing'),
				'Label_Title' 	=>__('Sets whether to display the Category Description in the links title tag.', 'widgetKing'),
				'Value' 		=>$desc_title));
		#show children of
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_child_of_$number",
				'Description' 	=>__('Show Children of Category', 'widgetKing'),
				'Label_Title' 	=>__('Show only children of the category id given. All other top level categories will disappear.', 'widgetKing'),
				'Value' 		=>$child_of,
				'Size' 			=>'3',
				'Max' 			=>'3'));
		#file to display category on
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_file_$number",
				'Description' 	=>__('File to show Category on', 'widgetKing'),
				'Label_Title' 	=>__('The php file a Category link is to be displayed on. Defaults to index.php.', 'widgetKing'),
				'Value' 		=>$file,
				'Size' 			=>'20',
				'Max' 			=>'200'));
		#insert feed text
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_feed_$number",
				'Description' 	=>__('Show Category Feed Text', 'widgetKing'),
				'Label_Title' 	=> __('Text to display for the link to each Categorys RSS2 feed. Default is no text, and no feed displayed.', 'widgetKing'),
				'Value' 		=>$feed,
				'Size' 			=>'20',
				'Max' 			=>'20'));
		#name of feed image  Path/filename
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_feed_image_$number",
				'Description' 	=>__('Show Category Feed Image', 'widgetKing'),
				'Label_Title' 	=> __('URL Path/filename for a graphic to act as a link to each Categories RSS2 feed.Overrides the feed parameter.', 'widgetKing'),
				'Value' 		=>$feed_image,
				'Size' 			=>'20',
				'Max' 			=>'300'));
		#exclude categories
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_exclude_$number",
				'Description' 	=> __('Exclude Categories (1,2,3)', 'widgetKing'),
				'Label_Title' 	=> __('Sets the Categories to be excluded. This must be in the form of an array (ex: 1, 2, 3).', 'widgetKing'),
				'Value' 		=>$exclude,
				'Size' 			=>'20',
				'Max' 			=>'100'));
		#set to defaults
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_cat_defaults_$number",
				'Description' 	=>  __('Insert default Options', 'widgetKing'),
				'Label_Title' 	=> __('Set Menu Options to Wordpress Defaults','widgetKing')
				));
		#copy
		echo king_get_select_p(array(
			'Label_Id_Name' => "king_cat_copy_$number",
			'Description' 	=> __('Copy Settings from Widget No.', 'widgetKing'),
			'Label_Title' 	=> __('Choose a Widget Number from which you want to copy the settings into this one. Make sure to choose the right widget, with some Options in it!', 'widgetKing'),
			'select_options'=> array('No','1', '2', '3', '4', '5', '6', '7', '8', '9')));
		#devider
        echo king_get_tab_section('cat'.$number.'-2');
		# Where To Show Options Panel
		widget_king_where_to_show('cat',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);
		# devider
		echo king_get_tab_section('cat'.$number.'-3');

		widget_king_htmloptions('cat',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);
		# show debug output
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_cat_debug_$number",
				'Description' 	=> __('Show Debug Output', 'widgetKing'),
				'Label_Title' 	=>  __('Shows all set options in Frontend to check what you have entered. The list_cats() is pretty bitchy so you might want to know whats going on.', 'widgetKing'),
				'Value' 			=>$debug));
		# foldlist
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_cat_foldlist_$number",
				'Description' 	=> __('Use Foldable Navigation', 'widgetKing'),
				'Label_Title' 	=> __('If you have the Fold Category List Plugin installed you can set this option to use the wswwpx_list_cats call.', 'widgetKing'),
				'Value' 		=>$foldlist));

		echo king_get_tab_section('cat'.$number.'-4');
		king_get_dump_options('cat',$number,'widget_king_categories');

		echo king_get_hidden("king_cat_submit_$number",'1',"king_cat_submit_$number");
		echo king_get_tab_end();

	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function widget_king_categories_setup() {
		$options = $newoptions = get_option('widget_king_categories');

		if ( isset($_POST['king_cat_number_submit']) ) {
			$number = (int) $_POST['king_cat_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_king_categories', $options);
			widget_king_categories_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of categories
	* @author Georg Leciejewski
	*/
	function widget_king_categories_page() {

		$options = $newoptions = get_option('widget_king_categories');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King Category Menu', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Category Menus would you like?', 'widgetKing');
		echo king_get_select("king_cat_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9'), 'king_cat_number' );
		echo king_get_submit('king_cat_number_submit','','king_cat_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();

	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_categories_register()
	{
		#include_once('widgets.php');
		$options = get_option('widget_king_categories');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++) {
			$name = array('King Cat %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_categories' : /* unregister */ '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_categories_control' : /* unregister */ '', 450, 450, $i);
		}
		add_action('sidebar_admin_setup', 'widget_king_categories_setup');

		add_action('sidebar_admin_page', 'widget_king_categories_page');

	}
widget_king_categories_register();
include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');

}# end init function
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');

add_action('plugins_loaded', 'widget_king_categories_init');

/**
*@desc the output of the categoriy menu
* @param array $data - holding the switches
* @param int $number - the curretn widget number
*/
function king_cat_output($data,$number)
{
	echo '<!-- Start King Cat ' . $number . ' -->'."\n";
	echo $data['before_widget']."\n";
	echo $data['before_widget_title']."\n";
	echo $data['title'] ."\n";
	echo $data['after_widget_title']."\n";
	if(function_exists('wswwpx_list_cats') && !empty($options[$number]['foldlist']))
	{
		wswwpx_list_cats($data['optionall'] = 0,$data['all'] = 'All',$data['sort_column'],$data['sort_order'],$data['file'],$data['list'],$data['optiondates'],$data['optioncount'],$data['hide_empty'],$data['use_desc_for_title'],$data['children'],$data['child_of'],$data['categories']=0,$data['recurse']=0,$data['feed'],$data['feed_image'],$data['exclude'],$data['hierarchical']);
	}
	else
	{
		list_cats($data['optionall'] = 0,$data['all'] = 'All',$data['sort_column'],$data['sort_order'],	$data['file'],$data['list'],$data['optiondates'] ,$data['optioncount'],	$data['hide_empty'],$data['use_desc_for_title'],$data['children'],$data['child_of'],$data['categories']=0,$data['recurse']=0,$data['feed'],$data['feed_image'],$data['exclude'],$data['hierarchical']);
	}
	echo $data['after_widget']."\n";
	echo '<!-- End Cat ' . $number . ' -->'."\n";


    return;
}

/**
* @desc Version Check Heading
* @todo rebuild thw whole version checking
*/
function widget_king_categories_version() {
	king_version_head('King_Categories_Widget',KINGCATEGORIESVERSION);
}
add_action('admin_head','widget_king_categories_version');

?>