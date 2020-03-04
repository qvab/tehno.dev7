<!DOCTYPE HTML>

<html <?php language_attributes(); ?>>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">   
	
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title><?php wp_title( '|', true, 'right' );?></title>
    
    <?php do_action('it_head'); ?>    
    	
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
    
    
	<?php wp_head(); ?>
<link href="https://tehno.guru/favicon.ico" rel="shortcut icon" />
<link href="https://tehno.guru/favicon.ico" rel="icon" type="image/x-icon" />
<link rel="manifest" href="/manifest.json">
<script src='https://www.google.com/recaptcha/api.js'></script>	
<script async src="https://aflt.market.yandex.ru/widget/script/api" type="text/javascript"></script>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-147440680-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-147440680-1');
</script>
</head>

<?php $body_class = 'it-background woocommerce bp-page'; 
global $post;
if(is_object($post)) {
    $category_id = it_page_in_category($post->ID);
} else {
    $category_id = false;
}
if($category_id) $body_class .= ' category-' . $category_id;
if(!it_get_setting('colorbox_disable')) $body_class .= ' colorbox-enabled';
if(it_get_setting('colorbox_slideshow')) {
	$body_class .= ' colorbox-slideshow';
} else {
	$body_class .= ' colorbox-slideshow-off';	
}
?>

<body <?php body_class($body_class); ?>>

    <div id="ajax-error"></div>
    
    <div id="fb-root"></div>
    
    <?php if(!it_get_setting('sticky_backtotop_disable')) { ?>
                        
        <a id="back-to-top" href="#top"><span class="theme-icon-up-open"></span></a> 
        
    <?php } ?>
    
    <?php it_get_template_part('header'); #header bar ?>
    
    <?php it_get_template_part('sticky'); #sticky bar ?>
    
    <?php #determine how far down and left to push main site content
    $header_disable = it_get_setting('header_disable_global');
    $sticky_disable = it_get_setting('sticky_disable_global');
    $cssheader = $header_disable ? ' no-header' : '';
    $csssticky = $sticky_disable ? ' no-sticky' : '';
    ?>
    
    <div class="after-header<?php echo esc_attr($cssheader . $csssticky); ?>">
    
    <?php echo it_background_ad(); #full screen background ad ?>
	
    <?php if(it_get_setting('ad_header')!='') { #header ad ?>
    
    	<div id="it-ad-header" class="container-fluid no-padding">
                        
            <div class="row">
        
                <div class="col-md-12">
            
                    <div class="container-inner">
                    
                        <div class="row it-ad">
                            
                            <div class="col-md-12">
                            
                                <?php echo do_shortcode(it_get_setting('ad_header')); ?>  
                                
                            </div>                    
                              
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
        
        </div>
    
    <?php } ?>