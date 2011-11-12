<?php
/*

Plugin URI: http://www.blog.mediaprojekte.de/
Description: Advanced Login Box Widget including redirect Options
Author: Georg Leciejewski
Version: 1.0
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
define("KINGLOGINVERSION",  "050");

require_once(ABSPATH . 'wp-content/plugins/king-framework/library/king_widget_functions.php');
                                
/**
* @desc init Put functions into one big function we'll call at the plugins_loaded action.
* This ensures that all required plugin functions are defined.
* @author Georg Leciejewski
*/
function widget_king_login_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	/**
	* @desc Output of plugin composing the list_cats function call
	* @author Georg Leciejewski
	*/
	function widget_king_login($args, $number = 1) {

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
			extract($args,EXTR_PREFIX_ALL,"default");
			$options 			= get_option('widget_king_login');
			$title 				= $options[$number]['title'];
			$redirect_url		= stripslashes($options[$number]['redirect_url']);
            $postlink			= $options[$number]['postlink'] ? 1 : 0;
			$myposts			= $options[$number]['myposts']? 1 : 0;
			$logout				= $options[$number]['logout'] ? 1 : 0;

			$show_category		= $options[$number]['show_category'] ? 1 : 0;
			$category_id		= $options[$number]['category_id'];
			$show_on_site_area	= $options[$number]['show_on_site_area'] ? 1 : 0;
			$show_not_on_site_area	= $options[$number]['show_not_on_site_area'] ? 1 : 0;
			$site_area_id		= $options[$number]['site_area_id'];
			$site_area			= $options[$number]['site_area'];
			$before_widget		= empty($options[$number]['before_widget']) ? $default_before_widget : stripslashes($options[$number]['before_widget']);
			$before_widget_title= empty($options[$number]['before_widget_title']) ? $default_before_title : stripslashes($options[$number]['before_widget_title']);
			$after_widget_title = empty($options[$number]['after_widget_title'] ) ? $default_after_title : stripslashes($options[$number]['after_widget_title']) ;
			$after_widget 		= empty($options[$number]['after_widget']) ? $default_after_widget : stripslashes($options[$number]['after_widget']) ;

		if( !empty($show_category) )
		{
			$post = $wp_query->post;
			if ( in_category($category_id) )
			{
				# if in category
				echo '<!-- Start King Login ' . $number . ' -->'."\n";
                echo $before_widget."\n";
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				king_login_output($redirect_url,$postlink,$logout,$myposts);
				echo $after_widget."\n";
				$already_out = 1;
			}
		}#end site area if


		if( !empty($show_on_site_area) )
		{ # sitearea Output

			if ( $site_area($site_area_id) && $already_out != 1)
			{
				echo '<!-- Start King Login ' . $number . ' -->'."\n";
                echo $before_widget."\n";
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				king_login_output($redirect_url,$postlink,$logout,$myposts);
				echo $after_widget."\n";
				echo '<!-- End King Login -->'."\n";
				$already_out = 1;
			}

		}
		elseif(!empty($show_not_on_site_area))
		{
            if (!$site_area($site_area_id) && $already_out != 1)
            {  	#if not in the site area
				echo '<!-- Start King Login ' . $number . ' -->'."\n";
                echo $before_widget."\n";
				echo $before_widget_title."\n";
				echo $title ."\n";
				echo $after_widget_title."\n";
				king_login_output($redirect_url,$postlink,$logout,$myposts);
				echo $after_widget."\n";
				echo '<!-- End King Login -->'."\n";
				}
		}


		if(empty($show_not_on_site_area) && empty($show_on_site_area) && empty($show_category))
		{
			echo '<!-- Start King Login ' . $number . ' -->'."\n";
            echo $before_widget."\n";
			echo $before_widget_title."\n";
			echo $title ."\n";
			echo $after_widget_title."\n";
			king_login_output($redirect_url,$postlink,$logout,$myposts);
			echo $after_widget."\n";
			echo '<!-- End King Login -->'."\n";
		}

	}#end function widget_king_login

	/**
	* @desc Output of plugin?s editform in te adminarea
	* @author Georg Leciejewski
	*/
	function widget_king_login_control($number=1)
	{
		// Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('widget_king_login');
		if ( $_POST["king_login_submit_$number"] ){
			//if defaults are choosen
			if ( isset($_POST["king_login_defaults_$number"]) )
			{

			}
			elseif( !empty($_POST["king_login_dump_$number"]) && isset($_POST["king_login_usedump_$number"]))
			{
				$newoptions[$number] = king_read_options($_POST["king_login_dump_$number"]);
			}
			else
			{// insert new form values

				$newoptions[$number]['title']				= strip_tags(stripslashes($_POST["king_login_title_$number"]));
				$newoptions[$number]['redirect_url']		= html_entity_decode($_POST["king_login_redirect_url_$number"]);
				$newoptions[$number]['postlink']			= isset($_POST["king_login_postlink_$number"]);
				$newoptions[$number]['logout']				= isset($_POST["king_login_logout_$number"]);
				$newoptions[$number]['postlink']			= isset($_POST["king_login_postlink_$number"]);
				$newoptions[$number]['myposts']				= isset($_POST["king_login_myposts_$number"]);
				$newoptions[$number]['category_id']			= $_POST["king_login_category_id_$number"];
				$newoptions[$number]['show_on_site_area']	= isset($_POST["king_login_show_on_site_area_$number"]);
				$newoptions[$number]['show_not_on_site_area'] = isset($_POST["king_login_show_not_on_site_area_$number"]);
				$newoptions[$number]['site_area']			= $_POST["king_login_site_area_$number"];
				$newoptions[$number]['site_area_id']		= $_POST["king_login_site_area_id_$number"];
				$newoptions[$number]['before_widget']		= html_entity_decode($_POST["king_before_login_widget_$number"]);
				$newoptions[$number]['after_widget']		= html_entity_decode($_POST["king_after_login_widget_$number"]);
				$newoptions[$number]['before_widget_title']	= html_entity_decode($_POST["king_before_login_widget_title_$number"]);
				$newoptions[$number]['after_widget_title']	= html_entity_decode($_POST["king_after_login_widget_title_$number"]);

			}
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_login', $options);
		}

		$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
		$redirect_url		= $options[$number]['redirect_url'];
		$postlink			= !empty($options[$number]['postlink'])? 'checked' : '';
		$logout				= !empty($options[$number]['logout'])? 'checked' : '';
		$myposts			= !empty($options[$number]['myposts'])? 'checked' : '';

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


		echo king_get_tab_start('login'.$number, array(
											 __('Basic','kingplugin'),
											 __('Show','kingplugin'),
											 __('HTML','kingplugin'),
											 __('Export','kingplugin') ) );
		echo king_get_textbox_p(array(
			'Label_Id_Name' =>"king_login_title_$number",
			'Description' 	=> __('Title', 'widgetKing'),
			'Label_Title' 	=> __('The title above your Login Widget', 'widgetKing'),
			'Value' 		=> $title,
			'Size' 			=>'20',
			'Max' 			=>'50'));
		echo king_get_textbox_p(array(
			'Label_Id_Name' =>"king_login_redirect_url_$number",
			'Description' 	=> __('Login Redirect URL', 'widgetKing'),
			'Label_Title' 	=> __('The URL where the User will be redirected to after logged on. If empty the user will be redirected to the page he logged in from. You maybe want to set this to your Backoffice URL or any other special Page. Just copy the URL from your Browser in here.', 'widgetKing'),
			'Value' 		=> $redirect_url,
			'Size' 			=>'20',
			'Max' 			=>'50'));
		echo king_get_checkbox_p(array(
			'Label_Id_Name' =>"king_login_postlink_$number",
			'Description' 	=>  __('Show New Post Link', 'widgetKing'),
			'Label_Title' 	=> __('A link which brings you in the admin area to post a new article.','widgetKing'),
			'Value' 		=>$postlink));

        echo king_get_checkbox_p(array(
			'Label_Id_Name' =>"king_login_myposts_$number",
			'Description' 	=>  __('Show Link to My Articles', 'widgetKing'),
			'Label_Title' 	=> __('Shows a Link to a list with all your articles.','widgetKing'),
			'Value' 			=>$myposts));

		echo king_get_checkbox_p(array(
			'Label_Id_Name' =>"king_login_logout_$number",
			'Description' 	=>  __('Show Logout Link', 'widgetKing'),
			'Label_Title' 	=> __('A link which Logs You out. Only shown when logged in of course.','widgetKing'),
			'Value' 		=>$logout));

		echo king_get_tab_section('login'.$number.'-1');

		widget_king_where_to_show('login',$number,$show_category,$category_id,$show_on_site_area,$show_not_on_site_area,$site_area,$site_area_id);

  		echo king_get_tab_section('login'.$number.'-2');

		widget_king_htmloptions('login',$number,$before_widget,$before_widget_title,$after_widget_title,$after_widget);

		echo king_get_tab_section('login'.$number.'-3');
        king_get_dump_options('login',$number,'widget_king_login');
		echo king_get_hidden("king_login_submit_$number",'1',"king_login_submit_$number");

		echo king_get_tab_end();
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function widget_king_login_setup()
	{
		$options = $newoptions = get_option('widget_king_login');

		if ( isset($_POST['king_login_number_submit']) )
		{
        	$number = (int) $_POST['king_login_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_king_login', $options);
			widget_king_login_register($options['number']);
		}
	}
	/**
	* @desc Admin Form to select number of logins
	* @author Georg Leciejewski
	*/
	function widget_king_login_page()
	{
        /*$framework_options = get_option('king_framework');
        for ($i = 1; $i <= $framework_options['widgets_number']; $i++) {
			$number[]=$i;
		}*/
		$options = $newoptions = get_option('widget_king_login');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('King Login', 'widgetKing'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Login Widgets do you need?', 'widgetKing');
		echo king_get_select("king_login_number",$options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9'), 'king_login_number' );
		echo king_get_submit('king_login_number_submit','','king_login_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function widget_king_login_register()
	{
		$options = get_option('widget_king_login');
		$number = $options['number'];
		//$framework_options = get_option('king_framework');
		//for ($i = 1; $i <= $framework_options['widgets_number']; $i++) {
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++)
		{
			$name = array('King Login %s', null, $i);
			register_sidebar_widget($name, $i <= $number ? 'widget_king_login' :  '', $i);
			register_widget_control($name, $i <= $number ? 'widget_king_login_control' :  '', 450, 400, $i);
		}
		add_action('sidebar_admin_setup', 'widget_king_login_setup');
		add_action('sidebar_admin_page', 'widget_king_login_page');
	}
	widget_king_login_register();
	include_once (ABSPATH . 'wp-content/plugins/king-framework/library/form.php');

}// end init function

add_action('plugins_loaded', 'widget_king_login_init');

/**
* @desc Version Check Heading
*/
function widget_king_login_version()
{
	king_version_head('King_Login_Widget',KINGLOGINVERSION);
}
add_action('admin_head','widget_king_login_version');

/**
*@desc the output of the widget
*/
function king_login_output($redirect='',$postlink='',$logout='',$myposts='')
{
	global $user_ID, $user_identity,$user_login;
	get_currentuserinfo();

	if (!$user_ID)
	{
		echo '
		<form name="loginform" id="loginform" action="' . get_settings('siteurl') . '/wp-login.php" method="post">
		<div><label>' . __('Login') . ':<br />
		<input type="text" name="log" id="log" value="" size="20" tabindex="7" /></label><br />
		<label>' .  __('Password')  . ':<br />
		<input type="password" name="pwd" id="pwd" value="" size="20" tabindex="8" /></label><br />
		<input type="hidden" name="rememberme" value="forever" />
		<input type="submit" name="submit" value="' . __('Login')  . ' &raquo;" tabindex="9" /><br />';
		if(!empty($redirect))
		{
			echo '<input type="hidden" name="redirect_to" value="' .$redirect. '"/>';
		}
		else
		{
			echo '<input type="hidden" name="redirect_to" value="' . $_SERVER['REQUEST_URI']  . '"/>';
		}
		echo '</div></form> '."\n";
		wp_register('', '');
	}
	else
	{
		echo "<ul>";
		echo '<li>';
		printf(__('Howdy, <strong>%s</strong>.'), $user_identity);
		echo '</li>'."\n";
		wp_register();
		echo "\n";
		if(!empty($postlink))
		{
			echo '<li><a href="'. get_settings('siteurl') . '/wp-admin/post.php">'. __('Write Post') . '</a></li>'."\n";
		}
        if(!empty($myposts))
        {
			echo '<li><a href="'. get_author_posts_url($user_ID) . '">'. __('My Articles') . '</a></li>'."\n";
		}
		if(!empty($logout))
		{
			echo '<li><a href="'. get_settings('siteurl') . '/wp-login.php?action=logout&amp;redirect_to=' . $_SERVER['REQUEST_URI'] .'">'. __('Logout') . '</a></li>'."\n";
		}

		echo "</ul>";
	}
}//end output

/*function frontend_edit($number)
{
	?>
    <form id="widgetadmin" method="post" action="wp-content/plugins/king-widgets/plugins/widgets.php">
		<?php  widget_king_login_control($number); ?>
		<p class="submit">
			<input type="submit" value="<?php _e('Save changes'); ?> &raquo;" />
		</p>
	</form>
	</div>
	<?php
}*/
?>
