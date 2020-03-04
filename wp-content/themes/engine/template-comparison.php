<?php
#Template Name: Comparison
define('DONOTCACHEPAGE', true);
?>
<?php get_header(); # show header ?>

<?php 
#get theme option
$builders = it_get_setting('page_builder');

#loop through builder panels
if(!empty($builders) && count($builders) > 2) {
	foreach($builders as $builder) {
		if(is_array($builder)) {
			if(array_key_exists('id',$builder)) {
				if($builder['id']=='page-content') $builder['id'] = 'comparison';	
			}
		}
		it_shortcode($builder);			
	}
} else {
	it_get_template_part('comparison');
} 
?>

<?php get_footer(); # show footer ?>