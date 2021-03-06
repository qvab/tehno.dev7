jQuery.noConflict(); 	
"use strict";	
//DOCUMENT.READY
jQuery(document).ready(function() { 
	//add bootstrap classes to wordpress generated elements
	jQuery('.avatar-70, .avatar-50, .avatar-40').addClass('img-circle');
	jQuery('.comment-reply-link').addClass('btn');
	jQuery('#reply-form input#submit').addClass('btn');
	
	//hide various jQuery elements until they are loaded
	jQuery('.trending-content .compact-panel').animate({ opacity: 1}, 600);		
	
	//superfish
	jQuery('#main-menu ul').superfish({
		hoverClass:  'over',
		delay:       400,
		speed:       100,
		speedOut:    0,
		disableHI:   true
	});
	jQuery('.section-menu-mobile ul').superfish({
		hoverClass:  'over',
		delay:       400,
		speed:       100,
		speedOut:    0,
		disableHI:   true
	});
	jQuery('#section-menu.standard-menu').superfish({
		hoverClass:  'over',
		delay:       400,
		speed:       100,
		speedOut:    0,
		disableHI:   true
	});
	jQuery('.builder-utility ul').superfish({
		hoverClass:  'over',
		delay:       400,
		speed:       100,
		speedOut:    0,
		disableHI:   true
	});
	
	//pinterest
	if(jQuery('#pinterest-social-tab').length > 0) {
		(function(d){
			var f = d.getElementsByTagName('SCRIPT')[0], p = d.createElement('SCRIPT');
			p.type = 'text/javascript';
			p.async = true;
			p.src = '//assets.pinterest.com/js/pinit.js';
			f.parentNode.insertBefore(p, f);
		}(document));
	}	
	//facebook
	if(jQuery('#facebook-social-tab').length > 0) {
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&status=0";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	}	
	//HD images		
	if (window.devicePixelRatio == 2) {	
		var images = jQuery("img.hires");		
		// loop through the images and make them hi-res
		for(var i = 0; i < images.length; i++) {		
			// create new image name
			var imageType = images[i].src.substr(-4);
			var imageName = images[i].src.substr(0, images[i].src.length - 4);
			imageName += "@2x" + imageType;		
			//rename image
			images[i].src = imageName;
		}
	}
	//user rating slider handles
	jQuery('.form-selector').slider({
		value: jQuery('.form-selector').data('fsvalue'),
		min: jQuery('.form-selector').data('fsmin'),
		max: jQuery('.form-selector').data('fsmax'),
		step: jQuery('.form-selector').data('fsstep'),
		orientation: "horizontal",
		range: "min",
		animate: true,
		slide: function( event, ui ) {
			var rating = ui.value;
			var metric = jQuery('.form-selector').data('metric');
			if(metric=='letter') {				
				var numbers = {'1':'F', '2':'F+', '3':'D-', '4':'D', '5':'D+', '6':'C-', '7':'C', '8':'C+', '9':'B-', '10':'B', '11':'B+', '12':'A-', '13':'A', '14':'A+'};					
				var rating = numbers[rating];
			} else if(metric=='percentage') {	
				var rating = rating + '<span class="percentage">&#37;</span>';
			}			
			jQuery(this).parent().siblings('.rating-value').html( rating );
		}
	});
	//insert content menu items
	jQuery(jQuery('#content-anchor-inner').find('.content-section-divider').get().reverse()).each(function () {
		var id = jQuery(this).attr('id');
		var label = jQuery(this).data('label');
		jQuery( '#content-anchor-wrapper' ).after( '<li><a href="#' + id + '">' + label + '</a></li>' );		
	});	
	//scrollspy	
	if(jQuery('#wpadminbar').length > 0) {
		fromTop = 125;
	} else {
		fromTop = 93;	
	}
	//attach scrollspy
	jQuery('body').scrollspy({ target: '.contents-menu', offset: fromTop });
	//placeholder text for IE9
	jQuery('input, textarea').placeholder();
	//sticky bar
	affixStickyBar();
	//magazine categories
	adjustMagazineCategories();
	//section menu
	adjustSectionMenu();
	jQuery('#section-menu').animate({ opacity: 1}, 600);	
	//menu heights
	adjustMenuHeight(jQuery('#main-menu > ul'));
	adjustMenuHeight(jQuery('.section-menu-mobile > ul'));
	//utility menu
	resizeUtilityMenu();
	//post left	
	var width = viewport().width;
	jQuery('.post-left').animate({ opacity: 1}, 100);
	affixObject(jQuery('.post-left'), jQuery('.post-right'), 'down', false);
	affixObject(jQuery('.single-sidebar'), jQuery('.post-right'), 'down', true);
	if(width > 991) {
		affixObject(jQuery('.loop-sidebar-left'), jQuery('.main-post-container'), 'down', true);
		affixObject(jQuery('.loop-sidebar-right'), jQuery('.main-post-container'), 'down', true);
	} else {
		moveLoop();	
	}
	showPopNav();	
	//functions that need to run after ajax buttons are clicked
	dynamicElements();
});
//WINDOW.LOAD
jQuery(window).load(function() {
	jQuery('.it-widget-tabs').css({'opacity': '1'});
	//tabs - these must go in window.load so pinterest will work inside a tab
	jQuery('.widgets-wrapper .it-social-tabs').tabs({ fx: { opacity: 'toggle', duration: 150 } });
	jQuery('#footer .it-social-tabs').tabs({ active: 2, fx: { opacity: 'toggle', duration: 150 } });		
	jQuery('.share-wrapper').show();
	jQuery('.sharing-wrapper-single').css({'opacity': '1'});
	//setup flickr
	if(jQuery('#flickr-social-tab').length > 0) {
		jQuery('.flickr').jflickrfeed({
			limit: jQuery('.flickr').data('limit'),
			qstrings: {
				id: jQuery('.flickr').data('id')
			},
			itemTemplate: '<li>'+
							'<a rel="colorbox" class="darken colorbox" href="{{image}}" title="{{title}}">' +
								'<img src="{{image_s}}" alt="{{title}}" width="96" height="96" />' +
							'</a>' +
						  '</li>'
		}, function(data) {
		});	
	}
	disqusContentsMenu();
	autoSizeText();			
});
//WINDOW.RESIZE
jQuery(window).resize(function() {		
	//magazine categories
	adjustMagazineCategories();
	//section menu
	adjustSectionMenu();
	//utility menu
	resizeUtilityMenu();
	//smart sidebars
	smartSidebars();	
	//menu heights
	adjustMenuHeight(jQuery('#main-menu > ul'));
	adjustMenuHeight(jQuery('.section-menu-mobile > ul'));			
});	
//WINDOW.SCROLL
jQuery(window).scroll(function() {
	//smart sidebars
	smartSidebars();
	affixStickyBar();	
	if(!isTouchDevice()) { //does not apply to touch devices
		showPopNav();		
	}
});

//begin functions

//applied to elements within ajax panels
function dynamicElements() {				
	//active hover
	jQuery(".add-active").hover(
		function() {
			jQuery(this).addClass("active");
		},
		function() {
			jQuery(this).removeClass("active");
		}
	);
	//over hover
	jQuery(".add-over").hover(
		function() {
			jQuery(this).addClass("over");
		},
		function() {
			jQuery(this).removeClass("over");
		}
	);		
	//show various jquery text elements
	jQuery('.trending-content .compact-panel').animate({ opacity: 1}, 600);	
	jQuery('.scroller-content').show();
	jQuery('.top-ten-content').show();
	jQuery('.topten-widget .article-title').show();		
	//non touch device calls
	if(!isTouchDevice()) {	
		//tooltips			
		jQuery('.info').tooltip({container: 'body'});	
		jQuery('.info-top').tooltip({container: 'body'});	
		jQuery('.info-bottom').tooltip({ placement: 'bottom', container: 'body' });
		jQuery('.info-left').tooltip({ placement: 'left', container: 'body' });
		jQuery('.info-right').tooltip({ placement: 'right', container: 'body' });
		//image hovers
		jQuery(".active-image").hover(
			function() {
				jQuery(this).find('img').stop().animate({ opacity: .4 }, 150);
			},
			function() {
				jQuery(this).find('img').stop().animate({ opacity: 1.0 }, 500);
			}
		);
		jQuery(".the_content").hover(
			function() {
				jQuery(this).find('img').stop().animate({ opacity: .4 }, 150);
			},
			function() {
				jQuery(this).find('img').stop().animate({ opacity: 1.0 }, 500);
			}
		);
		//scroller
		jQuery(".trending-content").smoothDivScroll({
			manualContinuousScrolling: true,
			visibleHotSpotBackgrounds: "always",
			hotSpotScrollingStep: 4,
			hotSpotScrollingInterval: 5,
			//autoScrollingMode: "onStart"
		});	
	} else {
		jQuery(".trending-content").smoothDivScroll({
			manualContinuousScrolling: true,
			visibleHotSpotBackgrounds: "",
			hotSpotScrolling: false,
			touchScrolling: true
		});
	}
	jQuery(".trending-content .scrollableArea").addClass("loop");	
	//jQuery popovers
	jQuery('.popthis').popover();
	jQuery('.popover-meta').popover({placement: 'top', trigger: 'click', template: '<div class="popover meta" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'});
	jQuery('.popover-sharing').popover({placement: 'top', trigger: 'click', template: '<div class="popover sharing" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'});
	//jQuery alert dismissals
	jQuery('.alert').alert();	
	//jQuery colorbox				
	jQuery('.colorbox-enabled a.featured-image').colorbox({maxWidth:'95%', maxHeight:'95%'});
	jQuery('.colorbox-enabled .colorbox').colorbox({maxWidth:'95%', maxHeight:'95%'});
	jQuery('.colorbox-enabled .colorbox-iframe').colorbox({iframe:true, width:'85%', height:'80%'});
	jQuery(".colorbox-enabled .the-content a[href$='.jpg'],.colorbox-enabled .the-content a[href$='.png'],.colorbox-enabled .the-content a[href$='.gif']").colorbox({maxWidth:'95%', maxHeight:'95%'});
	jQuery('.colorbox-slideshow .the-content .gallery a').colorbox({rel:'gallery', slideshow:true, maxWidth:'95%', maxHeight:'95%'});	
	jQuery('.colorbox-slideshow-off .the-content .gallery a').colorbox({rel:'gallery', maxWidth:'95%', maxHeight:'95%'});	
	
	loadAddThis();
	
}
//smart sidebars
var lastScrollTop = 0;
function smartSidebars() {	
	var width = viewport().width;
	var st = jQuery(window).scrollTop();
	//see which direction we're going	
	if (st > lastScrollTop){
		var direction = 'down';
	} else {
		var direction = 'up';
	}
	affixObject(jQuery('.post-left'), jQuery('.post-right'), direction, false);	
	if(width > 991) {
		affixObject(jQuery('.loop-sidebar-left'), jQuery('.main-post-container'), direction, true);
		affixObject(jQuery('.loop-sidebar-right'), jQuery('.main-post-container'), direction, true);
		affixObject(jQuery('.single-sidebar'), jQuery('.post-right'), direction, true);
	} else {
		moveLoop();
	}
	lastScrollTop = st;		
}
//move the loop above the sidebars
function moveLoop() {
	//loop pages
	var loop = jQuery('.builder-loop .col-md-7');
	var row = jQuery('.builder-loop .loop-row');
	if(!row.hasClass('layout-d')) {
		jQuery(loop).prependTo(row);
	}
	//single pages
	var post = jQuery('.single-post-selector');
	if(post.length > 0 && !post.hasClass('moved')) {
		var row = post.parent();
		jQuery(post).prependTo(row);
		post.addClass('moved');
	}	
	//clear inline style adjustments for sidebars
	jQuery('.fixed-object').not('.post-left').removeAttr('style');
}
//sticky bar adjustments
function affixStickyBar() {
	var st = jQuery(window).scrollTop();	
	//sticky bar
	if (st > 100) {
		jQuery('#sticky-bar').addClass('fixed');
		jQuery('.sticky-home').stop().animate({ opacity: 1.0, left: '0px' }, 80);
		jQuery('#menu-toggle .label-text').stop().animate({ opacity: 0}, 80);	
	} else {
		jQuery('#sticky-bar').removeClass('fixed');
		jQuery('.sticky-home').stop().animate({ opacity: 0, left: '-30px' }, 100);	
		jQuery('#menu-toggle .label-text').stop().animate({ opacity: 1}, 600);			
	}
	if(jQuery('#wpadminbar').length > 0) {
		var topOffset = 144;
	} else {
		var topOffset = 100;	
	}
	if(st > topOffset) {
		jQuery('#sticky-bar').addClass('sticky-mobile');
	} else {
		jQuery('#sticky-bar').removeClass('sticky-mobile');
	}	
	//back to top arrow
	if (st < 100) {
		jQuery("#back-to-top").fadeOut();
	} else {
		jQuery("#back-to-top").fadeIn();
	}	
}
//adjust main menu height for mobile
function adjustMenuHeight(obj) {
	if(viewport().width < 601) {		
		var maxHeight = jQuery(window).height() - 46;
		obj.css('max-height',maxHeight+'px');
	}
}
//set variables for section menu adjustments
var previouswidth2 = viewport().width;
var originalwidth = jQuery('#section-menu').width();
//section menu adjustments on window resize
function adjustSectionMenu() {
	//see if compact versions of menus should be shown
	if(jQuery('#section-menu').length > 0) {
		var stickyinner = jQuery('#sticky-inner');
		var categories = jQuery('#section-menu');		
		var category = jQuery('#section-menu > ul > li:last-child');
		var more = jQuery('#section-more');
		var buttons = jQuery('#section-more ul');
		var firstbutton = buttons.children(':first');		
		//see if scaling up or down
		var newwidth = viewport().width;
		if(newwidth > previouswidth2) {
			var scalingup = true;			
		} else {
			var scalingup = false;
		}
		previouswidth2 = newwidth;			
		//find available width				
		var usedwidth = -more.width();
		categories.siblings().not('.section-toggle').not('.section-menu-mobile').each(function() {
			var w = jQuery(this).width();
			usedwidth += w;
		});
		var available = stickyinner.width() - usedwidth;
		if(scalingup) {	
			//only restore original size if we're scaling up and original threshold is hit			
			if(available > originalwidth) { 
				stickyinner.removeClass('compact');
				//check again in case original width was compact
				if(available < categories.width()) {
					stickyinner.addClass('compact');
				}
				//more.hide();
			}
			/*
			if(stickyinner.hasClass('compact')) {				
				if(buttons.children().length > 1 && (categories.width() < available)) {	
					while(buttons.children().length > 1 && (categories.width() < available)) {		
						categories.children('ul').append(jQuery(firstbutton));
						firstbutton = buttons.children(':first');	
					}
				}
				//hide more toggle if we've removed the last one
				if(buttons.children().length < 1) {				
					more.hide();	
				}
			}
			*/
		} else {
			if(categories.width() > available) {						
				stickyinner.addClass('compact');
			}			
		}
		if(stickyinner.hasClass('compact') && (newwidth > 600)) {				
			if(categories.width() > available) {
				//more.show();				
				while(categories.width() > available) {	
					jQuery(category).remove();													
					//buttons.prepend(jQuery(category));
					category = jQuery('#section-menu > ul > li:last-child');						
				}	
			}				
		}		
	}
}
//addthis setup
function loadAddThis() {
	addthis.init();
}
var previouswidth = viewport().width;
//magazine categories
function adjustMagazineCategories() {
	panels = jQuery('.magazine-panel').not('#comments');
	panels.each(function () {
		var header = jQuery(this).find('.magazine-header');
		var title = jQuery(this).find('.magazine-title');
		var categories = jQuery(this).find('.magazine-categories');
		var available = header.width() - title.width() - 75;
		var buttons = jQuery(this).find('.magazine-more .sort-buttons');
		var firstbutton = buttons.children(':first');
		var firstwidth = realWidth(firstbutton);
		var more = jQuery(this).find('.magazine-more');
		var category = categories.find('a:last-child');
		//see if scaling up or down
		var newwidth = viewport().width;
		if(newwidth > previouswidth) {
			var scalingup = true;			
		} else {
			var scalingup = false;
		}
		previouswidth = newwidth;	
		if(scalingup) {
			if(buttons.children().length > 1 && (categories.width() + firstwidth < available)) {
				while(buttons.children().length > 1 && (categories.width() + firstwidth < available)) {
					categories.append(jQuery(firstbutton));
					firstbutton = buttons.children(':first');
					firstwidth = realWidth(firstbutton);	
				}
			}
		} else {
			if(categories.width() > available) {
				more.show();				
				while(categories.width() > available) {									
					buttons.prepend(jQuery(category));
					category = categories.find('a:last-child');
				}				
			}else if(buttons.children().length < 1) {				
				more.hide();	
			}
		}
	});
	jQuery('.magazine-categories').css('opacity', 1);
}
//section menus
function adjustSectionTerms(obj) {
	var wrapper = jQuery(obj).find('.mega-wrapper');
	var categories = jQuery(obj).find('.term-list');
	if(categories.length > 0) {		
		var available = wrapper.width() - 75;
		var buttons = jQuery(obj).find('.terms-more .sort-buttons');
		var firstbutton = buttons.children(':first');
		var firstwidth = realWidth(firstbutton);
		var more = jQuery(obj).find('.terms-more');
		var category = categories.find('a:last-child');
		//see if scaling up or down
		var newwidth = viewport().width;
		if(newwidth > previouswidth) {
			var scalingup = true;			
		} else {
			var scalingup = false;
		}
		previouswidth = newwidth;
		if(scalingup) {
			if(buttons.children().length > 1 && (categories.width() + firstwidth < available)) {
				while(buttons.children().length > 1 && (categories.width() + firstwidth < available)) {
					categories.append(jQuery(firstbutton));
					firstbutton = buttons.children(':first');
					firstwidth = realWidth(firstbutton);	
				}
				//alert(categories.width() + ', ' + firstwidth + ', ' + available);
			}
		} else {
			if(categories.width() > available) {
				more.show();
				while(categories.width() > available) {									
					buttons.prepend(jQuery(category));
					category = categories.find('a:last-child');
				}				
			}else if(buttons.children().length < 1) {				
				more.hide();	
			}
		}
	}
}
//show the popout navigation at bottom of posts
var lastScrollTop = 0;
function showPopNav() {
	var st = jQuery(window).scrollTop();
	//see which direction we're going	
	if (st > lastScrollTop){
		var direction = 'down';
	} else {
		var direction = 'up';
	}
	if(jQuery('.popnav').length > 0) {
		if(bottomVisible(jQuery('.the-content'))) {
			jQuery('.popnav').addClass('shown');
		} else {
			jQuery('.popnav').removeClass('shown');
		}
	}
	lastScrollTop = st;
}
//set object's position to fixed once its top edge hits the
//top of the viewport and un-fix it when the bottom edge hits
//the bottom of the viewport, taking into account if the
//object's total height is taller than the viewport
function affixObject(obj, rel, direction, addWidth) {
	//bookmark positioning			
	if(obj.length > 0) {
		var win = jQuery(window);			
		if(jQuery('#wpadminbar').length > 0) {
			topOffset = 120;
		} else {
			topOffset = 88;	
		}
		if(viewport().width < 601) topOffset = 45;
		//get object's inner height
		var objHeight = 0;		
		obj.children().not('.post-left-toggle').each(function() {
			objHeight += jQuery(this).outerHeight(true);		
		});
		//if object doesn't have a set width, it needs
		//to inherit parent's so fixed positioning works correctly	
		if(addWidth) {
			var parentWidth = obj.parent().width();
			obj.css('width', parentWidth);
		}
		var relHeight = rel.outerHeight(true);
		var relOffset = rel.offset().top - topOffset;	
		var winHeight = win.height() - topOffset;
		var objOffset = obj.offset().top - topOffset;
		var amountShorter = 0;
		var amountTaller = 0;						
		if(objHeight > winHeight) {
			var taller = true;	
			amountTaller = (objHeight - winHeight) * -1;			
		} else {
			var taller = false;
			amountShorter = (winHeight - objHeight) * -1;
		}
		var amountToOffset = amountTaller + topOffset;			
		var lastChild = obj.children().last();
		//no adjustments of object height is taller than rel height,
		//and also need to remove fixed-object class so it stays relative
		if(relHeight <= objHeight) { 
			obj.removeClass('fixed-object');
		} else {
			//add fixed-object back in case it was removed prior
			obj.addClass('fixed-object');
			if(win.scrollTop() >= relOffset) {
				//this is when the object becomes fixed
				if(!obj.hasClass('fixed') && !bottomVisible(rel)) {
					if(taller) {
						if(obj.hasClass('bottom-reached') && direction=='down') {
							obj.removeClass('bottom-reached').css('top','');	
						}
						obj.addClass('taller');
					} else {
						obj.addClass('fixed').css('top','');						
					}
				}
			} else {
				//reset everything - we're back to the top
				if(obj.hasClass('fixed')) {
					obj.removeClass('fixed').css('top','');			
				}else if(obj.hasClass('taller')) {
					obj.removeClass('taller-fixed').removeClass('taller').css('top','');	
				}
			}
			//unfix taller object when it reaches bottom of viewport
			if(obj.hasClass('taller')) {
				if(direction=='down') {
					if(obj.hasClass('fixed')) {
						obj.removeClass('fixed').css('top',objOffset - relOffset + 'px');
					}
					if(bottomVisible(lastChild)) {
						obj.addClass('taller-fixed').css('top', amountToOffset + 'px');						
					}
				} else {
					if(obj.hasClass('taller-fixed')) {
						obj.removeClass('taller-fixed').css('top',objOffset - relOffset + 'px');
					} else if (topVisible(obj,topOffset) && win.scrollTop() >= relOffset) {
						obj.addClass('fixed').css('top','');
					}
				}
			}
			//unfix at bottom of post content			
			if(obj.hasClass('fixed') || obj.hasClass('taller')) {
				if(bottomVisible(rel)) {			
					obj.removeClass('fixed').removeClass('taller').removeClass('taller-fixed');
					obj.css('top',(relHeight - objHeight) + (amountShorter - 25) + 'px');					
					//flag that says the bottom was reached
					obj.addClass('bottom-reached');
				}				
			}	
		}
	}
}

var originalMenuWidth = jQuery('.utility-menu-full > ul').width();
//utility menu page builder		
function resizeUtilityMenu() {
	//bookmark positioning			
	if(jQuery('.utility-menu-full').length > 0) {		
		var stickyNav = jQuery('#sticky-nav');
		var total = jQuery('.utility-inner').width();
		var menu = jQuery('.utility-menu-full > ul');	
		//find available width				
		var used = 10;
		menu.siblings().each(function() {
			var w = jQuery(this).width();
			used += w;
		});
		var available = total - used;
		if (menu.width() > available) {
			jQuery('.utility-menu-compact').show();		
			jQuery('.utility-menu-full').hide();			
		} else if(originalMenuWidth < available) {
			//alert('originalmenuwidth='+originalMenuWidth+'\navailable='+available);
			jQuery('.utility-menu-compact').hide();		
			jQuery('.utility-menu-full').show();
		}
	}
}

//begin jquery events
jQuery('body').on('mouseenter', '.scrollingHotSpotLeft', function(e) {
	jQuery(this).addClass('active');
});
jQuery('body').on('mouseleave', '.scrollingHotSpotLeft', function(e) {
	jQuery(this).removeClass('active');
});
jQuery('body').on('mouseenter', '.scrollingHotSpotRight', function(e) {
	jQuery(this).addClass('active');
});
jQuery('body').on('mouseleave', '.scrollingHotSpotRight', function(e) {
	jQuery(this).removeClass('active');
});
var timeout;
jQuery('body').on('mouseenter', '.sort-wrapper', function(e) {
	clearTimeout(timeout);
	jQuery(this).find('.sort-buttons').fadeIn(100);
	jQuery(this).find('.sort-toggle').addClass('over');
});
jQuery('body').on('mouseleave', '.sort-wrapper', function(e) {
	var self = this;
	timeout = setTimeout(function(){
		jQuery(self).find('.sort-buttons').fadeOut(100);
		jQuery(self).find('.sort-toggle').removeClass('over');
	}, 400);
});
jQuery('#menu-toggle').click(function() {
	var menu = jQuery('#main-menu');	
	jQuery('#sticky-search').removeClass('active').hide();
	jQuery('#search-toggle').removeClass('over');
	jQuery('.section-menu-mobile').removeClass('active').hide();
	jQuery('.section-toggle').removeClass('over');
	if(menu.hasClass('active')) {
		menu.fadeOut(100);
		menu.removeClass('active');
		jQuery('#nav-toggle').removeClass('open');
		jQuery(this).removeClass('over');
	} else {
		menu.fadeIn(100);
		menu.addClass('active');
		jQuery('#nav-toggle').addClass('open');
		jQuery(this).addClass('over');		
	}
});
jQuery('body').on('click', '#main-menu .menu-item-has-children>a', function(e) {
	if(viewport().width < 601) {	
		e.preventDefault();
		jQuery(this).siblings('ul').toggleClass('open');		
	}
});
jQuery('body').on('click', '.section-menu-mobile a.sf-with-ul', function(e) {
	if(viewport().width < 601) {	
		e.preventDefault();
		jQuery(this).siblings('ul').toggleClass('open');		
	}
});
jQuery('body').on('click', '.section-menu-mobile .menu-item-has-children>a', function(e) {
	if(viewport().width < 601 && !jQuery(this).hasClass('sf-with-ul')) {	
		e.preventDefault();
		jQuery(this).siblings('ul').toggleClass('open');		
	}
});
//use touchstart for android devices
jQuery('body').on('touchend', '#main-menu .menu-item-has-children>a', function(e) {
	if(viewport().width < 601) {	
		e.preventDefault();
		jQuery(this).siblings('ul').toggleClass('open');		
	}
});
jQuery('body').on('touchend', '.section-menu-mobile a.sf-with-ul', function(e) {
	if(viewport().width < 601) {	
		e.preventDefault();
		jQuery(this).siblings('ul').toggleClass('open');		
	}
});
jQuery('body').on('touchend', '.section-menu-mobile .menu-item-has-children>a', function(e) {
	if(viewport().width < 601 && !jQuery(this).hasClass('sf-with-ul')) {	
		e.preventDefault();
		jQuery(this).siblings('ul').toggleClass('open');		
	}
});
jQuery('body').on('click', '.section-toggle', function(e) {	
	var sections = jQuery('.section-menu-mobile');	
	jQuery('#sticky-search').removeClass('active').hide();
	jQuery('#search-toggle').removeClass('over');
	jQuery('#main-menu').removeClass('active').hide();
	jQuery('#nav-toggle').removeClass('open');
	jQuery('#menu-toggle').removeClass('over');
	if(sections.hasClass('active')) {
		sections.fadeOut(100);
		sections.removeClass('active');
		jQuery(this).removeClass('over');
	} else {
		sections.fadeIn(100);
		sections.addClass('active');
		jQuery(this).addClass('over');
	}
});
jQuery('#search-toggle').click(function() {
	var form = jQuery('#sticky-search');
	jQuery('#main-menu').removeClass('active').hide();
	jQuery('#nav-toggle').removeClass('open');
	jQuery('#menu-toggle').removeClass('over');
	jQuery('.section-menu-mobile').removeClass('active').hide();
	jQuery('.section-toggle').removeClass('over');
	if(form.hasClass('active')) {
		form.fadeOut(100);
		form.removeClass('active');
		jQuery(this).removeClass('over');
	} else {
		form.fadeIn(100);
		form.addClass('active');
		jQuery(this).addClass('over');
	}
});
jQuery('.after-header').click(function() {
	//hide menu
	var menu = jQuery('#main-menu');
	var menutoggle = jQuery('#menu-toggle');
	menu.fadeOut(100);
	menu.removeClass('active');
	menutoggle.removeClass('over');
	//hide search
	var form = jQuery('#sticky-search');
	var formtoggle = jQuery('#search-toggle');
	form.fadeOut(100);
	form.removeClass('active');
	formtoggle.removeClass('over');
});
jQuery('#header-bar').click(function() {
	//hide menu
	var menu = jQuery('#main-menu');
	var menutoggle = jQuery('#menu-toggle');
	menu.fadeOut(100);
	menu.removeClass('active');
	menutoggle.removeClass('over');
	//hide search
	var form = jQuery('#sticky-search');
	var formtoggle = jQuery('#search-toggle');
	form.fadeOut(100);
	form.removeClass('active');
	formtoggle.removeClass('over');
});
//hide popovers when necessary
jQuery('body').on('mouseleave', '#sticky-bar', function(e) {
	jQuery('.popover-meta').popover('hide');
	jQuery('.popover-sharing').popover('hide');	
});
jQuery('body').on('mouseenter', '.mega-menu li', function(e) {
	jQuery('.popover-meta').popover('hide');
	jQuery('.popover-sharing').popover('hide');	
});
jQuery(".searchform input").keypress(function(event) {
	if (event.which == 13) {
		event.preventDefault();
		var len = jQuery(this).val().length;
		if(len >=3) {
			jQuery(this).parent().submit();
		} else {
			alert("Search term must be at least 3 characters in length");	
		}
	}
});
//compacted control bar elements
jQuery('body').on('mouseenter', '.control-trending-wrapper', function(e) {
	if(viewport().width < 601)
		jQuery('.control-trending').fadeIn(100);
});
jQuery('body').on('mouseenter', '.control-awards-wrapper', function(e) {
	if(viewport().width < 601)
		jQuery('.control-awards').fadeIn(100);
});
jQuery('body').on('mouseleave', '.control-trending-wrapper', function(e) {
	if(viewport().width < 601)
		jQuery('.control-trending').fadeOut(300);
});
jQuery('body').on('mouseleave', '.control-awards-wrapper', function(e) {
	if(viewport().width < 601)
		jQuery('.control-awards').fadeOut(300);
});
//show post left
jQuery('.post-left-toggle').click(function() {
	jQuery('.post-content').toggleClass('post-left-opened');	
});
//show post right
jQuery('.longform-right-selector').click(function() {
	jQuery('.longform-right').toggleClass('longform-right-opened');	
});
//show account info
jQuery('#sticky-login-toggle').click(function() {
	jQuery('#sticky-account-dropdown').animate({				 
		height: 'toggle'				 
	}, 100, 'linear' );	
	jQuery(this).toggleClass('active');
});
//toggle login/register forms
jQuery("#sticky-login").click(function() {
	jQuery('.sticky-login-form').fadeIn(400);	
	jQuery(this).addClass('active');
	jQuery('.sticky-register-form').hide();	
	jQuery('#sticky-register').removeClass('active');
});
jQuery("#sticky-register").click(function() {
	jQuery('.sticky-register-form').fadeIn(400);
	jQuery(this).addClass('active');
	jQuery('.sticky-login-form').hide();	
	jQuery('#sticky-login').removeClass('active');
});	
//submit button hover effects
jQuery(".sticky-submit").hover(function() {
	jQuery(this).toggleClass("active");
});
//login form submission
jQuery(".sticky-login-form #user_pass").keypress(function(event) {
	if (event.which == 13) {
		jQuery(".sticky-form-placeholder .loading").show();
		jQuery('form.sticky-login-form').css({'opacity': '.15'});
		event.preventDefault();
		jQuery(".sticky-login-form").submit();
	}		
});
jQuery("#sticky-login-submit").click(function() {
	jQuery(".sticky-form-placeholder .loading").show();
	jQuery('form.sticky-login-form').css({'opacity': '.15'});
	jQuery(".sticky-login-form").submit();
});
//register form submission
jQuery(".sticky-register-form #user_email").keypress(function(event) {
	if (event.which == 13) {
		jQuery(".sticky-form-placeholder .loading").show();
		jQuery('form.sticky-register-form').css({'opacity': '.15'});
		event.preventDefault();
		jQuery(".sticky-register-form").submit();
	}
});
jQuery("#sticky-register-submit").click(function() {
	jQuery(".sticky-form-placeholder .loading").show();
	jQuery('form.sticky-register-form').css({'opacity': '.15'});
	jQuery(".sticky-register-form").submit();
});
//hide check password message
jQuery(".check-password").click(function() {
	jQuery(this).animate({				 
		height: 'toggle'				 
	}, 100, 'linear' );	
});		
jQuery('.new-articles .selector').click(function() {
	jQuery('#sticky-search').removeClass('active').hide();
	jQuery('#search-toggle').removeClass('over');
	jQuery('#main-menu').removeClass('active').hide();
	jQuery('#menu-toggle').removeClass('over');
	var container = jQuery('.new-articles .post-container');	
	if(container.is(':visible')) {
		container.fadeOut(400);
		jQuery('.new-articles .selector').removeClass('over');	
	} else {
		container.fadeIn(150);
		jQuery('.new-articles .selector').addClass('over');	
	}	
});	
//if disqus is active need to adjust anchor link from comments to disqus thread
function disqusContentsMenu() {
	if (jQuery("#disqus_thread").length > 0){
		jQuery("#comments-anchor-wrapper a").attr("href", "#disqus_thread");
	}	
}
/**
* Check a href for an anchor. If exists, and in document, scroll to it.
* If href argument ommited, assumes context (this) is HTML Element,
* which will be the case when invoked by jQuery after an event
*/
function scroll_if_anchor(href) {
	href = typeof(href) == "string" ? href : jQuery(this).attr("href");	
	if (typeof(href)=='undefined') return;	
	//do not interfere with bootstrap carousels
	if(jQuery(href).length > 0 && !jQuery(this).hasClass('no-scroll')) {			
		if(jQuery('#wpadminbar').length > 0) {
			fromTop = 120;
		} else {
			fromTop = 88;	
		}					
		// If our Href points to a valid, non-empty anchor, and is on the same page (e.g. #foo)
		// Legacy jQuery and IE7 may have issues: http://stackoverflow.com/q/1593174
		if(href.indexOf("#") == 0) {
			var $target = jQuery(href);
	
			// Older browser without pushState might flicker here, as they momentarily
			// jump to the wrong position (IE < 10)
			if($target.length) {
				jQuery('html, body').animate({ scrollTop: $target.offset().top - fromTop });
				if(history && "pushState" in history) {
					history.pushState({}, document.title, window.location.pathname + href);
					return false;
				}
			}
		}
	}
}
// When our page loads, check to see if it contains an anchor
scroll_if_anchor(window.location.hash);
// Intercept all anchor clicks
jQuery("body").on("click", "a", scroll_if_anchor);		

//email subscribe form submission
jQuery("#feedburner_subscribe button").click(function() {		
	jQuery("#feedburner_subscribe").submit();		
});
//scroll all #top elements to top
jQuery("a[href='#top']").click(function() {
	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
	return false;
});	
//image darkening
jQuery('body').on('mouseenter', '.darken img, .the-content a img', function(e) {
	jQuery(this).animate({ opacity: .4 }, 150);
}).on('mouseleave', '.darken img, .the-content a img', function(e) {
	jQuery(this).animate({ opacity: 1.0 }, 500);
});
//reaction mouseovers
jQuery('body').on('mouseenter', '.reaction.clickable', function(e) {
	jQuery(this).addClass('active');
}).on('mouseleave', '.reaction', function(e) {
	jQuery(this).removeClass('active');
});	
//user rating panel display	
jQuery('body').on('mouseover', '.rating-wrapper.rateable', function(e) {
	jQuery(this).addClass('over');
	jQuery(this).find('.form-selector-wrapper').fadeIn(100);		
});
jQuery('body').on('mouseleave', '.rating-wrapper', function(e) {	
	jQuery(this).stop().delay(100)
				.queue(function(n) {
					jQuery(this).removeClass('over');
					n();
				});	
	jQuery(this).find('.form-selector-wrapper').stop().fadeOut(500);		
});
//user comment rating
jQuery( "#respond .form-selector" ).on( "slidestop", function( event, ui ) {
	var divID = jQuery(this).parent().parent().parent().attr("id");	
	var rating = jQuery(this).parent().siblings('.rating-value').html();
	jQuery('#' + divID + ' .theme-icon-check').delay(100).fadeIn(100);
	jQuery('#' + divID + ' .hidden-rating-value').val(rating);
});	

/***************************************
UTILITY FUNCTIONS
***************************************/
//finds whether the bottom of the element is in the viewport
function bottomVisible(obj){
	var a = obj.offset().top;
	var b = obj.height();
	var c = jQuery(window).height();
	var d = jQuery(window).scrollTop();
	return ((c+d) > (a+b));
}
//finds whether the top of the element is in the viewport
function topVisible(obj,offset){	
	
	var viewportHeight = jQuery(window).height(),

        documentScrollTop = jQuery(document).scrollTop(),

        minTop = documentScrollTop + offset,
        maxTop = documentScrollTop + viewportHeight,

        objOffset = obj.offset().top;
		
    return (objOffset > minTop && objOffset < maxTop)
  
}
//utility for determining touch devices
function isTouchDevice() {
  return 'ontouchstart' in window // works on most browsers 
	  || window.navigator.msMaxTouchPoints > 0; // works on ie10
};
//utility for animating rotation
jQuery.fn.animateRotate = function(angle, duration, easing, complete) {
	var args = jQuery.speed(duration, easing, complete);
	var step = args.step;
	return this.each(function(i, e) {
		args.step = function(now) {
			jQuery.style(e, 'transform', 'rotate(' + now + 'deg)');
			if (step) return step.apply(this, arguments);
		};

		jQuery({deg: 0}).animate({deg: angle}, args);
	});
};
//finds width of hidden element
function realWidth(obj){
    var clone = obj.clone();
    clone.css("visibility","hidden");
    jQuery('body').append(clone);
    var width = clone.outerWidth();
    clone.remove();
    return width;
}
//adjust font sizes
var autoSizeText;
autoSizeText = function() {	
  var el, elements, _i, _len, _results;
  elements = jQuery('.textfill');
  //console.log(elements);
  if (elements.length < 0) {
	return;
  }
  _results = [];
  for (_i = 0, _len = elements.length; _i < _len; _i++) {
	el = elements[_i];
	_results.push((function(el) {
	  var reduceText, enlargeText, _results1;
	  if(el.scrollHeight > el.offsetHeight) {		  		  
		  reduceText = function() {			  
			var elNewFontSize;
			elNewFontSize = (parseInt(jQuery(el).css('font-size').slice(0, -2)) - 1) + 'px';
			return jQuery(el).css('font-size', elNewFontSize);
		  };
		  _results1 = [];
		  while (el.scrollHeight > el.offsetHeight) {
			_results1.push(reduceText());
		  }
	  }
	  return _results1;
	})(el));
  }
  return _results;
};
//find actual viewport width to match css media queries
function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}