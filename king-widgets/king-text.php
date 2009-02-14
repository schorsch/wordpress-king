<?php
/*
Plugin Name: King_Text_Widget
Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-text/
Description: Adds a Text widget Options are: in which category or Site Area to show + php/Html output + the html before and after the Widget.
Author: Georg Leciejewski
Version: 0.72
Author URI: http://www.blog.mediaprojekte.de
*/

/*  Copyright 2006  georg leciejewski  (email : georg@mediaprojekte.de)
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
define("KINGTEXTVERSION","072");

include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');
/**
* @desc init Put functions into one big function we'll call at the plugins_loaded action.
* This ensures that all required plugin functions are defined.
* @author Georg Leciejewski
*/
function widget_king_text_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the list_cats function call
	* @author Georg Leciejewski
	*/
	function widget_king_text($args, $number = 1) {

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args,EXTR_PREFIX_ALL,"default");
		$options 			= get_option('widget_king_text');
		$title 				= $options[$number]['title'];
		$text 				= $options[$number]['text'];
		$show_category		= $options[$number]['show_category'] ? 1 : 0;
		$use_php			= $options[$number]['use_php'] ? 1 : 0;
		$category_id		= $options[$number]['category_id'];
		$show_on_site_area	= $options[$number]['show_on_site_area'] ? 1 : 0;
		$show_not_on_site_area	= $options[$number]['show_not_on_site_area'] ? 1 : 0;
		$site_area_id		= $options[$number]['site_area_id'];
		$site_area			= $options[$number]['site_area'];
		$slide				= $options[$number]['slide']? 1 : 0;
		$before_widget		= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
		$before_widget_title= empty($options[$number]['before_widget_title']) ? $default_before_title : stripslashes($options[$number]['before_widget_title']);
		$after_widget_title = empty($options[$number]['after_widget_title'] ) ? $default_after_title : stripslashes($options[$number]['after_widget_title']) ;
		$after_widget 		= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;

		$textparts = explode('<!--more-->', $text);
		$partno = mt_rand(0, sizeof($textparts) - 1);

		if( !empty($show_category) )
		{
			$post = $wp_query->post;
			if ( in_category($category_id) )
			{
				//if in category
				echo '<!-- Start King Text ' . $number . ' -->'."\n";
				echo $before_widget."\n";
				if(!empty($slide))
				{
					king_height_slide('textslide'.$number,'text'.$number,1,'400');
				}
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				if( !empty($use_php) )
				{
					eval('?>'.$textparts[$partno]);
				}
				else
				{
					echo $textparts[$partno];
				}
				echo $after_widget."\n";
				echo '<!-- End King Text -->'."\n";
				$already_out = 1;
			}
		}//end site area if

		// sitearea Output
		if( !empty($show_on_site_area) ){
			if ( king_in_site_area($site_area, $site_area_id) && $already_out != 1)
			{ //if in the site area
				echo '<!-- Start King Text ' . $number . ' -->'."\n";
				echo $before_widget."\n";
				if(!empty($slide))
				{
					king_height_slide('textslide'.$number,'text'.$number,1,'400');
				}
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				if( !empty($use_php) )
				{
					eval('?>'.$textparts[$partno]);
				}
				else
				{
					echo $textparts[$partno];
				}
				echo $after_widget."\n";
				echo '<!-- End King Text -->'."\n";
			}
		}
		elseif(!empty($show_not_on_site_area))
		{
			if (!king_in_site_area($site_area, $site_area_id) && $already_out != 1)
			{# not in the site area
				echo '<!-- Start King Text ' . $number . ' -->'."\n";
				echo $before_widget."\n";
				if(!empty($slide))
				{
					king_height_slide('textslide'.$number,'text'.$number,1,'400');
				}
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				if( !empty($use_php) )
				{
					eval('?>'.$textparts[$partno]);
				}
				else
				{
					echo $textparts[$partno];
				}
				echo $after_widget."\n";
				echo '<!-- End King Text -->'."\n";
			}
		}

		if(empty($show_not_on_site_area) && empty($show_on_site_area) && empty($show_category))
		{# alway show
			echo '<!-- Start King Text ' . $number . ' -->'."\n";
			echo $before_widget."\n";
			if(!empty($slide)){
					king_height_slide('textslide'.$number,'text'.$number,1,'400');
				}
			echo $before_widget_title."\n";
			echo $title ."\n";
			echo $after_widget_title."\n";
			if( !empty($use_php) ) {
				eval('?>'.$textparts[$partno]);
			}else{
				echo $textparts[$partno];
			}
			echo $after_widget."\n";
			echo '<!-- End King Text -->'."\n";
		}
	}

	/**
	* @desc Output of plugins edit form in the adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_text_control($number) {

		// Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_text');

		if ( $_POST["king_text_submit_$number"] )
		{
			//if defaults are choosen
			if ( isset($_POST["king_text_defaults_$number"]) )
			{
			/*  no defaults atm	*/
            }elseif( $_POST["king_text_copy_$number"] !=='No' && $_POST["king_text_copy_$number"] != $number){
				$copy = $_POST["king_text_copy_$number"];
				$newoptions[$number] = array();
				foreach($options[$copy] as $key => $val){
					$newoptions[$number][$key] = $val;
				}
			}else{// insert new form values

				$newoptions[$number]['title']				= strip_tags(stripslashes($_POST["king_text_title_$number"]));
				$newoptions[$number]['text'] 				= stripslashes($_POST["king_text_text_$number"]);
				if ( !current_user_can('unfiltered_html') )
					$newoptions[$number]['text'] = stripslashes(wp_filter_post_kses($newoptions[$number]['text']));

				$newoptions[$number]['use_php']				= isset($_POST["king_text_use_php_$number"]);
				$newoptions[$number]['show_category']		= isset($_POST["king_text_showcategory_$number"]);
				$newoptions[$number]['category_id']			= $_POST["king_text_category_id_$number"];
				$newoptions[$number]['slide']				= isset($_POST["king_text_slide_$number"]);
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_text_show_on_site_area_$number"]);
				$newoptions[$number]['show_not_on_site_area']= isset($_POST["king_text_show_not_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_text_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_text_site_area_id_$number"];
				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_text_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_text_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_text_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_text_widget_title_$number"]);

			}
		}

		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_text', $options);
		}

		$title 				= htmlspecialchars($options[$number]['title'], ENT_QUOTES);
		$text 				= htmlspecialchars($options[$number]['text'], ENT_QUOTES);
		$show_category		= $options[$number]['show_category'] ? 'checked' : '';
		$category_id		= $options[$number]['category_id'];
		$use_php			= $options[$number]['use_php'] ? 'checked' : '';
		$show_on_site_area	= $options[$number]['show_on_site_area'] ? 'checked' : '';
		$show_not_on_site_area	= $options[$number]['show_not_on_site_area'] ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];
		$slide				= !empty($options[$number]['slide'])? 'checked' : '';
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));
		$before_widget_title= stripslashes(htmlentities($options[$number]['before_widget_title']));
		$after_widget_title = stripslashes(htmlentities($options[$number]['after_widget_title']));

		echo king_get_tab_start('text'.$number, array(
						__('Basic Features', 'widgetKing'),
						__('Show', 'widgetKing'),
						__('HTML', 'widgetKing')
								) );
		# show title
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_text_title_$number",
				'Description' 	=> __('Title', 'widgetKing'),
				'Label_Title' 	=> __('The title above your text menu', 'widgetKing'),
				'Value' 		=> $title
				));
		#use_php in textarea
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_text_use_php_$number",
				'Description' 	=> __('Use PHP in Text', 'widgetKing'),
				'Label_Title' 	=>  __('If checked the inserted code is evaluated as php.PHP Code MUST be enclosed in &lt;?php and ?&gt; tags! You can also insert Wordpress Code if you have not found a Widget for it yet.', 'widgetKing'),
				'Value' 		=>$use_php));

		# show child text
		echo king_get_textarea_p(array(
				'Label_Id_Name' =>"king_text_text_$number",
				'Description' 	=>  __('Text or HTML', 'widgetKing'),
				'Label_Title' 	=> __('Insert your Text Freely. This can be bannercode, images or whatever you like. The HTML gets stripped if you do not have the right to insert unfiltered html.', 'widgetKing'),
				'Value' 		=> $text,
				'Class' 		=>'big'
				));
        #copy
		echo king_get_select_p(array(
			'Label_Id_Name' => "king_text_copy_$number",
			'Description' 	=> __('Copy Settings from Widget No.', 'widgetKing'),
			'Label_Title' 	=> __('Choose a Widget Number from which you want to copy the settings into this one. Make sure to choose the right widget, with some Options in it!', 'widgetKing'),
			'select_options'=> array('No','1', '2', '3', '4', '5', '6', '7', '8', '9','10','11','12','13','14','15','16','17','18','19')));

		echo king_get_tab_section('text'.$number.'-1');
		# Where To Show Options Panel
		widget_king_where_to_show('text',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);

		echo king_get_tab_section('text'.$number.'-2');
		# Widget HTML
		widget_king_htmloptions('text',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);

		echo king_get_hidden("king_text_submit_$number",'1',"king_text_submit_$number");    
		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function widget_king_text_setup() {
		$options = $newoptions = get_option('widget_king_text');

		if ( isset($_POST['king_text_number_submit']) ) {
			$number = (int) $_POST['king_text_number'];
			if ( $number > 20 ) $number = 20;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_king_text', $options);
			widget_king_text_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of categories
	* @author Georg Leciejewski
	*/
	function widget_king_text_page() {

		$options = $newoptions = get_option('widget_king_text');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King Text Boxes', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Text Boxes would you like? ', 'widgetKing');
		echo king_get_select("king_text_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9','10','11', '12', '13', '14', '15', '16', '17', '18','19','20'), 'king_text_number' );
		echo king_get_submit('king_text_number_submit','','king_text_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
		//echo '<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce_gzip.php?ver=20051211"></script>';
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_text_register()
	{

		$options = get_option('widget_king_text');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 20 ) $number = 20;
		for ($i = 1; $i <= 20; $i++) {
			$name = array('King Text %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_text' : /* unregister */ '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_text_control' : /* unregister */ '', 450, 400, $i);
		}

		add_action('sidebar_admin_setup', 'widget_king_text_setup');
		add_action('sidebar_admin_page', 'widget_king_text_page');
	}
	widget_king_text_register();

}# end init function

add_action('plugins_loaded', 'widget_king_text_init');
/**
* @desc Version Check Heading
*/
function widget_king_text_version()
{
	king_version_head('King_Text_Widget',KINGTEXTVERSION);
}
add_action('admin_head','widget_king_text_version');

?>
