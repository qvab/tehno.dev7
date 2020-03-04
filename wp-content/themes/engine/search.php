<?php get_header(); # show header ?>

<?php 
#loop through builder panels
$builders = it_get_setting('search_builder');
if(!empty($builders) && count($builders) > 2) {
	foreach($builders as $builder) {
		it_shortcode($builder);			
	}
} else {
	echo do_shortcode('[loop loading="paged" layout="d"]');
} 
?>

<?php get_footer(); # show footer ?>