<?php
/*
Plugin Name: Sticky Ads
Plugin URI: http://seocherry.ru/dev/sticky-ads
Description: Плагин для вывода прилипающей рекламы внутри контента для мобильных устройств.
Version: 1.0.7
Author: SeoCherry.ru
Author URI: http://seocherry.ru/
*/

$version = 0;

function get_stickyplugin_version() {
    $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');
    $version = $plugin_data['version'];

    return $plugin_data['version'];
} 


add_action( 'init', 'sticky_ads_create_posttype' );
function sticky_ads_create_posttype() {
  register_post_type( 'sticky_ad',
    array(
      'labels' => array(
        'name' => __( 'Sticky Ads' ),
        'singular_name' => __( 'Sticky Ad' ),
        'all_items' => __( 'Все блоки' ),
        'add_new' => __( 'Добавить новый' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'sticky_ads'),
    )
  );
}


add_filter( 'user_can_richedit', 'sticky_ads_disable_rich' );

function sticky_ads_disable_rich( $default ) {
    global $post;
    if ( 'sticky_ad' == get_post_type( $post ) )
        return false;
    return $default;
}

add_action( 'add_meta_boxes', 'sticky_register_meta_boxes' );
function sticky_register_meta_boxes() {
  add_meta_box('sticky_meta', 'Настройки блока', 'sticky_ads_input_meta_box', 'sticky_ad');

}
function sticky_ads_input_meta_box($post) {
        $option_container_height = 'sticky_height';
        $container_height = get_post_meta($post->ID, $option_container_height, true);
        if (!isset($container_height) || $container_height == '') {
          $container_height = 600;
        }

        wp_nonce_field('sticky_nonce', 'sticky_nonce');
        ?>
        <table class="form-table">
            <tr>
                <th> <label for="<?php echo $option_container_height; ?>">Высота блока (в пикселях)</label></th>
                <td>
                    <input id="<?php echo $option_container_height; ?>"
                           name="<?php echo $option_container_height; ?>"
                           type="number"
                           min = "0"
                           max = "10000"
                           value="<?php echo esc_attr($container_height); ?>"
                    /> - если поставить 0, то реклама будет выглядеть как обычно, без пустых областей
                </td>
            </tr>
            <tr>
                <th> <label>Шорткод</label></th>
                <td>
                    <strong>[sticky-ad id=<?php echo $post->ID ?>]</strong> - уникальный шорткод для этого блока
                </td>
            </tr>
        </table>
        <?php
    }

// Check for empty string allowing for a value of `0`
function empty_str( $str ) {
    return ! isset( $str ) || $str === "";
}

// Save and delete meta but not when restoring a revision
add_action('save_post', function($post_id){
    $post = get_post($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $option_container_height = 'sticky_height';

    // Do not save meta for a revision or on autosave
    if ( $post->post_type != 'sticky_ad' || $is_revision )
        return;

    // Do not save meta if fields are not present,
    // like during a restore.
    if( !isset($_POST[$option_container_height]) )
        return;

    // Secure with nonce field check
    if( ! check_admin_referer('sticky_nonce', 'sticky_nonce') )
        return;

    // Clean up data
    $container_height = trim($_POST[$option_container_height]);
    // Do the saving and deleting
    if( ! empty_str( $container_height ) ) {
        update_post_meta($post_id, $option_container_height, $container_height);
    } elseif( empty_str( $container_height ) ) {
        delete_post_meta($post_id, $option_container_height);
    }

});

add_shortcode('sticky-ad', 'sticky_get_ad_content');
function sticky_get_ad_content($arg) {

      // get the options defined for this shortcode
    extract( shortcode_atts( array(
        'id' => ''
    ), $arg ) );

    $option_container_height = 'sticky_height';
    $container_height = get_post_meta($id, $option_container_height, true);

    $top_margin = get_option('sticky_margin_top');

    $output = '';
    $output .=   '<div class="sticky-code-block" data-sticky-container data-height="'.$container_height.'"    ><div data-margin-top="'.$top_margin.'" data-shortcode="true" data-sticky-wrap=true class="sticky-ad-block sticky-ad-'.$id.'" data-sticky-class="is-sticky">';
    $output .= get_post_field('post_content', $id);
    $output .= '</div></div>';
    return $output;
}


//  <div class="sticky-ad-block" data-height="" data-margin-top=""> </div>

// Plugin setup

function sticky_ads_scripts() {
    wp_register_style( 'stickyads-style', plugins_url( '/css/sticky-front.css', __FILE__ ), '', get_stickyplugin_version() );
    wp_enqueue_style ('stickyads-style');
    wp_register_script( 'stickyads-script', plugins_url( '/js/sticky-front.js', __FILE__ ), array( 'jquery' ), get_stickyplugin_version() );
    wp_enqueue_script( 'stickyads-script' );

    if (get_option('sticky_css_js') !== null && get_option('sticky_css_js') == "3") {    
      wp_register_script( 'sticky-lib', plugins_url( '/js/sticky.min.js', __FILE__ ), array( 'jquery' ), get_stickyplugin_version() );
      wp_enqueue_script( 'sticky-lib' );
    }
}
function sticky_ads_scripts_admin() {
    wp_register_style( 'stickyads-style', plugins_url( '/css/sticky-back.css', __FILE__ ), '', get_stickyplugin_version() );
    wp_enqueue_style ('stickyads-style');
}



function sticky_ads_register_buttons( $buttons ) {
   array_push( $buttons, 'separator', 'sticky_ads_btn_class' );
   return $buttons;
}

// Hook new button with some actions
function sticky_ads_register_tinymce_javascript( $plugin_array ) {
   $plugin_array['sticky_ads_btn_class'] = plugins_url( '/js/sticky-back.js',__FILE__ );
   return $plugin_array;
}




function setup_stickyads_plugin() {
    
  if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
      return;
  }
  if ( get_user_option( 'rich_editing' ) !== 'true' ) {
    return;
  }
  
  //add_action( 'admin_enqueue_scripts', 'sticky_ads_scripts' );

  add_action('admin_footer', 'sticky_append_list', 10);
  add_action ( 'admin_head', 'inject_list_stickyads', 10 );

  add_filter( 'mce_buttons', 'sticky_ads_register_buttons', 15 );
  add_filter( 'mce_external_plugins', 'sticky_ads_register_tinymce_javascript', 15 );

  add_action( 'admin_enqueue_scripts', 'sticky_ads_scripts_admin' );
}

add_action( 'wp_enqueue_scripts', 'sticky_ads_scripts' );


if (is_admin()) {
  add_action('init', 'setup_stickyads_plugin');
}



function sticky_append_list() {
  ?>
    <div  id="sticky_list_container" class="sticky-list-container">
      <div>Прилипающие блоки</div>
      <hr>
    </div>
  <?php
}

function inject_list_stickyads() {
  $ads = get_posts([
    'post_type' => 'sticky_ad',
    'post_status' => 'publish',
    'numberposts' => -1
    // 'order'    => 'ASC'
  ]);


    ?>
    <script>
    var sticky_posts = [];
    <?php
      foreach ($ads as $a) {
        ?>
        sticky_posts.push([<?php echo $a->ID; ?>, <?php echo '"'.htmlspecialchars($a->post_title, ENT_QUOTES ).'"'; ?>]);
        <?php
      }
    ?>
    </script>
    <?php
}

function sticky_ads_check_update(){
    if (!class_exists('Puc_v4_Factory')) {
        require 'updater/plugin-update-checker.php';
    }

    $update_file= 'http://seocherry.ru/plugin-updates/sticky-ads/sticky-ads.json';

    $update_checker= Puc_v4_Factory::buildUpdateChecker(
        $update_file,
        __FILE__,
        'stickyads'
    );
}

add_action('admin_init', 'sticky_ads_check_update');

// Settings

add_action('admin_menu', 'sticky_ads_option_menu', 1);
function sticky_ads_option_menu() {
  add_options_page('Sticky Ads', 'Настройка прилипающей рекламы', 'manage_options', 'sticky-ads', 'sticky_ads_options_page');
  add_action( 'admin_init', 'register_sticky_ads_settings' );
}

function register_sticky_ads_settings() {
  //register our settings
  register_setting( 'sticky-ads', 'sticky_margin_top' );
  register_setting( 'sticky-ads', 'sticky_css_js' );
  register_setting( 'sticky-ads', 'sticky_wrap_span' );
  register_setting( 'sticky-ads', 'sticky_load_delay' );

  if ( get_option( 'sticky_margin_top' ) === false ) {
    add_option( 'sticky_margin_top', '40' );
    
  }    
  if ( get_option( 'sticky_css_js' ) === false ) {
    add_option( 'sticky_css_js', "1" );
  }    
  if ( get_option( 'sticky_wrap_span' ) === false ) {
    add_option( 'sticky_wrap_span', "false" );
  }    
  if ( get_option( 'sticky_load_delay' ) === false ) {
    add_option( 'sticky_load_delay', 3000 );
  } 
}

function sticky_send_options_frontend() {
    ?>
    <script>
    var sticky_options = [];
    sticky_options['sticky_load_delay'] = <?php echo '"'. get_option('sticky_load_delay') . '"'; ?>;
    sticky_options['sticky_margin_top'] = <?php echo '"'. get_option('sticky_margin_top') . '"'; ?>;
    sticky_options['sticky_wrap_span'] = <?php echo '"'. get_option('sticky_wrap_span') . '"'; ?>;
    sticky_options['sticky_css_js'] = <?php echo '"'. get_option('sticky_css_js') . '"'; ?>;
    </script>
    <?php
}
add_action ( 'wp_head', 'sticky_send_options_frontend' );


function sticky_ads_options_page(){
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'Не достаточно прав для доступа к странице.' ) );
  }
  ?>
  <div class="wrap"><h1>Опции плагина Sticky Ads</h1>
    <hr>
