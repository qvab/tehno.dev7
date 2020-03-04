<?php
#default settings (use "Standard Pages" theme options for defaults)
$sidebar = __('Page Sidebar',IT_TEXTDOMAIN);
$layout = 'classic';
$post_article_title_disable = it_get_setting('post_article_title_disable');
$billboard_featured_image_disable = it_get_setting('billboard_featured_image_disable');
$editor_rating_disable = it_get_setting('review_editor_rating_disable');
$user_rating_disable = it_get_setting('review_user_rating_disable');
$clickable = !it_get_setting('clickable_image_disable');
$sidebar_layout = it_get_setting('page_sidebar_layout');
$disable_image = it_component_disabled('image', $post->ID);
$disable_heat_index = it_component_disabled('heat', $post->ID);
$disable_view_count = it_component_disabled('views', $post->ID);
$disable_like_count = it_component_disabled('likes', $post->ID);
$disable_comment_count = it_component_disabled('comment_count', $post->ID);
$disable_awards = it_component_disabled('awards', $post->ID);
$disable_badges = it_component_disabled('badges', $post->ID);
$disable_authorship = it_component_disabled('authorship', $post->ID);
$disable_comments = it_component_disabled('comments', $post->ID);
$disable_postnav = it_component_disabled('postnav', $post->ID);
$disable_sharing = it_component_disabled('sharing', $post->ID);
$disable_controlbar = it_component_disabled('controlbar', $post->ID, $forcepage = true);
$disable_video = it_component_disabled('video', $post->ID);
$caption = it_get_setting('featured_image_caption');
$template = it_get_template_file();
$details_position = 'none';
$ratings_position = 'none';
$reactions_position = 'none';
$affiliate_position = 'none';
$contents_menu = 'none';
$disable_authorinfo = true;
$disable_postinfo = true;
$image_can_float = false;
$disabled_menu_items = array();
$article_title = '';
$disable_main_header = false;
$disable_recommended = false;
$disable_title = false;
$isreview = false;
$pagecss = '';
$layoutcss = '';
$item_type = 'http://schema.org/Article';
$item_prop = '';
$has_details = it_has_details($post->ID);
$cssadmin = is_admin_bar_showing() ? ' admin-bar' : '';
$imagesize = 'single';
$imagewidth = 800;
$imageheight = 600;

#get just the primary category id
$categoryargs = array('postid' => $post->ID, 'label' => false, 'icon' => false, 'white' => true, 'single' => true, 'wrapper' => false, 'id' => true);	
$category_id = it_get_primary_categories($categoryargs);
$categorycss = ' category-' . $category_id;
#reset args for category display
$categoryargs = array('postid' => $post->ID, 'label' => true, 'icon' => true, 'white' => true, 'single' => true, 'wrapper' => false, 'id' => false, 'size' => 16);
$category = it_get_primary_categories($categoryargs);

