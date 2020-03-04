<?php
$postid = isset($post) ? $post->ID : '';
$unstick=it_get_setting('sticky_unstick');
$menu_disable = it_get_setting('menu_disable');
$search_disable = it_get_setting('search_disable');
$link_url=home_url();
$site_icon_url=it_get_setting('site_icon_url');
$site_icon_url_hd=it_get_setting('site_icon_url_hd');
$alternate_login=it_get_setting('sticky_alternate_login');
$login_url=it_get_setting('sticky_login_url');
$alternate_register=it_get_setting('sticky_alternate_register');
$register_url=it_get_setting('sticky_register_url');
$login_url = !empty($login_url) ? $login_url : wp_login_url( home_url() );
$register_url = !empty($register_url) ? $register_url : wp_registration_url();
$account_url=it_get_setting('sticky_account_url');
$account_url = !empty($account_url) ? $account_url : admin_url( 'profile.php' );
#determine if user is logged in
$current_user = wp_get_current_user();
if ( 0 == $current_user->ID ) {
    $logged_in = false;
} else {
    $logged_in = true;
}
#setup sticky bar css
$cssadmin = is_admin_bar_showing() ? ' admin-bar' : '';
$csssticky = $unstick ? ' unstick' : '';
#setup login/register variables
$idregister = 'sticky-register';
$hrefregister = '';
$hrefaccount = force_ssl_admin() ? str_replace('http://','https://',$account_url) : $account_url;
#if buddypress is active the register button should redirect to the register page
#and the account button should redirect to the BuddyPress profile page
if(function_exists('bp_current_component') && !it_get_setting('bp_register_disable')) {
	$idregister = 'sticky-register-bp';
	$hrefregister = 'href="' . home_url() . '/register"';
	$hrefaccount = bp_loggedin_user_domain();
}
#use alternate login/register forms
$idlogin = 'sticky-login-toggle';
$hreflogin = '';
if($alternate_login && !$logged_in) {
	$idlogin = 'sticky-login-toggle-wp';
	$hreflogin = 'href="' . $login_url . '"';
}
if($alternate_register) {
	$idregister = 'sticky-register-bp';
	$hrefregister = 'href="' . $register_url . '"';
}
#new articles setup
$disable_new_articles = it_get_setting('new_articles_disable');
$timeperiod = it_get_setting('new_timeperiod');
if(empty($timeperiod)) $timeperiod = 'Today'; 
$prefix = it_get_setting('new_prefix');
if(!empty($prefix)) $prefix .= ' ';
$timeperiod_label = $prefix . it_timeperiod_label($timeperiod);
$number = it_get_setting('new_number');
if(empty($number)) $number = 16;
$label_override = it_get_setting('new_label_override');
if(!empty($label_override)) $timeperiod_label = $label_override;
#setup wp_query args
$args = array('posts_per_page' => $number);
#setup loop format
$format = array('loop' => 'main', 'location' => 'compact', 'nonajax' => true, 'numarticles' => $number, 'thumbnail' => true);
#add time period to args
$day = date('j');
$week = date('W');
$month = date('n');
$year = date('Y');
switch($timeperiod) {
	case 'Today':
		$args['day'] = $day;
		$args['monthnum'] = $month;
		$args['year'] = $year;
		$timeperiod='';
	break;
	case 'This Week':
		$args['year'] = $year;
		$args['w'] = $week;
		$timeperiod='';
	break;	
	case 'This Month':
		$args['monthnum'] = $month;
		$args['year'] = $year;
		$timeperiod='';
	break;
	case 'This Year':
		$args['year'] = $year;
		$timeperiod='';
	break;
	case 'all':
		$timeperiod='';
	break;			
}
#perform the loop function to retrieve post count
$loop = it_loop($args, $format, $timeperiod);
$post_count = $loop['posts'];
if($post_count == 0) $disable_new_articles = true; 
$cssnew = $disable_new_articles ? '' : ' no-margin'; 
?>

