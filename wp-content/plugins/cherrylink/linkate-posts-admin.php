<?php
/*
 * Linkate Posts
 */


function linkate_posts_option_menu() {
	add_options_page(__('CherryLink Options', 'linkate_posts'), __('CherryLink', 'linkate_posts'), 'cherrylink_settings', 'linkate-posts', 'linkate_posts_options_page');
}

add_action('admin_menu', 'linkate_posts_option_menu', 1);

function linkate_posts_options_page(){
	echo '<div class="wrap"><h2>';
	_e('CherryLink - Внутренняя перелинковка', 'linkate_posts');
	echo '</h2></div>';


	$m = new lp_admin_subpages();
	$m->add_subpage('Лицензия', 'accessibility', 'linkate_posts_accessibility_options_subpage');
	$m->add_subpage('Шаблон ссылок', 'output', 'linkate_posts_output_options_subpage');
	$m->add_subpage('Фильтрация', 'general', 'linkate_posts_general_options_subpage');
	$m->add_subpage('Алгоритм поиска', 'other', 'linkate_posts_other_options_subpage');
	$m->add_subpage('Экспорт', 'export', 'linkate_posts_export_options_subpage');
	//$m->add_subpage('TEST', 'test', 'linkate_posts_test_options_subpage');
	$m->display();

	add_action('in_admin_footer', 'linkate_posts_admin_footer');
}

function linkate_posts_admin_footer() {
	//link_cf_admin_footer(str_replace('-admin', '', __FILE__), "linkate-posts");

}

