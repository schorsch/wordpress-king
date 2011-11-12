<?php
/*

Plugin URI: http://www.blog.mediaprojekte.de/cms-systeme/wordpress-plugins/wordpress-widget-king-rss/
Description: Adds multiple damn advanced RSS widgets to your sidebars.
Author: Georg Leciejewski
Version: 0.52
Author URI: http://www.blog.mediaprojekte.de
*/
define("KINGRSSVERSION",  "052");

/*
License:
NOT OPEN SOURCE
This Software is free for private and charity use.
If your are using this in any commercial enviroment please contact me for the little licence fee.
I?m also available for further support and development in such a case!
You are not allowed to redistribute this code without my permission.

George Leciejewski
georg@mediaprojekte.de

*/

require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');
require_once(ABSPATH . 'wp-content/plugins/king-framework/library/class-simplepie-rss.php');
/**
* @desc init Put functions into one big function we'll call at the plugins_loaded action.
* This ensures that all required plugin functions are defined.
* @author Georg Leciejewski
*/
function widget_king_rss_init() {

	# Check for the required plugin functions. This will prevent fatal
	# errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the list_cats function call
	* @author Georg Leciejewski
	*/
	function widget_king_rss($args, $number = 1) {

		# $args is an array of strings that help widgets to conform to
		# the active theme: before_widget, before_title, after_widget,
		# and after_title are the array keys. Default tags: li and h2.
			extract($args,EXTR_PREFIX_ALL,"default");
			$options 				= get_option('widget_king_rss');
			$data['rss_url']		= $options[$number]['rss_url'];
			$data['max_items']		= $options[$number]['max_items'];
			$data['cache_time']		= $options[$number]['cache_time'];
			$data['nosort']			= $options[$number]['nosort']? 1 : 0;
			$data['shortdesc']		= $options[$number]['shortdesc'];
			$data['showdate']		= $options[$number]['showdate'];
			$data['error']			= $options[$number]['error'];
			$data['rsshtml']		= $options[$number]['rsshtml'];
			$data['titlehtml']		= $options[$number]['titlehtml'];
			$data['stripads']		= $options[$number]['stripads'];

			$show_category			= $options[$number]['show_category'] ? 1 : 0;
			$category_id			= $options[$number]['category_id'];
			$show_on_site_area		= $options[$number]['show_on_site_area'] ? 1 : 0;
			$show_not_on_site_area	= $options[$number]['show_not_on_site_area'] ? 1 : 0;
			$site_area_id			= $options[$number]['site_area_id'];
			$site_area				= $options[$number]['site_area'];
			$before_widget			= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
			$after_widget 			= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;

		# These lines generate our output. Widgets can be very complex
		# but as you can see here, they can also be very, very simple.
		if( !empty($show_category) )
		{
			$post = $wp_query->post;
			if ( in_category($category_id) )
			{
				#if in category
				echo '<!-- Start King RSS ' . $number . ' -->'."\n";
				echo $before_widget."\n";
				echo kingRssOutput($data) . "\n";
				echo $after_widget."\n";
				echo '<!-- End King RSS ' . $number . ' -->'."\n";
				$already_out = 1;
			}
		}#end site area if

		# sitearea Output
		if( !empty($show_on_site_area) ){

			if ( $site_area($site_area_id) && $already_out != 1){
				#if in the site area
				echo '<!-- Start King RSS ' . $number . ' -->'."\n";
				echo $before_widget."\n";
				echo kingRssOutput($data) . "\n";
				echo $after_widget."\n";
				echo '<!-- End King RSS ' . $number . ' -->'."\n";
			}#else{}

		}elseif(!empty($show_not_on_site_area)){
			if (!$site_area($site_area_id) && $already_out != 1){
				#if not in the site area
				echo $before_widget."\n";;
				echo kingRssOutput($data) . "\n";
				echo $after_widget."\n";
				echo '<!-- End King RSS ' . $number . ' -->'."\n";
				}#else{}
		}#end site area if

		if(empty($show_not_on_site_area) && empty($show_on_site_area) && empty($show_category)){
			echo '<!-- Start King RSS ' . $number . ' -->';
			echo $before_widget."\n";
			echo kingRssOutput($data) . "\n";
			echo $after_widget."\n";
			echo '<!-- End King RSS ' . $number . ' -->'."\n";
		}
	} # end function

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_rss_control($number) {

		# Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_rss');

		if ( $_POST["king_rss_submit_$number"] ){


			if ( isset($_POST["king_rss_defaults_$number"]) )
			{#if defaults are choosen
				if(!empty($_POST["king_rss_url_$number"])){
					$newoptions[$number]['rss_url']	= strip_tags(stripslashes($_POST["king_rss_url_$number"]));
				} else{
					$newoptions[$number]['rss_url'] 		= "http://www.blog.mediaprojekte.de/feed/";
				}
				$newoptions[$number]['max_items'] 			= "10";
				$newoptions[$number]['shortdesc']			= "50";
				$newoptions[$number]['showdate']			= "j F Y"; #php time format
				$newoptions[$number]['error']				= "Sorry can not grab the Feed!";
				$newoptions[$number]['titlehtml'] 			= '<h2><a class="rsswidget" href="%rssurl%" title="Syndicate this content"><img src="%rssicon%" alt="RSS" height="14" width="14"/></a>
<a class="rsswidget" href="%link%" title="%descr%">%title%</a></h2><ul>';
				$newoptions[$number]['rsshtml'] 			= '<li><strong>%date%</strong><br /><a class="rsswidget" href="%link%" title="%text%">%title%</a></li>';
				$newoptions[$number]['before_widget']		= '<li class="widget">';
				$newoptions[$number]['after_widget']		= "</ul></li>";
			}
			elseif($_POST["king_rss_copy_$number"] !=='No' && $_POST["king_rss_copy_$number"] != $number)
			{# do the copying
				$copy = $_POST["king_rss_copy_$number"]; #$copyoption
				$newoptions[$number]['title']				= $options[$copy]['title'];
				$newoptions[$number]['rss_url'] 			= $options[$copy]['rss_url'];
				$newoptions[$number]['max_items'] 			= $options[$copy]['max_items'];
				$newoptions[$number]['cache_time']			= $options[$copy]['cache_time'];
				$newoptions[$number]['nosort']				= $options[$copy]['nosort'];
				$newoptions[$number]['shortdesc']			= $options[$copy]['shortdesc'];
				$newoptions[$number]['showdate']			= $options[$copy]['showdate'];
				$newoptions[$number]['error']				= $options[$copy]['error'];
				$newoptions[$number]['rsshtml'] 			= $options[$copy]['rsshtml'];
				$newoptions[$number]['titlehtml'] 			= $options[$copy]['titlehtml'];
				$newoptions[$number]['stripads']			= $options[$copy]['stripads'];
				$newoptions[$number]['show_category']		= $options[$copy]['show_category'];
				$newoptions[$number]['category_id']			= $options[$copy]['category_id'];
				$newoptions[$number]['show_on_site_area']	= $options[$copy]['show_on_site_area'];
				$newoptions[$number]['show_not_on_site_area']= $options[$copy]['show_not_on_site_area'];
				$newoptions[$number]['site_area']			= $options[$copy]['site_area'];
				$newoptions[$number]['site_area_id']		= $options[$copy]['site_area_id'];
				$newoptions[$number]['before_widget']		= $options[$copy]['before_widget'];
				$newoptions[$number]['after_widget']		= $options[$copy]['after_widget'];
			}
			else
			{# insert new form values
				$newoptions[$number]['title']				= strip_tags(stripslashes($_POST["king_rss_title_$number"]));
				$newoptions[$number]['rss_url'] 			= strip_tags(stripslashes($_POST["king_rss_url_$number"]));
				$newoptions[$number]['max_items'] 			= strip_tags(stripslashes($_POST["king_rss_max_items_$number"]));
				$newoptions[$number]['cache_time']			= strip_tags(stripslashes($_POST["king_rss_cache_time_$number"]));
				$newoptions[$number]['nosort']				= isset($_POST["king_rss_nosort_$number"]);
				$newoptions[$number]['shortdesc']			= $_POST["king_rss_shortdesc_$number"];
				$newoptions[$number]['showdate']			= $_POST["king_rss_showdate_$number"]; #php time format
				$newoptions[$number]['error']				= strip_tags(stripslashes($_POST["king_rss_error_$number"]));
				$newoptions[$number]['rsshtml'] 			= stripslashes($_POST["king_rss_rsshtml_$number"]);
				$newoptions[$number]['titlehtml'] 			= stripslashes($_POST["king_rss_titlehtml_$number"]);
				$newoptions[$number]['stripads']			= isset($_POST["king_rss_stripads_$number"]);
				$newoptions[$number]['show_category']		= isset($_POST["king_rss_showcategory_$number"]);
				$newoptions[$number]['category_id']			= $_POST["king_rss_category_id_$number"];
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_rss_show_on_site_area_$number"]);
				$newoptions[$number]['show_not_on_site_area']= isset($_POST["king_rss_show_not_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_rss_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_rss_site_area_id_$number"];
				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_rss_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_rss_widget_$number"]);

			}
		}
		if ( $options != $newoptions ){

			$options = $newoptions;
			update_option('widget_king_rss', $options);
		}
		$title 				= htmlspecialchars($options[$number]['title'], ENT_QUOTES);
		$rss_url	 		= htmlspecialchars($options[$number]['rss_url'], ENT_QUOTES);
		$max_items			= $options[$number]['max_items'];
		$cache_time 		= $options[$number]['cache_time'];
		$nosort				= $options[$number]['nosort'] ? 'checked' : '';
		$shortdesc 			= $options[$number]['shortdesc'];
		$showdate			= $options[$number]['showdate'];
		$error				= htmlspecialchars($options[$number]['error'], ENT_QUOTES);
		$titlehtml			= htmlspecialchars($options[$number]['titlehtml'], ENT_QUOTES);
		$rsshtml 			= htmlspecialchars($options[$number]['rsshtml'], ENT_QUOTES);
		$stripads 			= $options[$number]['stripads'] ? 'checked' : '';

		$show_category		= $options[$number]['show_category'] ? 'checked' : '';
		$category_id		= $options[$number]['category_id'];
		$show_on_site_area	= $options[$number]['show_on_site_area'] ? 'checked' : '';
		$show_not_on_site_area	= $options[$number]['show_not_on_site_area'] ? 'checked' : '';
		$site_area			= $options[$number]['site_area'];
		$site_area_id		= $options[$number]['site_area_id'];
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));
		$after_widget		= stripslashes(htmlentities($options[$number]['after_widget']));

		# Here is the form segment. Notice that I have outsourced the form elements to be a little cleaner
         echo king_get_tab_start('rss'.$number, array(
								__('Basic', 'widgetKing'),
								__('Advanced', 'widgetKing'),
								__('Show', 'widgetKing'),
								__('Export', 'widgetKing')
								));
		# show rss URL
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_url_$number",
				'Description' 	=>  __('The Feed URL', 'widgetKing'),
				'Label_Title' 	=> __('Insert your rss Feed URL.', 'widgetKing'),
				'Value' 		=> $rss_url,
				'Class'			=>'big'));
		# show child rss
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_max_items_$number",
				'Description' 	=>  __('Feed Items to show', 'widgetKing'),
				'Label_Title' 	=> __('How many Articles or Items do you want to show from the Feed. Of course this is a numeric Value!', 'widgetKing'),
				'Value' 		=> $max_items,
				'Max' 			=>'3',
				'Class'			=>'small'));
		# show only short description
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_shortdesc_$number",
				'Description' 	=>  __('Show only the first No. Letters ', 'widgetKing'),
				'Label_Title' 	=> __('How much of the Feed Article Text should be shown? Of course this is a numeric Value! f.ex. Set to 100 to show the first 100 letters.', 'widgetKing'),
				'Value' 		=> $shortdesc,
				'Max' 			=>'3',
				'Class'			=>'small'));
		# show Date
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_showdate_$number",
				'Description' 	=>  __('Dateformat (php time)', 'widgetKing'),
				'Label_Title' 	=> __('Enter the date  in php time() format. If left empty none is shown. You can check this f.ex. in your blog settings. or juggle with j F Y, g:i a', 'widgetKing'),
				'Value' 		=> $showdate,
				'Max' 			=>'20'));
		# show Error Message
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_error_$number",
				'Description' 	=>  __('Error Message', 'widgetKing'),
				'Label_Title' 	=> __('The error Message shown if the feed cant be fetched', 'widgetKing'),
				'Value' 		=> $error,
				'Max' 			=>'60'));
        # sort order
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_rss_nosort_$number",
				'Description' 	=> __('Do not sort Feed', 'widgetKing'),
				'Label_Title' 	=>  __('Prevent sorting the feed by the time of its items. f.ex. used when grabbing a google calendar feed', 'widgetKing'),
				'Value' 		=>$nosort));
		# strip Ads
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_rss_stripads_$number",
				'Description' 	=> __('Strip Ads', 'widgetKing'),
				'Label_Title' 	=>  __('Strip out Feed-Ads from Google/Pheedo/Doubleclicks.', 'widgetKing'),
				'Value' 		=>$stripads));
        #cache time
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_cache_time_$number",
				'Description' 	=> __('Refresh the feed every Minutes', 'widgetKing'),
				'Label_Title' 	=>  __('The Feed will be refreshed every given Minutes. If the feed does not update frequently set a high value, else make it grab the feed more often. Default is 60 minutes, if left empty', 'widgetKing'),
				'Class'			=>'small',
				'Value' 		=>$cache_time));

		# set to defaults
		echo king_get_checkbox_p(array(
				'Label_Id_Name' =>"king_rss_defaults_$number",
				'Description' 	=>  __('Insert default Options', 'widgetKing'),
				'Label_Title' 	=> __('Set all Widget Options to (hopefully failsave) Defaults. You should definitly try out some more of the HTML Placeholders for the RSS','widgetKing')
				));
		# copy
		echo king_get_select_p(array(
			'Label_Id_Name' => "king_rss_copy_$number",
			'Description' 	=> __('Copy Settings from Widget No.', 'widgetKing'),
			'Label_Title' 	=> __('Choose a Widget Number from which you want to copy the settings into this one. Make sure to choose the right widget, with some Options in it!', 'widgetKing'),
			'select_options'=> array('No','1', '2', '3', '4', '5', '6', '7', '8', '9','10','11', '12', '13', '14', '15', '16', '17', '18','19','20')));

		# show title
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_rss_title_$number",
				'Description' 	=> __('Internal Widget Title', 'widgetKing'),
				'Label_Title' 	=> __('The title inside this widget admin page. Is shown next to the widgets name, if you applied my Widget Title Hack -> try to google on that. Or search the MP:Blog', 'widgetKing'),
				'Value' 		=> $title,
				'Max' 			=>'50'));
		# devider
		echo king_get_tab_section('rss'.$number.'-1');
		#before widget
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_before_rss_widget_$number",
				'Description' 	=> __('HTML before widget', 'widgetKing'),
				'Label_Title' 	=>  __('HTML which opens this widget. Can be something linke ul with a class, depending on your css and Theme', 'widgetKing'),
				'Value' 		=>$before_widget,
				'Class' 		=> 'big'));
		# rss template html
		echo king_get_textarea_p(array(
				'Label_Id_Name' =>"king_rss_titlehtml_$number",
				'Description' 	=>  __('RSS Title HTML Template', 'widgetKing'),
				'Label_Title' 	=> __('The HTML that will be used to format the RSS-Title output. There are several Placeholders wich can be used. <br />%link% - Link to Website <br />%title% - RSS Title<br />%descr% - RSS Description<br />%rssurl% - Feed URL<br />%rssicon% - RSS Icon <br />%feedimg% - Feed Image if provided by Feed.', 'widgetKing'),
				'Value' 		=> $titlehtml

				));
		# rss template html
		echo king_get_textarea_p(array(
				'Label_Id_Name' =>"king_rss_rsshtml_$number",
				'Description' 	=>  __('RSS Item formatting HTML Template', 'widgetKing'),
				'Label_Title' 	=> __('The HTML that will be used to format each Item of the RSS output. There are several Placeholders wich can be used: <br />%title% - Item Title<br />%date% - Item Date<br />%link% - Link to Item Title<br />%text% - Item Text<br />%category% - Item Categories if provided delimited by | <br />%author% - Item Author if provided', 'widgetKing'),
				'Value' 		=> $rsshtml
				));
		#after widget
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"king_after_rss_widget_$number",
				'Description' 	=> __('HTML after widget', 'widgetKing'),
				'Label_Title' 	=>__('HTML which closes this widget. Can be something linke /ul , depending on what you set as HTML before', 'widgetKing'),
				'Value' 		=>$after_widget,
				'Class' 		=> 'big'));
		#devider
		echo king_get_tab_section( 'rss'.$number.'-2');
		#Where To Show Options Panel
		widget_king_where_to_show('rss',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);
		echo king_get_tab_section('rss'.$number.'-3');
		king_get_dump_options('rss',$number,'widget_rss_categories');
		echo king_get_hidden("king_rss_submit_$number",'1',"king_rss_submit_$number");
		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function widget_king_rss_setup() {
		$options = $newoptions = get_option('widget_king_rss');

		if ( isset($_POST['king_rss_number_submit']) )
		{
			$number = (int) $_POST['king_rss_number'];
			if ( $number > 20 ) $number = 20;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_rss', $options);
			widget_king_rss_register($options['number']);
		}
	}

	/**
	* @desc Admin Form to select number of categories
	* @author Georg Leciejewski
	*/
	function widget_king_rss_page() {

		$options = $newoptions = get_option('widget_king_rss');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King RSS Boxes', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many RSS Boxes would you like? ', 'widgetKing');
		echo king_get_select("king_rss_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9','10','11', '12', '13', '14', '15', '16', '17', '18','19','20'), 'king_rss_number' );
		echo king_get_submit('king_rss_number_submit','','king_rss_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_rss_register()	{
		global $kingwidgetversion;
                                               
		$options = get_option('widget_king_rss');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 20 ) $number = 20;
		for ($i = 1; $i <= 20; $i++) {
			$name = array('King RSS %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_rss' : /* unregister */ '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_rss_control' : /* unregister */ '', 450, 450, $i);
		}

		add_action('sidebar_admin_setup', 'widget_king_rss_setup');
		add_action('sidebar_admin_page', 'widget_king_rss_page');

	}
	widget_king_rss_register();
	include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');

}# end init function
add_action('plugins_loaded', 'widget_king_rss_init');

