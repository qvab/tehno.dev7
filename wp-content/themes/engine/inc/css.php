<?php global $post; ?>
<style type="text/css">
<?php 	 
#TEMPLATE COLORS - do these before accent colors so hovers apply correctly
$c = it_get_setting('color_accent_alt');
if(!empty($c)) echo '.trending-color{background:'.$c.'}.highlighted-label{color:'.$c.'}';	
$c = it_get_setting('color_sticky_bar_bg');
if(!empty($c)) echo '#sticky-bar,#section-menu.standard-menu ul ul li a.parent-item:hover,#section-menu.standard-menu ul ul li.over > a.parent-item,#section-menu.standard-menu ul ul li.current-menu-item > a.parent-item,#section-menu.standard-menu ul ul li a:hover,#section-menu.standard-menu ul ul li.over > a,#section-menu.standard-menu ul ul li.current-menu-item > a,.category-circle{background:'.$c.'}';	
$c = it_get_setting('color_active_bg');
if(!empty($c)) echo '.new-articles .selector.active,.new-articles .selector.over,.new-articles .post-container,#section-menu ul li a.parent-item:hover,#section-menu ul li.over > a.parent-item,#section-menu ul li.current-menu-item > a.parent-item,#section-menu .placeholder,#section-menu .term-list,.terms-more .sort-toggle,#section-menu.standard-menu ul li a.parent-item:hover,#section-menu.standard-menu ul li.over > a.parent-item,#section-menu.standard-menu ul li.current-menu-item > a.parent-item,#section-menu.standard-menu ul li a:hover,#section-menu.standard-menu ul li.over > a,#section-menu.standard-menu ul li.current-menu-item > a,#section-menu.standard-menu ul ul,.sticky-toggle:hover,.sticky-toggle.active,.sort-buttons,.sort-toggle.active,.sort-toggle.over,.topten-number,.section-toggle.active,.section-toggle.over,.section-menu-mobile.active{background:'.$c.'}';
$c = it_get_setting('color_sticky_bar_fg');
if(!empty($c)) echo '#section-menu>ul>li>a,.new-articles .selector,a.sticky-toggle,#sticky-bar .social-badges a,#section-menu .article-info,#section-menu .header,#section-menu .read-more,#section-menu .compact-panel .article-title,.dark-bg .trending-toggle,.dark-bg .sharing-toggle,.new-articles .compact-panel .article-title,#sticky-bar .social-badges a:hover,.sticky-toggle:hover,.sticky-toggle.active,.section-toggle,.section-menu-mobile ul li a,.sort-toggle.active,.sort-toggle.over,.sort-buttons a, .sort-buttons span.page-numbers,.topten-number{color:'.$c.'} .terms-more .sort-toggle.active span,.terms-more .sort-toggle.over span{color:'.$c.'!important} .section-menu-mobile ul li a:hover,.section-menu-mobile ul li.over > a,.section-menu-mobile ul li.current-menu-item > a{background-color:rgba(0,0,0,.2)}';
$c = get_background_color();
if(!empty($c)) {
	$c = '#' . $c;
	$c_rgb = implode(",", hex2rgb($c)); 
	?>
body.it-background,.after-billboard{background-color:<?php echo $c; ?> !important;}	
	<?php 
}
$c = it_get_setting('color_panel_bg');
if(!empty($c)) {
	$c_rgb = implode(",", hex2rgb($c));
	?>
#header-posts .scroller,.content-panel,#header-terms>div,.builder-widgets .widgets-inner,.loop-info-wrapper,.widget-topics .trending-bar,.builder-trending .scroller,.builder-utility .utility-inner,.builder-connect .connect-inner,.builder-html .html-inner,.builder-topten .scroller,.builder-utility ul li ul,.compact-panel.active,#header-terms a:hover,.longform-post .longform-left,.ratings .rating-label,.ratings.stars-wrapper .rating-value-wrapper,.ratings .rating-value-wrapper{background:<?php echo $c; ?>}.ratings .rating-line,#comments .comment-ratings,.comment-ratings-inner,.panel,.progress,.single-page .wp-caption{background:rgba(0,0,0,.1)}.ratings .rating-value-wrapper{border-color:rgba(0,0,0,.1)}
		
<?php }	
$c = it_get_setting('color_panel_header_bg');
if(!empty($c)) {
	$c_rgb = implode(",", hex2rgb($c));
	?>
.bar-header,.magazine-header,.boxed .sort-toggle,.builder-connect .connect-social,.widget_c .overlay-panel,div.scrollingHotSpotLeft,div.scrollingHotSpotRight{background-color:<?php echo $c; ?>}
.boxed .bar-header,.load-more,.last-page,.subfooter,.load-more-wrapper.active .load-more,.load-more-wrapper.active a.load-more,.post-left,.reactions-wrapper,.reactions-label,.big-like{background:rgba(<?php echo $c_rgb; ?>,.95)}
		
<?php }	
$c = it_get_setting('color_borders');
if(!empty($c)) echo '.magazine-right .magazine-label,.magazine-content .more-link,.builder-utility .bar-header,.builder-utility ul li ul,.builder-utility ul li ul li ul,.builder-widgets .widgets .header,.builder-widgets .widgets .bar-header,.compact-panel,.form-404,.single-page.shadowed,.post-right,.post-right.content-panel,.single-page .divider-line,.procon-box .procon.pro,.procon-box .procon.con,.reactions-wrapper,.big-like,.reactions-label,.reaction,.postinfo,#recommended,#comments,#comments .comment-wrapper,#comments ul ul .comment,#comments .comment-ratings,#reply-form,.longform-right-panel,.author-panel,.bp-page .widget div.item-options,div.scrollingHotSpotLeft,div.scrollingHotSpotRight,div.scrollingHotSpotLeft,.builder-connect .boxed .bar-header,.builder-connect .connect-email,.builder-connect .connect-social,#main-menu ul li a,.builder-topten .center-panel,.builder-connect .connect-social,#recommended .overlay-panel,.shadowed,#header-posts .compact-panel,#main-menu ul,#sticky-search,.boxed .bar-header,.boxed .sort-toggle,.builder-trending .compact-panel,.builder-topten .center-panel,.builder-connect .connect-social,.widgets .header,.widgets .bar-header,.widget_c .overlay-panel,.widgets-sections.widget_b .load-more-wrapper.compact .load-more,.widgets .it-widget-tabs ul.sort-buttons,.post-left-opened .post-left,.contents-menu .contents-title,.ratings .rating-wrapper .theme-icon-check,.signoff,.bp-page #buddypress form#whats-new-form textarea,.bp-page #buddypress div.item-list-tabs a,.bp-page #buddypress div#group-create-tabs ul li span,.builder-topten .boxed .bar-header,.load-more,.last-page,.load-more-wrapper.compact .load-more,.bar-header,.panel-style .bar-header,.pagination,.magazine-mid,.magazine-right,#header-terms .term-panel.alt,#header-terms .term-panel.first,#header-terms a,.load-more, .last-page,.single-page .wp-caption,.panel,.control-bar{border-color:'.$c.'}.widget-section .border,.center-panel .border,.single-page .divider-line{background:'.$c.'}.control-bar .triangle{border-color:'.$c.' rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) rgba(0, 0, 0, 0)}';
	
