<?php
    /**
     * The Sidebar containing the main widget areas.
     *
     * @package GeneratePress
     */

    if (!defined('ABSPATH'))
    {
        exit; // Exit if accessed directly.
    }

    function wp_get_sidebar_menu() {

        $current_menu = 'sidebar-menu';
        $array_menu = wp_get_nav_menu_items($current_menu);

        $menu = '
                <div class="bar-header">
                    <div class="bar-label">
                        <div class="label-text">'. ($_COOKIE["wp-wpml_current_language"] == "ru" ? "Категории техники" : "Categories") .'</div>
                     </div>
                </div>';
        $menu .= '<div id="sidebar-menu" class="sidebar-plate-menu">';
        $menu .= '<ul id="menu-sidebar-menu" class=" menu sf-menu">';

        foreach ($array_menu as $m) {

            $image = '';

            $latest_posts = get_posts(array('posts_per_page' => 1, 'category' => $m->object_id));

            foreach($latest_posts as $post) {
                $postid = $post->ID;
            }

            #category image
            $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($postid), [140, 146] );
            $image = $featured_image[0];
            $image = mishanin_resizeImage($image, 140, 146);

            $icon_image = get_field('white_icon_128x128', $m);

            if (empty($icon_image)) {
              $icon_image = mishanin_resizeImage('/wp-content/uploads/2018/01/reyting_white-64x64.png', 64, 64);
            } else {
              $icon_image = mishanin_resizeImage($icon_image, 64, 64);
            }
            $menu .=
                '<li class="category-tile menu-item menu-item-type-taxonomy menu-item-object-category menu-item-' . $m->ID. '">
                    <div class="category-tile-inner">
                        <div class="tile-image" style="background-image:url(' . $image . ');"></div>
                        <div class="tile-layer"></div>
                        <div class="tile-icon">
                            <span class="category-icon category-icon" style="background:url(' . $icon_image . ') no-repeat 0 0;"></span>
                        </div>
                        <a class="tile-link" href="' . $m->url . '"></a>
                        <div class="tile-name">' . $m->title. '</div>
                    </div>
                </li>';
        }

        $menu .= '</ul>';
        $menu .= '</div>';

        return $menu;
    }

?>
<div id="right-sidebar" <?php generate_do_element_classes('right_sidebar'); ?> <?php generate_do_microdata('sidebar'); ?>>
    <div class="inside-right-sidebar">
        <div class="widget tile-blocks">
            <?php
                echo wp_get_sidebar_menu();
                /*wp_nav_menu(
                    array(
                        'theme_location'  => 'sidebar-menu',
                        'container'       => 'div',
                        'container_class' => 'sidebar-plate-menu',
                        'container_id'    => 'sidebar-menu',
                        'menu_class'      => '',
                        'items_wrap'      => '<ul id="%1$s" class="%2$s ' . join(' ', generate_get_element_classes('menu')) . '">%3$s</ul>',
                    )
                );*/
            ?>
        </div>
        <?php
            /**
             * generate_before_right_sidebar_content hook.
             *
             * @since 0.1
             */
            do_action('generate_before_right_sidebar_content');

            if (!dynamic_sidebar('sidebar-1'))
            {
                generate_do_default_sidebar_widgets('right-sidebar');
            }

            /**
             * generate_after_right_sidebar_content hook.
             *
             * @since 0.1
             */
            do_action('generate_after_right_sidebar_content');
        ?>
    </div><!-- .inside-right-sidebar -->
</div><!-- #secondary -->
