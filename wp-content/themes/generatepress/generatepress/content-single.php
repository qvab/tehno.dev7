<?php
    /**
     * The template for displaying single posts.
     *
     * @package GeneratePress
     */

    if (!defined('ABSPATH'))
    {
        exit; // Exit if accessed directly.
    }

    $subtitle = get_post_meta($post->ID, "_subtitle", $single = true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_do_microdata('article'); ?>>
    <div class="inside-article">
        <?php
            /**
             * generate_before_content hook.
             *
             * @since 0.1
             *
             * @hooked generate_featured_page_header_inside_single - 10
             */
            do_action('generate_before_content');
        ?>
        <div class="entry-content" itemprop="text">
            <?php
                the_content();

                wp_link_pages(array(
                                  'before' => '<div class="page-links">' . __('Pages:', 'generatepress'),
                                  'after'  => '</div>',
                              ));
            ?>
        </div><!-- .entry-content -->

        <?php
            /**
             * generate_after_entry_content hook.
             *
             * @since 0.1
             *
             * @hooked generate_footer_meta - 10
             */
            do_action('generate_after_entry_content');

            /**
             * generate_after_content hook.
             *
             * @since 0.1
             */
            do_action('generate_after_content');
        ?>
    </div><!-- .inside-article -->
</article><!-- #post-## -->
