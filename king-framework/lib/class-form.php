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
class KForm {

  /*
   * Wrap HTML tags around label/ form-field
   */
  public $wrap = array('<p>', '</p>');
  /**
  * @desc get a textbox element row with surrounding p and label. Is used for the AJAX popup Options
  * @author Georg Leciejewski
  * @param array $args 		with following params
  * @return string 				paragaph with textbox
  * @example
  *   $f = new KForm();
  *		echo $f->text(array(
	*			'name' 	=>"king_text_title",
	*			'id' 		=>"king_p",
	*			'class' 		=>"king_p",
	*			'descr' 	=> __('Title', 'widgetKing'),
	*			'title' 	=> __('The title above your text menu', 'widgetKing'),
	*			'val' 			=> $title));
  */
  public function text($args) {
    $default_args = array( 'id' => '', 'descr' => '', 'title' => '',
                           'name' => '', 'val'=>'', 'class'=>'widefat' );
    $args = wp_parse_args( $args, $default_args );
    $res = $this->wrap[0];
    $res .= $this->label_tag( $args['id'], $args['descr'], $args['title'] );
    $res .= $this->text_tag($args['name'], $args['val'], $args['id'], $args['class']);
    $res .= $this->wrap[1];
    return $res;
  }

  /**
  * @desc get textarea element row with p and label. Is used for the AJAX popup Options
  * @author Georg Leciejewski
  * @param array $args 		with following params
  * @return string 				p with textarea
  */
  public function textarea($args) {
    $default_args = array( 'id' => '', 'descr' => '', 'title' => '',
                           'name' => '', 'val'=>'', 'class'=>'widefat' ,
                           'cols'=>'' , 'rows'=>''  );
    $args = wp_parse_args( $args, $default_args );
    $res = $this->wrap[0];
    $res .= $this->label_tag( $args['id'], $args['descr'], $args['title'] );
    $res .= $this->textarea_tag($args['name'], $args['val'], $args['id'],
                              $args['class'], $args['cols'],$args['rows']);
    $res .= $this->wrap[1];
    return $res;
  }

  public function checkbox($args) {
    $default_args = array( 'id' => '', 'descr' => '', 'title' => '', 'name' => '',
                           'val'=>'' );
    $args = wp_parse_args( $args, $default_args );
    $res = $this->wrap[0];
    $res .= $this->checkbox_tag( $args['name'], $args['val'], $args['id'] );
    $res .= $this->label_tag( $args['id'], $args['descr'], $args['title'] );
    $res .= $this->wrap[1];
    return $res;
  }
  /**
  * @desc get select element row with p and label. Is used for the AJAX popup Options
  * @author Georg Leciejewski
  * @param array $args
  * @return string  				p with checkbox
  */
  function select($args) {
    $default_args = array( 'id' => '', 'descr' => '', 'title' => '',
                           'options' => '', 'name' => '', 'val'=>'' );
    $args = wp_parse_args( $args, $default_args );
    $res = $this->wrap[0];
    $res .= $this->label_tag($args['id'],$args['descr'], $args['title']);
    $res .= $this->select_tag($args['name'], $args['val'], $args['options'], $args['id'] );
    $res .= $this->wrap[1];
    return $res;
  }

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
  public function text_tag($name, $value, $id='', $class='', $size='', $max='') {
    $res ='<input type="text" name="' . $name . '" value="' . $value . '"';
    $res .= !empty($id)     ? ' id="' . $id . '"' : '';
    $res .= !empty($class)  ? ' class="' . $class . '"' : '';
    $res .= !empty($size)   ? ' size="' . $size . '"' : '';
    $res .= !empty($max)    ?	' maxlength="' . $max . '"' : '';
    $res .=' />';
    return 	$res;
  }
  /**
  * @desc checkbox form element
  * 		the if around the $value is kindof a hack. if you give the value "checked" the CHECKED atribute is automaticly added
  * @author Georg Leciejewski
  * @param string $name the Name of the checkbox required
  * @param string $value if not empty the checkbox is checked
  * @param string $id
  * @param string $class
  * @return string whole checkbox element
  */
  public function checkbox_tag($name, $value, $id='', $class='') {
    $res ='<input type="checkbox"  name="' . $name . '"' ;
    $res .= !empty($value)   ? ' value="'.$value.'" checked="checked"' : '';
    $res .= !empty($id)      ? ' id="' . $id . '"' : '';
    $res .= !empty($class)   ? ' class="' . $class . '"' : '';
    $res .=' />';
    return $res;
  }

  /**
  * @desc textarea form element
  * @author Georg Leciejewski
  * @param string $name - the Name of the textarea required
  * @param string $value - the Value of the textarea required
  * @param string $id - css ID
  * @param string $class - css class
  * @param string $cols - colums
  * @param string $rows - rows
  * @return string whole textarea element
  */
  public function textarea_tag($name, $value, $id='', $class='', $cols='', $rows='') {
    $res ='<textarea name="' . $name . '"';
    $res .= !empty($id)    ? ' id="' . $id . '"' : '';
    $res .= !empty($class) ? ' class="' . $class . '"' : '';
    $res .= !empty($rows) ? ' rows="' . $rows . '"' : '';
    $res .= !empty($cols)  ? ' cols="' . $cols . '"' : '';
    $res .=' >'.$value.'</textarea>'."\n";
    return $res;
  }

  /**
  * @desc selectbox form element
  * @author Georg Leciejewski
  * @param string $name - required Name of select
  * @param string $value - required Value of select
  * @param array $options - required Options passed as array if option=value than it shows as selected
  * @param string $id - ID of select
  * @param string $class - css class
  * @return string whole select element
  */
  public function select_tag($name, $value, $options, $id ='', $class = '') {
    $res = '<select size="1" name="' . $name . '"';

    $res .= !empty($id) ? ' id="' . $id . '"': '';
    $res .= !empty($class) ? ' class="' . $class . '"': '';
    $res .= '>'."\n";
    foreach ($options as $option)	{
      ($option == $value) ? $selected = ' selected="selected"' : $selected = '';
      $res .= '<option value="' . $option . '"' . $selected . '>' . $option . '</option>'."\n";
    }
    $res .= '</select>'."\n";
    return $res;
  }

  public function label_tag( $id, $descr, $title='' ) {
    $res = '<label for="' . $id . '"';
    $res .= !empty($title) ? ' title="' . $title . '"' : '';
    $res .='>'.$descr.'</label>';
    return $res;
  }

}

?>