/**
* @desc Version Check Heading
*/
function widget_king_rss_version() {
	king_version_head('King_RSS_Widget',KINGRSSVERSION);
}
add_action('admin_head','widget_king_rss_version');


# Snap into image mode, if necessary
if (isset($_GET['i']) && !empty($_GET['i'])) {
	$feed = new SimplePie();
	$feed->bypass_image_hotlink();
	$feed->init();
}

/**
* @desc Output Parsing of RSS
* @author Georg Leciejewski
*/
//function kingRssOutput($rss_url,$max_items,$clearcache,$shortdesc='',$showdate,$error,$rsshtml,$titlehtml,$stripads) {
function kingRssOutput($data) {

	$feed = new SimplePie();
	$feed->feed_url($data['rss_url']);
	$path = explode($_SERVER["SERVER_NAME"], get_bloginfo('wpurl'));
	$feed->cache_location($_SERVER['DOCUMENT_ROOT'] . $path[1] . "/wp-content/cache");


	if(!empty($data['cache_time'])) $feed->max_minutes = $data['cache_time'];
	if(!empty($data['nosort'])) $feed->order_by_date = false;

	if (!empty($data['stripads']))	$feed->strip_ads(1);

	$feed->bypass_image_hotlink();
	$feed->bypass_image_hotlink_page($path[1] . "/index.php"); #if images in feed are protected
	$success = $feed->init();

	if ($success && $feed->data) {
		$output = '';
		$replace_title_vars[0] = $feed->get_feed_link();
		$replace_title_vars[1] = $feed->get_feed_title();
		$replace_title_vars[2] = $feed->get_feed_description();
		$replace_title_vars[3] = $data['rss_url'];
		$replace_title_vars[4] = get_settings('siteurl').'/wp-content/plugins/king-framework/images/rss.png';

		if($feed->get_image_exist() == true ){
			$replace_title_vars[5] = $feed->get_image_url() ;
		}
		$search_title_vars =array('%link%','%title%','%descr%','%rssurl%','%rssicon%','%feedimg%');
		#parse template placeholders
		$output .= str_replace($search_title_vars, $replace_title_vars, $data['titlehtml']);

		$max = $feed->get_item_quantity();
		if (!empty($data['max_items'])) $max = min($data['max_items'], $feed->get_item_quantity());

		for($x=0; $x<$max; $x++) {
			$item = $feed->get_item($x);
			$replace_vars[0] = stupifyEntities($item->get_title());
			$replace_vars[1] = $item->get_permalink();
			$replace_vars[2] = $item->get_date($data['showdate']);
			$replace_vars[3] = stupifyEntities($item->get_description());

			if($item->get_categories() != false){
				$categories = $item->get_categories();
				$replace_vars[4] =  implode(" | ", $categories);
			}

			if($item->get_author(0) != false){
				$author = $item->get_author(0);
				$replace_vars[5] =   $author->get_name();
			}

			# cut article text to length ... do the butcher
			if (!empty($data['shortdesc'])) {
				$suffix = '...';
				$short_desc = trim(str_replace("\n", ' ', str_replace("\r", ' ', strip_tags(stupifyEntities($item->get_description())))));
				$desc = substr($short_desc, 0, $data['shortdesc']);
				$lastchar = substr($desc, -1, 1);
				if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?') $suffix='';
					$desc .= $suffix;

				$replace_vars[3] = $desc;

			}
			$search_vars = array('%title%','%link%','%date%','%text%','%category%','%author%');
			#parse template placeholders
			$output .= str_replace($search_vars, $replace_vars, $data['rsshtml']);
		}

	}else{
		if (!empty($data['error'])) $output = $data['error'];
		else if (isset($feed->error)) $output = $feed->error;

	}
	return $output;
}

# SmartyPants 1.5.1 changes rolled in May 2004 by Alex Rosenberg, http://monauraljerk.org/smartypants-php/
function stupifyEntities($s = '') {
	$inputs = array('&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8230;', '&#91;', '&#93;');
	$outputs = array('-', '--', "'", "'", '"', '"', '...', '[', ']');
	$s = str_replace($inputs, $outputs, $s);
	return $s;
}
?>