<div class="sticky-ads-settings-wrap">
  <form method="post" action="options.php">

  <?php settings_fields( 'sticky-ads' ); ?>
    <?php do_settings_sections( 'sticky-ads' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><label for="sticky_margin_top">Отступ от верхнего края экрана в пикселях</label></th>
        <td><input type="number" min="0" name="sticky_margin_top" value="<?php echo esc_attr( get_option('sticky_margin_top') ); ?>" /></td>
        </tr>      

        <tr valign="top">
        <th scope="row"><label for="sticky_css_js">Метод прилипания</label></th>
        <td>    <select name="sticky_css_js" id="sticky_css_js" >
    <option <?php if(esc_attr( get_option('sticky_css_js') ) == '1') { echo 'selected="selected"'; } ?> value="1">Метод 1 (CSS): Рекомендуемый</option>
    <option <?php if(esc_attr( get_option('sticky_css_js') ) == '2') { echo 'selected="selected"'; } ?> value="2">Метод 2 (CSS + хак для некоторых тем)</option>
    <option <?php if(esc_attr( get_option('sticky_css_js') ) == '3') { echo 'selected="selected"'; } ?> value="3">Метод 3 (CSS+JS): Если другие не работают</option>
    </select> - на разных темах могут работать разные методы. Рекомендуемые 1 и 2, т.к. 3-й работает менее плавно из-за неободимости вычислений с помощью JavaScript. <br><strong>Важно!</strong> Метод 2 меняет стили вашей темы (не в файлах, только на стороне посетителя сайта), поэтому проверяйте корректность отображения всех страниц.</td>
        </tr>     
        <tr valign="top">
        <th scope="row"><label for="sticky_wrap_span">Обернуть блок в дополнительный span</label></th>
        <td>    <select name="sticky_wrap_span" id="sticky_wrap_span" >
    <option <?php if(esc_attr( get_option('sticky_wrap_span') ) == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
    <option <?php if(esc_attr( get_option('sticky_wrap_span') ) == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
    </select> - Работает только с 3-м методом, может предотвратить прыжки и дергание блока.</td>
        </tr>        
        <tr valign="top">
        <th scope="row"><label for="sticky_load_delay">Задержка подгрузки скрипта</label></th>
        <td><input type="number" min="0" name="sticky_load_delay" value="<?php echo esc_attr( get_option('sticky_load_delay') ); ?>" /> - если сайт медленный, то установка задержки может помочь (2000-3000 мс должно хватить). Может быть полезно для 2 и 3 методов.</td>
        </tr>       
        <tr valign="top">
        <th scope="row"><label>Код для ручной вставки</label></th>
        <td>Не обязательно использовать шорткоды для вывода прилипающей рекламы, можно обернуть рекламный блок в такой код:

          <?php
          echo '<pre>';
echo htmlspecialchars('<div class="sticky-ad-block" data-height="600" data-margin-top="60"> </div>');
echo '</pre>';
?>
        - здесь аттрибуты data-height и data-margin-top - опциональны, означают высоту контейнера для прокрутки и отступ от верхнего края экрана для рекламного блока в пикселях соответственно.</td>
        </tr>    

        <tr valign="top">
        <th scope="row"><label>Инструкция по использованию</label></th>
        <td>Как использовать плагин <a href="http://seocherry.ru/ads/kak-ispolzovat-plagin-sticky-ads-shortkody-ruchnaja-vstavka-cherez-ads-inserter-flat-realbig">смотрите тут</a>. Все расписано подробно со скриншотами. 
          <br><br>
        Хотите упростить процесс перелинковки статей? Обратите внимание на мой плагин <a href="http://seocherry.ru/dev/cherrylink/">CherryLink</a>. Релевантные ссылки, наглядное отображение ссылок и вставка их в текст в 1 клик.
        </td>
        </tr>  
    </table>
    
    <?php submit_button(); ?>

  </form>
  </div></div>
  <?php

}