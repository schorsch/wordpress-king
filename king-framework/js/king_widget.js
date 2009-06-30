jQuery(document).ready(function () {
    
    // du to wordpress hiding active widget the tabs dont find the right panel id's 
    var active = jQuery("div[id*='_king']", '#widgets-right, #wp_inactive_widgets' );
    active.each(function() {
        jQuery(this).find('ul.anchors li a').each(function(){ //change anchors href
            jQuery(this).attr('href', jQuery(this).attr('href') + '_active');
        });
        jQuery(this).find("div[id^='section-']").each(function(){ //change panels id
            jQuery(this).attr('id', jQuery(this).attr('id') + '_active');
        });

    });  
    //add tabs to our widgets
    jQuery("div[id*='_king'] div.widget-content").tabs();  
    //add labels containing help
    //jQuery('label').Tooltip(350);

});