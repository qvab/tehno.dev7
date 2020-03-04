<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package GeneratePress
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

get_header(); ?>

  <div id="primary" <?php generate_do_element_classes('content', "page-home"); ?>>
    <main id="main" <?php generate_do_element_classes('main'); ?>>
      <?php
      /**
       * generate_before_main_content hook.
       *
       * @since 0.1
       */
      do_action('generate_before_main_content');

      global $wp_query;

      $wp_query = new WP_Query(array(
        //	'category_name' => 'classes',
        'posts_per_page' => '9',
        'meta_key' => 'rating',
        'meta_value' => 'main',
        'paged' => get_query_var('paged') ?: 1 // страница пагинации
      ));

      ?>
      <div class="list-post-home">
        <?php
        while (have_posts()) {
          the_post();
          get_template_part('content-home', get_post_format());

        }
        ?>
      </div>
      <?php
      /*
      echo '<nav id="nav-below" class="paging-navigation">';
      the_posts_pagination();
      echo '</nav>';
*/

      wp_reset_query(); // сброс $wp_query

      // if(have_posts()) : query_posts("meta_key=rating&meta_value=main");

      // 	while ( have_posts() ) : the_post();

      // 		/* Include the Post-Format-specific template for the content.
      // 		 * If you want to override this in a child theme, then include a file
      // 		 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
      // 		 */
      // 		get_template_part( 'content', get_post_format() );

      // 	endwhile;

      // 	/**
      // 	 * generate_after_loop hook.
      // 	 *
      // 	 * @since 2.3
      // 	 */
      // 	do_action( 'generate_after_loop' );

      // 	generate_content_nav( 'nav-below' );

      // else :

      // 	get_template_part( 'no-results', 'index' );

      // endif;

      /**
       * generate_after_main_content hook.
       *
       * @since 0.1
       */
      do_action('generate_after_main_content');
      ?>
    </main><!-- #main -->
  </div><!-- #primary -->

<?php
/**
 * generate_after_primary_content_area hook.
 *
 * @since 2.0
 */
do_action('generate_after_primary_content_area');

//generate_construct_sidebars();
?>

  <style>
    div#primary.page-home {
      width: 100% !important;
    }

    .list-post-home:after {
      content: "";
      display: block;
      float: none;
      clear: both;
    }

    .list-post-home .post-image {
      height: 200px;
      overflow: hidden;
      position: relative;
      margin-top: 0;
    }

    .list-post-home article {
      width: 30%;
      float: left;
      margin: 0 1.5% 2em;
    }

    .list-post-home .post-image h1 {
      font-size: 16px;
      position: absolute;
      bottom: 0;
      background-color: rgba(0,0,0,0.65);
      margin: 0;
      box-sizing: border-box;
      padding: 10px 5px;
      color: #fff;
      width: 100%;
    }

    .list-post-home .post-image a:hover h1 {
      text-decoration: underline;
    }

    .list-post-home .inside-article {
      padding: 0;
    }

    @media (max-width: 960px) {
      .list-post-home article {
        width: 46.6%;
        float: left;
        margin: 0 1.5% 2em;
      }
    }


    @media (max-width: 560px) {
      .list-post-home article {
        width: 100%;
        float: none;
        margin: 0 0 2em;
        box-sizing: border-box;
        padding: 0 10px;
      }
    }

  </style>
<?php
get_footer();