#TEXT COLORS
$c = it_get_setting('color_panel_fg');
if(!empty($c)) echo 'body,.loop-panel .excerpt,.loop-panel h2.article-title,.compact-panel .article-title,.widget_b .article-title,.trending-toggle,.sharing-toggle,.widget_a .heat-index,.loop-panel .authorship,.heat-index,.builder-utility a,.social-counts a,#header-terms a,.builder-widgets .bar-label .label-text,.pagination .active,.pagination a:active,.pagination a.active:hover,.pagination span.page-number,.social-counts .social-label,.social-counts .social-icon,.heat-index .theme-icon-flame,.social-badges a,.bar-label .label-text span,.authorinfo a.author-name,#respond p.logged-in-as,.single-page .author a,.single-page .authorship,.section-title,.section-subtitle,.ratings .rating-label,.ratings.stars-wrapper .rating-value .stars span:before,.longform-right .heat-index,.longform-right a,.ratings .rating-value-wrapper,.longform-right .review-label,.longform-right .review-label,.control-bar .heat-index .theme-icon-flame,.longform-right .heat-index .theme-icon-flame,.single-page .main-subtitle,.procon-box .procon,.single-page .nav-tabs>li>a,.single-page .nav-pills>li>a,.single-page .top a:hover,.single-page .nav-pills>li>a:hover,.single-page .nav-pills>li>a,.authorinfo .author-bio,#comments .comment-author a,#comments .comment-author a:hover,#comments .comment-rating .section-subtitle,.jumbotron .tagline,.single-page .wp-caption-text,.single-page blockquote small,.single-page .top a,.control-bar .heat-index, .longform-right .heat-index,.control-bar .metric,.single-page .review-label,.control-bar .meta-comments a,h2.author-name a,.author-link,.template-authors .author-profile-fields a,.author-link a,.compact-panel .stars span, .overlay-panel .stars span{color:'.$c.'}.authorinfo .author-profile-fields a,.authorinfo .author-profile-fields a:hover,#comments a.comment-meta,#comments a.comment-meta:hover{color:'.$c.'!important}';
$c = it_get_setting('color_panel_header_fg');
if(!empty($c)) echo '.bar-label .label-text,.bar-header .pagination .active,.bar-header .pagination a:active,.bar-header .pagination a.active:hover,.bar-header .pagination span.page-number,.load-more,.last-page,.load-more-wrapper.active .load-more,.load-more-wrapper.active a.load-more,.pagination a:hover,.magazine-categories a,.contents-menu .nav>li>a,.reactions-label,.reaction.clickable,.big-like .labeltext,.big-like a.like-button{color:'.$c.'}';
$c = it_get_setting('color_panel_alt_fg');
if(!empty($c)) echo '.bar-label .metric-text,.sort-toggle,.pagination a,.pagination>span.page-number,.pagination>span.page-numbers,.widget-section a.more-link,.builder-connect .follow-label,.magazine-content .more-link,a.load-more,.subfooter,.ratings .hovertorate .hover-text,.ratings .hovertorate .theme-icon-down-fat{color:'.$c.'}';

		
#MAIN ACCENT COLOR 
$accent = it_get_setting('color_accent');
if(empty($accent)) $accent = '#0077DB';
$accent_rgb = implode(",", hex2rgb($accent));
$opacity = it_get_setting('hover_opacity');
$opacity = empty($opacity) ? '.25' : '.' . $opacity;
$overlay_opacity = it_get_setting('overlay_opacity');
$overlay_opacity = empty($overlay_opacity) ? '.15' : '.' . $overlay_opacity;
?>	
#logo:not(.no-color),.sticky-home{background:rgba(<?php echo $accent_rgb; ?>,1);filter:none;}
#header-terms a:hover,#section-menu .mega-wrapper .term-list a,.review-star,.sort-buttons a.active,.sort-buttons a:hover,.center-panel.active,.widget-section a.more-link:hover,.topic-panel.active .topic-name,.trending-bar.active .title,.social-counts a:hover .social-number,.widgets .it-widget-tabs .sort-buttons a:hover,#comments-social-tab a:hover,.widgets .social-badges a:hover,.magazine-left .compact-panel.active .article-title,.terms-more .sort-toggle span,.the-content a:not(.styled),a.nav-link:hover,.contents-menu .nav>li>a:hover,.reaction.clickable.active,.reaction.selected,.reaction.selected .theme-icon-check,.postinfo a:hover,.magazine-categories a.active,#comments .comment-pagination a:hover,.utility-menu a:hover,.utility-menu li.over>a,.utility-menu li.current-menu-item a,.utility-menu li.current-menu-parent>a,.utility-menu li.current-menu-ancestor>a,.widgets #menu-utility-menu a:hover,.widgets .it-widget-tabs .ui-tabs-active a,.widgets #wp-calendar a:hover,.contents-menu .nav>li.active>a,.builder-connect .social-badges a:hover,.woocommerce.woocommerce-page ul.cart_list li a:hover, .woocommerce.woocommerce-page ul.product_list_widget li a:hover,.compare-block,#section-menu .term-link{color:<?php echo $accent; ?>;}
.overlay-hover{background:rgba(<?php echo $accent_rgb; ?>,<?php echo $opacity; ?>) !important;filter:none;}	
.sticky-color,.color-line,.center-panel.active .topten-number,.magazine-title,.main-category,.postnav-layer,.post-left-toggle,.woocommerce.woocommerce-page #content input.button, .woocommerce.woocommerce-page #respond input#submit, .woocommerce.woocommerce-page a.button, .woocommerce.woocommerce-page button.button, .woocommerce.woocommerce-page input.button{background:rgba(<?php echo $accent_rgb; ?>,1);filter:none;}
.magazine-header,.post-left,.meter-wrapper .meter,.large-meter .meter-wrapper .meter,.compare-block{border-color:<?php echo $accent; ?>;}	
.details-box-wrapper,.ratings .total-wrapper,.woocommerce.woocommerce-page ul.products li.product a img{border-bottom-color:<?php echo $accent; ?>;}
.overlay-layer{background:rgba(<?php echo $accent_rgb; ?>,<?php echo $overlay_opacity; ?>) !important;filter:none;}
.overlay-hover{text-shadow:0 0 30px rgba(<?php echo $accent_rgb; ?>,1);}
	
