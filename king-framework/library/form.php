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
	$res = '<div class="'.$div_class.'" >'."\n";
	$res .= '<form ';
	if(!empty($name))
	{
		$res .=' name="'.$name . '"';
	}
	if(!empty($action))
	{
		$res .=' action="'.$action . '"';
	}else{
		$res .=' action=""';
	}
	if(!empty($method))
	{
		$res .=' method="'.$method . '"';
	}
	if(!empty($target))
	{
		$res .=' target="'.$target . '"';
	}
	if(!empty($title))
	{
		$res .=' title="'.$title . '"';
	}
	if(!empty($form_class))
	{
		$res .=' class="'.$form_class . '"';
	}
	$res .='>'."\n";
	return $res;
}

/**
* @desc get a textbox element row with surrounding p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $args 		with following params
* @return string 				p with textbox
*/
/*		echo king_text_p(array(
				'name' 	=>"king_text_title",
				'id' 		=>"king_p",
				'class' 		=>"king_p",
				'descr' 	=> __('Title', 'widgetKing'),
				'title' 	=> __('The title above your text menu', 'widgetKing'),
				'val' 			=> $title));*/
function king_text_p($args) {
  $default_args = array( 'id' => '', 'descr' => '', 'title' => '', 'name' => '', 'val'=>'', 'class'=>'widefat' );
	$args = wp_parse_args( $args, $default_args );
  $res = "<p>";
  $res .= king_label( $args['id'], $args['descr'], $args['title'] );
	$res .= king_textbox($args['name'], $args['val'], $args['id'], $args['class']);
  $res .=  '</p>';
	return $res;
	
}
/**
* @desc get textarea element row with p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $args 		with following params
* @return string 				p with textarea
*/
function king_textarea_p($args) {
  $default_args = array( 'id' => '', 'descr' => '', 'title' => '', 'name' => '', 'val'=>'', 'class'=>'' , 'cols'=>'' , 'rows'=>''  );
	$args = wp_parse_args( $args, $default_args );
  $res = "<p>";
  $res .= king_label( $args['id'], $args['descr'], $args['title'] );
	$res .= king_get_textarea($args['name'], $args['val'], $args['id'],
                            $args['class'], $args['cols'],$args['rows']);
  $res .=  '</p>';
	return $res;
}
/**
* @desc get checkbox element row with p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $args 		with following params
* @param string $k_Label_Title  Description shown for label
* @return string  				p with checkbox
*/
function king_checkbox_p($args) {
  $default_args = array( 'id' => '', 'descr' => '', 'title' => '', 'name' => '', 'val'=>'' );
	$args = wp_parse_args( $args, $default_args );
	$res = "<p>";
	$res .= king_checkbox( $args['name'], $args['val'], $args['id'] );
  $res .= king_label( $args['id'], $args['descr'], $args['title'] );
  $res .=  '</p>';
	return $res;
}
/**
* @desc get select element row with p and label. Is used for the AJAX popup Options
* @author Georg Leciejewski
* @param array $args
* @return string  				p with checkbox
*/
function king_select_p($args) {
  $default_args = array( 'id' => '', 'descr' => '', 'title' => '', 'options' => '', 'name' => '', 'val'=>'' );
	$args = wp_parse_args( $args, $default_args );
  $res =  '<p>';
  $res .= king_label($args['id'],$args['descr'], $args['title']);
	$res .= king_select($args['name'], $args['val'], $args['options'], $args['id'] );
	$res .=  '</p>';
	return $res;
}

/**
* @desc get the end of a Form with closing div. Is used for main widget Options (number of widgets)
* @author Georg Leciejewski
* @return string closing form and div
*/
function king_get_end_form()
{
	return '</form></div>';
}

?>
