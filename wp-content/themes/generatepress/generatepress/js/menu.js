( function($) {
	'use strict';

	if ( 'querySelector' in document && 'addEventListener' in window ) {
		/**
		 * matches() pollyfil
		 * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Browser_compatibility
		 */
		if ( ! Element.prototype.matches ) {
			Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
		}

		/**
		 * closest() pollyfil
		 * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Browser_compatibility
		 */
		if ( ! Element.prototype.closest ) {
			Element.prototype.closest = function( s ) {
				var el = this;
				var ancestor = this;
				if ( ! document.documentElement.contains( el ) ) {
					return null;
				}
				do {
					if ( ancestor.matches( s ) ) {
						return ancestor;
					}
					ancestor = ancestor.parentElement;
				} while ( ancestor !== null );
				return null;
			};
		}
	}

	const menu_block = $('#primary-menu ul li');
	const button = $('#primary-menu ul li > a');

	menu_block.hover(function() {
        $(this).addClass("isOpened");
    }, function() {
        $(this).removeClass("isOpened");
    });

	button.on('click', function() {
		if (menu_block.hasClass('mobile-allow'))
		{
			menu_block.toggleClass("isOpened");
		}
		menu_block.addClass('mobile-allow');
    });

})(jQuery);
