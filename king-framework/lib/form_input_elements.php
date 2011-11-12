<?php
/*
Helper Function for creating widget admin form elemts. gets included by form.php
Author: Georg Leciejewski
Version: 0.71
URI: http://www.blog.mediaprojekte.de
*/
/*  Copyright 2006 -2012  georg leciejewski 

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



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
	$res = '<select size="1" name="' . $k_Name . '" id="' . $k_Id . '" >'."\n";

    foreach($wp_roles->role_names as $role_key => $name)
	{
		$selected = ($role == $role_key) ? ' selected="selected"' : '';
		$res .= "<option value=\"$role_key\"{$selected}>{$name}</option>\n";
	}

	$res .= '</select>'."\n";
    $res .=  '<input type="hidden" name="' . $k_Name . '" value="'.$role. '" />'."\n";
	//capability
	$res .= '<input type="hidden" name="' . $capability . '" value="'. $capability . '" />'."\n";
	return $res;
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
	$res .= '<select size="1" name="' . $name . '" id="' . $id . '" >'."\n";
    foreach($wp_roles->role_objects as $key => $role) {
		foreach($role->capabilities as $capability => $grant)
			$all_cap_names[$capability] = $capability;
	}

    foreach($all_cap_names as $key => $val)
	{
		$selected = ($value == $val) ? ' selected="selected"' : '';
		$res .= "<option value=\"$key\"{$selected}>{$val}</option>\n";
	}

	$res .= '</select>'."\n";

	return $res;

}