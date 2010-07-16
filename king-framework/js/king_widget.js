jQuery(document).ready(function () {

	jQuery("div[id*='_king'] .widget-content h3").live('click', function() {
		jQuery(this).next().toggle();
		return false;
	}).next().hide();

});