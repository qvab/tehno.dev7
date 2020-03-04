jQuery(document).ready(function($) {
	// ѕлавающий блок –—я
	function show_lip() {
		var need_ad = "#desktop_lip";
		if (document.body.clientWidth <= 550) {
			need_ad = "#mobile_lip";
		}
		$(need_ad).show();
	}
	setTimeout(show_lip, 10000);
	
        $('.close_lip').delay(14000).animate({'opacity':'1'},2000);
    	$('.close_lip').click(function(event) {
		$('#desktop_lip, #mobile_lip').hide();
	});
});