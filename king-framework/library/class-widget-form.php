<?php
include_once 'class-form.php';

class WidgetForm extends KForm {


  /**
  * @desc Output export/import fields
  * @author Georg Leciejewski
  */
  public function export_opts($widget,$opts) {
    echo $this->textarea(array(
      'name'  => $widget->get_field_name('import'),
      'id'    => $widget->get_field_id('import'),
      'descr' =>__('Import (JSON)', 'widgetKing'),
      'title' => __('A valid JSON string comming from another category widget', 'widgetKing'),
      'val'   => ''));

    echo $this->textarea(array(
      'name'  => $widget->get_field_name('export'),
      'id'    => $widget->get_field_id('export'),
      'descr' =>__('Export(JSON)', 'widgetKing'),
      'title' => __('Copy this json string into another category widget to copy its settings', 'widgetKing'),
      'val'   => king_export_json($opts) ) );
  }
  /**
  * @desc Output HTML Options fields for before/after widget html
  * @author Georg Leciejewski
  */
  public function html_opts($widget,$before_widget,$before_widget_title,$after_widget_title,$after_widget) {
    //before widget
    echo $this->text(array(
        'name'  => $widget->get_field_name('before_widget'),
        'id'    => $widget->get_field_id('before_widget'),
        'descr' => __('HTML before widget', 'widgetKing'),
        'title' => __('HTML which opens this widget. Can be something linke ul with a class, depending on your css and Theme', 'widgetKing'),
        'val'   => $before_widget));
    //before title
    echo $this->text(array(
        'name'  => $widget->get_field_name('before_widget_title'),
        'id'    => $widget->get_field_id('before_widget_title'),
        'descr' => __('HTML before widget Title', 'widgetKing'),
        'title' => __('HTML before the widget title. Can be something linke strong or h2 with a class, depending on your css and Theme', 'widgetKing'),
        'val' 	=>$before_widget_title));
    //after title
    echo $this->text(array(
        'name'  => $widget->get_field_name('after_widget_title'),
        'id'    => $widget->get_field_id('after_widget_title'),
        'descr' => __('HTML after widget Title', 'widgetKing'),
        'title' => __('HTML after the widget title but before the text list output. Can be something linke /strong ul or /h2 ul , depending on what you set as before-title', 'widgetKing'),
        'val' 	=> $after_widget_title));
    //after widget
    echo $this->text(array(
        'name'  => $widget->get_field_name('after_widget'),
        'id'    => $widget->get_field_id('after_widget'),
        'descr' => __('HTML after widget', 'widgetKing'),
        'title' => __('HTML which closes this widget. Can be something linke /ul , depending on what you set as HTML before', 'widgetKing'),
        'val' 	=> $after_widget));

  }//end widgethtml

  /**
  * @desc Form fields to define where a widget will be shown on.
  * @author Georg Leciejewski
  */
  public function where_to_show($widget, $show_category, $cat_ids, $show_on_site_area, $show_not_on_site_area, $site_area, $site_area_id) {
    echo '<p>';
      //show only in category
    echo $this->checkbox_tag(  $widget->get_field_name('show_category'), $show_category, $widget->get_field_id('show_category') );
    echo $this->label_tag(  $widget->get_field_id('show_category'), __('Show in Categories (ids)', 'widgetKing'),
                        __('Show box only within given category ids: 1,14,13. This Switch can be combined with Show/Not in Area. This gives you more flexibility. f.ex. You can show a box on the Frontpage and inside a category or show a box in a category and everywhere else but the home-page. ', 'widgetKing') );
    //Category ID
    echo $this->text_tag( $widget->get_field_name('cat_ids'), $cat_ids, $widget->get_field_id('cat_ids'), 'widefat' );
    echo '</p><p>';
    //show only on Special Page Area
    echo $this->checkbox_tag(  $widget->get_field_name('show_on_site_area'), $show_on_site_area, $widget->get_field_id('show_on_site_area') );
    echo $this->label_tag( $widget->get_field_id('show_on_site_area'), __('Show only on Special Page Area', 'widgetKing'),
                     __('The box is only shown on Area of the following select. Dont use together with following Show-Not-in Area checkbox!', 'widgetKing') );
    echo '<br/>';
    echo $this->checkbox_tag( $widget->get_field_name('show_not_on_site_area'), $show_not_on_site_area, $widget->get_field_id('show_not_on_site_area') );
    echo $this->label_tag(  $widget->get_field_id('show_not_on_site_area'), __('DO NOT show on Special Page Area', 'widgetKing'),
                      __('The box is shown on all Areas BUT the one from the following selectbox or the ID/URL/Title field below. !! Do NOT use together with previous checkbox Show on Site Area !!', 'widgetKing') );

    // ID Name of special website area
    echo '<p>';
    echo $this->select_tag($widget->get_field_name('site_area'), $site_area,
            array('is_home', 'is_page','is_single','is_category','is_archive','is_search','is_author','is_404'),
            $widget->get_field_id('site_area'),  'widefat' );
    echo '</p>';
    //Item  ID
    echo $this->text(array(
        'name'  => $widget->get_field_name('site_area_id'),
        'id'    => $widget->get_field_id('site_area_id'),
        'descr' => __('Area ID/Slug/Title', 'widgetKing'),
        'title' =>  __('The ID, Title or Slug of the Page Area(depending on type choosen) the box is to be shown on. You can enter a comma seperated list. Only needed for single, page and category. If left empty the box will appear on all f.ex. single pages. Definitly READ the Wordpress Codex -> Conditional_Tags.', 'widgetKing'),
        'val' 	=>$site_area_id
        ));
  }// end where to Show
}
?>
