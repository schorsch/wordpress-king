jQuery(document).ready(function () {

  //observer submit and re-add  events
  //DOMSubtreeModified   DOMNodeInsertedIntoDocument DOMNodeInserted
	jQuery("div[id*='_king'] .widget-content").bind('DOMNodeInserted', function() { //http://www.w3.org/TR/2000/REC-DOM-Level-2-Events-20001113/events.html#Events-MutationEvent
    //event is fired multiple times ?? should check why //    console.log('hello'); 
    jQuery("div[id*='_king'] .widget-content h3").next().hide();
    return false;
  });

	jQuery("div[id*='_king'] .widget-content h3").live('click', function() {
		jQuery(this).next().toggle('fast');
		return false;
	}).next().hide();

  //hide cat id input when showcategory is not selected
  var cat_ids = jQuery("div[id*='_king_'].widget input[id*='show_category']");
  cat_ids.each(function(){
    if ( !jQuery(this).is(':checked') ) {  jQuery(this).nextAll('input').hide(); };
  });
  cat_ids.live('change', function() {
    jQuery(this).is(':checked') ?  jQuery(this).nextAll('input').show() : jQuery(this).nextAll('input').hide();
  });
 // show on / not on area selects hide the following fields if not set
  var show_ons = jQuery("div[id*='_king_'].widget input[id*='_on_site_area']");
  show_ons.each(function(){
    if ( !jQuery(this).is(':checked') ) {  
      jQuery(this).parent().nextAll('p').hide();
    };
  });
  show_ons.live('change', function() {
    jQuery(this).is(':checked') ?  jQuery(this).parent().nextAll('p').show() : jQuery(this).parent().nextAll('p').hide();
  });

});