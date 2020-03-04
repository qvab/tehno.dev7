jQuery(document).ready(function($){

	var delay = sticky_options['sticky_load_delay']
	var def_margin_top = sticky_options['sticky_margin_top']
	var def_wrap_span = sticky_options['sticky_wrap_span']
	var def_method = sticky_options['sticky_css_js']
	var def_height = 600;

	setTimeout(function() {

		find_and_wrap();

		var blocks = $('.sticky-code-block')
		for (var i = blocks.length - 1; i >= 0; i--) {
			var disable_sticky = false;
			let height = $(blocks[i]).attr('data-height');

				if (height > 0) {
					$(blocks[i]).css('height', height+'px');
					disable_sticky = false;
				}
				else {
					$(blocks[i]).css('height', 'auto');
					disable_sticky = true;
				}
			

			if (!disable_sticky) {
				let this_block = $(blocks[i]).find('.sticky-ad-block')[0];
				let classes = this_block.classList;
				for (var j = classes.length - 1; j >= 0; j--) {
					if (classes[j] !== 'sticky-ad-block') {
						if (def_method == 3)
							var sticky = new Sticky('.'+classes[j]);
						if (def_method == 2) {
							sticky_css_hax(classes[j])
						}
					}
				}
			}

			if (def_method == 2) {
				parents_overflow(blocks[0]);
			}


			let top_margin = $(blocks[i]).find('.sticky-ad-block').attr('data-margin-top');
			top_margin = top_margin ? top_margin : def_margin_top;
			$(blocks[i]).find('.sticky-ad-block').attr('data-margin-top', top_margin+'px');
			$(blocks[i]).find('.sticky-ad-block').css('top',top_margin+'px');
		}
	}, delay)

	function find_and_wrap() { //for manual insertion
		var m_blocks = $('.sticky-ad-block')

		for (var i = m_blocks.length - 1; i >= 0; i--) {

			if (!$(m_blocks[i]).attr('data-shortcode')) {
				let cont_height = $(m_blocks[i]).attr('data-height');
				cont_height = cont_height ? cont_height : def_height;
				$(m_blocks[i]).wrap('<div class="sticky-code-block" data-sticky-container data-height="'+cont_height+'"></div>')
				if (def_wrap_span == "true") {
					$(m_blocks[i]).attr('data-sticky-wrap', 'true');
				}
				$(m_blocks[i]).attr('data-sticky-class', 'is-sticky');

				let id; // generate smth unique
				id = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);

				$(m_blocks[i]).addClass('sticky-ad-'+id)
				
			}


		}

	}

	// change overflow properties
	function parents_overflow(element) {
		if (element.parentElement != undefined) {

			element.parentElement.style.overflow = 'visible';
			parents_overflow(element.parentElement);
		}
		return;
	}

	function sticky_css_hax(el_class) {
		let el = document.getElementsByClassName(el_class)[0];
		el.style.alignSelf = 'flex-start';
		el.style.display = 'block';
		el.parentElement.style.display = 'block';
	}
})