#section-specific settings
if(is_404()) {
	wp_reset_postdata();
	#settings for 404 pages
	$main_title = __('404 Error - Page Not Found', IT_TEXTDOMAIN);
	$subtitle = __('We could not find the page you were looking for. Try searching for it:', IT_TEXTDOMAIN);		
	$disable_controlbar = true;	
	$disable_main_header = true;
	$disable_recommended = true;
	$disable_sharing = true;
	$disable_authorship = true;
	$disable_postnav = true;
	$layout = 'classic';
} elseif(is_page()) {
	#settings for all standard WordPress pages	
	$subtitle = get_post_meta($post->ID, "_subtitle", $single = true);	
	$page_comments = it_get_setting('page_comments');
	$disable_recommended = true;
	$disable_authorship = true;
	$disabled_menu_items[] = 'rating';
	$disabled_menu_items[] = 'overview';
	$layout = 'classic';
	$disable_postnav = true;
	if(!$page_comments) {
		$disable_comments = true;
		$disable_comment_count = true;
		$disabled_menu_items[] = 'comments';
	}
} elseif(is_single()) {
	#settings for single posts
	$layout = it_get_setting('post_layout');
	$sidebar_layout = it_get_setting('post_sidebar_layout');
	$subtitle = get_post_meta($post->ID, "_subtitle", $single = true);	
	$contents_menu = it_get_setting('contents_menu');	
	$article_title = it_get_setting('article_title');
	$schema = it_get_setting('review_schema');	
	$disable_authorinfo = it_get_setting('post_author_disable');
	$disable_postinfo = false;
	$details_position = it_get_setting('review_details_position');
	$details_position = !empty($details_position) ? $details_position : 'top';
	$ratings_position = it_get_setting('review_ratings_position');
	$ratings_position = !empty($ratings_position) ? $ratings_position : 'top';	
	$reactions_position = it_get_setting('reactions_position');
	$reactions_position = !empty($reactions_position) ? $reactions_position : 'bottom';	
	if(!comments_open()) $disabled_menu_items[] = 'comments';
	$affiliate_position = it_get_setting('affiliate_position');	
	$affiliate_position = !empty($affiliate_position) ? $affiliate_position : 'after-content';	
}
#settings for buddypress pages
if(it_buddypress_page()) {	
	$disable_postnav = true;
	$disable_controlbar = true;
	$disable_recommended = true;
	$disable_authorinfo = true;
	$disable_postinfo = true;
	$disable_authorship = true;
	$disable_comments = true;
	$disable_sharing = true;
	$layout = 'classic';
	$pagecss = 'bp-page';
	$article_title = '';
	$contents_menu = 'none';
	$reactions_position = 'none';
	$sidebar_layout = it_get_setting('bp_sidebar_layout');
	if(it_get_setting('bp_sidebar_unique')) $sidebar = __('BuddyPress Sidebar',IT_TEXTDOMAIN);	
}
#settings for woocommerce pages
if(it_woocommerce_page()) {	
	$disable_postnav = true;
	$disable_controlbar = true;
	$disable_recommended = true;
	$disable_authorinfo = true;
	$disable_postinfo = true;
	$disable_authorship = true;
	$disable_comments = true;
	$disable_sharing = true;
	$layout = 'classic';
	$pagecss = 'woo-page';
	$article_title = '';
	$contents_menu = 'none';
	$reactions_position = 'none';
	$sidebar_layout = it_get_setting('woo_sidebar_layout');
	if(it_get_setting('woo_sidebar_unique')) $sidebar = __('WooCommerce Sidebar',IT_TEXTDOMAIN);	
}
#specific template files
switch($template) {
	case 'template-authors.php':
		$pagecss = 'template-authors';		
		$disable_controlbar = true;	
		$disable_main_header = true;
		$disable_recommended = true;	
		$layout = 'classic';	
	break;	
}

#page-specific settings
$sidebar_layout_meta = get_post_meta($post->ID, "_sidebar_layout", $single = true);
if(!empty($sidebar_layout_meta) && $sidebar_layout_meta!='') $sidebar_layout = $sidebar_layout_meta;
$layout_meta = get_post_meta($post->ID, "_post_layout", $single = true);
if(!empty($layout_meta) && $layout_meta!='' && !is_404()) $layout = $layout_meta;
$image_meta = get_post_meta($post->ID, "_image_disable", $single = true);
if(!empty($image_meta) && $image_meta!='') $disable_image = $image_meta;
$image_display_meta = get_post_meta($post->ID, "_image_display", $single = true);
if(!empty($image_display_meta) && $image_display_meta!='') $disable_image = false;
$sidebar_meta = get_post_meta($post->ID, "_custom_sidebar", $single = true);
if(!empty($sidebar_meta) && $sidebar_meta!='') $sidebar = $sidebar_meta;
$post_type = get_post_meta( $post->ID, IT_META_POST_TYPE, $single = true );
$disable_title_meta = get_post_meta($post->ID, IT_META_DISABLE_TITLE, $single = true);
if(!empty($disable_title_meta) && $disable_title_meta!='') $disable_title = $disable_title_meta;
$article_title_meta = get_post_meta($post->ID, "_article_title", $single = true);
if(!empty($article_title_meta) && $article_title_meta!='') $article_title = $article_title_meta;
$disable_review = get_post_meta($post->ID, IT_META_DISABLE_REVIEW, $single = true);
$video = get_post_meta($post->ID, "_featured_video", $single = true);
$sharing_disable_meta = get_post_meta($post->ID, "_sharing_disable", $single = true);
if(!empty($sharing_disable_meta) && $sharing_disable_meta!='') $disable_sharing = $sharing_disable_meta;
$heat_index_disable_meta = get_post_meta($post->ID, "_heat_index_disable", $single = true);
if(!empty($heat_index_disable_meta) && $heat_index_disable_meta!='') $disable_heat_index = $heat_index_disable_meta;
$view_count_disable_meta = get_post_meta($post->ID, "_view_count_disable", $single = true);
if(!empty($view_count_disable_meta) && $view_count_disable_meta!='') $disable_view_count = $view_count_disable_meta;
$like_count_disable_meta = get_post_meta($post->ID, "_like_count_disable", $single = true);
if(!empty($like_count_disable_meta) && $like_count_disable_meta!='') $disable_like_count = $like_count_disable_meta;
$unwrap_page = get_post_meta($post->ID, "_unwrap_page", $single = true);
$postnav_meta = get_post_meta($post->ID, "_post_nav_disable", $single = true);
if(!empty($postnav_meta) && $postnav_meta!='') $disable_postnav = $postnav_meta;
$schema_meta = get_post_meta($post->ID, IT_META_SCHEMA, $single = true);
if(!empty($schema_meta) && $schema_meta!='') $schema = $schema_meta;