function linkate_posts_general_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('linkate-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('linkate-posts-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = link_cf_options_from_post($options, array('excluded_posts', 'included_posts', 'excluded_authors', 'included_authors', 'excluded_cats', 'included_cats', 'tag_str', 'custom', 'limit', 'show_private', 'show_pages', 'status', 'age', 'omit_current_post', 'match_cat', 'match_tags'));
		update_option('linkate-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated settings-error notice"><p>' . __('<b>Настройки обновлены.</b>', 'linkate_posts') . '</p></div>';
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap linkateposts-tab-content">

        
		<form method="post" action="">
        <h2>Какие ссылки выводить?</h2>
        <hr>
		<table class="optiontable form-table">
			<?php
				link_cf_display_limit($options['limit']);
				//link_cf_display_skip($options['skip']);
				link_cf_display_show_private($options['show_private']);
				link_cf_display_show_pages($options['show_pages']);
				//link_cf_display_show_attachments($options['show_attachments']);
				link_cf_display_status($options['status']);
				link_cf_display_age($options['age']);
				link_cf_display_omit_current_post($options['omit_current_post']);
				link_cf_display_match_cat($options['match_cat']);
				link_cf_display_match_tags($options['match_tags']);
				//link_cf_display_match_author($options['match_author']);
				link_cf_display_tag_str($options['tag_str']);
			?>
		</table>
        <hr>
		<h2>Расширенные настройки</h2>
        <hr>		
		<table class="optiontable form-table">
			<?php
				link_cf_display_excluded_posts($options['excluded_posts']);
				link_cf_display_included_posts($options['included_posts']);
				link_cf_display_authors($options['excluded_authors'], $options['included_authors']);
				link_cf_display_cats($options['excluded_cats'], $options['included_cats']);

				link_cf_display_custom($options['custom']);
			?>
		</table>
		<div class="submit"><input type="submit" class="button button-primary" name="update_options" value="<?php _e('Сохранить настройки', 'linkate_posts') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-update-options'); ?>
		</form>
	</div>
	<?php
}

function linkate_posts_output_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('linkate-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('linkate-posts-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...

		$options = link_cf_options_from_post($options, array('output_template', 'sort', 'link_before','link_after','term_before','term_after', 'anons_len'));

		if (isset($_POST['multilink'])) {
			$options['multilink'] = 'checked';
		} else {
			$options['multilink'] = '';
		}

		// if (isset($_POST['link_before'])) {
		// 	if(!mb_check_encoding($_POST['link_before'], 'UTF-8'))
		// 		$options['link_before'] = mb_convert_encoding($_POST['link_before'], 'UTF-8');
		// }		
		// if (isset($_POST['link_after'])) {
		// 	if(!mb_check_encoding($_POST['link_after'], 'UTF-8'))
		// 		$options['link_after'] = mb_convert_encoding($_POST['link_after'], 'UTF-8');
		// }

		update_option('linkate-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated settings-error notice"><p>' . __('<b>Настройки обновлены.</b>', 'linkate_posts') . '</p></div>';
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap linkateposts-tab-content">
        <form method="post" action="">
            <div style="float:right;max-width:25%;"><?php link_cf_display_available_tags('linkate-posts'); ?></div>
    		<div style="float:left; max-width:75%;">
    		    
    		    <h2>Вывод списка ссылок в редакторе</h2>
    		    <p>В нужные поля подставить желаемый HTML код с использованием тегов из списка справа. Теги выводят данные о записе или странице. </p>
                <hr>
        		<table class="optiontable form-table">
        			<?php
        				link_cf_display_output_template($options['output_template']);
        				//link_cf_display_none_text($options['none_text']);
        				link_cf_display_sort($options['sort']);
        				link_cf_display_multilink($options['multilink']);
        				link_cf_display_anons_len($options['anons_len']);
        			?>
        		</table>
    		</div>
		    <div style="clear:both"></div>
		    <hr>
		    <h2>Вывод ссылки на запись/страницу в тексте</h2>
		    <p>Шаблон обрамления выделенного текста.</p>
		    <hr>

		        <table class="optiontable form-table">
		          <?php link_cf_display_replace_template($options['link_before'], $options['link_after']); ?>
		        </table>
		    <hr>
		    <h2>Вывод ссылки на рубрику/таксономию в тексте</h2>
		    <p>Шаблон обрамления выделенного текста. Здесь можно использовать только {url} и {title}.</p>
		    <hr>

		        <table class="optiontable form-table">
		          <?php link_cf_display_replace_term_template($options['term_before'], $options['term_after']); ?>
		        </table>
		    <div class="submit"><input type="submit" class="button button-primary" name="update_options" value="<?php _e('Сохранить настройки', 'linkate_posts') ?>" /></div>
		    <?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-update-options'); ?>
		</form>
	</div>
	<?php
}


function linkate_posts_other_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('linkate-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('linkate-posts-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = link_cf_options_from_post($options, array('term_extraction', 'weight_title', 'weight_content', 'weight_tags','custom_stopwords','term_length_limit'));
		$wcontent = $options['weight_content'] + 0.0001;
		$wtitle = $options['weight_title'] + 0.0001;
		$wtags = $options['weight_tags'] + 0.0001;
		$wcombined = $wcontent + $wtitle + $wtags;
		$options['weight_content'] = $wcontent / $wcombined;
		$options['weight_title'] = $wtitle / $wcombined;
		$options['weight_tags'] = $wtags / $wcombined;
		$customwords = array_filter(explode("\n", str_replace("\r", "", $options['custom_stopwords'])));
		$customwords = implode(PHP_EOL, $customwords); // remove empty lines
		$options['custom_stopwords'] = $customwords;

		if (isset($_POST['compare_seotitle'])) {
			$options['compare_seotitle'] = 'checked';
		} else {
			$options['compare_seotitle'] = '';
		}
		update_option('linkate-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated settings-error notice"><p>' . __('<b>Настройки обновлены.</b>', 'linkate_posts') . '</p></div>';
	}
	
	if (isset($_POST['reindex_all'])) {
		check_admin_referer('linkate-posts-manage-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		$options = get_option('linkate-posts');
		$options['utf8'] = 'true';
		$options['cjk'] = 'false';
		$options['use_stemmer'] = 'false';
		$options['batch'] = 100;
		flush();
		$termcount = linkate_posts_save_index_entries (($options['utf8']==='true'), $options['use_stemmer'], $options['batch'], ($options['cjk']==='true'));
		update_option('linkate-posts', $options);
		//show a message
		printf('<div class="updated fade"><p>'.__('Реиндексировано %d ссылок.').'</p></div>', $termcount);
	}
	
	//now we drop into html to display the option page form
	?>
		<div class="wrap linkateposts-tab-content">

		<form method="post" action="">
		    <h2>Настройка алгоритма</h2>
		    <p>Настройки схожести применяются только к записям и страницам. На таксономии они не распространяются.</p>
		    <hr>
		<table class="optiontable form-table">
			<?php
				link_cf_display_weights($options);
				//link_cf_display_num_terms($options['num_terms']);
				link_cf_display_term_extraction($options['term_extraction']);
			?>
		</table>
			<hr>
			<h2>Стоп-слова</h2>
			<p>Список стоп-слов индивидуальный для вашего сайта. В плагин уже строены самые распространенные слова из русского языка, которые не учитываются в поиске схожести. Если их требуется расширить - используйте данное поле.</p> <p>Слова нужно вводить без знаков препинания, каждое слово с новой строки. По умолчанию, все слова состоящие из 3 и меньше букв автоматически <strong>не учитывается алгоритмом</strong>. </p><p>Необходимо вписать все возможные словоформы (пример: узнать, узнал, узнала, узнают, узнавать и тд.) </p>
		    <hr>
			<table class="optiontable form-table">
			<?php
				link_cf_display_stopwords($options['custom_stopwords']);
				link_cf_display_num_term_length_limit($options['term_length_limit']);
			?>
		</table>
		
		<div class="submit"><input type="submit" class="button button-primary" name="update_options" value="<?php _e('Сохранить настройки', 'linkate_posts') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-update-options'); ?>
		</form>
		
		<form method="post" action="">
		    <hr>
		    <h2>Обновление индекса ссылок</h2>
		    <hr>
		    <p>Если ссылки не выводятся - нажмите для обновления индекса вручную.</p>
		    <p>При изменении настроек схожести можно тоже тыкнуть, если автоматически не обновились.</p>
		    <div class="submit"><input type="submit" class="button button-primary" name="reindex_all" value="<?php _e('Реиндексировать ссылки', 'linkate_posts') ?>" /></div>
		    <?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-manage-update-options'); ?>
		</form>
	</div>
	<?php
}

function linkate_posts_accessibility_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('linkate-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('linkate-posts-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = link_cf_options_from_post($options, array('hash_field'));
		update_option('linkate-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated settings-error notice"><p>' . __('<b>Настройки обновлены.</b>', 'linkate_posts') . '</p></div>';
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap linkateposts-tab-content">
			<div class="linkateposts-column-left">	
				<?php 
			    	link_cf_display_accessibility_response(checkNeededOption());
			    ?>	
		        <form method="post" action="">
				    <h2>Лицензионный ключ</h2>
		    			<?php
		    				link_cf_display_accessibility_template($options['hash_field']);
		    			?>
				    <div class="submit"><input type="submit" class="button button-primary" name="update_options" value="<?php _e('Сохранить', 'linkate_posts') ?>" /></div>
				    <?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-update-options'); ?>
				</form>
			</div>
			<div class="linkateposts-column-right">
				<iframe width="480" height="270" src="https://www.youtube.com/embed/y3W6PGUJd28" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				<h2>О плагине</h2>
				<p>Плагин помогает при ручной перелинковке статей на сайте. <br><br>В его функции входит вывод наиболее релевантных статей, вставка ссылки по шаблону вокруг выделенного текста в 1 клик.<br><br>Дополнительно включены подсчет ссылок, проверка повторов, быстрый переход к ссылке в тексте, быстрый фильтр по найденным ссылкам и другое. </p>
				<h2>Где взять ключ?</h2>
				<p>Вся информация о плагине и его покупке находится на официальном сайте по адресу: <a href="http://seocherry.ru/dev/cherrylink" >SeoCherry.ru</a>.</p>
				<h2>Техподдержка</h2>
				<p>Если есть вопросы о работе плагина, покупке или баг репорт (найденные ошибки) - пишите в <a href="https://t.me/joinchat/HCjIHgtC9ePAkJOP1V_cPg">телеграм-чат</a> или на почту <strong>mail@seocherry.ru</strong>. </p>
			</div>
			<div style="clear:both"></div>
	</div>
	<?php
}
function linkate_posts_test_options_subpage(){
	global $wpdb, $wp_version, $linkate_overusedwords;
	$options = get_option('linkate-posts');
	$has = array();

	//now we drop into html to display the option page form
	?>
	<div class="wrap linkateposts-tab-content">
		<?php 
			$r = $wpdb->get_row("SELECT * FROM `wp_linkate_posts` WHERE `pID` = 4501", OBJECT);
			echo $r->content;

			$arr = explode(" ", $r->content);
			foreach ($arr as $key => $value) {
				if (isset($linkate_overusedwords[$value])) {
					$has[] = $value;
				}
			}
		?>	
		<hr>
		<?php
			echo implode(" ", $has);
		?>
		<hr>
		<?php
			echo implode(' ', array_flip($linkate_overusedwords));
		?>
	</div>
	<?php
}
function linkate_posts_export_options_subpage(){
	global $wpdb;
	$options = get_option('linkate-posts');
	if (isset($_POST['update_options']) && isset($_POST['export'])) {
		check_admin_referer('linkate-posts-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = link_cf_options_from_post($options, array('export'));
		update_option('linkate-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated settings-error notice"><p>' . __('<b>Настройки импортированы.</b>', 'linkate_posts') . '</p></div>';
	}
	if (isset($_POST['reset_options'])) {
		check_admin_referer('linkate-posts-update-options');
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		fill_options(NULL);
		// Show a message to say we've done something
		echo '<div class="updated settings-error notice"><p>' . __('<b>Настройки сброшены.</b>', 'linkate_posts') . '</p></div>';
	}

	//now we drop into html to display the option page form
	?>
	<div class="wrap linkateposts-tab-content">
		<div class="linkateposts-column-left column-left-export">	
		<form method="post" action="">
		    <h2>Экспорт и импорт</h2>
		    <p>В поле ниже закодированы все настройки плагина на данном сайте. Для переноса настроек на другой сайт скопируйте все, что находится в данном поле и вставьте в это же поле на другом сайте.</p>
    			<?php
    				link_cf_display_export_template(base64_encode(http_build_query($options)));
    			?>
		    <div class="submit"><input type="submit" class="button button-primary" name="update_options" value="<?php _e('Сохранить', 'linkate_posts') ?>" /></div>
		    <?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-update-options'); ?>
		</form>
		</div>
		<div class="linkateposts-column-right column-right-export">
			<form method="post" action="">
				<h2>Вернуть настройки по умолчанию</h2>
		    	<p>Нажмите эту волшебную кнопочку, чтобы начать все с чистого листа. <strong>Внимание! Все настройки будут сброшены, в том числе лицезионный ключ!</strong></p>
			    <div class="submit"><input type="submit" class="button button-primary" name="reset_options" value="<?php _e('Сбросить настройки', 'linkate_posts') ?>" /></div>
			    <?php if (function_exists('wp_nonce_field')) wp_nonce_field('linkate-posts-update-options'); ?>
		    </form>
		</div>
					<div style="clear:both"></div>
	</div>
	<?php
}

// sets up the index for the blog
function linkate_posts_save_index_entries ($utf8=true, $use_stemmer='false', $batch=100, $cjk=false) {
	global $wpdb, $table_prefix;
	$options = get_option('linkate-posts');
	//$t0 = microtime(true);
	$table_name = $table_prefix.'linkate_posts';
	$wpdb->query("TRUNCATE `$table_name`");
	$termcount = 0;
	$start = 0;
	// in batches to conserve memory
	while ($posts = $wpdb->get_results("SELECT `ID`, `post_title`, `post_content`, `post_type` FROM $wpdb->posts LIMIT $start, $batch", ARRAY_A)) {
		reset($posts);
		while (list($dummy, $post) = each($posts)) {
			if ($post['post_type'] === 'revision') continue;
			$content = linkate_sp_get_post_terms($post['post_content'], $utf8, $use_stemmer, $cjk);
			$postID = $post['ID'];
			if ($options['compare_seotitle'] === 'checked') { 
			    $seotitle = get_post_meta( $postID, "_yoast_wpseo_title", true);
			    if (!$seotitle || $seotitle === $post['post_title'])
			        $seotitle = get_post_meta( $postID, "_aioseop_title", true);
			    if (!$seotitle || $seotitle === $post['post_title']) {
			        $title = linkate_sp_get_title_terms($post['post_title'], true, false, false);
			    } else {
			    	$title = linkate_sp_get_title_terms($seotitle, true, false, false);
			    }
			} else {
				$title = linkate_sp_get_title_terms($post['post_title'], true, false, false);
			}

			$tags = linkate_sp_get_tag_terms($postID, $utf8);
			$wpdb->query("INSERT INTO `$table_name` (pID, content, title, tags) VALUES ($postID, \"$content\", \"$title\", \"$tags\")");
			$termcount = $termcount + 1;
		}
		$start += $batch;
		if (!ini_get('safe_mode')) set_time_limit(30);
	}
	unset($posts);
	//$t = microtime(true) - $t0; echo "t = $t<br>";
	return $termcount;
}

// this function gets called when the plugin is installed to set up the index and default options
function linkate_posts_install() {
   	global $wpdb, $table_prefix;

	$table_name = $table_prefix . 'linkate_posts';
	$errorlevel = error_reporting(0);
	$suppress = $wpdb->hide_errors();
	$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`pID` bigint( 20 ) unsigned NOT NULL ,
			`content` longtext NOT NULL ,
			`title` text NOT NULL ,
			`tags` text NOT NULL ,
			FULLTEXT KEY `title` ( `title` ) ,
			FULLTEXT KEY `content` ( `content` ) ,
			FULLTEXT KEY `tags` ( `tags` )
			) ENGINE = MyISAM CHARSET = utf8;";
	$wpdb->query($sql);
	// MySQL before 4.1 doesn't recognise the character set properly, so if there's an error we can try without
	if ($wpdb->last_error !== '') {
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`pID` bigint( 20 ) unsigned NOT NULL ,
				`content` longtext NOT NULL ,
				`title` text NOT NULL ,
				`tags` text NOT NULL ,
				FULLTEXT KEY `title` ( `title` ) ,
				FULLTEXT KEY `content` ( `content` ) ,
				FULLTEXT KEY `tags` ( `tags` )
				) ENGINE = MyISAM;";
		$wpdb->query($sql);
	}
	

	$options = (array) get_option('linkate-posts');

	$options = fill_options($options);
	linkate_posts_save_index_entries ('true', 'false', $options['batch'], ($options['cjk'] === 'true'));

	$wpdb->show_errors($suppress);
	error_reporting($errorlevel);
}

// used on install, import settings, revert to defaults
function fill_options($options) {
	if ($options == NULL) {
		$options = array();
	}
	if (!isset($options['append_on'])) $options['append_on'] = 'false';
	if (!isset($options['append_priority'])) $options['append_priority'] = '10';
	if (!isset($options['append_parameters'])) $options['append_parameters'] = 'prefix=<h3>'.__('Linkate Posts', 'linkate-posts').':</h3><ul class="linkate-posts">&suffix=</ul>';
	if (!isset($options['append_condition'])) $options['append_condition'] = 'is_single()';
	if (!isset($options['limit'])) $options['limit'] = 9999;
	if (!isset($options['skip'])) $options['skip'] = 0;
	if (!isset($options['age'])) {$options['age']['direction'] = 'none'; $options['age']['length'] = '0'; $options['age']['duration'] = 'month';}
	if (!isset($options['divider'])) $options['divider'] = '';
	if (!isset($options['omit_current_post'])) $options['omit_current_post'] = 'true';
	if (!isset($options['show_private'])) $options['show_private'] = 'false';
	if (!isset($options['show_pages'])) $options['show_pages'] = 'false';
	if (!isset($options['show_attachments'])) $options['show_attachments'] = 'false';
	// show_static is now show_pages
	if ( isset($options['show_static'])) {$options['show_pages'] = $options['show_static']; unset($options['show_static']);};
	if (!isset($options['none_text'])) $options['none_text'] = __('Ничего не найдено...', 'linkate_posts');
	if (!isset($options['no_text'])) $options['no_text'] = 'false';
	if (!isset($options['tag_str'])) $options['tag_str'] = '';
	if (!isset($options['excluded_cats'])) $options['excluded_cats'] = '';
	if ($options['excluded_cats'] === '9999') $options['excluded_cats'] = '';
	if (!isset($options['included_cats'])) $options['included_cats'] = '';
	if ($options['included_cats'] === '9999') $options['included_cats'] = '';
	if (!isset($options['excluded_authors'])) $options['excluded_authors'] = '';
	if ($options['excluded_authors'] === '9999') $options['excluded_authors'] = '';
	if (!isset($options['included_authors'])) $options['included_authors'] = '';
	if ($options['included_authors'] === '9999') $options['included_authors'] = '';
	if (!isset($options['included_posts'])) $options['included_posts'] = '';
	if (!isset($options['excluded_posts'])) $options['excluded_posts'] = '';
	if ($options['excluded_posts'] === '9999') $options['excluded_posts'] = '';
	if (!isset($options['stripcodes'])) $options['stripcodes'] = array(array());
    $options['prefix'] = '<div class="linkate-box-container"><ol id="linkate-links-list">';
	$options['suffix'] = '</ol></div>';
	if (!isset($options['output_template'])) $options['output_template'] = '{title_seo}';
	if (!isset($options['match_cat'])) $options['match_cat'] = 'false';
	if (!isset($options['match_tags'])) $options['match_tags'] = 'false';
	if (!isset($options['match_author'])) $options['match_author'] = 'false';
	if (!isset($options['content_filter'])) $options['content_filter'] = 'false';
	if (!isset($options['custom'])) {$options['custom']['key'] = ''; $options['custom']['op'] = '='; $options['custom']['value'] = '';}
	if (!isset($options['sort'])) {$options['sort']['by1'] = ''; $options['sort']['order1'] = SORT_ASC; $options['sort']['case1'] = 'false';$options['sort']['by2'] = ''; $options['sort']['order2'] = SORT_ASC; $options['sort']['case2'] = 'false';}
	if (!isset($options['status'])) {$options['status']['publish'] = 'true'; $options['status']['private'] = 'false'; $options['status']['draft'] = 'false'; $options['status']['future'] = 'false';}
	if (!isset($options['group_template'])) $options['group_template'] = '';
	if (!isset($options['weight_content'])) $options['weight_content'] = 0.9;
	if (!isset($options['weight_title'])) $options['weight_title'] = 0.1;
	if (!isset($options['weight_tags'])) $options['weight_tags'] = 0.0;
	if (!isset($options['num_terms'])) $options['num_terms'] = 20;
	if (!isset($options['term_extraction'])) $options['term_extraction'] = 'frequency';
	if (!isset($options['hand_links'])) $options['hand_links'] = 'false';
	if (!isset($options['utf8'])) $options['utf8'] = 'true';
	if (!function_exists('mb_internal_encoding')) $options['utf8'] = 'false';
	if (!isset($options['cjk'])) $options['cjk'] = 'false';
	if (!function_exists('mb_internal_encoding')) $options['cjk'] = 'false';
	if (!isset($options['use_stemmer'])) $options['use_stemmer'] = 'false';
	if (!isset($options['batch'])) $options['batch'] = '100';
	if (!isset($options['link_before'])) $options['link_before'] = base64_encode('<a href="{url}" title="{title}">');
	if (!isset($options['link_after'])) $options['link_after'] = base64_encode('</a>');
	if (!isset($options['term_before'])) $options['term_before'] = base64_encode('<a href="{url}" title="{title}">');
	if (!isset($options['term_after'])) $options['term_after'] = base64_encode('</a>');
	if (!isset($options['hash_field'])) $options['hash_field'] = '';
	if (!isset($options['custom_stopwords'])) $options['custom_stopwords'] = '';
	if (!isset($options['term_length_limit'])) $options['term_length_limit'] = 3;
	if (!isset($options['multilink'])) $options['multilink'] = '';
	if (!isset($options['compare_seotitle'])) $options['compare_seotitle'] = '';
	if (!isset($options['hash_last_check'])) $options['hash_last_check'] = 1523569887;
	if (!isset($options['hash_last_status'])) $options['hash_last_status'] = false;
	if (!isset($options['anons_len'])) $options['anons_len'] = 200;

	update_option('linkate-posts', $options);
	return $options;
}

if (!function_exists('link_cf_plugin_basename')) {
	if ( !defined('WP_PLUGIN_DIR') ) define( 'WP_PLUGIN_DIR', ABSPATH . 'wp-content/plugins' );
	function link_cf_plugin_basename($file) {
		$file = str_replace('\\','/',$file); // sanitize for Win32 installs
		$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
		$plugin_dir = str_replace('\\','/',WP_PLUGIN_DIR); // sanitize for Win32 installs
		$plugin_dir = preg_replace('|/+|','/', $plugin_dir); // remove any duplicate slash
		$file = preg_replace('|^' . preg_quote($plugin_dir, '|') . '/|','',$file); // get relative path from plugins dir
		return $file;
	}
}

function checkNeededOption() {
	$options = get_option('linkate-posts');
	$arr = getNeededOption();
	$final = false;
	$status = '';
	if ($arr != NULL) {
		$k = base64_decode('c2hhMjU2');
		$d = isset($_SERVER[base64_decode('SFRUUF9IT1NU')]) ?  $_SERVER[base64_decode('SFRUUF9IT1NU')] : $_SERVER[base64_decode('U0VSVkVSX05BTUU=')];
		$h = hash($k,$d);
		for ($i = 0; $i < sizeof($arr); $i++) {
			$a = base64_decode($arr[$i]);
			if ($h == $a) {
				$final = true; //'true,oldkey_good';
				$status = 'ok_old';
				//echo $status;
				return $final;
			}
		}


		if (function_exists('curl_init')) {
			$resp = explode(',',callHome(base64_encode(implode(',',$arr)), $d));
			$final = $resp[0] == 'true' ? true : false; // new
			$status = $resp[1];
		} else {
			$final = false;
			$status = 'Не найдена библиотека curl. Плагин не может быть активирован (обратитесь к техподдержке хостинга).';
			echo $status;
		}

	}

	if ($final) {
		$options['hash_last_check'] = time() + 2592000;
		$options['hash_last_status'] = true;
	} else {
		$options['hash_last_check'] = 0;
		$options['hash_last_status'] = false;
	}
	update_option('linkate-posts', $options);
	//echo $status;
	return $final;
}

function getNeededOption() {
	$options = get_option('linkate-posts');
	$s = $options[base64_decode('aGFzaF9maWVsZA==')];
	if (empty($s)) {
		return NULL;
	} else {
		return explode(",", base64_decode($s));
	}
}

function callDelay() {
	$options = get_option('linkate-posts');
	if (time() > $options['hash_last_check']) {
		return false;
	}
	return true;
}
function lastStatus() {
	$options = get_option('linkate-posts');
	return $options['hash_last_status'];
}

function callHome($val,$d) {
	$data = array('key' => $val, 'action' => 'getInfo', 'domain' =>$d);
	$url = base64_decode('aHR0cDovL3Nlb2NoZXJyeS5ydS9wbHVnaW5zLWxpY2Vuc2Uv');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 2);
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if(curl_errno($curl)){
    	return 'true,curl_error';
	}
	if($status != 200) {
		return 'true,'.$status;
	}
    curl_close($curl);
    return $response;
}

add_action('activate_'.str_replace('-admin', '', link_cf_plugin_basename(__FILE__)), 'linkate_posts_install');
add_action('upgrader_process_complete', 'linkate_on_update', 10, 2);
add_action('plugins_loaded', 'linkate_redirectToUpdatePlugin');


function linkate_on_update( $upgrader_object, $options ) {
    $current_plugin_path_name = str_replace('-admin', '', link_cf_plugin_basename(__FILE__));

// echo '<div class="notice notice-success">Thanks for updating CherryLink</div>';

    if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
       foreach($options['plugins'] as $each_plugin){
          if ($each_plugin==$current_plugin_path_name){
          	
          	
          	set_transient('cherrylink_updated', 1);
          	break;
          }
       }
    }
}

function linkate_redirectToUpdatePlugin() {
    if (get_transient('cherrylink_updated') && current_user_can('update_plugins')) {
        linkate_posts_install();
		set_transient('cherrylink_updated', 0);
    }// endif;
}// redirectToUpdatePlugin


function cherrylink_add_cap() {
	$role = get_role( 'administrator' );
	if ( is_object( $role ) ) {
		$role->add_cap( 'cherrylink_settings' );
	}
}

add_action( 'plugins_loaded', 'cherrylink_add_cap' );