<?php if (!it_component_disabled('sticky', $postid)) { ?>

	<div class="container-fluid no-padding">
   
        <div id="sticky-bar" class="<?php echo $cssadmin . $csssticky; ?>">
            
            <div class="row"> 
            
                <div class="col-md-12"> 
                    
                    <div id="sticky-inner" class="container-inner">
                    
                    	<div class="sticky-color">
                        
							<?php if(!$menu_disable && has_nav_menu('main-menu')) { ?>
                            
                            	<div class="sticky-home">
                                    <a href="<?php echo $link_url; ?>/">
                                        <?php if($site_icon_url!='') { ?>
                                            <img id="site-icon" alt="<?php bloginfo('name'); ?>" src="<?php echo $site_icon_url; ?>" width="40" height="40" />   
                                            <img id="site-icon-hd" alt="<?php bloginfo('name'); ?>" src="<?php echo $site_icon_url_hd; ?>" width="80" height="80" /> 
                                        <?php } else { ?>                            
                                            <span class="theme-icon-home"></span>                                        
                                        <?php } ?>                            
                                    </a>
                                </div>
                                    
                                <div id="menu-toggle" class="add-active">
                                
                                    <span class="label-text"><?php _e('MENU',IT_TEXTDOMAIN); ?></span>
                                    
                                    <a id="nav-toggle"><span></span></a>
                                
                                </div>
                            
                                <?php 
                                #get the sticky menu, stripping out title attributes
                                $menu = preg_replace('/title=\"(.*?)\"/','',wp_nav_menu( array( 'theme_location' => 'main-menu', 'container' => 'nav', 'container_id' => 'main-menu', 'fallback_cb' => 'fallback_pages', 'echo' => false) ) );											
                                echo $menu; 
                                ?>
                                      
                            <?php } ?>
                            
                            <?php if(!$search_disable) { ?>
                            
                            	<div id="search-toggle" class="add-active"><span class="theme-icon-search"></span></div>
                                
                                <div id="sticky-search">
                                
                                    <?php echo it_search_form(''); ?>
                                    
                                </div>
                            
                            <?php } ?>
                            
                        </div>
                        
                        <?php if(!$disable_new_articles) { ?>
                
                            <div class="new-articles clearfix">
                            
                                <div class="selector add-active">
                                
                                    <div class="new-number"><?php echo $post_count; ?></div>
                                    
                                    <div class="new-label"><?php _e('new',IT_TEXTDOMAIN); ?></div> 
                                    
                                    <div class="new-time"><?php echo $timeperiod_label; ?></div> 
                                    
                                </div>
                                
                                <div class="post-container">
                                            
                                    <div class="column">
                                    
                                        <?php echo $loop['content']; wp_reset_query(); ?>
                                    
                                    </div>
                                
                                </div>
                                
                            </div>
                        
                        <?php } ?>
                        
                        <?php if(has_nav_menu('section-menu')) { ?>
                        
							<?php	
                            switch(it_get_setting('section_menu_type')) {
                                case 'standard':
                                    #get the section menu, stripping out title attributes
                                    $section_menu = preg_replace('/title=\"(.*?)\"/','',wp_nav_menu( array( 'theme_location' => 'section-menu', 'container' => false, 'fallback_cb' => 'fallback_categories', 'echo' => false ) ) );
                                    echo '<div id="section-menu" class="standard-menu">' . $section_menu . '</div>';
									#mobile
									echo '<div class="section-toggle add-active">';
										echo '<span class="section-more-label">' . __('Sections',IT_TEXTDOMAIN) . '</span><span class="theme-icon-sort-down"></span>';
									echo '</div>';
									echo '<div class="section-menu-mobile">' . $section_menu . '</div>'; 
                                break;
                                case 'mega':  
                                    #get the mega menu
                                    $mega_menu = it_section_menu(); 
									$mega_menu_compact = it_section_menu(true);
                                    echo '<div id="section-menu" class="mega-menu' . $cssnew . '">' . $mega_menu . '</div>';
									#mobile
									echo '<div class="section-toggle add-active">';
										echo '<span class="section-more-label">' . __('Sections',IT_TEXTDOMAIN) . '</span><span class="theme-icon-sort-down"></span>';
									echo '</div>';
									echo '<div class="section-menu-mobile">' . $mega_menu_compact . '</div>'; 
                                break;
                            } 							                    
                            ?>
                    
						<?php } ?>             
                        
                        <?php if(!it_get_setting('sticky_account_disable')) { ?> 
                            
                            <div id="sticky-account"> 
                                
                                <a id="<?php echo $idlogin; ?>" class="theme-icon-key sticky-toggle" <?php echo $hreflogin; ?>></a>
                            
                                <div class="sticky-dropdown clearfix" id="sticky-account-dropdown">             
                            
                                    <?php if(!$logged_in) { ?>
                                    
                                        <div class="clearfix">
                                    
                                            <a id="sticky-login" class="sticky-dropdown-button active"><span class="theme-icon-password"></span><?php _e('Login',IT_TEXTDOMAIN); ?></a>
                                            
                                            <a id="<?php echo $idregister; ?>" class="sticky-dropdown-button" <?php echo $hrefregister; ?>><span class="theme-icon-pencil"></span><?php _e('Register',IT_TEXTDOMAIN); ?></a> 
                                            
                                        </div> 
                                        
                                        <div class="sticky-form-placeholder">
                                        
                                            <div class="loading"><span class="theme-icon-spin2"></span></div>
                                        
                                            <?php echo it_login_form(); ?>
                                        
                                            <?php echo it_register_form(); ?>
                                        
                                        </div>                      
                                    
                                    <?php } else { ?>
                                    
                                        <a class="sticky-dropdown-button left-button" href="<?php echo $hrefaccount; ?>"><span class="theme-icon-cog"></span><?php _e('Account',IT_TEXTDOMAIN); ?></a>
                                        
                                        <a class="sticky-dropdown-button" href="<?php echo wp_logout_url( home_url() ); ?>"><span class="theme-icon-power"></span><?php _e('Logout',IT_TEXTDOMAIN); ?></a>                                   
                                    
                                    <?php } ?>
                                
                                </div>
                                
                                <?php $register = 'false';
                                if(!empty($_GET)) {
                                    if(array_key_exists('register', $_GET)) $register = $_GET['register']; 
                                }
                                if($register == 'true') { ?>
                                
                                    <div class="sticky-dropdown check-password info" title="<?php _e('click to dismiss',IT_TEXTDOMAIN); ?>" data-placement="bottom">
                                        
                                        <span class="theme-icon-thumbs-up"></span>
                                
                                        <?php _e('Check your email for your password.',IT_TEXTDOMAIN); ?>
                                    
                                    </div>
                                
                                <?php } ?>
                                
                            </div>
                                
                        <?php } ?>  
                            
						<?php if(!it_get_setting('sticky_social_disable')) echo it_social_badges(); ?>  
                        
                    </div>
                    
                </div>
                
            </div>
    
        </div>
        
    </div>

<?php } wp_reset_query();?>