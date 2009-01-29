<?php
/*
Helper Function for creating widget admin forms
Author: Georg Leciejewski
Version: 0.8
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
include_once 'form_input_elements.php';

/**
* @desc get the start of a Form with opening div . Is used for the Main Widget Options
* @author Georg Leciejewski
* @param string $div_class  class for the div
* @param string $name  Class of the surrounding p
* @param string $action  Action of the form
* @param string $method  Post Method
* @param string $target
* @param string $title
* @param string $form_class  css class of the form
* @return string a form start tag with a div element
*/
function king_get_start_form($div_class='', $name='', $action='', $method='', $target='', $title='', $form_class='')
{
	$result = '<div class="'.$div_class.'" >'."\n";
	$result .= '<form ';
	if(!empty($name))
	{
		$result .=' name="'.$name . '"';
	}
	if(!empty($action))
	{
		$result .=' action="'.$action . '"';
	}else{
		$result .=' action=""';
	}
	if(!empty($method))
	{
		$result .=' method="'.$method . '"';
	}
	if(!empty($target))
	{
		$result .=' target="'.$target . '"';
	}
	if(!empty($title))
	{
		$result .=' title="'.$title . '"';
	}
	if(!empty($form_class))
	{
		$result .=' class="'.$form_class . '"';
	}
	$result .='>'."\n";
	return $result;
}

/**
* @desc get the start of an element row with opening p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param string $label  Label name
* @param string $description  Description shown before from element
* @param string $label_title  Description shown before from element
* @param string $class  Class of the surrounding p
* @return string a p with a label element
*/
function king_get_start_p($label='',$description='', $label_title='', $class='')
{
	$result = '<p';
	if(!empty($class))
	{
		$result .=' class="'.$class . '">'."\n";
	}
	else
	{
		$result .='>'."\n";
	}

	$result .= '<label for="' . $label . '"';


	if(!empty($label_title))
	{
		$result .= ' title="' . $label_title . '"';
	}
	$result .='>'."\n";
	$result .= $description ."\n";
	$result .=  '</label>'."\n";

	return $result;
}

/**
* @desc get a textbox element row with surrounding p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $options 		with following params
* @param string $k_Label_Id_Name p Label + IDs + Textbox Name
* @param string $k_P_Class 	Class of the surrounding p
* @param string $k_Label_Class  Class of the label field
* @param string $k_Description  Description shown before textbox
* @param string $k_Value 		Value for textbox
* @param string $k_Class  		Textbox Class
* @param string $k_Label_Title  Description shown for label
* @param string $k_Size  		Textbox size
* @param string $k_Max  		Textbox max chars
* @return string 				p with textbox
*/
/*		echo king_get_textbox_p(array(
				'k_Label_Id_Name' 	=>"king_text_title_$number",
				'k_P_Class' 		=>"king_p",
				'k_Description' 	=> __('Title', 'widgetKing'),
				'k_Label_Title' 	=> __('The title above your text menu', 'widgetKing'),
				'k_Value' 			=> $title,
				'k_Size' 			=>'20',
				'k_Max' 			=>'50'));*/
function king_get_textbox_p($options)
{
	$result = king_get_start_p($options['Label_Id_Name'],$options['Description'], $options['Label_Title'],$options['Class']);
	$result .= king_get_textbox($options['Label_Id_Name'],$options['Value'],'', '',$options['Size'],$options['Max']);
	$result .=  king_get_end_p();
	return $result;
}
/**
* @desc get textarea element row with p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $options 		with following params
* @param string $k_Label_Id_Name p Label + IDs + textarea Name
* @param string $k_P_Class of the surrounding p
* @param string $k_Label_Class  of the label field
* @param string $k_Description  shown before textarea
* @param string $k_Value 		Value for Textarea
* @param string $k_Class  		Textarea Class
* @param string $k_Label_Title  Description shown for label
* @param string $k_cols  		Textarea colums
* @param string $k_rows  		Textarea rows
* @return string 				p with textarea
*/
function king_get_textarea_p($options)
{

	$result = king_get_start_p($options['Label_Id_Name'], $options['Description'], $options['Label_Title'],$options['Class']);
	$result .= king_get_textarea($options['Label_Id_Name'],$options['Value'],'', '',$options['cols'],$options['rows']);
	$result .=  king_get_end_p();
	return $result;
}
/**
* @desc get checkbox element row with p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $options 		with following params
* @param string $k_Label_Id_Name p Label + IDs + Textbox Name
* @param string $k_P_Class 	Class of the surrounding p
* @param string $k_Label_Class  Class of the label field
* @param string $k_Description  Description shown before textbox
* @param string $k_Value 		Value for textbox
* @param string $k_Class  		Textbox Class
* @param string $k_Label_Title  Description shown for label
* @return string  				p with checkbox
*/
function king_get_checkbox_p($options)
{
	$result = king_get_start_p($options['Label_Id_Name'], $options['Description'], $options['Label_Title']);
	$result .= king_get_checkbox($options['Label_Id_Name'],$options['Value'],'', '');
	$result .=  king_get_end_p();
	return $result;
}
/**
* @desc get select element row with p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $options 		with following params
* @param string $k_Label_Id_Name p Label + IDs + select Name
* @param string $k_P_Class 	Class of the surrounding p
* @param string $k_Label_Class  Class of the label field
* @param string $k_Description  Description shown before select
* @param string $k_Value 		Value for select
* @param string $select_options Select Options
* @param string $k_Label_Title  Description shown for label
* @return string  				p with checkbox
*/
function king_get_select_p($options)
{

	$result = king_get_start_p($options['Label_Id_Name'],$options['Description'], $options['Label_Title']);
	$result .= king_get_select($options['Label_Id_Name'],$options['Value'],$options['select_options'],'');
	$result .=  king_get_end_p();
	return $result;
}


/**
* @desc get the end of an element row with closing p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @return string a closing p
*/
function king_get_end_p()
{
	return '</p>'."\n";
}
/**
* @desc get the end of a Form with closing div. Is used for main widget Options (number of widgets)
* @author Georg Leciejewski
* @return string closing form and div
*/
function king_get_end_form()
{
	return '</form>'."\n".'</div>'."\n";
}

/**
* @desc open div with ajax accordeon effect and first panel
* @param string $plugin  - id of the container holding the tabbed navigation
* @param array $sections - the tab names as array. should be in the order of the tabs array('first nav', 'second Nav')
* 							whatch the names since they are needed for the sections
* @author Georg Leciejewski
* @return string
*/
function king_get_tab_start($plugin, $sections)
{
	$result = '<ul class="anchors">'."\n";
	foreach($sections as $key => $val )
	{
         $result .='<li><a href="#section-'.$plugin.'-'.$key.'">'.$val.'</a></li>'."\n";
	}
	$result .='</ul>'."\n";
	$result .='<div id="section-'.$plugin.'-0" class="fragment">'."\n";
	return $result;
}

/**
* @desc get a section div for a tabbed navigation also closing the previous section
* @param string $section - the section of the form. must be unique and must contain th -number at the end:  myPlugin-1 for the second tab
* 							the number at the end is the auto generated array key from the start_tab
* @author Georg Leciejewski
* @return string hr
*/
function king_get_tab_section($section)
{
    $result = '
    		</div>
		<div id="section-'.$section.'" class="fragment">'."\n";
	return $result;
}
/**
* @desc close tabs navigation
* @author Georg Leciejewski
* @return closing divs
*/
function king_get_tab_end()
{
	return "</div>\n";
}
?>
