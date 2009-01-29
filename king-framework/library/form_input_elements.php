<?php
/*
Helper Function for creating widget admin form elemts. gets included by form.php
Author: Georg Leciejewski
Version: 0.71
URI: http://www.blog.mediaprojekte.de
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
/**
* @desc renders textbox form element
* @author Georg Leciejewski
* @param string $name the Name of the textbox required
* @param string $value the Value of the textbox required
* @param string $id the id of the textbox
* @param string $class the Class of the textbox
* @param int $size width of the box
* @param int $max maximum input size
* @return string whole textbox element
*/
function king_get_textbox($name, $value, $id='', $class='', $size='', $max='')
{
	$result ='<input type="text" name="' . $name . '" value="' . $value . '"';
	if(!empty($id))
	{
		$result .=' id="' . $id . '"';
	}
	if(!empty($class))
	{
		$result .=' class="' . $class . '"';
	}
	if(!empty($size))
	{
		$result .=' size="' . $size . '"';
	}
	if(!empty($max))
	{
		$result .=' maxlength="' . $max . '"';
	}

	$result .=' />'."\n";
	return 	$result;

}
/**
* @desc checkbox form element
* 		the if around the $value is kindof a hack. if you give the value "checked" the CHECKED atribute is automaticly added
* @author Georg Leciejewski
* @param string $name the Name of the checkbox required
* @param string $value the Value of the checkbox required
* @param string $id
* @param string $class
* @return string whole checkbox element
*/
function king_get_checkbox($name, $value, $id='', $class='',$checked='')
{
	$result ='<input type="checkbox"  name="' . $name . '" value="' . $value . '"' ;

	if(!empty($value))
	{
		if(strstr( $value, "checked"))
		{
			$result .=' CHECKED';
		}
	}

	if(!empty($id))
	{
		$result .=' id="' . $id . '"';
	}
    if(!empty($class))
	{
		$result .=' class="' . $class . '"';
	}

	if(!empty($checked))
	{
		$result .=' CHECKED"';
	}
	$result .=' />'."\n";
	return $result;

}
/**
* @desc textarea form element
* @author Georg Leciejewski
* @param string $name - the Name of the textarea required
* @param string $value - the Value of the textarea required
* @param string $id - the ID of the textarea
* @param string $k_col - colums of textarea
* @param string $k_rows - rows of the textarea
* @return string whole textarea element
*/
function king_get_textarea($name, $value, $id='', $class='', $cols='', $rows='')
{

	$result ='<textarea name="' . $name . '"';
    if(!empty($id))
	{
		$result .=' id="' . $id . '"';
	}
	if(!empty($cols))
	{
		$result .=' cols="' . $cols . '" ';
	}
	if(!empty($rows))
	{
		$result .=' rows="' . $rows . '"';
	}
	if(!empty($class))
	{
		$result .=' class="' . $class . '"';
	}
	$result .=' >'.$value.'</textarea>'."\n";
	return $result;

}

/**
* @desc selectbox form element
* @author Georg Leciejewski
* @param string $name - required Name of select
* @param string $value - required Value of select
* @param array $options - required Options passed as array if option=value than it shows as selected
* @param string $id - ID of select
* @return string whole select element
*/
function king_get_select($name, $value, $options, $id ='')
{
	$result = '<select size="1" name="' . $name . '"';

	if(!empty($id))
	{
		$result .= ' id="' . $id . '"';
	}
	$result .= '>'."\n";
	foreach ($options as $option)
	{
		($option == $value) ? $selected = ' selected="selected"' : $selected = '';

		$result .= '<option value="' . $option . '"' . $selected . '>' . $option . '</option>'."\n";
	}
	$result .= '</select>'."\n";
	return $result;
}

/**
* @desc Role selectbox form element
* @author Georg Leciejewski
* @param string $k_Name Name of select
* @param string $k_Value Value of select
* @param array $k_Options Options passed as array if option=value than it shows as selected
* @param string $k_Id ID of select
* @return string whole select element
*/
function king_get_roleselect($k_Name, $k_Value, $k_Options, $k_Id, $capability, $role)
{
	global  $wp_roles;
	$result = '<select size="1" name="' . $k_Name . '" id="' . $k_Id . '" >'."\n";

    foreach($wp_roles->role_names as $role_key => $name)
	{
		$selected = ($role == $role_key) ? ' selected="selected"' : '';
		$result .= "<option value=\"$role_key\"{$selected}>{$name}</option>\n";
	}

	$result .= '</select>'."\n";
    $result .=  '<input type="hidden" name="' . $k_Name . '" value="'.$role. '" />'."\n";
	//capability
	$result .= '<input type="hidden" name="' . $capability . '" value="'. $capability . '" />'."\n";
	return $result;
}
/**
* @desc Capabilities selectbox form element
* @author Georg Leciejewski
* @param string $name - Name of select
* @param string $value - Value of select
* @param array $k_Options Options passed as array if option=value than it shows as selected
* @param string $id - ID of select
* @return string whole select element
*/
function king_get_capabilities_select($name, $value, $id)
{
	global $wp_roles;
	$result .= '<select size="1" name="' . $name . '" id="' . $id . '" >'."\n";
    foreach($wp_roles->role_objects as $key => $role) {
		foreach($role->capabilities as $capability => $grant)
			$all_cap_names[$capability] = $capability;
	}

    foreach($all_cap_names as $key => $val)
	{
		$selected = ($value == $val) ? ' selected="selected"' : '';
		$result .= "<option value=\"$key\"{$selected}>{$val}</option>\n";
	}

	$result .= '</select>'."\n";

	return $result;

}

/**
* @desc hidden form element
* @author Georg Leciejewski
* @param $name string - required the name of the hidden element
* @param $value string - required the value of the hidden element
* @param $id string -  the value of the hidden element
* @return string whole hidden element
*/
function king_get_hidden($name, $value, $id='')
{
	$result = '<input type="hidden" name="' . $name . '" value="' . $value . '"';
    if(!empty($id))
	{
		$result .=' id="'.$id.'" ';
	}
	$result .= " />\n";

	return $result;

}

/**
* @desc Submit Button for form element
* @author Georg Leciejewski
* @param $k_Id string
* @param $name string
* @param $k_Value string
* @return string  submit element
*/
function king_get_submit($name, $value, $id)
{
if(empty($value))
	{
		$value=__('Save');
	}

	return '<input type="submit" name="' . $name . '" id="'.$id.'" value="' . $value . '" />'."\n";
}

?>