#contents menu
$contents_menu_display = false;
$contents_menu_meta = get_post_meta($post->ID, "_contents_menu", $single = true);
if($contents_menu=='optin' && $contents_menu_meta) $contents_menu_display = true;
if(($contents_menu=='both' || ($contents_menu=='reviews' && $disable_review!='true')) && !$contents_menu_meta) $contents_menu_display = true;
if(is_page() && $contents_menu_meta) $contents_menu_display = true; #pages do not follow the same logic as posts - default is hidden unless displayed per page
if($details_position=='none' || !$has_details) $disabled_menu_items[] = 'overview';
$menucss = $contents_menu_display ? '' : ' hidden-contents-menu';

#this post is a review
if(it_has_rating($post->ID)) {
	#rich snippets
	if($schema=='editor') {
		$item_type = 'http://schema.org/Review';
		$item_prop = ' itemprop="itemReviewed"';
	} else {
		$item_type = 'http://schema.org/Product';
		$item_prop = ' itemprop="name"';
	}
	$isreview = true;
} elseif($user_rating_disable) {
	$disabled_menu_items[] = 'rating';
	$ratings_position = 'none';
}
if(($post_type=='article' || $disable_review=='true') && $post_article_title_disable) $article_title = '';

#post left
$disable_post_left = !$contents_menu_display && $disable_postnav ? true : false;

#determine layouts and css classes
$csswrapper = empty($unwrap_page) || $unwrap_page=='' ? ' shadowed' : ' unwrapped';
$cssright = empty($unwrap_page) || $unwrap_page=='' ? ' content-panel' : ' unwrapped';
if($disable_post_left) $cssright .= ' wide';
if($disable_controlbar) $cssright .= ' no-control-bar';
switch($layout) {
	case 'classic':
		$layoutcss = ' classic-post';
	break;	
	case 'billboard':
		$layoutcss = ' billboard-post';
		if($billboard_featured_image_disable && !empty($image_meta) && $image_meta!='') $disable_image = true;
	break;
	case 'longform':
		$layoutcss = ' longform-post';
		$sidebar_layout = 'full';
		$imagesize = 'longform';
		$imagewidth = 1200;
	break;
}
$sidebar_layout = empty($sidebar_layout) ? 'right' : $sidebar_layout;
switch($sidebar_layout) {
	case 'left':
		$csscol1 = 'col-md-3 single-sidebar-selector clearfix';
		$csscol2 = 'col-md-9 single-post-selector';
		$csscol3 = '';
	break;
	case 'right':
		$csscol1 = '';
		$csscol2 = 'col-md-9 single-post-selector';
		$csscol3 = 'col-md-3 single-sidebar-selector clearfix';
	break;
	case 'full':
		$csscol1 = '';
		$csscol2 = 'col-md-12';
		$csscol3 = '';
	break;	
}

$disable_subtitle = empty($subtitle) ? true : false;

