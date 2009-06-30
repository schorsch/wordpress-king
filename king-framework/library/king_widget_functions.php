<?php
/*
King Widgets common used functions and includes. Mostly for the Widgets Admin and Options Area.
Author: Georg Leciejewski
Version: 0.70
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
include_once 'form.php';
                                      

/**
* @desc Output of king widget css and js in admin head area. Is included in every king widget
* @author Georg Leciejewski         
*/
 
function widget_admin_head() {
  #$plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );  
  $js_dir = get_settings('siteurl').'/wp-content/plugins/king-framework/js/';
  $css_dir = get_settings('siteurl').'/wp-content/plugins/king-framework/css/';    
  //add the javascript to widgets admin page                           
  wp_enqueue_script('king_widget_script', $js_dir.'king_widget.js', array('jquery'));
  //add widget css containing tab styles
  echo '<link rel="stylesheet" href="'.$css_dir.'/king_widget.css" type="text/css" />';     
  // the translations
  load_plugin_textdomain('widgetKing','/wp-content/plugins/king-framework/lang');

}
add_action( "admin_print_scripts-widgets.php", 'widget_admin_head' );

/**
* @desc Where should a widget be shown on. Echo?s a couple of form fields for the Widget Options panel
* @author Georg Leciejewski
*/
function widget_king_where_to_show($widgetname,$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id)
{
	//show only in category
	echo king_get_checkbox_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_showcategory_'.$number.'',
			'Description' 	=>  __('Show only in Category', 'widgetKing'),
			'Label_Title' 	=>  __('The box is only shown on pages belonging to the given Category. This Switch can be combined with Show(Not) on Special Area.<br /> This gives you more flexibility. f.ex. You can show a box on the Frontpage and inside a category or show a box in a category and everywhere else but the home-page. ', 'widgetKing'),
			'Value' 		=>$show_category));

	//Category ID
	echo king_get_textbox_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_category_id_'.$number.'',
			'Description' 	=>  __('Category ID', 'widgetKing'),
			'Label_Title' 	=>  __('The category (id) in which the box will be shown. You can insert the ID comma seperated 1,2,3', 'widgetKing'),
			'Value' 		=>$category_id));
	//show only on Special Page Area
	echo king_get_checkbox_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_show_on_site_area_'.$number.'',
			'Description' 	=> __('Show only on Special Page Area', 'widgetKing'),
			'Label_Title' 	=>  __('The box is only shown on Area of the following select. Dont use together with following Show-Not-in Area checkbox!', 'widgetKing'),
			'Value' 		=>$show_on_site_area));
	//show not on Special Page Area
	echo king_get_checkbox_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_show_not_on_site_area_'.$number.'',
			'Description' 	=> __('DO NOT show on Special Page Area', 'widgetKing'),
			'Label_Title' 	=>  __('The box is shown on all Areas BUT the one from the following selectbox or the ID/URL/Title field below. !! Do NOT use together with previous checkbox Show on Site Area !!', 'widgetKing'),
			'Value' 		=>$show_not_on_site_area));
	// ID Name of special website area
	echo king_get_select_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_site_area_'.$number.'',
			'Description' 	=> __('Website Area', 'widgetKing'),
			'Label_Title' 	=> __('Select the special Area on the page where the box is to be diplayed on. A full Description on each can be found in the Wordpress Codex -> Conditional_Tags.', 'widgetKing'),
			'select_options'=>array('is_home', 'is_page','is_single','is_category','is_archive','is_search','is_author','is_404'),
			'Value' 		=>$site_area));
	//Item  ID
	echo king_get_textbox_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_site_area_id_'.$number.'',
			'Description' 	=> __('Area ID/Slug/Title', 'widgetKing'),
			'Label_Title' 	=>  __('The ID, Title or Slug of the Page Area(depending on type choosen) the box is to be shown on. You can enter a comma seperated list. Only needed for single, page and category. If left empty the box will appear on all f.ex. single pages. Definitly READ the Wordpress Codex -> Conditional_Tags.', 'widgetKing'),
			'Value' 		=>$site_area_id
			));

}// end where to Show