<?php
#FONT FACES
$f = it_get_setting('font_main');	    
if(!empty($f) && $f!='spacer') echo 'body {font-family:'.$f.';font-weight:normal;font-style:normal}';
$f = it_get_setting('font_menus');	    
if(!empty($f) && $f!='spacer') echo '.term-list a,#menu-toggle .label-text,.new-articles .selector .new-label,#main-menu ul li a,#section-menu ul li a,.builder-utility ul a,.magazine-categories a,.magazine-more a,.contents-menu a{font-family:'.$f.';font-weight:normal;font-style:normal}';
$f = it_get_setting('font_panel_headers');	    
if(!empty($f) && $f!='spacer') echo '.bar-label .label-text,.bar-label .metric-text,.sort-toggle,.pagination a,.pagination>span.page-number,.pagination>span.page-numbers,.widget-section .category-name,.more-link,.magazine-title a,.magazine-label,.subfooter,#section-menu .header,#header-terms .trending-label,.more-text,.main-category,a.nav-link,.contents-title,.section-title,.meta-label,.section-subtitle,.labeltext,.reactions-label,.postinfo-label,.author-label,.magazine-title,.now-reading-label,.review-label{font-family:'.$f.';font-weight:normal;font-style:normal}';
$f = it_get_setting('font_numbers');	    
if(!empty($f) && $f!='spacer') echo '.new-number,.trending-meta,.heat-index,.metric,.topten-number,.social-number,.rating-value,.editor_rating,.user_rating,.reaction-percentage,.rating-wrapper .value{font-family:'.$f.';font-weight:normal;font-style:normal}';
$f = it_get_setting('font_headers');	    
if(!empty($f) && $f!='spacer') echo 'h1, h2, h3, h4, h5, h6 {font-family:'.$f.';font-weight:normal;font-style:normal}';

