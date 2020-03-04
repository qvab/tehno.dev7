<?php get_header(); # show header ?>

<?php
	/** @var Object $wp_query */

	if (!is_category() && !is_home() && empty($_GET['s']))
	{
		$wp_query->is_404 = true;
		include_once 'single.php';
		die();
	}

    #loop through builder panels
    $builders = it_get_setting('front_builder');

    if (!empty($builders) && count($builders) > 2)
    {
        foreach ($builders as $builder)
        {
            it_shortcode($builder);
        }
    } else
    {
        echo do_shortcode('[loop loading="paged" layout="d"]');
    }
?>

<?php get_footer(); # show footer ?>