/**
* @desc The HTML Options fields for the Admin Area
* @author Georg Leciejewski
*/
function widget_king_htmloptions($widgetname,$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget)
{
	//before widget
	echo king_get_textbox_p(array(
			'Label_Id_Name' =>'king_before_'.$widgetname.'_widget_'.$number.'',
			'Description' 	=> __('HTML before widget', 'widgetKing'),
			'Label_Title' 	=>  __('HTML which opens this widget. Can be something linke ul with a class, depending on your css and Theme', 'widgetKing'),
			'Value' 		=>$before_widget,
			'Class' 		=> 'big'));

	//before title
	echo king_get_textbox_p(array(
			'Label_Id_Name' 	=>'king_before_'.$widgetname.'_widget_title_'.$number.'',
			'Description' 	=> __('HTML before widget Title', 'widgetKing'),
			'Label_Title' 	=> __('HTML before the widget title. Can be something linke strong or h2 with a class, depending on your css and Theme', 'widgetKing'),
			'Value' 			=>$before_widget_title,
			'Class' 		=> 'big'));

	//after title
	echo king_get_textbox_p(array(
			'Label_Id_Name' =>'king_after_'.$widgetname.'_widget_title_'.$number.'',
			'Description' 	=>  __('HTML after widget Title', 'widgetKing'),
			'Label_Title' 	=> __('HTML after the widget title but before the text list output. Can be something linke /strong ul or /h2 ul , depending on what you set as before-title', 'widgetKing'),
			'Value' 		=>$after_widget_title,
			'Class' 		=> 'big'));

	//after widget
	echo king_get_textbox_p(array(
			'Label_Id_Name' =>'king_after_'.$widgetname.'_widget_'.$number.'',
			'Description' 	=> __('HTML after widget', 'widgetKing'),
			'Label_Title' 	=>__('HTML which closes this widget. Can be something linke /ul , depending on what you set as HTML before', 'widgetKing'),
			'Value' 		=>$after_widget,
			'Class' 		=> 'big'));

}//end widgethtml

/**
* @desc The HTML Options fields to show the login dump
* @author Georg Leciejewski
* @param string $widgetname Short Name of the widget ie: text for king_text_widget
* @param int $number Number of the widget
* @param string $widget_longname Full widget Name ie: king_text_widget . is used for the get option call in king_dump_options
*/
function king_get_dump_options($widgetname,$number,$widget_longname)
{

	echo king_get_textarea_p(array(
			'Label_Id_Name' =>'king_'.$widgetname.'_dump_'.$number.'',
			'Description' 	=>  __('Current Configuration Code', 'widgetKing'),
			'Label_Title' 	=> __('Copy this Configuration code into another widget, send it to your friends or paste new config options here.', 'widgetKing'),
			'Value' 		=> stripslashes(king_dump_options($widget_longname,$number)),
			'Class' 		=> 'big'
			));

	echo king_get_checkbox_p(array(
		'Label_Id_Name' =>'king_'.$widgetname.'_usedump_'.$number.'',
		'Description' 	=>  __('Use Config Code for new settings', 'widgetKing'),
		'Label_Title' 	=> __('Your inserted config code will be taken to set the widgets options. <br />!CAREFULL only paste Config Options from SAME WIDGET TYPE! <br />If the text is in the wrong format it can f*** up your Options. In such a case you can empty the field to reset the options.','widgetKing')
		));
}


