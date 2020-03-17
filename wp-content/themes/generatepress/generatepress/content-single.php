<?php
/**
 * The template for displaying single posts.
 *
 * @package GeneratePress
 */

if (!defined('ABSPATH')) {
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

    $sRezident = get_field('rezident_name', $post);
    $linkRezident = get_field('rezident_link', $post);
    $linkAv = get_field('avtor_link', $post);
    $socialNameAv = get_field('avtor_social_name', $post);
    $socialLinkAv = get_field('avtor_social_link', $post);

    $arAvtor = get_field('post_author', $post);
    $idUser = $post->post_author;
    $imageUser = get_wp_user_avatar($idUser);
    $sName = get_the_author();
    $sUserURL = get_the_author_meta("user_url");
    $langAuthor = !empty(IS_SITE_LANG_EN) ? "Author" : "Автор";
    $langRezident = !empty(IS_SITE_LANG_EN) ? "Reviewed by" : "Эксперт";
    $imageUser = preg_replace_callback("|src=\"(.*?)\"|", function ($arMatch) {
      $url = parse_url($arMatch[1]);
      if ($url["host"] == "tehno.guru") {
        $arMatch[1] = mishanin_resizeImage($url["path"], 26, 26);
      }
      return 'src="'.$arMatch[1].'"';
    }, $imageUser);
    ?>
    <div class="block-autors">
      <div class="author_image"><?=$imageUser?></div>
      <div class="author_data">
        <p><span><?=$langAuthor?>: <a href="<?=$sUserURL?>"><?=$sName?></a><?php if (!empty($socialLinkAv)) { ?>, <a
                    href="<?=$socialLinkAv?>"><?=$socialNameAv?></a>
            <?php } ?>
              </span>
          <?php
          if (!empty($sRezident)) {
            ?><span>
            | <?=$langRezident?>:<a target="_blank" href="<?=$linkRezident?>"> <?=$sRezident?></a></span>
            <?php
          }
          ?></p>
      </div>
    </div>
    <style>
      .block-autors:after {
        display: block;
        float: none;
        clear: both;
        content: "";
      }

      .author_image {
        float: left;
        margin-right: 15px;
      }

      .author_image img {
        width: 26px;
        height: auto;
        border-radius: 160px;
      }

      .author_data span {
        display: inline-block;
      }

    </style>

    <?php

    ?>
    <div class="entry-content" itemprop="text">
      <?php
      the_content();

      wp_link_pages(array(
        'before' => '<div class="page-links">'.__('Pages:', 'generatepress'),
        'after' => '</div>',
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
