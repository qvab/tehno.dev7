<?php


    /*
    * Sets up the theme by loading the IndustrialThemes class & initializing the framework
    * which activates all classes and functions needed for theme's operation.
    */

    # load the IndustrialThemes class
    if (file_exists(get_stylesheet_directory() . '/framework.php'))
        require_once(get_stylesheet_directory() . '/framework.php');
    else if (file_exists(get_template_directory() . '/framework.php'))
        require_once(get_template_directory() . '/framework.php');

    # get theme data
    $theme_data = wp_get_theme();
    # initialize the IndustrialThemes framework
    IndustrialThemes::init(array(
                               'theme_name'    => $theme_data->name,
                               'theme_version' => $theme_data->version
                           ));

    if (!isset($content_width)) $content_width = 1200;
    add_shortcode('advantages', 'sc_advantages');
    function sc_advantages($atts, $content = null)
    {
        return '<div class="advantage">' . do_shortcode($content) . '</div>';
    }

    add_shortcode('disadvantages', 'sc_disadvantages');
    function sc_disadvantages($atts, $content = null)
    {
        return '<div class="disadvantage">' . do_shortcode($content) . '</div>';
    }

    add_shortcode('badge', 'sc_badge');
    function sc_badge($atts, $content = null)
    {
        return '<div class="badge">' . do_shortcode($content) . '</div>';
    }

    function add_shortcode_button()
    {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
        {
            return;
        }

        if ('true' == get_user_option('rich_editing'))
        {
            add_filter('mce_external_plugins', 'add_tinymce_plugin');
            add_filter('mce_buttons', 'register_tinymce_button');
        }
    }

    add_action('admin_head', 'add_shortcode_button');

    function register_tinymce_button($buttons)
    {
        array_push($buttons, 'tinymce_dropbutton');
        return $buttons;
    }

    function add_tinymce_plugin($plugin_array)
    {
        $plugin_array['tinymce_dropbutton'] = get_stylesheet_directory_uri() . '/js/btns.js';
        return $plugin_array;
    }

    /*add_filter( 'WPML_filter_link', 'custom_filter_link', 10, 2 );
    function custom_filter_link( $lang_url, $lang )
    {
        if( $lang['code'] == "en" && $lang_url == 'https://tehno.guru/en/')
        {
            $lang_url = $lang_url . 'reviews';
        }
        return $lang_url;
    }*/


    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'wp_resource_hints', 2);

    function ny_remove_recent_comments_style()
    {
        global $wp_widget_factory;
        remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
    }

    add_action('widgets_init', 'ny_remove_recent_comments_style');

    add_filter('the_content', 'morkovin_noindex_toc', 1000);
    function morkovin_noindex_toc($content)
    {
        return preg_replace('/(<div id="toc_container"[^>]+>[^\n]+)/', '<!--noindex-->$1<!--/noindex-->', $content);
    }

    function adjust_single_breadcrumb($link_output)
    {
        if (strpos($link_output, 'breadcrumb_last') !== false)
        {
            $link_output = '';
        }
        return $link_output;
    }

    add_filter('wpseo_breadcrumb_single_link', 'adjust_single_breadcrumb');
    remove_filter('the_content', 'wpautop');

    /*** ФОРМА КОММЕНТИРОВАНИЯ - КОНФИДЕНЦИАЛЬНОСТЬ ***/
    function privacy_syte_ats($id)
    {
        if (!is_user_logged_in()) :
            print '<span class="posts-comm"><span class="refe"><input type="checkbox" name="submit-privacy" value="1" checked="checked" /></span>&nbsp;Я ознакомлен с условиями <a title"name" href="https://tehno.guru/polzovatelskoe-soglashenie/">пользовательского соглашения</a></span><span class="required"> *</span>';
        endif;
    }

    function privacy_atss_syte($id)
    {
        if (!is_user_logged_in()) :
            if (!$_POST['submit-privacy']) :
                $updated_status = 'trash';
                wp_set_comment_status($id, $updated_status);
                wp_die('Вы не приняли условия пользовательского соглашения: вернитесь и подтвердите согласие... Ваш набранный текст в форме замечательно сохранён!<p><a href="javascript:history.back();">&larr;Назад</a></p>');
            endif;
        endif;
    }

    add_action('comment_form', 'privacy_syte_ats');
    add_action('comment_post', 'privacy_atss_syte');
    /*** ФОРМА КОММЕНТИРОВАНИЯ - КОНФИДЕНЦИАЛЬНОСТЬ ***/


    // Удаляем URL из формы отправки комментариев
    add_filter('comment_form_default_fields', 'sheens_unset_url_field');
    function sheens_unset_url_field($fields)
    {
        if (isset($fields['url']))
            unset ($fields['url']);
        return $fields;
    }

    /*  AMP functionality fixing */
    /*  ------------------------ */

    function remove_amp_nofollow()
    {
        if (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint())
        {
            if (is_plugin_active('no-external-links/no-external-links.php'))
            {
                deactivate_plugins('no-external-links/no-external-links.php');
            }
        }
    }
    add_action( 'init', 'remove_amp_nofollow' );

    /*  ------------------------ */