#FONT SIZES
$f = it_get_setting('font_menus_size');	    
if(!empty($f)) echo '#section-menu ul li a,.builder-utility ul a,.magazine-categories a{font-size:'.$f.'px;}';
$f = it_get_setting('font_content_size');	    
if(!empty($f)) echo '.the-content,.the-content p{font-size:'.$f.'px;}.the-content p{line-height:1.42}';
$f = it_get_setting('font_excerpt_size');	    
if(!empty($f)) echo '.excerpt{font-size:'.$f.'px;}.loop-panel .excerpt{line-height:1.2}';

#BILLBOARD BACKGROUND
$bg_billboard = get_background_color();
if(empty($bg_billboard)) $bg_billboard = '#F0F0F0';
#override the main site background with the billboard background
if(is_single()) echo 'body.it-background {background-color:' . $bg_billboard . ' !important;}';	

#GET PAGE SPECIFIC BACKGROUNDS
if(is_single() || is_page()) { 		
	$bg_color = get_post_meta($post->ID, "_bg_color", $single = true);
	if(!empty($bg_color) && $bg_color!='#') $bg_billboard = $bg_color;		
	$bg_color_override = get_post_meta($post->ID, "_bg_color_override", $single = true);
	$bg_image = get_post_meta($post->ID, "_bg_image", $single = true);
	$bg_position = get_post_meta($post->ID, "_bg_position", $single = true);
	$bg_repeat = get_post_meta($post->ID, "_bg_repeat", $single = true);
	$bg_attachment = get_post_meta($post->ID, "_bg_attachment", $single = true);		
	$layout = is_single() ? it_get_setting('post_layout') : 'classic';
	$layout_meta = get_post_meta($post->ID, "_post_layout", $single = true);
	if(!empty($layout_meta) && $layout_meta!='') $layout = $layout_meta;		
}
#GET CATEGORY SPECIFIC BACKGROUNDS - overwrites page-specific if any
$category_id = it_page_in_category($post->ID);
if($category_id) {
	$categories = it_get_setting('categories');	 
	foreach($categories as $category) {
		if(is_array($category)) {
			if(array_key_exists('id',$category)) {
				if($category['id'] == $category_id) {
					if(!empty($category['bg_color'])) {
						$bg_color=$category['bg_color'];
						$bg_color_override='';
					}
					if(!empty($category['bg_image'])) $bg_image=$category['bg_image'];
					if(!empty($category['bg_position'])) $bg_position=$category['bg_position'];
					if(!empty($category['bg_repeat'])) $bg_repeat=$category['bg_repeat'];
					if(!empty($category['bg_attachment'])) $bg_attachment=$category['bg_attachment'];
					break;
				}
			}
		}
	}		
}
#APPLY BACKGROUNDS
if(is_single() || is_page() || $category_id) {
	if($bg_color) { 
		$bg_color_rgb = implode(",", hex2rgb($bg_color)); ?>
		body.it-background {background-color:<?php echo $bg_color; ?> !important}		
	<?php } ?>
	<?php if($bg_color_override) { ?>
		body.it-background {background-image:none !important}
	<?php } ?>
	<?php if($bg_image) { ?>
		body.it-background {background-image:url(<?php echo $bg_image; ?>) !important}
	<?php } ?>
	<?php if($bg_position) { ?>
		body.it-background {background-position:top <?php echo $bg_position; ?> !important}
	<?php } ?>
	<?php if($bg_repeat) { ?>
		body.it-background {background-repeat:<?php echo $bg_repeat; ?> !important}
	<?php } ?>
	<?php if($bg_attachment) { ?>
		body.it-background {background-attachment:<?php echo $bg_attachment; ?> !important}
	<?php } ?>	
	<?php if($layout=='billboard') { ?>
		body.it-background {background-image:none !important}
	<?php } ?>
	<?php if($bg_repeat!='repeat-x' && $bg_repeat!='repeat-y' && $bg_repeat!='repeat' && $bg_attachment=='fixed') { ?>
		body.it-background {background-size:cover !important}
	<?php }		
} 
#display this after the page specific in case this post has a unique background color assigned 
$billboard_rgb = implode(",", hex2rgb($bg_billboard)); ?>	
.billboard-overlay {
background: -moz-linear-gradient(top,  rgba(0,0,0,.48) 68%, rgba(<?php echo $billboard_rgb; ?>,1) 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(68%,rgba(0,0,0,.48)), color-stop(100%,rgba(<?php echo $billboard_rgb; ?>,1)));
background: -webkit-linear-gradient(top,  rgba(0,0,0,.48) 68%,rgba(<?php echo $billboard_rgb; ?>,1) 100%);
background: -o-linear-gradient(top,  rgba(0,0,0,.48) 68%,rgba(<?php echo $billboard_rgb; ?>,1) 100%);
background: -ms-linear-gradient(top,  rgba(0,0,0,.48) 68%,rgba(<?php echo $billboard_rgb; ?>,1) 100%);
background: linear-gradient(to bottom,  rgba(0,0,0,.48) 68%,rgba(<?php echo $billboard_rgb; ?>,1) 100%);}	
<?php #CATEGORIES
$categories = it_get_setting('categories');
foreach($categories as $category) {
	if(is_array($category)) {
		if(array_key_exists('id',$category)) {
			if(!empty($category['id'])) {
				$id = $category['id'];					
				$color = $category['color'];
				$color_rgb = empty($color) ? '' : implode(",", hex2rgb($color));
				$icon = $category['icon'];
				$icon_white = $category['iconwhite'];
				if(empty($icon_white)) $icon_white = $icon;
				#get other icon sizes
				$icon_path = pathinfo($icon);
				$dir = $icon_path['dirname'] . '/';
				$file = $icon_path['filename'];
				$ext = $icon_path['extension'];
				$icon_small = $dir . $file . '-16x16.' . $ext;
				$icon_small_hd = $dir . $file . '-32x32.' . $ext;
				$icon_med = $dir . $file . '-28x28.' . $ext;
				$icon_med_hd = $dir . $file . '-56x56.' . $ext;
				$icon_large = $dir . $file . '-64x64.' . $ext;
				$icon_large_hd = $dir . $file . '.' . $ext;				
				$icon_white_path = pathinfo($icon_white);
				$dir = $icon_white_path['dirname'] . '/';
				$file = $icon_white_path['filename'];
				$ext = $icon_white_path['extension'];
				$icon_small_white = $dir . $file . '-16x16.' . $ext;
				$icon_small_white_hd = $dir . $file . '-32x32.' . $ext;
				$icon_med_white = $dir . $file . '-28x28.' . $ext;
				$icon_med_white_hd = $dir . $file . '-56x56.' . $ext;
				$icon_large_white = $dir . $file . '-64x64.' . $ext;
				$icon_large_white_hd = $dir . $file . '.' . $ext;
				?>
#section-menu ul li.menu-item-<?php echo $id; ?> .mega-wrapper .term-list a,#section-menu ul li.menu-item-<?php echo $id; ?> .terms-more .sort-toggle span,.widget-section.category-<?php echo $id; ?> .center-panel.active,.widget-section.category-<?php echo $id; ?> a:hover,.overlay-panel.category-<?php echo $id; ?> .review-star,.magazine-panel.category-<?php echo $id; ?> a:hover,.magazine-panel.category-<?php echo $id; ?> .magazine-left .compact-panel.active .article-title,.single-page.category-<?php echo $id; ?> a.nav-link:hover,.single-page.category-<?php echo $id; ?> .contents-menu .nav>li.active>a,.single-page.category-<?php echo $id; ?> .contents-menu .nav>li>a:hover.category-<?php echo $id; ?> .reaction.clickable.active,.category-<?php echo $id; ?> .reaction.selected,.category-<?php echo $id; ?> .reaction.selected .theme-icon-check,.category-<?php echo $id; ?> .postinfo .category-list a:hover,.single-page.category-<?php echo $id; ?> .magazine-categories a.active,.single-page.category-<?php echo $id; ?> #comments .comment-pagination a:hover,#section-menu li.menu-item-<?php echo $id; ?> .term-link{color:<?php echo $color; ?>;}

.category-<?php echo $id; ?> #logo:not(.no-color),.category-<?php echo $id; ?> .sticky-home,.category-<?php echo $id; ?> .sticky-color,.loop-panel.category-<?php echo $id; ?> .color-line,.widget .category-<?php echo $id; ?> .color-line,.menu-item-<?php echo $id; ?> .color-line,.overlay-panel.category-<?php echo $id; ?> .overlay-layer .color-line,.overlay-panel.category-<?php echo $id; ?> .overlay-hover .color-line,.menu-item-<?php echo $id; ?> .color-line,.compact-panel.category-<?php echo $id; ?> .color-line,.sort-buttons.sort-sections a.category-<?php echo $id; ?>.active,.widget-section.category-<?php echo $id; ?> .category-circle,.category-tile.category-<?php echo $id; ?>.active .tile-layer,.magazine-panel.category-<?php echo $id; ?> .magazine-title,.single-page.category-<?php echo $id; ?> .main-category,.single-page.category-<?php echo $id; ?> .postnav-layer,.single-page.category-<?php echo $id; ?> .post-left-toggle{background:<?php echo $color; ?> !important;filter:none;}

.overlay-panel.category-<?php echo $id; ?> .overlay-layer{background:rgba(<?php echo $color_rgb; ?>,<?php echo $overlay_opacity; ?>) !important;filter:none;}

.overlay-panel.category-<?php echo $id; ?> .overlay-hover,.compact-panel.category-<?php echo $id; ?> .overlay-hover{text-shadow:0 0 30px rgba(<?php echo $color_rgb; ?>,1);}
.overlay-panel.category-<?php echo $id; ?> .overlay-hover,.compact-panel.category-<?php echo $id; ?> .overlay-hover{background:rgba(<?php echo $color_rgb; ?>,<?php echo $opacity; ?>) !important;filter:none;}	

.sort-buttons.sort-sections a.category-<?php echo $id; ?>,.magazine-panel.category-<?php echo $id; ?> .magazine-header,.single-page.category-<?php echo $id; ?> .details-box-wrapper,.single-page.category-<?php echo $id; ?> .ratings .total-wrapper {border-bottom-color:<?php echo $color; ?>}

.single-page.category-<?php echo $id; ?> .post-left{border-color:<?php echo $color; ?>}

.overlay-panel.category-<?php echo $id; ?> .overlay-hover,.compact-panel.category-<?php echo $id; ?> .overlay-hover,

.category-<?php echo $id; ?> .large-meter .meter-wrapper .meter {border-color:<?php echo $color; ?>;}

@media screen {
.category-icon-<?php echo $id; ?>-16 {background:url(<?php echo $icon_small; ?>) no-repeat 0px 0px;background-size:16px 16px !important;width:16px;height:16px;float:left;}
.category-icon-<?php echo $id; ?>-16.white, .sort-sections a.active .category-icon-<?php echo $id; ?>-16 {background:url(<?php echo $icon_small_white; ?>) no-repeat 0px 0px;background-size:16px 16px !important;width:16px;height:16px;float:left;}	
.category-icon-<?php echo $id; ?>-28 {background:url(<?php echo $icon_med; ?>) no-repeat 0px 0px;background-size:28px 28px !important;width:28px;height:28px;float:left;}
.category-icon-<?php echo $id; ?>-28.white {background:url(<?php echo $icon_med_white; ?>) no-repeat 0px 0px;background-size:28px 28px !important;width:28px;height:28px;float:left;}	
.category-icon-<?php echo $id; ?>-64 {background:url(<?php echo $icon_large; ?>) no-repeat 0px 0px;background-size:64px 64px !important;width:64px;height:64px;float:left;}
.category-icon-<?php echo $id; ?>-64.white {background:url(<?php echo $icon_large_white; ?>) no-repeat 0px 0px;background-size:64px 64px !important;width:64px;height:64px;float:left;}
}
/*use this to get rid of chrome console warning, but doesn't work yet in firefox*/
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dppx) {}
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) { 
.category-icon-<?php echo $id; ?>-16 {background:url(<?php echo $icon_small_hd; ?>) no-repeat 0px 0px;background-size:16px 16px !important;width:16px;height:16px;float:left;}
.category-icon-<?php echo $id; ?>-16.white, .sort-sections a.active .category-icon-<?php echo $id; ?>-16 {background:url(<?php echo $icon_small_white_hd; ?>) no-repeat 0px 0px;background-size:16px 16px !important;width:16px;height:16px;float:left;}	
.category-icon-<?php echo $id; ?>-28 {background:url(<?php echo $icon_med_hd; ?>) no-repeat 0px 0px;background-size:28px 28px !important;width:28px;height:28px;float:left;}
.category-icon-<?php echo $id; ?>-28.white {background:url(<?php echo $icon_med_white_hd; ?>) no-repeat 0px 0px;background-size:28px 28px !important;width:28px;height:28px;float:left;}	
.category-icon-<?php echo $id; ?>-64 {background:url(<?php echo $icon_large_hd; ?>) no-repeat 0px 0px;background-size:64px 64px !important;width:64px;height:64px;float:left;}
.category-icon-<?php echo $id; ?>-64.white {background:url(<?php echo $icon_large_white_hd; ?>) no-repeat 0px 0px;background-size:64px 64px !important;width:64px;height:64px;float:left;}	
}
			<?php } 
		}
	}
}	
#AWARDS/BADGES
$awards = it_get_setting('review_awards');
foreach($awards as $award){ 
	if(is_array($award)) {
		if(array_key_exists(0, $award)) {
			$awardname = stripslashes($award[0]->name);
			$awardid = it_get_slug($awardname, $awardname);
			$awardicon = $award[0]->icon;
			$awardiconwhite = $award[0]->iconwhite;
			if(empty($awardiconwhite)) $awardiconwhite = $awardicon;
			?>
.award-icon-<?php echo $awardid; ?> {background:url(<?php echo $awardicon; ?>) no-repeat 0px 0px;background-size:16px 16px !important;width:16px;height:16px;float:left;}
.white .award-icon-<?php echo $awardid; ?> {background:url(<?php echo $awardiconwhite; ?>) no-repeat 0px 0px;background-size:16px 16px !important;width:16px;height:16px;float:left;}
		<?php } 
	}
}	
#APPLY CUSTOM CSS - leave the opening/closing php tags to create better view source readability
#general
if( it_get_setting( 'custom_css' ) ) echo stripslashes( it_get_setting( 'custom_css' ) );	
?>
<?php 
#large only
if( it_get_setting( 'custom_css_lg' ) ) { ?> 
@media (min-width: 1200px) {<?php echo stripslashes( it_get_setting( 'custom_css_lg' ) )?>} 
<?php }
#medium and down
if( it_get_setting( 'custom_css_md' ) ) { ?> 
@media (max-width: 1199px) {<?php echo stripslashes( it_get_setting( 'custom_css_md' ) )?>} 
<?php }
#medium only
if( it_get_setting( 'custom_css_md_only' ) ) { ?> 
@media (min-width: 992px) and (max-width: 1199px) {<?php echo stripslashes( it_get_setting( 'custom_css_md_only' ) )?>} 
<?php }
#small and down
if( it_get_setting( 'custom_css_sm' ) ) { ?> 
@media (max-width: 991px) {<?php echo stripslashes( it_get_setting( 'custom_css_sm' ) )?>} 
<?php }
#small only
if( it_get_setting( 'custom_css_sm_only' ) ) { ?> 
@media (min-width: 768px) and (max-width: 991px) {<?php echo stripslashes( it_get_setting( 'custom_css_sm_only' ) )?>} 
<?php }
#extra small only
if( it_get_setting( 'custom_css_xs' ) ) { ?> 
@media (max-width: 767px) {<?php echo stripslashes( it_get_setting( 'custom_css_xs' ) )?>} 
<?php }	?>
</style>