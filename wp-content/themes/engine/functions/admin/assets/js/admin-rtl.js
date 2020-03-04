/*rtl-specific javascript goes here*/
jQuery.noConflict();

var itAdmin = {
	init : function() {
		itAdmin.tooltipHelp();		
	},
	tooltipHelp : function() {		
		jQuery('body').on('mouseover', '.it_option_help a', function(){
			jQuery(this).removeData('tooltip').unbind().next('div.tooltip').remove();
			jQuery(this).tooltip({ delay: 0, predelay: 0, offset: [0, 0], position: 'top right', relative: true, tipClass: 'it_help_tooltip' });
			jQuery(this).tooltip().show();		   		   
		});		
	}
}
	
jQuery(document).ready(function(){
	itAdmin.init();
});