<?php
/*

Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-search/
Description: Advanced Search Box Widget including Category Dropdown and Search Word Spellcheck Suggestion
Author: Georg Leciejewski
Version: 0.54
Author URI: http://www.blog.mediaprojekte.de
*/
define("KINGSEARCHVERSION",  "054");
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
function widget_king_search_init() {

	# Check for the required plugin functions. This will prevent fatal
	# errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the list_cats function call
	* @author Georg Leciejewski
	*/
	function widget_king_search($args, $number = 1) {
		global $s;
		# $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.
			extract($args,EXTR_PREFIX_ALL,"default");
			$options 			= get_option('widget_king_search');
			$title 				= $options[$number]['title'];
			$use_suggestion		= $options[$number]['use_suggestion'] ? 1 : 0;
			$use_catsearch		= $options[$number]['use_catsearch'] ? 1 : 0;
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

		#if events search mode
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
					?>
					<form id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<p>
					<input type="text" name="s" id="s" size="15" value="<?php echo wp_specialchars($s, 1); ?>" /><br />
					<?php
					if( !empty($use_catsearch) ){
					_e('Search only category:','widgetKing');
					dropdown_cats();
					} ?><br />
					<input type="submit" value="<?php _e('Search'); ?>" />
					</p>
					</form>
					<?php
					if( !empty($use_suggestion) ){
						if(!empty($s)){
							king_search_suggest( wp_specialchars($s, 1) );
						}
					}
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
					?>
					<form id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<p>
					<input type="text" name="s" id="s" size="15" value="<?php echo wp_specialchars($s, 1); ?>" /><br />
					<?php
					if( !empty($use_catsearch) ){
					_e('Search only category:','widgetKing');
					dropdown_cats();
					} ?><br />
					<input type="submit" value="<?php _e('Search'); ?>" />
					</p>
					</form>
					<?php
					if( !empty($use_suggestion) ){
						if(!empty($s)){
							king_search_suggest( wp_specialchars($s, 1) );
						}
					}
					echo $after_widget."\n";

				} #else{}

			}else{
				#no category id selected
				echo $before_widget."\n";
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				?>
				<form id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<p>
				<input type="text" name="s" id="s" size="15" value="<?php echo wp_specialchars($s, 1); ?>" /><br />
				<?php
				if( !empty($use_catsearch) ){
					_e('Search only in category:','widgetKing');
					echo '<br />';
					dropdown_cats();
				} ?>
				<br />
				<input type="submit" value="<?php _e('Search'); ?>" />
				</p>
				</form>
				<?php
				if( !empty($use_suggestion) ){
					if(!empty($s)){
						king_search_suggest( wp_specialchars($s, 1) );
					}
				}
				echo $after_widget."\n";
			}

	}#end function widget_king_search

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_search_control($number=1) {

		# Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_search');

		if ( $_POST["king_search_submit_$number"] )
		{
			#if defaults are choosen
			if ( isset($_POST["king_search_defaults_$number"]) )
			{
				/* no defaults at the moment
				$newoptions[$number]['title']				= "search";
				$newoptions[$number]['before_widget']		= "<ul>";
				$newoptions[$number]['after_widget']		= addslashes("</li></ul>");
				$newoptions[$number]['before_widget_title'] = "<h2>";
				$newoptions[$number]['after_widget_title']	= addslashes("</h2><li>");
				*/
			}else{# insert new form values

				$newoptions[$number]['title']				= strip_tags(stripslashes($_POST["king_search_title_$number"]));
				$newoptions[$number]['show_category']		= isset($_POST["king_search_showcategory_$number"]);
				$newoptions[$number]['use_suggestion']		= isset($_POST["king_search_use_suggestion_$number"]);
				$newoptions[$number]['use_catsearch']		= isset($_POST["king_search_use_catsearch_$number"]);

				$newoptions[$number]['category_id']			= $_POST["king_search_category_id_$number"];
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_search_show_on_site_area_$number"]);
				$newoptions[$number]['show_not_on_site_area']= isset($_POST["king_search_show_not_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_search_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_search_site_area_id_$number"];

				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_search_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_search_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_search_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_search_widget_title_$number"]);

			}
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_search', $options);
		}

		$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
		$show_category		= $options[$number]['show_category'] ? 'checked' : '';
		$use_suggestion		= $options[$number]['use_suggestion'] ? 'checked' : '';
		$use_catsearch		= $options[$number]['use_catsearch'] ? 'checked' : '';

		$category_id		= $options[$number]['category_id'];
		$show_on_site_area	= $options[$number]['show_on_site_area'] ? 'checked' : '';
		$show_not_on_site_area 	= $options[$number]['show_not_on_site_area'] ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];

		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));
		$before_widget_title= stripslashes(htmlentities($options[$number]['before_widget_title']));
		$after_widget_title = stripslashes(htmlentities($options[$number]['after_widget_title']));

		echo king_get_tab_start('search'.$number, array(
								__('Basic Features', 'widgetKing'),
								__('Show', 'widgetKing'),
								__('HTML', 'widgetKing'),
								));
		# title
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"king_search_title_$number",
				'Description' 	=> __('Title', 'widgetKing'),
				'Label_Title' 	=> __('The title above your search', 'widgetKing'),
				'Value' 			=> $title,
				'Size' 			=>'20',
				'Max' 			=>'50'));

		# use events instead of normal
		echo king_get_checkbox_p(array(
				'Label_Id_Name' 	=>"king_search_use_suggestion_$number",
				'Description' 	=> __('Use Search Suggestion', 'widgetKing'),
				'Label_Title' 	=>  __('Check this box for Search Word-Suggestions. Makes a spellcheck and looks up the right word.', 'widgetKing'),
				'Value' 			=>$use_suggestion));
		# show category search feature
		echo king_get_checkbox_p(array(
				'Label_Id_Name' 	=>"king_search_use_catsearch_$number",
				'Description' 	=> __('Use Category Search', 'widgetKing'),
				'Label_Title' 	=>  __('A dropdown containing all your categories. Searches only in the choosen Category.', 'widgetKing'),
				'Value' 			=>$use_catsearch));
