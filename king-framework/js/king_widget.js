jQuery(document).ready(function () {

	jQuery("div[id*='_king'] .widget-content h3").live('click', function() {
		jQuery(this).next().toggle('fast');
		return false;
	}).next().hide();
  
  var cat_ids = jQuery("div[id*='_king_'].widget input[id*='show_category']");
  cat_ids.each(function(){
    if ( !jQuery(this).is(':checked') ) {  jQuery(this).nextAll('input').hide(); };
  });
  cat_ids.live('change', function(){
//    if ( !jQuery(this).is(':checked') ) {  jQuery(this).next('input').hide(); };
    jQuery(this).is(':checked') ?  jQuery(this).nextAll('input').show() : jQuery(this).nextAll('input').hide();
  });

//  var show_ons = jQuery("div[id*='_king_'].widget input[id*='_on_site_area']");
//  show_ons.each(function(){
//    if ( !jQuery(this).is(':checked') ) {  jQuery(this).nextAll('select').hide(); };
//  });
//  show_ons.live('change', function(){
////    if ( !jQuery(this).is(':checked') ) {  jQuery(this).next('input').hide(); };
//    jQuery(this).is(':checked') ?  jQuery(this).nextAll('select').show() : jQuery(this).nextAll('select').hide();
//  });
});