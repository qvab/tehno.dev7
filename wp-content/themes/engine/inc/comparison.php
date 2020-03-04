<?php
define('DONOTCACHEPAGE', true);
#default settings (use "Standard Pages" theme options for defaults)
$sidebar = __('Page Sidebar',IT_TEXTDOMAIN);
$sidebar_layout = 'full'; #use full by default
$disable_title = false;
$disable_title_meta = get_post_meta($post->ID, IT_META_DISABLE_TITLE, $single = true);
if(!empty($disable_title_meta) && $disable_title_meta!='') $disable_title = $disable_title_meta;
$sidebar_layout_meta = get_post_meta($post->ID, "_sidebar_layout", $single = true);
if(!empty($sidebar_layout_meta) && $sidebar_layout_meta!='') $sidebar_layout = $sidebar_layout_meta;
$sidebar_meta = get_post_meta($post->ID, "_custom_sidebar", $single = true);
if(!empty($sidebar_meta) && $sidebar_meta!='') $sidebar = $sidebar_meta;
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
?>

<div <?php post_class('container-fluid no-padding single-wrapper comparison-page', $post->ID); ?> data-location="single-page">
		
    <div class="row">
    
        <div class="col-md-12">
        
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
                
                        <div class="single-page shadowed">
    
                            <?php do_action('it_content_page_before'); ?>                                                
                                
                            <?php if (have_posts()) : ?>
                        
                                <?php while (have_posts()) : the_post(); ?>
                                	
                                    <div id="post-<?php the_ID(); ?>" class="post-content post-selector">
                                        
                                        <div class="post-right content-panel wide clearfix">
                                                        
                                            <?php if(!$disable_title) { ?>
                                             
												<h1 class="main-title single-title entry-title"><?php echo get_the_title(); ?></h1>                                            
                                            
                                            <?php } ?>                                           
                                                                          
                                            <?php echo it_get_content(''); ?>
											
											<?php #prepare values
											$compare = array();
											$compare = it_get_setting('comparison_aspects');
											$arr = it_compared_items(); 
											$num = count($arr);
											$i = 0;
											$items = array();
											$deets = array();
											$crits = array();
											$positives_label = it_get_setting('review_positives_label');
											$negatives_label = it_get_setting('review_negatives_label');
											$bottomline_label = it_get_setting('review_bottomline_label');
											$positives_label = ( !empty($positives_label) ) ? $positives_label : __('Positives',IT_TEXTDOMAIN);
											$negatives_label = ( !empty($negatives_label) ) ? $negatives_label : __('Negatives',IT_TEXTDOMAIN);											
											$bottomline_label = ( !empty($bottomline_label) ) ? $bottomline_label : __('Bottom Line',IT_TEXTDOMAIN);											
											if($num > 0) {
												foreach($arr as $postid) { 
													#create inner array
													$item = array();
													$item['postid'] = $postid;
													$item['title'] = it_title(180, $postid);														
													$item['image'] = it_featured_image(array('postid' => $postid, 'size' => 'square-small', 'width' => 68, 'height' => 60, 'wrapper' => false, 'itemprop' => false, 'link' => false, 'type' => 'normal', 'caption' => false));	
													$video = get_post_meta($postid, "_featured_video", $single = true);
													$item['video'] = it_video(array('url' => $video, 'video_controls' => 'true', 'parse' => true, 'frame' => true, 'autoplay' => 0, 'type' => 'embed', 'width' => 640, 'height' => 360));	
													$item['positives'] = do_shortcode(wpautop(get_post_meta($postid, IT_META_POSITIVES, $single = true)));	
													$item['negatives'] = do_shortcode(wpautop(get_post_meta($postid, IT_META_NEGATIVES, $single = true)));	
													$item['bottomline'] = do_shortcode(wpautop(get_post_meta($postid, IT_META_BOTTOM_LINE, $single = true)));
													$item['awards'] = it_get_awards(array('postid' => $postid, 'single' => false, 'badge' => false, 'white' => true, 'wrapper' => true));
													$item['badges'] = it_get_awards(array('postid' => $postid, 'single' => false, 'badge' => true, 'white' => false, 'wrapper' => true));
													$total_score = get_post_meta($postid, IT_META_TOTAL_SCORE, $single = true);
													$total_score_user = get_post_meta($postid, IT_META_TOTAL_USER_SCORE, $single = true);
													$item['total_score'] = it_get_rating($total_score, 'editor', $postid);
													$item['total_score_user'] = it_get_rating($total_score_user, 'user', $postid);
													$item['heat'] = it_get_heat_index(array('postid' => $postid, 'icon' => false, 'tooltip' => false));
													$item['likes'] = it_get_likes(array('postid' => $postid, 'label' => false, 'icon' => false, 'clickable' => false, 'tooltip_hide' => true, 'showifempty' => true));
													$item['views'] = it_get_views(array('postid' => $postid, 'label' => false, 'icon' => false, 'tooltip_hide' => true));
													$item['comments'] = it_get_comments(array('postid' => $postid, 'label' => false, 'icon' => false, 'showifempty' => true, 'tooltip_hide' => true, 'anchor_link' => false));
													$item['categories'] = it_get_categories($postid, ', ');
													$item['tags'] = it_get_tags($postid, ', ');
													#rating criteria
													$criteria = it_get_setting('review_criteria');
													$metric = it_get_setting('review_rating_metric');
													$metric_meta = get_post_meta($postid, IT_META_METRIC, $single = true);
													if(!empty($metric_meta) && $metric_meta!='') $metric = $metric_meta;													
													foreach($criteria as $criterion) {
														if(is_array($criterion)) {
															if(array_key_exists(0, $criterion)) {
																$name = $criterion[0]->name;
																$meta_name = $criterion[0]->meta_name;																
																$value = get_post_meta($postid, $meta_name, $single = true);
																$value_user = get_post_meta($postid, $meta_name . '_user', $single = true);																
																$item[$name . '_editor'] = '';
																$item[$name . '_user'] = '';																
																if(!empty($value) && $value!='none') {	
																	$crits[] = $name;
																	$item[$name . '_editor'] = it_get_rating($value, 'editor', $postid, false, false);
																	$item[$name . '_user'] = it_get_rating($value_user, 'user', $postid, false, false);																	
																}
															}
														}
													}
													#details													
													$details = it_get_setting('review_details');
													foreach($details as $detail) {	
														if(is_array($detail)) {
															if(array_key_exists(0, $detail)) {
																$name = $detail[0]->name;	
																if(!empty($name)) { 
																	$meta_name = $detail[0]->meta_name; 
																	$item[$name] = '';																	
																	$meta = do_shortcode(wpautop(get_post_meta($postid, $meta_name, $single = true)));
																	if(!empty($meta)) {
																		$deets[] = $name;
																		$item[$name] = $meta;
																	}
																}
															}
														}
													}
													
													#add to outer array
													$items[] = $item;
												}
												
												#remove duplicates from arrays
												$deets = array_unique($deets);
												$crits = array_unique($crits);
												
												?>
                                            
                                            	<div class="the-content compare-content">
                                            	
                                                    <div class="compare-count">
                                                    
                                                        <?php _e('Comparing',IT_TEXTDOMAIN); ?>&nbsp;<span class="compare-num"><?php echo $num; ?></span>&nbsp;<?php _e('Items',IT_TEXTDOMAIN); ?>
                                                        
                                                    </div>
                                                    
                                                    <table class="table table-hover table-comparison"> 
                                                    
                                                        <tbody> 
                                                
                                                            <tr>
                                                            
                                                                <th class="row-label"></th>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <th class="col-<?php echo $item['postid']; ?> add-active">
																	
																		<div class="item-title"><?php echo $item['title']; ?></div>
                                                                        
                                                                        <div class="compare-remove" data-postid="<?php echo $item['postid']; ?>">
                                                                        
                                                                            <span class="theme-icon-x"></span>
                                                                        
                                                                        </div>
                                                                        
                                                                    </th>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php if(in_array('image',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <th class="row-label"></th>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <th class="col-<?php echo $item['postid']; ?>">
                                                                    
                                                                    	<div class="item-image"><?php echo $item['image']; ?></div>
                                                                        
                                                                    </th>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('video',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <th class="row-label"></th>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <th class="col-<?php echo $item['postid']; ?>">
                                                                    
                                                                    	<div class="item-video"><?php echo $item['video']; ?></div>
                                                                        
                                                                    </th>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('positives',$compare) || in_array('negatives',$compare) || in_array('bottomline',$compare) || in_array('awards',$compare) || in_array('badges',$compare)) { ?>
                                                            
                                                            <tr class="separator">
                                                            
                                                            	<td colspan="<?php echo $num + 1; ?>"><?php _e('Overview',IT_TEXTDOMAIN); ?><span class="theme-icon-down-open"></span></td>
                                                                
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('positives',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php echo $positives_label; ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['positives']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('negatives',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php echo $negatives_label; ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['negatives']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('bottomline',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php echo $bottomline_label; ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['bottomline']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('awards',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Awards',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['awards']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('badges',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Badges',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['badges']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('criteria',$compare) || in_array('total',$compare)) { ?>
                                                            
                                                            <tr class="separator">
                                                            
                                                            	<td colspan="<?php echo $num + 1; ?>"><?php _e('Rating',IT_TEXTDOMAIN); ?><span class="theme-icon-down-open"></span></td>
                                                                
                                                            </tr>
                                                            
                                                            <tr class="separator subheader">
                                                            
                                                            	<td class="row-label"></td>
                                                            
                                                            	<?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?> no-padding">
																	
																		<div class="editor-rating"><?php _e('Editor',IT_TEXTDOMAIN); ?></div>
                                                                        
                                                                        <div class="user-rating"><?php _e('User',IT_TEXTDOMAIN); ?></div>
                                                                        
                                                                    </td>
                                                                
                                                                <?php } ?>
                                                                
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('criteria',$compare)) { ?>
                                                            
                                                            <?php #ratings
															foreach($crits as $crit) { ?>
                                                            
                                                            <tr>
                                                            
                                                            	<td class="row-label"><?php echo $crit; ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?> no-padding">
																	
																		<div class="editor-rating"><?php echo $item[$crit . '_editor']; ?></div>
                                                                        
                                                                        <div class="user-rating"><?php echo $item[$crit . '_user']; ?></div>
                                                                        
                                                                    </td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
																
															<?php } ?>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('total',$compare)) { ?>
                                                            
                                                            <tr class="total-row">
                                                            
                                                                <td class="row-label"><?php _e('Total',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?> no-padding">
																	
																		<div class="editor-rating"><?php echo $item['total_score']; ?></div>
                                                                        
                                                                        <div class="user-rating"><?php echo $item['total_score_user']; ?></div>
                                                                        
                                                                    </td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('details',$compare)) { ?>
                                                            
                                                            <tr class="separator">
                                                            
                                                            	<td colspan="<?php echo $num + 1; ?>"><?php _e('Details',IT_TEXTDOMAIN); ?><span class="theme-icon-down-open"></span></td>
                                                                
                                                            </tr>
                                                            
                                                            <?php #details
															foreach($deets as $deet) { ?>
                                                            
                                                            <tr>
                                                            
                                                            	<td class="row-label"><?php echo $deet; ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item[$deet]; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
																
															<?php } ?>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('heat',$compare) || in_array('views',$compare) || in_array('likes',$compare) || in_array('comments',$compare) || in_array('categories',$compare) || in_array('tags',$compare)) { ?>
                                                            
                                                            <tr class="separator">
                                                            
                                                            	<td colspan="<?php echo $num + 1; ?>"><?php _e('Meta',IT_TEXTDOMAIN); ?><span class="theme-icon-down-open"></span></td>
                                                                
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('heat',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Heat Index',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['heat']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('views',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Views',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['views']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('likes',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Likes',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['likes']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('comments',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Comments',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['comments']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('categories',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Categories',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['categories']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                            <?php if(in_array('tags',$compare)) { ?>
                                                            
                                                            <tr>
                                                            
                                                                <td class="row-label"><?php _e('Tags',IT_TEXTDOMAIN); ?></td>
                                                                
                                                                <?php foreach($items as $item) { ?>
                                                                
                                                                    <td class="col-<?php echo $item['postid']; ?>"><?php echo $item['tags']; ?></td>
                                                                
                                                                <?php } ?>
                                                            
                                                            </tr>
                                                            
                                                            <?php } ?>
                                                            
                                                        </tbody>
                                                                                                    
                                                    </table> 
                                                    
                                                </div>
                                            
                                            <?php } ?>  
                                            
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