/**
* @desc Admin Header for Version Check
* @author georg leciejewski /  Per Soderlind
* @param string $widgetname full Widgetname ie: King_Text_Widget
* @param int $localversion widget Version Number
*/
function king_remote_version_check($widgetname,$localversion)
{
	require_once(ABSPATH . WPINC . '/class-snoopy.php');
	if (class_exists(snoopy))
	{
		$client = new Snoopy();
		$client->_fp_timeout = 10;
		if (@$client->fetch('http://website-king.com/versiontrack/'.$widgetname.'.txt') === false)
		{
			return -1;
		}
		$remote = $client->results;
		if (!$remote || strlen($remote) > 8 )
		{
			return -1;
		}
		if (intval($remote) > intval($localversion))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}

/**
* @desc Admin Header for Version Check on Pluginpage
* @author georg leciejewski /  Per Soderlind
* @param string $widgetname full Widgetname ie: King_Text_Widget
* @param int $localversion widget Version Number
*/
function king_version_head($widgetname,$localversion)
{
	if ((strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false) && (king_remote_version_check($widgetname,$localversion) == 1))
	{
		$alert = "\n";
		$alert .= "\n<script type='text/javascript'>";
		$alert .= "\n//<![CDATA[";
		$alert .= "\nfunction alertNewVersion" . $widgetname . "() {";
		$alert .= "\n	pluginname = '" . $widgetname . "';";
		$alert .= "\n	allNodes = document.getElementsByClassName('name');";
		$alert .= "\n	for(i = 0; i < allNodes.length; i++) {";
		$alert .= "\n			var regExp=/<\S[^>]*>/g;";
		$alert .= "\n	    temp = allNodes[i].innerHTML;";
		$alert .= "\n	    if (temp.replace(regExp,'') == pluginname) {";
		$alert .= "\n		    Element.setStyle(allNodes[i].getElementsByTagName('a')[0], {color: '#f00'});";
		$alert .= "\n		    new Insertion.After(allNodes[i].getElementsByTagName('strong')[0],'<br/><small>" .  __("new version available","widgetKing") . "</small>');";
		$alert .= "\n	  	}";
		$alert .= "\n	}";
		$alert .= "\n}";
		$alert .= "\naddLoadEvent(alertNewVersion" . $widgetname . ");";
		$alert .= "\n//]]>";
		$alert .= "\n</script>";
		$alert .= "\n";
		echo $alert;
	}
}


/**
* @desc Dumps Plugin Options Array into a delimited string with newlines between key/value Pairs. can be use in textareas
* @author Georg Leciejewski
* @param string $plugin - Name of the Plugin used in the get_option call
* @param int $number	- if used with multi-widgets or if the plugin has an numbered instance array you need to set this to get only the sub array values
* @return | delimited key value pairs with newlines. to be used in textareas
*/
function king_dump_options($plugin,$number='')
{
	$options = get_option($plugin);

	$output = '';
	if(!empty($number) && !empty($options[$number]) )
	{
		foreach($options[$number] as $key => $value )
		{
			if(!empty($options[$number][$key]))  $output.= $key.'|'.$value."\n";
		}
	}
	elseif(!empty($options))
	{
		foreach($options as $key => $value )
		{
			if(!empty($options[$key]))$output.= $key.'|'.$value."\n";
		}
	}
	else
	{
		return;
	}
	return $output;
}

/**
* @desc Read Plugin Options from String back into Array
* @author Georg Leciejewski
* @param string $input 	- String from textarea with new options
* @param int $number 	- if used with multi-widgets or if the plugin has an numbered instance array you need to set this
* 						to get only the sub array values. could not be save .. hab some probs
* @return array for the update_option call
*/
function king_read_options($input,$number='')
{
	$line = explode("\n", $input);

	foreach($line as $key => $value)
	{
		$tmpoptions = explode('|', $value);

		if(!empty($number))
		{
			$newoptions[$number][$tmpoptions[0]] = $tmpoptions[1];
		}
		else
		{
			$newoptions[$tmpoptions[0]] = $tmpoptions[1];
		}
	}
	return $newoptions;
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
function king_in_category($categories)
{
	global $category_cache, $post;

	$cat_ids = explode(',', $categories);

	foreach($cat_ids as $cat_id)
	{
        if (in_category($cat_id) )
        {
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
