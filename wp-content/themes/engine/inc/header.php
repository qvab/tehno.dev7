<?php
$postid = isset($post) ? $post->ID : '';
#theme options
$logo_url=it_get_setting('logo_url');
$logo_url_hd=it_get_setting('logo_url_hd');
$logo_width=it_get_setting('logo_width');
$logo_height=it_get_setting('logo_height');
$logo_color_disable=it_get_setting('logo_color_disable');
$link_url=home_url();
$dimensions = '';
$tagline_disable = true;
if(!it_get_setting('description_disable') && get_bloginfo('description')!=='') $tagline_disable = false;

#category specific logo
$category_id = it_page_in_category($postid);
if($category_id) {
	$categories = it_get_setting('categories');	 
	foreach($categories as $category) {
		if(is_array($category)) {
			if(array_key_exists('id',$category)) {
				if($category['id'] == $category_id) {
					if(!empty($category['logo'])) $logo_url=$category['logo'];
					if(!empty($category['logohd'])) $logo_url_hd=$category['logohd'];
					if(!empty($category['logowidth'])) $logo_width=$category['logowidth'];
					if(!empty($category['logoheight'])) $logo_height=$category['logoheight'];
					if(array_key_exists('tagline_disable',$category)) {
						if($category['tagline_disable']) $tagline_disable = true;
					}
					break;
				}
			}
		}
	}
}
if(!empty($logo_width)) $dimensions .= ' width="'.$logo_width.'"';
if(!empty($logo_height)) $dimensions .= ' height="'.$logo_height.'"';

$termargs = array('num' => 7, 'tax' => array('post_tag','category'));

?>

<?php if (!it_component_disabled('header', $postid)) { ?>

	<div class="container-fluid no-padding">
   
        <div id="header-bar">
            
            <div class="row"> 
            
                <div class="col-md-12"> 
                    
                    <div id="header-inner" class="container-inner">
                    
                    	<?php echo it_background_ad(); #full screen background ad ?>
                        
						<?php if(!it_component_disabled('logo', $postid)) { ?>
                        
                            <div id="logo"<?php if($logo_color_disable) { ?> class="no-color"<?php } ?>>
                
                                <?php if(it_get_setting('display_logo') && $logo_url!='') { ?>
                                    <a href="<?php echo $link_url; ?>">
                                        <img id="site-logo" alt="<?php bloginfo('name'); ?>" src="<?php echo $logo_url; ?>"<?php echo $dimensions; ?> />   
                                        <img id="site-logo-hd" alt="<?php bloginfo('name'); ?>" src="<?php echo $logo_url_hd; ?>"<?php echo $dimensions; ?> />  
                                    </a>
                                <?php } else { ?>     
                                    <h1><a class="textfill" href="<?php echo $link_url; ?>/"><?php bloginfo('name'); ?></a></h1>
                                <?php } ?>
                                
                                <?php if(!$tagline_disable) { ?>
                                
                                    <div class="subtitle"><?php bloginfo('description'); ?></div>
                                    
                                <?php } ?>
                                
                            </div>
                            
                        <?php } ?> 
                        
                        <?php if(!it_component_disabled('header_posts', $postid)) { ?>
                        
                        	<div id="header-posts">
                            
                            	<?php echo do_shortcode('[trending position="inline" suppress_actions="true" postsperpage="6"]'); ?>
                            
                            </div>
                        
                        <?php } ?>
                        
                        <?php if(!it_component_disabled('header_terms', $postid)) { ?>
                        
                        	<?php $terms = it_get_trending_terms($termargs); ?>
                        
                        	<div id="header-terms">
                            
                            	<div class="shadowed">
                            
                            		<div class="term-panel first"><span class="theme-icon-flame"></span><span class="trending-label"><?php _e('TRENDING',IT_TEXTDOMAIN); ?></span></div>
                                    
                                    <?php 
									$i = 0;
									foreach($terms as $term) { 
										$i++; 
										$cssalt = ($i % 2 == 0) ? '' : ' alt'; 
										?>
                                    
                                    	<div class="term-panel<?php echo $cssalt; ?>">
                                        
                                    		<a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
                                            
                                        </div>
                                    
                                    <?php } ?>
                                    
                               </div>
                            
                            </div>
                        
                        <?php } ?>
                    
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
    
<?php } ?>