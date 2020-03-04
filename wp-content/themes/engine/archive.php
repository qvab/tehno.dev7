<?php get_header(); # show header ?>

<?php 
#get the current category archive
$catid = get_query_var('cat');
$catid = empty($catid) ? -1 : $catid; #if no catid it will return all posts, so set to -1 in that case
$args = array('meta_key' => '_category_frontpage', 'meta_value' => $catid, 'post_type' => 'page');
$itpage = new WP_Query( $args );
if ($itpage->have_posts()) : while ($itpage->have_posts()) : $itpage->the_post();

	the_content();
	
endwhile; 
else:

	#loop through builder panels from theme options
	$builders = it_get_setting('archive_builder');
	if(!empty($builders) && count($builders) > 1) {
		foreach($builders as $builder) {
			it_shortcode($builder);			
		}
	} else {
		echo do_shortcode('[loop loading="paged" layout="d"]');
	} 
	
endif;
?>

<?php get_footer(); # show footer ?>