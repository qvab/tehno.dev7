jQuery.noConflict(); 
	
"use strict";
	
(function ($) {
    $(function () {		
		$(document).ready(function(e) { 
			//user view count
			var postID = $('.post-selector').data('postid');
			if(postID) {
				$.post(itAjax.ajaxurl, {
					action: 'itajax-view',
					postID: postID
				}, function (response) {
					$('.control-bar .view-count').html(response.content);
					//heat index
					updateHeatIndex(postID);
				});
			}
			//compare panel display
			$.post(itAjax.ajaxurl, {
				action: 'itajax-comparepanel'
			}, function (response) {
				$('.compare-items').html(response.content);
				$('.compare-panel').addClass(response.cssactive);	
				$('.compare-toggle').removeClass('comparing');			
				$.each( response.comparing, function( index, value ){					
					$('.compare-toggle-' + value).addClass('comparing');
				});	
			});
		});
		// get addthis shares into custom field
		$('body').on('DOMSubtreeModified', '.addthis_counter', function(e){
			var postID = $('#page-content').data('postid');
			var shareCount = $('.addthis_counter .addthis_button_expanded').html();
			if(shareCount && postID) {
				$.post(itAjax.ajaxurl, {
					action: 'itajax-share-count',
					shareCount: shareCount,
					postID: postID
				}, function (response) {
					//heat index
					updateHeatIndex(postID);					
				});
			}
		});		
		//trending meta click
		$('body').on('click', '.trending-toggle', function(e){
			$(this).toggleClass('over');
			var postID = $(this).data('postid');
			$('.popover-sharing').popover('hide');
			$('.sharing-toggle').removeClass('over');
			$.post(itAjax.ajaxurl, {
				action: 'itajax-trending',
				postID: postID		
			}, function (response) {
				$('.popover.meta .popover-content').each(function(e){
					if(!$(this).children().length > 0)
						$(this).html(response.content);	
				});
			});		
		});
		//sharing meta click
		$('body').on('click', '.sharing-toggle', function(e){
			$(this).toggleClass('over');
			var postID = $(this).data('postid');
			$('.popover-meta').popover('hide');
			$('.trending-toggle').removeClass('over');
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sharing',
				postID: postID		
			}, function (response) {
				$('.popover.sharing .popover-content').each(function(e){
					if(!$(this).children().length > 0)
						$(this).html(response.content);	
				});
				//initialize addthis	
				addthis.toolbox('.addthis_toolbox');
			});		
		});
		//compare meta click
		$('body').on('click', '.compare-toggle', function(e){
			var thistoggle = $(this);
			var loading = thistoggle.children('.loading');
			var panel = $('.compare-panel');			
			var postID = thistoggle.data('postid');
			var alltoggles = $('.compare-toggle-' + postID);
			loading.show();
			if(thistoggle.hasClass('comparing')) {
				alltoggles.removeClass('comparing');
				$.post(itAjax.ajaxurl, {
					action: 'itajax-compare',
					postID: postID,
					perform: 'remove'		
				}, function (response) {				
					$('.compare-block-' + postID).remove();	
					loading.hide();
					if($('.compare-items').children().length == 0) {
						panel.removeClass('active');
					}
				});	
			} else {
				alltoggles.addClass('comparing');
				panel.addClass('active');
				$.post(itAjax.ajaxurl, {
					action: 'itajax-compare',
					postID: postID,
					perform: 'add'		
				}, function (response) {				
					$('.compare-items').append(response.content);
					loading.hide();	
					dynamicElements();				
				});	
			}
		});
		//compare block click
		$('body').on('click', '.compare-block', function(e){
			var block = $(this);
			var panel = $('.compare-panel');			
			var postID = block.data('postid');
			var alltoggles = $('.compare-toggle-' + postID);
			block.css({'opacity': '.17'});
			alltoggles.removeClass('comparing');
			$.post(itAjax.ajaxurl, {
				action: 'itajax-compare',
				postID: postID,
				perform: 'remove'		
			}, function (response) {				
				block.remove();
				if($('.compare-items').children().length == 0) {
					panel.removeClass('active');
				}				
			});	
		});
		//compare remove click
		$('body').on('click', '.compare-remove', function(e){					
			var postID = $(this).data('postid');
			$('.col-' + postID).css({'opacity': '.25'});
			var alltoggles = $('.compare-toggle-' + postID);
			alltoggles.removeClass('comparing');
			var num = $('.compare-num').html();
			$.post(itAjax.ajaxurl, {
				action: 'itajax-compare',
				postID: postID,
				perform: 'remove'		
			}, function (response) {				
				$('.col-' + postID).remove();
				//update number
				num = num - 1;
				$('.compare-num').html(num);
				//hide table if necessary
				var len = $('.table-comparison > tbody').find('> tr:first > th').length;	
				if(len < 2) $('.table-comparison').hide();	
			});	
		});
		// like button
		$('body').on('click', 'a.do-like', function(e){
			$(this).removeClass('do-like');
			$(this).find('.icon').css({'opacity': '.15'});
			var postID = $(this).data('postid');
			var likeaction = $(this).data('likeaction');
			var location = $(this).closest('.container-fluid').data('location');	 
			var _this = this;
			$.post(itAjax.ajaxurl, {
				action: 'itajax-like',
				postID: postID,
				likeaction: likeaction,
				location: location
			}, function (response) {
				$(_this).addClass('do-like');
				$(_this).find('.icon').css({'opacity': '1'});
				$('a.like-button.' + postID + ' .numcount').html(response.content);
				if(likeaction=='like') {
					$('a.like-button.' + postID + ' .icon').removeClass('like').addClass('unlike');
					$('a.like-button.' + postID).data('likeaction', 'unlike');
				} else {
					$('a.like-button.' + postID + ' .icon').removeClass('unlike').addClass('like');
					$('a.like-button.' + postID).data('likeaction', 'like');
				}
				$('#ajax-error').hide();				
				//heat index
				updateHeatIndex(postID);
			});			
		});		
		// menu top level item mouseovers
		var timeout;
		$('.mega-menu li').hoverIntent(
			function(e) {
				clearTimeout(timeout);			
				$(this).siblings('li').removeClass('over');
				$(this).siblings('li').children(".mega-content").hide();
				$(this).siblings('li').children(".mega-loader").hide();
				$(this).addClass('over');
				$(this).children(".mega-content").show();
				adjustSectionTerms(this);
			},
			function(e) {
				var self = this;		
				timeout = setTimeout(function(){
					$(self).removeClass('over');
					$(self).children(".mega-content").hide();
					$(self).children(".mega-loader").hide();
				}, 400);
			}
		);
		$('body').on('mouseenter', '.mega-content', function(e) {	
			clearTimeout(timeout);
		});	
		$('.mega-menu li.unloaded').hoverIntent(
			function(e) {
				var loop = $(this).data('loop');
				var object = $(this).data('object');
				var objectid = $(this).data('objectid');
				var object_name = $(this).data('object_name');
				var type = $(this).data('type');
				var _this = this;	
				$(_this).children(".mega-loader").show();		
				
				timeout = setTimeout(function(){
					
					$.post(itAjax.ajaxurl, {
						action: 'itajax-menu-terms',
						object: object,
						objectid: objectid,
						object_name: object_name,
						loop: loop,
						type: type
					}, function (response) {
						$(_this).children(".mega-content").html(response.content);		
						$('#ajax-error').hide();
						$(_this).children(".mega-loader").hide();
						dynamicElements();	
						$(_this).addClass('loaded').removeClass('unloaded');
					});				
				}, 400);
			},
			function(e) {
				clearTimeout(timeout);
			}
		);
		// menu second-level item clicks
		$('body').on('click', '.mega-menu a.list-item.inactive', function(e) {
			
			var loop = $(this).closest('.menu-item').data('loop');			
			var object = $(this).closest('.menu-item').data('object');
			var object_name = $(this).closest('.menu-item').data('object_name');
			var numarticles = $(this).data('numarticles');
			var len = $(this).data('len');
			var location = $(this).data('location');
			var csscol = $(this).data('csscol');
			var size = $(this).data('size');
			var sorter = $(this).data('sorter');
			var _this = this;			
			
			$(".mega-menu .post-list .loading").show();
			$(".mega-menu .post-list-inner").animate({opacity: "0.15"}, 0);	
			$(_this).addClass('active').removeClass('inactive');
			$(_this).siblings().addClass('inactive').removeClass('active');
			
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sort',
				sorter: sorter,
				object: object,
				object_name: object_name,
				loop: loop,
				location: location,
				len: len,
				numarticles: numarticles,
				csscol: csscol,
				size: size
			}, function (response) {
				$(_this).parent().siblings().find('.post-list-inner').html(response.content);	
				if(response.label!='') {
					var termlink = $(_this).closest('.mega-wrapper').find('.term-link');
					termlink.show();
					termlink.html(response.label);	
					termlink.attr('href', response.href);
				}			
				$('#ajax-error').hide();
				$(".mega-menu .post-list .loading").hide();
				$(".mega-menu .post-list-inner").animate({opacity: "1"}, 500);	
				dynamicElements();	
			});	
		});		
		// main loop sorting
		$('body').on('click', '.sortbar .sort-metrics a', function(e){	
			$(this).addClass('active');
			$(this).siblings().removeClass('active');	
						
			var loop = $(this).parent().data('loop');
			var location = $(this).parent().data('location');
			var layout = $(this).parent().data('layout');
			var thumbnail = $(this).parent().data('thumbnail');
			var rating = $(this).parent().data('rating');
			var meta = $(this).parent().data('meta');
			var icon = $(this).parent().data('icon');
			var award = $(this).parent().data('award');
			var badge = $(this).parent().data('badge');
			var authorship = $(this).parent().data('authorship');
			var size = $(this).parent().data('size');
			var excerpt = $(this).parent().data('excerpt');
			var sorter = $(this).data('sorter');
			var numarticles = $(this).parent().data('numarticles');
			var paginated = $(this).parent().data('paginated');
			var timeperiod = $(this).parent().data('timeperiod');
			var disablecategory = $(this).parent().data('disable-category');
			var disablereviewlabel = $(this).parent().data('disable-reviewlabel');
			var title = $(this).attr('title');
			var container = $(this).closest('.post-container');
			var widget = $(this).closest('.widget-panel.compact-header');
			var currentquery = container.data('currentquery');
			var _this = this;			
			
			container.find(".load-more-wrapper").hide();
			container.find(".loading.load-sort").show();
			container.find(".loop").animate({opacity: "0.2"}, 0);
			container.find(".loop-placeholder").animate({opacity: "0"}, 0);	
			container.find(".sortbar .metric-text").not(".static").animate({opacity: "0.2"}, 0);
					
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sort',
				loop: loop,
				location: location,
				layout: layout,
				thumbnail: thumbnail,
				rating: rating,
				meta: meta,
				award: award,
				badge: badge,
				authorship: authorship,
				icon: icon,
				size: size,
				excerpt: excerpt,
				sorter: sorter,
				numarticles: numarticles,				
				paginated: paginated,
				title: title,
				timeperiod: timeperiod,
				disablecategory: disablecategory,
				disablereviewlabel: disablereviewlabel,
				currentquery: currentquery
			}, function (response) {				
				container.find(".loading").hide();
				container.find(".loop").animate({opacity: "1"}, 500);
				container.find(".loop-placeholder").animate({opacity: "1"}, 500);
				container.find(".sortbar .metric-text").animate({opacity: "1"}, 500);
				container.find(".loop").html(response.content);
				if(response.updatepagination==1) {
					container.find(".pagination-wrapper").html(response.pagination);
					container.find(".pagination-wrapper.mobile").html(response.paginationmobile);
				}
				container.find(".sortbar .metric-text").not(".static").html(title);
				widget.find('.bar-label').css('padding-top', '4px');
				container.find(".pagination").data("sorter", sorter);
				container.find(".load-more-wrapper").data("paginated", 1);
				container.find(".load-more-wrapper").data("sorter", sorter);
				if(response.pages > 1) {
					container.find(".load-more-wrapper").show();
					container.find(".last-page").hide();
				}
				$("#ajax-error").hide();
				//sort button clicked is within the trending slider
				if($(_this).closest('.trending').length > 0) {
					$(".trending-content").smoothDivScroll("jumpToElement", "first");
				}
				dynamicElements();
			});
		});	
		// sections sorting
		$('body').on('click', '.sortbar .sort-sections a', function(e){	
			$(this).addClass('active');
			$(this).siblings().removeClass('active');				
			
			var sorter = $(this).data('sorter');
			var loop = $(this).parent().data('loop');
			var location = $(this).parent().data('location');
			var layout = $(this).parent().data('layout');
			var thumbnail = $(this).parent().data('thumbnail');
			var rating = $(this).parent().data('rating');
			var meta = $(this).parent().data('meta');
			var icon = $(this).parent().data('icon');
			var award = $(this).parent().data('award');
			var badge = $(this).parent().data('badge');
			var authorship = $(this).parent().data('authorship');
			var excerpt = $(this).parent().data('excerpt');	
			var numarticles = $(this).parent().data('numarticles');
			var size = $(this).parent().data('size');
			var container = $(this).closest('.post-container');
			var currentquery = container.data('currentquery');
			var _this = this;	
			
			container.find(".loading.load-sort").show();
			container.find(".loop").animate({opacity: "0.15"}, 0);	
					
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sort',
				loop: loop,
				location: location,
				layout: layout,
				thumbnail: thumbnail,
				rating: rating,
				meta: meta,
				award: award,
				badge: badge,
				authorship: authorship,
				icon: icon,
				excerpt: excerpt,
				sorter: sorter,
				size: size,
				numarticles: numarticles,
				currentquery: currentquery
			}, function (response) {				
				container.find(".loading").hide();
				container.find(".loop").animate({opacity: "1"}, 500);
				container.find(".loop").html(response.content);		
				$("#ajax-error").hide();
				dynamicElements();
			});
		});		
		// main loop pagination
		$('body').on('click', '.pagination a', function(e){
			$(this).addClass('active');
			$(this).siblings().removeClass('active');
			
			$('html, body').animate({
				scrollTop: $(this).parent().parent().parent().offset().top - 100
			}, 300);
			
			var loop = $(this).parent().data('loop');
			var location = $(this).parent().data('location');
			var layout = $(this).parent().data('layout');
			var sorter = $(this).parent().data('sorter');
			var thumbnail = $(this).parent().data('thumbnail');
			var rating = $(this).parent().data('rating');
			var meta = $(this).parent().data('meta');
			var icon = $(this).parent().data('icon');
			var award = $(this).parent().data('award');
			var badge = $(this).parent().data('badge');
			var size = $(this).parent().data('size');
			var authorship = $(this).parent().data('authorship');
			var excerpt = $(this).parent().data('excerpt');
			var numarticles = $(this).parent().data('numarticles');
			var disablecategory = $(this).parent().data('disable-category');
			var disablereviewlabel = $(this).parent().data('disable-reviewlabel');
			var paginated = $(this).data('paginated');
			var container = $(this).closest('.post-container');
			var currentquery = container.data('currentquery');
			var _this = this;
			
			container.find(".loading.load-sort").show();
			container.find(".loop").animate({opacity: "0.2"}, 0);
			container.find(".loop-placeholder").animate({opacity: "0"}, 0);		
			
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sort',
				loop: loop,
				location: location,
				layout: layout,
				thumbnail: thumbnail,
				rating: rating,
				meta: meta,
				award: award,
				badge: badge,
				authorship: authorship,
				icon: icon,
				size: size,
				excerpt: excerpt,
				sorter: sorter,
				numarticles: numarticles,
				disablecategory: disablecategory,
				disablereviewlabel: disablereviewlabel,		
				paginated: paginated,
				currentquery: currentquery
			}, function (response) {
				container.find(".loading").hide();
				container.find(".loop").animate({opacity: "1"}, 500);
				container.find(".loop-placeholder").animate({opacity: "1"}, 500);
				container.find(".loop").html(response.content);
				if(response.updatepagination==1) {
					container.find(".pagination-wrapper").html(response.pagination);
					container.find(".pagination-wrapper.mobile").html(response.paginationmobile);
				}
				container.find('.sortbar .sort-buttons').data('paginated', paginated);
				$('#ajax-error').hide();
				//add to browser history
				//history.pushState({}, document.title, window.location.pathname + window.location.hash);
				dynamicElements();
			});
		});		
		// infinite load more
		$('body').on('click', '.load-more-wrapper', function(e){
			var loop = $(this).data('loop');
			var location = $(this).data('location');
			var layout = $(this).data('layout');
			var sorter = $(this).data('sorter');
			var thumbnail = $(this).data('thumbnail');
			var rating = $(this).data('rating');
			var meta = $(this).data('meta');
			var icon = $(this).data('icon');
			var award = $(this).data('award');
			var badge = $(this).data('badge');
			var authorship = $(this).data('authorship');
			var excerpt = $(this).data('excerpt');
			var numarticles = $(this).data('numarticles');
			var numpages = $(this).data('numpages');
			var paginated = $(this).data('paginated') + 1;
			var container = $(this).closest('.post-container');
			var currentquery = container.data('currentquery');
			var _this = this;
			
			$(this).hide();
			container.find(".loading.load-infinite").show();	
			
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sort',
				loop: loop,
				location: location,
				layout: layout,
				thumbnail: thumbnail,
				rating: rating,
				meta: meta,
				award: award,
				badge: badge,
				authorship: authorship,
				icon: icon,
				excerpt: excerpt,
				sorter: sorter,
				numarticles: numarticles,	
				numpages: numpages,			
				paginated: paginated,
				currentquery: currentquery
			}, function (response) {
				if(numpages > paginated && response.pages > paginated) {
					$(_this).show();
					container.find(".last-page").hide();
				} else {
					container.find(".last-page").show();
				}
				container.find(".loading").hide();
				container.find(".loop").append(response.content);
				$(_this).data('paginated', paginated);
				$('#ajax-error').hide();
				dynamicElements();
			});
		});
		// recommended filtering
		$('body').on('click', '#recommended .magazine-header a', function(e){
			$("#recommended .loading").show();
			$("#recommended .loop").animate({opacity: "0.15"}, 0);
			$(this).closest('.magazine-header').find('a').removeClass('active');	
			$(this).addClass('active');			
			
			var postID = $(this).closest('.magazine-header').data('postid');
			var loop = $(this).closest('.magazine-header').data('loop');
			var location = $(this).closest('.magazine-header').data('location');
			var numarticles = $(this).closest('.magazine-header').data('numarticles');
			var disablecategory = $(this).closest('.magazine-header').data('disable-category');
			var disabletrending = $(this).closest('.magazine-header').data('disable-trending');
			var disablesharing = $(this).closest('.magazine-header').data('disable-sharing');
			var sorter = $(this).data('sorter');
			var method = $(this).data('method');
			
			$.post(itAjax.ajaxurl, {
				action: 'itajax-sort',
				postID: postID,
				loop: loop,
				location: location,
				numarticles: numarticles,	
				disablecategory: disablecategory,
				disabletrending: disabletrending,
				disablesharing: disablesharing,			
				sorter: sorter,
				method: method
			}, function (response) {
				$("#recommended .loading").hide();
				$("#recommended .loop").animate({opacity: "1"}, 500);
				$("#recommended .loop").html(response.content);
				$('#ajax-error').hide();
				dynamicElements();
			});
		});		
		// star user ratings
		$('.rating-wrapper .rateit').bind('rated reset', function (e) {
			 var ri = $(this);
			
			 var noupdate = ri.data('noupdate');
			 var rating = ri.rateit('value');
			 var postID = ri.data('postid');
			 var meta = ri.data('meta');
			 var divID = ri.parent().parent().parent().attr('id');
			 var metric = 'stars';
			 var unlimitedratings = ri.data('unlimitedratings');
		
			 //disable rating ability after user submits rating
			 if(unlimitedratings != 1) {
				ri.rateit('readonly', true);
			 }
			 
			 if(noupdate==1) {
				 var divID = $(this).parent().parent().parent().attr("id");
				 $('#' + divID + ' .hidden-rating-value').val(rating);
			 } else {	
			 
			 	$.post(itAjax.ajaxurl, {
					action: 'itajax-user-rate',
					postID: postID,
					meta: meta,
					rating: rating,
					metric: metric,
					divID: divID
				}, function (response) {
					$('.user-rating .rated-legend').addClass('active');
					$('.ratings .total .user_rating > div').stop().delay(200)
						.fadeOut(200)
						.delay(500)
						.queue(function(n) {
							$(this).html(response.totalrating);
							n();
						}).fadeIn(400);
					 $('#' + response.divID + ' .theme-icon-check').delay(200).fadeIn(100);	
					 $('.user-container	.meter').css('-webkit-transform','rotate(' + response.amount + ')');
					 $('.user-container	.meter').css('-moz-transform','rotate(' + response.amount + ')');
					 $('.user-container	.meter').css('-o-transform','rotate(' + response.amount + ')');
					 $('.user-container	.meter').css('msTransform','rotate(' + response.amount + ')');
					 $('.user-container	.meter').css('transform','rotate(' + response.amount + ')');					 
					 if(response.cssfill == 'showfill') {
						 $('.user-container .meter-slice').addClass('showfill');
						 if($('.user-container .meter-slice .meter.fill').length > 0) {
							$('.user-container .meter-slice .meter.fill').show();
							$('.user-container .meter-slice .meter.fill').css('-webkit-transform','rotate(' + response.amount + ')');
							$('.user-container .meter-slice .meter.fill').css('-moz-transform','rotate(' + response.amount + ')');
							$('.user-container .meter-slice .meter.fill').css('-o-transform','rotate(' + response.amount + ')');
							$('.user-container .meter-slice .meter.fill').css('msTransform','rotate(' + response.amount + ')');
							$('.user-container .meter-slice .meter.fill').css('transform','rotate(' + response.amount + ')');							
						 } else {
							$('.user-container .meter-slice').append('<div class="meter fill" style="-webkit-transform:rotate(' + response.amount + ');-moz-transform:rotate(' + response.amount + ');-o-transform:rotate(' + response.amount + ');-ms-transform:rotate(' + response.amount + ');transform:rotate(' + response.amount + ');"></div>');
						 }
					 } else {
						  $('.user-container .meter-slice').removeClass('showfill');
						  $('.user-container .meter-slice .meter.fill').hide();
					 }	
					 // hide comment ratings after top ratings are added
				 	 $('#respond .rating-wrapper').hide();
					 $('.hidden-rating-value').val('');					 
				});
			 }
		 });
	
		// update user ratings
		$( ".user-rating .form-selector" ).on( "slidestop", function( event, ui ) {
			var meta = $(this).parent().parent().parent().data('meta');
			var divID = $(this).parent().parent().parent().attr("id");			
			var postID = $(this).parent().parent().parent().data('postid');
			var rating = ui.value;
			var metric = $(this).parent().parent().parent().data('metric');	
			
			$.post(itAjax.ajaxurl, {
				action: 'itajax-user-rate',
				postID: postID,
				meta: meta,
				rating: rating,
				metric: metric,
				divID: divID
			}, function (response) {
				$('.ratings .rated-legend').addClass('active');
				$('#' + response.divID + '_wrapper').addClass('active');
				 if(response.unlimitedratings != 1) {
					$('#' + response.divID + '_wrapper').removeClass('rateable');
				 }				 
				 $('#' + response.divID + ' .rating-value').fadeOut(100)
					.delay(100)
					.queue(function(n) {
						$(this).html(response.newrating);
						n();
					}).fadeIn(150);
				 $('.ratings .total .user_rating > div').stop().delay(200)
					.fadeOut(200)
					.delay(500)
					.queue(function(n) {
						$(this).html(response.totalrating);
						n();
					}).fadeIn(400);
				 $('#' + response.divID + ' .theme-icon-check').delay(200).fadeIn(100);	
				 $('.user-container	.meter').css('-webkit-transform','rotate(' + response.amount + ')');
				 $('.user-container	.meter').css('-moz-transform','rotate(' + response.amount + ')');
				 $('.user-container	.meter').css('-o-transform','rotate(' + response.amount + ')');
				 $('.user-container	.meter').css('msTransform','rotate(' + response.amount + ')');
				 $('.user-container	.meter').css('transform','rotate(' + response.amount + ')');				 
				 if(response.cssfill == 'showfill') {
					 $('.user-container .meter-slice').addClass('showfill');
					 if($('.user-container .meter-slice .meter.fill').length > 0) {
						$('.user-container .meter-slice .meter.fill').show();
					 	$('.user-container .meter-slice .meter.fill').css('-webkit-transform','rotate(' + response.amount + ')');
						$('.user-container .meter-slice .meter.fill').css('-moz-transform','rotate(' + response.amount + ')');
						$('.user-container .meter-slice .meter.fill').css('-o-transform','rotate(' + response.amount + ')');
						$('.user-container .meter-slice .meter.fill').css('msTransform','rotate(' + response.amount + ')');
						$('.user-container .meter-slice .meter.fill').css('transform','rotate(' + response.amount + ')');
					 } else {
					 	$('.user-container .meter-slice').append('<div class="meter fill" style="-webkit-transform:rotate(' + response.amount + ');-moz-transform:rotate(' + response.amount + ');-o-transform:rotate(' + response.amount + ');-ms-transform:rotate(' + response.amount + ');transform:rotate(' + response.amount + ');"></div>');
					 }
				 } else {
					  $('.user-container .meter-slice').removeClass('showfill');
					  $('.user-container .meter-slice .meter.fill').hide();
				 }	
				 // hide comment ratings after top ratings are added
				 $('#respond .rating-wrapper').hide();
				 $('.hidden-rating-value').val('');				 				
			});			
		});
		
		// reaction button
		$('body').on('click', '.reaction.clickable', function(e){
			var postID = $('.reactions-wrapper').data('postid');
			var unlimitedreactions = $('.reactions-wrapper').data('unlimitedreactions');
			var reaction = $(this).data('reaction'); 
			var _this = this;
			$('.reaction-percentage').stop().animate({opacity: ".1"}, 100);
			$(this).addClass('selected');
			$(this).siblings().removeClass('selected');
			if(unlimitedreactions==0) {
				$(this).removeClass('clickable');
				$(this).siblings().addClass('clickable');
			}
			$.post(itAjax.ajaxurl, {
				action: 'itajax-reaction',
				postID: postID,
				reaction: reaction,
				unlimitedreactions: unlimitedreactions
			}, function (response) {
				if(unlimitedreactions==1) $(_this).addClass('clickable');
				$.each(response, function(key, value) {
					$('.reaction-percentage.' + key).html(value);
					var c = parseInt(value.replace('%',''));
					c = Math.round(c / 10);
					$('.reaction-percentage.' + key).removeClass().addClass('size' + c).addClass('reaction-percentage').addClass(key);					
				});
				$('.reaction-percentage').stop().animate({opacity: "1"}, 1200);
				$('#ajax-error').hide();
				//heat index
				updateHeatIndex(postID);
			});			
		});
		
		//heat index
		function updateHeatIndex(postID) {
			$.post(itAjax.ajaxurl, {
				action: 'itajax-heat-index',
				postID: postID
			}, function (response) {
				$('.control-bar .heat-index .numcount').html(response.content);
			});
		}
	});
}(jQuery));