#get largest size featured images for overlay backgrounds
$overlay_image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
$bg_image = get_post_meta($post->ID, "_bg_image", $single = true);
$billboard_overlay = !empty($bg_image) ? $bg_image : $overlay_image[0];
#get smaller image for now reading overlay
$overlay_image_small = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'square-large' );

#setup args
$awardsargs = array('postid' => $post->ID, 'single' => true, 'badge' => false, 'white' => true, 'wrapper' => true);
$badgesargs = array('postid' => $post->ID, 'single' => false, 'badge' => true, 'white' => false, 'wrapper' => true);	
$likesargs = array('postid' => $post->ID, 'label' => false, 'icon' => true, 'clickable' => true, 'tooltip_hide' => true, 'showifempty' => true);
$viewsargs = array('postid' => $post->ID, 'label' => false, 'icon' => true, 'tooltip_hide' => true);
$commentsargs = array('postid' => $post->ID, 'label' => false, 'icon' => true, 'showifempty' => true, 'tooltip_hide' => true, 'anchor_link' => true);
$imageargs = array('postid' => $post->ID, 'size' => $imagesize, 'width' => $imagewidth, 'height' => $imageheight, 'wrapper' => true, 'itemprop' => true, 'link' => $clickable, 'type' => 'normal', 'caption' => $caption);
$videoargs = array('url' => $video, 'video_controls' => 'true', 'parse' => true, 'frame' => true, 'autoplay' => 0, 'type' => 'embed', 'width' => 640, 'height' => 360);
$sharingargs = array('title' => get_the_title(), 'description' => get_excerpt_by_id($post->ID), 'url' => get_permalink(), 'showmore' => true, 'style' => 'visible');
$heatargs = array('postid' => $post->ID, 'icon' => true, 'tooltip' => false);

#get awards and badges into variables
$awards = $disable_awards ? '' : it_get_awards($awardsargs);
$badges = $disable_badges ? '' : it_get_awards($badgesargs);

?>