/*
		#devider
		echo king_get_tab_section('search'.$number.'-3');
		# URL Format
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"king_search_urlformat_$number",

				'Description' 	=> __('URL Format', 'widgetKing'),
				'Label_Title' 	=> __('The URL Format for your Search results page. Default is: /?s=%s', 'widgetKing'),
				'Value' 			=> $url_format,
				'Size' 			=>'50',
				'Max' 			=>'200'));
		#before suggest
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"king_search_beforesuggest_$number",

				'Description' 	=> __('Before Suggest', 'widgetKing'),
				'Label_Title' 	=> __('The HTML and Text before the Suggested Words. Defaults can be seen in Frontend Sourcecode.', 'widgetKing'),
				'Value' 			=> $before_suggest_text,
				'Size' 			=>'50',
				'Max' 			=>'200'));

		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"king_search_aftersuggest_$number",

				'Description' 	=> __('After Suggest', 'widgetKing'),
				'Label_Title' 	=> __('The HTML after the Suggested Words. Defaults can be seen in Frontend Sourcecode.', 'widgetKing'),
				'Value' 			=> $after_suggest_text,
				'Size' 			=>'50',
				'Max' 			=>'200'));
		echo king_get_textbox_p(array(
				'Label_Id_Name' 	=>"king_search_suggesterror_$number",

				'Description' 	=> __('Suggest Error Text', 'widgetKing'),
				'Label_Title' 	=> __('The HTML and Text before the Suggested Words. Defaults can be seen in Frontend Sourcecode.', 'widgetKing'),
				'Value' 			=> $error_text,
				'Size' 			=>'50',
				'Max' 			=>'200'));
*/
		#devider
		echo king_get_tab_section('search'.$number.'-1');

		#Where To Show Options Panel
		widget_king_where_to_show('search',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);
		#devider
		echo king_get_tab_section('search'.$number.'-2');
		# HTML
		widget_king_htmloptions('search',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);
		echo king_get_hidden("king_search_submit_$number",'1',"king_search_submit_$number");
		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
*/
	function widget_king_search_setup() {
		$options = $newoptions = get_option('widget_king_search');

		if ( isset($_POST['king_search_number_submit']) ) {
			$number = (int) $_POST['king_search_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_king_search', $options);
			widget_king_search_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of searchs
	* @author Georg Leciejewski
	*/
	function widget_king_search_page() {

		$options = $newoptions = get_option('widget_king_search');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King search', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many searchs would you like?', 'widgetKing');
		echo king_get_select("king_search_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9'), 'king_search_number' );
		echo king_get_submit('king_search_number_submit','','king_search_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();

	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_search_register()
	{
		
		$options = get_option('widget_king_search');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++) {
			$name = array('King Search %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_search' :  '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_search_control' :  '', 450, 400, $i);
		}
		add_action('sidebar_admin_setup', 'widget_king_search_setup');
		add_action('sidebar_admin_page', 'widget_king_search_page');


	}
widget_king_search_register();

include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');

}# end init function

require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');

add_action('plugins_loaded', 'widget_king_search_init');

/**
* @desc search suggestions
* @author Georg Leciejewski
*/
function king_search_suggest($query,
							$url_format='/?s=%s',
							$before_suggest_text='<p><font color=red>Did you mean: </font><strong>',
							$after_suggest_text='</strong></p>',
							$no_suggest_text='',
							$error_text='<span class="error">Sorry, could not get spelling suggestions.</span>')
{

    $yahoorequest =  'http://api.search.yahoo.com/WebSearchService/V1/spellingSuggestion?appid=king_search_suggest&query='.urlencode($query).'&output=php';
    $answer = file_get_contents($yahoorequest);
    if ($answer === false) {
        echo $error_text;
        return false;
    }
    $searchobj = unserialize($answer);
    if(isset($searchobj['ResultSet']['Result']) && $searchobj['ResultSet']['Result']!="") {
        echo $before_suggest_text;
        echo '<a href="'.get_settings('home');
        printf($url_format,urlencode($searchobj['ResultSet']['Result']));
        echo '" title=" '. __('Do you want to search for:', 'widgetKing').' '.htmlspecialchars($searchobj['ResultSet']['Result']).'">'.htmlspecialchars($searchobj['ResultSet']['Result']).'</a>';
        echo $after_suggest_text;
    }
    else {
        echo $no_suggest_text;
    }
    return true;
}
/**
* @desc Version Check Heading
*/
function widget_king_search_version() {
	king_version_head('King_Search_Widget',KINGSEARCHVERSION);
}
add_action('admin_head','widget_king_search_version');

?>