<div <?php post_class('container-fluid no-padding single-wrapper', $post->ID); ?> data-location="single-page">
		
    <div class="row">
    
        <div class="col-md-12">
        
        	<?php if($layout=='billboard') { ?>
        	
        	
            
            	<div class="billboard-image-wrapper">
                
                    <div class="billboard-image" style="background-image:url(<?php echo $billboard_overlay; ?>);"></div>
                    
                    <div class="billboard-overlay"></div>
                    
                </div>
                            
                <div class="billboard-wrapper">
                            
                    <?php echo it_ad_action('billboard_title_before'); ?>
                    
                    <h1 class="main-title single-title entry-title"<?php echo $item_prop; ?>><?php echo get_the_title(); ?></h1>
                    
                    <?php if($isreview) 
						echo '<div class="review-label padded-panel"><span class="theme-icon-star-full"></span>' . __('Review',IT_TEXTDOMAIN) . '</div>'; ?>          
                    
                    <?php if(!$disable_authorship) 
						echo '<div class="billboard-authorship">' . it_get_authorship('both', true, false, '', $isreview) . '</div>'; ?>
                    
                    <?php echo it_ad_action('billboard_title_after'); ?>
                                
                    <?php if(!$disable_sharing) 
						echo '<div class="billboard-sharing">' . it_get_sharing($sharingargs) . '</div>'; ?>
                    
                </div>
            
            <?php } ?>
            
            <?php echo it_background_ad(); #full screen background ad ?>
    
            <div class="container-inner">
            
            	<?php do_action('it_page_wrapper_before'); ?>
            
                <div class="row">
                
                	<?php if($sidebar_layout=='left') { ?> 
                    
                    <div class="<?php echo $csscol1 ?>">
                    
                    	<div class="content-panel shadowed fixed-object single-sidebar<?php echo $cssadmin; ?>">
                    
                            <?php echo it_widget_panel($sidebar); ?>
                            
                        </div>                         
                                    
                	</div>
                    
                    <?php } ?>
                    
                    <div class="<?php echo $csscol2; ?>">
                
                        <div class="single-page<?php echo $csswrapper . ' ' . $pagecss . ' ' . $categorycss . $menucss . $layoutcss ?>">
    
                            <?php do_action('it_content_page_before'); ?> 
                                              
                            <?php if (is_404()) : ?>
                            
                            	<div class="post-right<?php echo $cssright; ?>"> 
                                
                                	<div class="post-content page-content">
                            
										<?php echo it_ad_action('classic_title_before'); ?>
                                    
                                        <h1 class="main-title"><?php echo $main_title; ?></h1>
                            
                                        <p><?php echo $subtitle; ?></p>
                                               
                                        <div class="form-404">   
                                            <form method="get" class="form-search" action="<?php echo home_url(); ?>/">
                                                <input class="search-query form-control" name="s" type="text" placeholder="<?php _e('type to search',IT_TEXTDOMAIN); ?>">
                                            </form> 
                                        </div> 
                                        
                                    </div> 
                                
                                </div>          
                            
                            <?php elseif($template=='template-authors.php') : ?>
                            
                            	<div class="post-right<?php echo $cssright; ?>"> 
                                
                                	<div class="post-content page-content">
                            
										<?php echo it_ad_action('classic_title_before'); ?>
                                    
                                        <h1 class="main-title"><?php echo get_the_title(); ?></h1>
                                            
                                        <?php echo it_get_content($article_title); ?>
                                        
                                        <?php echo it_get_author_loop(); #get authors and display loop ?> 
                                        
                                    </div>
                                    
                                </div>                   
                                
                            <?php elseif (have_posts()) : ?>
                        
                                <?php while (have_posts()) : the_post(); ?>
                                
                                	<?php #this div is special. it is targeted specifically by the ajax view incrementer via the post-selector class ?>
                                    <div id="post-<?php the_ID(); ?>" class="post-content post-selector" data-postid="<?php echo get_the_ID(); ?>">
                                    
                                    	<?php if(!$disable_post_left) { ?>
                                    
                                            <div class="post-left fixed-object<?php echo $cssadmin; ?>">
                                            
                                            	<div class="post-left-toggle add-active"><span class="theme-icon-bookmark"></span><span class="theme-icon-right-open"></span><span class="theme-icon-left-open"></span></div>
                                            
                                            	<?php if(!is_page()) { #pages don't have categories ?>
                                            
                                                    <div class="main-category">
                                                
                                                        <?php echo $category ?>
                                                    
                                                    </div>
                                                    
                                                <?php } ?>
                                                
                                                <?php if(!$disable_postnav) { ?>
                                                
                                                    <div class="postnav-wrapper" style="background-image:url(<?php echo $overlay_image_small[0]; ?>);">
                                                    
                                                        <div class="postnav-layer"></div>
                                                        
                                                        <div class="now-reading">
                                                        
                                                            <div class="now-reading-label"><?php _e('Now Reading',IT_TEXTDOMAIN); ?></div>
                                                            
                                                            <div class="now-reading-title"><?php echo get_the_title(); ?></div>
                                                        
                                                        </div>
                                                        
                                                        <?php echo it_get_postnav(); ?>
                                                    
                                                    </div>
                                                    
                                                <?php } ?>
                                                
                                                <?php if($contents_menu_display) 
													echo it_get_contents_menu(get_the_ID(), $disabled_menu_items); ?>
                                                                                        
                                            </div>
                                            
                                        <?php } ?>
                                        
                                        <div class="post-right<?php echo $cssright; ?>">                                      
                                    
											<?php if($layout=='longform') { ?>
                                            
                                                <?php #featured image
                                                if(!$disable_image && has_post_thumbnail()) { 
                                                    $imageargs['height'] = 1200;
                                                    $imageargs['link'] = false;
                                                    $imageargs['wrapper'] = false;
                                                    $imageargs['caption'] = false;                                       
                                                    echo '<div class="image-container"><div class="longform-overlay"></div>' . it_featured_image($imageargs) . '</div>';                                       
                                                } ?>
                                                
                                                
                                                <div class="longform-right add-active">
                                                        
                                                    <div class="longform-right-selector"><span class="theme-icon-trending"></span></div>
                                                
                                                    <div class="longform-right-panel clearfix"> 
                                                    
                                                        <?php if(!$disable_heat_index) echo it_get_heat_index($heatargs); ?> 
                                                        
                                                        <?php if(!$disable_view_count) echo it_get_views($viewsargs); ?>                                                  
                                                
                                                        <?php if(!$disable_like_count) echo it_get_likes($likesargs); ?>
                                                        
                                                        <?php if(!$disable_comment_count) echo it_get_comments($commentsargs); ?>
                                                        
                                                        <?php echo it_get_compare_toggle(get_the_ID()); ?>
                                                        
                                                    </div>
                                                    
                                                    <?php if(!empty($awards) || !empty($badges)) { ?>
                                                    
                                                        <div class="longform-right-panel clearfix">
                                                        
                                                            <?php echo $awards; ?>
                                                            
                                                            <?php echo $badges; ?>
                                                            
                                                        </div>
                                                    
                                                    <?php } ?>
                                                    
                                                    <?php if($isreview) 
                                                        echo '<div class="review-label"><span class="theme-icon-star-full"></span>' . __('Review',IT_TEXTDOMAIN) . '</div>'; ?>
                                                
                                                </div>
                                                                
                                                <div class="longform-left">
                                                
                                                	<?php if(!$disable_sharing) 
														echo '<div class="longform-sharing">' . it_get_sharing($sharingargs) . '</div>'; ?>
                                                    
                                                    <?php echo it_ad_action('longform_title_before'); ?>
                                                
                                                    <h1 class="main-title single-title entry-title"<?php echo $item_prop; ?>><?php echo get_the_title(); ?></h1>
                                                    
                                                    <?php echo it_ad_action('longform_title_before'); ?>
                                                                                                            
                                                    <?php if($affiliate_position=='before-content') echo it_get_affiliate_code(get_the_ID()); ?>
                                                        
                                                    <?php #featured video
                                                    if(!$disable_video && !empty($video)) {                                    
                                                        echo it_ad_action('video_before');								
                                                        echo it_video($videoargs); 									
                                                        echo it_ad_action('video_after');                                    
                                                    } ?>
                                                    
                                                </div>
                                            
                                            <?php } else { ?>
                                    
                                                <?php if(!$disable_controlbar) { ?>
                                                
                                                    <div class="control-bar clearfix">
                                                    
                                                    	<div class="triangle-border"></div>
                                                        <div class="triangle"></div>
                                                        
                                                        <div class="control-trending-wrapper add-active">
                                                        
                                                            <div class="control-trending-selector"><span class="theme-icon-trending"></span></div>
                                                            
                                                            <div class="control-trending">
                                                        
                                                                <?php if(!$disable_heat_index) echo it_get_heat_index($heatargs); ?>
                                                                
                                                                <?php if(!$disable_view_count) echo it_get_views($viewsargs); ?>
                                                                
                                                                <?php if(!$disable_like_count) echo it_get_likes($likesargs); ?>
                                                                
                                                                <?php echo it_get_compare_toggle(get_the_ID()); ?>
                                                                
                                                            </div>
                                                            
                                                        </div>
                                                        
                                                        
                                                        <?php if(!empty($awards) || !empty($badges)) { ?>
                                                        
                                                            <div class="control-awards-wrapper add-active">
                                                            
                                                                <div class="control-awards-selector"><span class="theme-icon-award"></span></div>
                                                                
                                                                <div class="control-awards">
                                                                
                                                                    <?php echo $awards; ?>
                                                                    
                                                                    <?php echo $badges; ?>
                                                                    
                                                                </div>
                                                                
                                                            </div>
                                                        
                                                        <?php } ?>
                                                        
                                                        <?php if(!$disable_comment_count) echo it_get_comments($commentsargs); ?>
                                                        
                                                    </div>
                                                    
                                                <?php } ?>
                                                
                                                <?php if($layout!='billboard') { ?>	 
                                                    
                                                    <?php if($isreview) 
														echo '<div class="review-label padded-panel"><span class="theme-icon-star-full"></span>' . __('Review',IT_TEXTDOMAIN) . '</div>'; ?>
														
														
                                                    <?php if(!$disable_title) {
														echo '<div class="padded-panel">';
															echo it_ad_action('classic_title_before');
															 
															echo it_ad_action('classic_title_after');
														echo '</div>';
														
													} ?>	
                                                    
                                                <?php } ?>
                                                
                                            <?php } ?>
                                            
                                            <?php 											                   
                                            if($layout!='longform') { 
                                                #featured video
                                                if(!$disable_video && !empty($video)) {                                    
                                                    echo it_ad_action('video_before');								
                                                    echo it_video($videoargs); 									
                                                    echo it_ad_action('video_after');                                 
                                                }						                                                          
                                                #featured image
                                                if(!$disable_image && has_post_thumbnail()) {
													echo it_ad_action('image_before');
													echo '<div class="homius">';
													
													echo '<div class="image-container">' . it_featured_image($imageargs) . '</div>';
													echo it_ad_action('image_after');
												 if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<p id="breadcrumbs">','</p>');}
													echo '<h1 class="main-title single-title entry-title"' . $item_prop . '>' . get_the_title() . '</h1>';
													echo '</div>';
												}
                                            }
											
																					
											#subtitle
											if(!$disable_subtitle) 
												echo '<div class="main-subtitle padded-panel"><span>' . $subtitle . '</span><div class="divider-line"></div></div>';      
                                                    
                                            #details
                                            if($details_position=='top')    								
                                                echo it_get_details(get_the_ID(), $overlay_image[0], $isreview); 								
                                                
                                            #rating criteria
                                            if($ratings_position=='top')       								
                                                echo it_get_criteria(get_the_ID(), $overlay_image[0]);								
                                                
                                            #reactions
                                            if($reactions_position=='top') 								
                                                echo it_get_reactions(get_the_ID());								
                                                
                                            #affiliate code
                                            if($affiliate_position=='before-content' && $layout!='longform') 
												echo it_get_affiliate_code(get_the_ID()); 
                                            
                                            #content                               
                                            echo it_get_content($article_title);			

                                            #authorship
											if((!$disable_authorship || !$disable_sharing) && $layout!='billboard') { 											
												echo '<div class="authorship-row clearfix padded-panel">';			
													if(!$disable_sharing && $layout!='longform') echo it_get_sharing($sharingargs);
												echo '</div>';												
											}											
                                            
                                            #affiliate code
                                            if($affiliate_position=='after-content') 
												echo it_get_affiliate_code(get_the_ID(), 'after-content'); 
											
                                            #details
                                            if($details_position=='bottom') 							
                                                echo it_get_details(get_the_ID(), $overlay_image[0], $isreview); 								
                                                
                                            #rating criteria  
                                            if($ratings_position=='bottom') 								
                                                echo it_get_criteria(get_the_ID(), $overlay_image[0]);								
                                                
                                            #reactions
                                            if($reactions_position=='bottom') 								
                                                echo it_get_reactions(get_the_ID());								
                                                
											#tags and categories
                                            if(!$disable_postinfo) 
												echo it_get_post_info(get_the_ID()); 
												 
                                            #author info
                                            if(!$disable_authorinfo) 
                                                echo it_get_author_info(get_the_ID());
                                                
                                            #recommended
                                            if(!$disable_recommended)
                                                echo it_get_recommended(get_the_ID());
                                                
                                            #comments
                                            if(!$disable_comments && comments_open()) 
                                                comments_template();
                                            
											?> 
                                            
                                        </div> <?php #end post-right container ?>                            
                                        
                                    </div> <?php #end post container ?>
                                
                                <?php endwhile; ?> 
                            
                            <?php endif; ?> 
                            
                            <?php wp_reset_query(); ?>
    
                            <?php do_action('it_content_page_after'); ?>
                        
                        </div> 
                        
                    </div>                 
                    
                    <?php if($sidebar_layout=='right') { ?> 
                    
                    <div class="<?php echo $csscol3 ?>">
                    
                    	<div class="content-panel shadowed fixed-object single-sidebar<?php echo $cssadmin; ?>">
                    
                            <?php echo it_widget_panel($sidebar); ?>
                            
                        </div>                         
                                    
                	</div>
                    
                    <?php } ?>
                
                </div>
                
                <?php do_action('it_page_wrapper_after'); ?>
            
            </div>
        
        </div>
    
    </div>

</div>

<?php wp_reset_query(); ?>