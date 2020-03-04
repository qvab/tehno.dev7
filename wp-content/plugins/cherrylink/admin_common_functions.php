<?php
/*
 * Linkate Posts
 */
 
define('LINKATE_ACF_LIBRARY', true);

function link_cf_options_from_post($options, $args) {
	foreach ($args as $arg) {
		switch ($arg) {
		case 'limit':
		case 'skip':
		    $options[$arg] = link_cf_check_cardinal($_POST[$arg]);
			break;
		case 'excluded_cats':
		case 'included_cats':
			if (isset($_POST[$arg])) {	
				// get the subcategories too
				if (function_exists('get_term_children')) {
					$catarray = $_POST[$arg];
					foreach ($catarray as $cat) {
						$catarray = array_merge($catarray, get_term_children($cat, 'category'));
					}
					$_POST[$arg] = array_unique($catarray);
				}
				$options[$arg] = implode(',', $_POST[$arg]);
			} else {
				$options[$arg] = '';
			}	
			break;
		case 'excluded_authors':
		case 'included_authors':
			if (isset($_POST[$arg])) {
				$options[$arg] = implode(',', $_POST[$arg]);
			} else {
				$options[$arg] = '';
			}	
			break;
		case 'excluded_posts':
		case 'included_posts':
			$check = explode(',', rtrim($_POST[$arg]));
			$ids = array();
			foreach ($check as $id) {
				$id = link_cf_check_cardinal($id);
				if ($id !== 0) $ids[] = $id;
			}
			$options[$arg] = implode(',', array_unique($ids));
			break;
		case 'stripcodes':
			$st = explode("\n", trim($_POST['starttags']));
			$se = explode("\n", trim($_POST['endtags']));
			if (count($st) != count($se)) {
				$options['stripcodes'] = array(array());
			} else {
				$num = count($st);
				for ($i = 0; $i < $num; $i++) {
					$options['stripcodes'][$i]['start'] = $st[$i];
					$options['stripcodes'][$i]['end'] = $se[$i];
				}
			}
			break;
		case 'age':
			$options['age']['direction'] = $_POST['age-direction'];
			$options['age']['length'] = link_cf_check_cardinal($_POST['age-length']);
			$options['age']['duration'] = $_POST['age-duration'];
			break;
		case 'custom':
			$options['custom']['key'] = $_POST['custom-key'];
			$options['custom']['op'] = $_POST['custom-op'];
			$options['custom']['value'] = $_POST['custom-value'];
			break;
		case 'sort':
			$options['sort']['by1'] = $_POST['sort-by1'];
			$options['sort']['order1'] = $_POST['sort-order1'];
			if ($options['sort']['order1'] === 'SORT_ASC') $options['sort']['order1'] = SORT_ASC; else $options['sort']['order1'] = SORT_DESC; 
			$options['sort']['case1'] = $_POST['sort-case1'];
			$options['sort']['by2'] = $_POST['sort-by2'];
			$options['sort']['order2'] = $_POST['sort-order2'];
			if ($options['sort']['order2'] === 'SORT_ASC') $options['sort']['order2'] = SORT_ASC; else $options['sort']['order2'] = SORT_DESC; 
			$options['sort']['case2'] = $_POST['sort-case2'];
			if ($options['sort']['by1'] === '') {
				$options['sort']['order1'] = SORT_ASC;
				$options['sort']['case1'] = 'false';
				$options['sort']['by2'] = '';
			}
			if ($options['sort']['by2'] === '') {
				$options['sort']['order2'] = SORT_ASC;
				$options['sort']['case2'] = 'false';
			}
			break;
		case 'status':
			unset($options['status']);
			$options['status']['publish'] = $_POST['status-publish'];
			$options['status']['private'] = $_POST['status-private'];
			$options['status']['draft'] = $_POST['status-draft'];
			$options['status']['future'] = $_POST['status-future'];
			break;
		case 'num_terms':
			$options['num_terms'] = $_POST['num_terms'];
			if ($options['num_terms'] < 1) $options['num_terms'] = 20;
			break;
		case 'link_before':
		case 'link_after':		
		case 'term_before':
		case 'term_after':
		    $options[$arg] = base64_encode(urlencode($_POST[$arg]));
            break;
        case 'export':
        	parse_str(base64_decode($_POST['export']),$options);
        	break;        
		default:
			$options[$arg] = trim($_POST[$arg]);
		}
	}
	return $options;
}

function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

function link_cf_check_cardinal($string) {
	$value = intval($string);
	return ($value > 0) ? $value : 0;
}

function link_cf_display_available_tags($plugin_name) {
	?>
		<h3><?php _e('Доступные теги', 'post_plugin_library'); ?></h3>
		<ul style="list-style-type: none;">
		<li title="">{title} - Заголовок H1</li>
		<li title="">{title_seo} - Из AIOSeo или Yoast</li>
		<li title="">{url}</li>
        <li title="">{categorynames}</li> 
		<li title="">{date}</li>
		<li title="">{author}</li>
		<li title="">{postid}</li>
		<li title="">{imagesrc}</li>
		<li title="">{anons}</li>
		</ul>
	<?php
}


function get_linkate_version($prefix) {
	$plugin_version = str_replace('-', '_', $prefix) . '_version';
	global $$plugin_version;
	return ${$plugin_version};
}

function link_cf_display_accessibility_template($hash_field) {
	?>
	<label for="hash_field"><?php _e('Введите ваш ключ:', 'post_plugin_library') ?></label>
	<textarea name="hash_field" id="hash_field" rows="10"><?php echo htmlspecialchars(stripslashes($hash_field)); ?></textarea> 
	<?php
}
function link_cf_display_export_template($export) {
	?>
	<label for="export"><?php _e('Настройки плагина:', 'post_plugin_library') ?></label>
	<textarea name="export" id="export" rows="10"><?php echo $export; ?></textarea> 
	<?php
}

function link_cf_display_accessibility_response($info) {
	if ($info) {
		echo base64_decode('PGRpdiBjbGFzcz0ibGlua2F0ZXBvc3RzLWFjY2Vzc2liaWxpdHktZ29vZCI+PGgyPtCf0LvQsNCz0LjQvSDQsNC60YLQuNCy0LjRgNC+0LLQsNC9ITwvaDI+PC9kaXY+');
	} else {
		echo base64_decode('PGRpdiBjbGFzcz0ibGlua2F0ZXBvc3RzLWFjY2Vzc2liaWxpdHktd2FybmluZyI+PGgyPtCf0LvQsNCz0LjQvSDQvdC1INCw0LrRgtC40LLQuNGA0L7QstCw0L0hPC9oMj48cD7QlNC70Y8g0L/QvtC70YPRh9C10L3QuNGPINC60LvRjtGH0LAg0L/QvtGB0LXRgtC40YLQtSDRgdGC0YDQsNC90LjRhtGDINC/0LvQsNCz0LjQvdCwOiBbPHN0cm9uZz48YSBocmVmPSJodHRwOi8vc2VvY2hlcnJ5LnJ1L2Rldi9jaGVycnlsaW5rIj5TZW9DaGVycnkucnU8L2E+PC9zdHJvbmc+XS48L3A+DQoJCQkJPC9kaXY+');
	}
}

/*

	inserts a form button to completely remove the plugin and all its options etc.

*/

function link_cf_confirm_eradicate() {
 return (isset($_POST['eradicate-check']) && 'yes'===$_POST['eradicate-check']);
}

function link_cf_deactivate_plugin($plugin_file) {
	$current = get_option('active_plugins');
	$plugin_file = substr($plugin_file, strlen(WP_PLUGIN_DIR)+1);
	$plugin_file = str_replace('\\', '/', $plugin_file);
	if (in_array($plugin_file, $current)) {
		array_splice($current, array_search($plugin_file, $current), 1); 
		update_option('active_plugins', $current);
	}
}


/*

	For the display of the option pages

*/

function link_cf_display_multilink($multilink) {
	?>
		<tr valign="top">
			<th scope="row"><label for="multilink"><?php _e('Разрешить множественную вставку ссылок:', 'post_plugin_library') ?></label></th>
			<td><input name="multilink" type="checkbox" id="multilink" value="cb_multilink" <?php echo $multilink; ?>/></td>
		</tr>
	<?php
}

function link_cf_display_limit($limit) {
	?>
	<tr valign="top">
		<th scope="row"><label for="limit"><?php _e('Количество ссылок:', 'post_plugin_library') ?></label></th>
		<td><input name="limit" type="number" id="limit" style="width: 60px;" value="<?php echo $limit; ?>" size="2" /> - Рекомендую ставить большое число и использовать фильтрацию прямо в редакторе. Если больше доверяете алгоритму, чем КМу - ставьте ограничение и пусть вписывают то, что предложил плагин :)</td>
	</tr>
	<?php
}


function link_cf_display_skip($skip) {
	?>
	<tr valign="top">
		<th scope="row"><label for="skip"><?php _e('Сдвиг от начала на кол-во ссылок:', 'post_plugin_library') ?></label></th>
		<td><input name="skip" type="number" id="skip" style="width: 60px;" value="<?php echo $skip; ?>" size="2" /></td>
	</tr>
	<?php
}

function link_cf_display_omit_current_post($omit_current_post) {
	?>
	<tr valign="top">
		<th scope="row"><label for="omit_current_post"><?php _e('Скрыть ссылку на текущий пост?', 'post_plugin_library') ?></label></th>
		<td>
		<select name="omit_current_post" id="omit_current_post" >
		<option <?php if($omit_current_post == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
		<option <?php if($omit_current_post == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
		</select> 
		</td>
	</tr>
	<?php
}


function link_cf_display_show_private($show_private) {
	?>
	<tr valign="top">
		<th scope="row"><label for="show_private"><?php _e('Показывать защищенные паролем?', 'post_plugin_library') ?></label></th>
		<td>
		<select name="show_private" id="show_private">
		<option <?php if($show_private == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
		<option <?php if($show_private == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
		</select> 
		</td>
	</tr>
	<?php
}

function link_cf_display_show_pages($show_pages) {
	?>
	<tr valign="top">
		<th scope="row"><label for="show_pages"><?php _e('Показывать ссылки на страницы или записи?', 'post_plugin_library') ?></label></th>
		<td>
			<select name="show_pages" id="show_pages">
			<option <?php if($show_pages == 'false') { echo 'selected="selected"'; } ?> value="false">Только записи</option>
			<option <?php if($show_pages == 'true') { echo 'selected="selected"'; } ?> value="true">Записи и страницы</option>
			<option <?php if($show_pages == 'but') { echo 'selected="selected"'; } ?> value="but">Только страницы</option>
			</select>
		</td> 
	</tr>
	<?php
}

function link_cf_display_show_attachments($show_attachments) {
	?>
	<tr valign="top">
		<th scope="row"><label for="show_attachments"><?php _e('Show attachments?', 'post_plugin_library') ?></label></th>
		<td>
			<select name="show_attachments" id="show_attachments">
			<option <?php if($show_attachments == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($show_attachments == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

function link_cf_display_match_author($match_author) {
	?>
	<tr valign="top">
		<th scope="row"><label for="match_author"><?php _e('Только ссылки на посты от того же автора?', 'post_plugin_library') ?></label></th>
		<td>
			<select name="match_author" id="match_author">
			<option <?php if($match_author == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
			<option <?php if($match_author == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
			</select>
		</td> 
	</tr>
	<?php
}

function link_cf_display_match_cat($match_cat) {
	?>
	<tr valign="top">
		<th scope="row"><label for="match_cat"><?php _e('Только ссылки из той же категории?', 'post_plugin_library') ?></label></th>
		<td>
			<select name="match_cat" id="match_cat">
			<option <?php if($match_cat == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
			<option <?php if($match_cat == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
			</select>
		</td> 
	</tr>
	<?php
}

function link_cf_display_match_tags($match_tags) {
	global $wp_version;
	?>
	<tr valign="top">
		<th scope="row"><label for="match_tags"><?php _e('Ссылки с совпадающими метками (поле для ввода ниже)', 'post_plugin_library') ?></label></th>
		<td>
			<select name="match_tags" id="match_tags" <?php if ($wp_version < 2.3) echo 'disabled="true"'; ?> >
			<option <?php if($match_tags == 'false') { echo 'selected="selected"'; } ?> value="false">Все равно</option>
			<option <?php if($match_tags == 'any') { echo 'selected="selected"'; } ?> value="any">Один из перечесленных</option>
			<option <?php if($match_tags == 'all') { echo 'selected="selected"'; } ?> value="all">Все обязательно</option>
			</select>
		</td> 
	</tr>
	<?php
}

function link_cf_display_none_text($none_text) {
	?>
	<tr valign="top">
		<th scope="row"><label for="none_text"><?php _e('Текст, если ничего не найдено:', 'post_plugin_library') ?></label></th>
		<td><input name="none_text" type="text" id="none_text" value="<?php echo htmlspecialchars(stripslashes($none_text)); ?>" size="40" /></td>
	</tr>
	<?php
}


function link_cf_display_anons_len($len) {
	?>
	<tr valign="top">
		<th scope="row"><label for="anons_len"><?php _e('Длина анонса в символах (тег {anons}):', 'post_plugin_library') ?></label></th>
		<td><input name="anons_len" type="number" min="0" id="anons_len" value="<?php echo htmlspecialchars(stripslashes($len)); ?>"  /></td>
	</tr>
	<?php
}

function link_cf_display_no_text($no_text) {
	?>
	<tr valign="top">
		<th scope="row"><label for="no_text"><?php _e('Скрывать вывод, если нет ссылок?', 'post_plugin_library') ?></label></th>
		<td>
			<select name="no_text" id="no_text">
			<option <?php if($no_text == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
			<option <?php if($no_text == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
			</select>
		</td> 
	</tr>
	<?php
}

function link_cf_display_prefix($prefix) {
	?>
	<tr valign="top">
		<th scope="row"><label for="prefix"><?php _e('Префикс (код перед ссылками):', 'post_plugin_library') ?></label></th>
		<td><input name="prefix" type="text" id="prefix" value="<?php echo htmlspecialchars(stripslashes($prefix)); ?>" size="40" /></td>
	</tr>
	<?php
}

function link_cf_display_suffix($suffix) {
	?>
	<tr valign="top">
		<th scope="row"><label for="suffix"><?php _e('Суффикс (код после ссылок):', 'post_plugin_library') ?></label></th>
		<td><input name="suffix" type="text" id="suffix" value="<?php echo htmlspecialchars(stripslashes($suffix)); ?>" size="40" /></td>
	</tr>
	<?php
}


function link_cf_display_output_template($output_template) {
	?>
	<tr valign="top">
		<th scope="row"><label for="output_template"><?php _e('Содержание ссылки в списке:', 'post_plugin_library') ?></label></th>
		<td><input type="text" name="output_template" id="output_template" value="<?php echo htmlspecialchars(stripslashes($output_template)); ?>" size="40"/></td>
	</tr>
	<?php
}

function link_cf_display_replace_template($link_before, $link_after) {
	?>
	<tr valign="top">
		<th scope="row"><label for="link_before"><?php _e('Вывод ссылки перед выделенным текстом:', 'post_plugin_library') ?></label></th>
		<td><textarea name="link_before" id="link_before" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes(urldecode(base64_decode($link_before)))); ?></textarea></td>
	</tr>
		<tr valign="top">
		<th scope="row"><label for="link_after"><?php _e('Вывод после выделенного текста:', 'post_plugin_library') ?></label></th>
		<td><textarea name="link_after" id="link_after" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes(urldecode(base64_decode($link_after)))); ?></textarea></td>
	</tr>
	<?php
}

function link_cf_display_replace_term_template($term_before, $term_after) {
	?>
	<tr valign="top">
		<th scope="row"><label for="term_before"><?php _e('Вывод ссылки перед выделенным текстом:', 'post_plugin_library') ?></label></th>
		<td><textarea name="term_before" id="term_before" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes(urldecode(base64_decode($term_before)))); ?></textarea></td>
	</tr>
		<tr valign="top">
		<th scope="row"><label for="term_after"><?php _e('Вывод после выделенного текста:', 'post_plugin_library') ?></label></th>
		<td><textarea name="term_after" id="term_after" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes(urldecode(base64_decode($term_after)))); ?></textarea></td>
	</tr>
	<?php
}

function link_cf_display_divider($divider) {
	?>
	<tr valign="top">
		<th scope="row"><label for="divider"><?php _e('Разделитель между ссылками:', 'post_plugin_library') ?></label></th>
		<td><input name="divider" type="text" id="divider" value="<?php echo $divider; ?>" size="40" /></td>
	</tr>
	<?php
}

function link_cf_display_tag_str($tag_str) {
	global $wp_version;
	?>
	<tr valign="top">
		<th scope="row"><label for="tag_str"><?php _e('Совпадающие метки:<br />(a,b _через запятую_, чтобы совпала любая из перечисленных, a+b _через плюс_, чтобы совпали все метки)', 'post_plugin_library') ?></label></th>
		<td><input name="tag_str" type="text" id="tag_str" value="<?php echo $tag_str; ?>" <?php if ($wp_version < 2.3) echo 'disabled="true"'; ?> size="40" /></td>
	</tr>
	<?php
}

function link_cf_display_excluded_posts($excluded_posts) {
	?>
	<tr valign="top">
		<th scope="row"><label for="excluded_posts"><?php _e('Исключить записи с ID (через запятую):', 'post_plugin_library') ?></label></th>
		<td><input name="excluded_posts" type="text" id="excluded_posts" value="<?php echo $excluded_posts; ?>" size="40" /> <?php _e('', 'post_plugin_library'); ?></td>
	</tr>
	<?php
}

function link_cf_display_included_posts($included_posts) {
	?>
	<tr valign="top">
		<th scope="row"><label for="included_posts"><?php _e('Только записи из списка ID (через запятую):', 'post_plugin_library') ?></label></th>
		<td><input name="included_posts" type="text" id="included_posts" value="<?php echo $included_posts; ?>" size="40" /> <?php _e('', 'post_plugin_library'); ?></td>
	</tr>
	<?php
}

function link_cf_display_authors($excluded_authors, $included_authors) {
	global $wpdb;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Записи каких авторов выводить:', 'post_plugin_library') ?></th>
		<td>
			<table class="linkateposts-inner-table">	
			<?php 
				$users = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users ORDER BY user_login");
				if ($users) {
					$excluded = explode(',', $excluded_authors);
					$included = explode(',', $included_authors);
					echo "\n\t<tr valign=\"top\"><td><strong>Имя юзера</strong></td><td><strong>Скрыть</strong></td><td><strong>Показать</strong></td></tr>";
					foreach ($users as $user) {
						if (false === in_array($user->ID, $excluded)) {
							$ex_ischecked = '';
						} else {
							$ex_ischecked = 'checked';
						}
						if (false === in_array($user->ID, $included)) {
							$in_ischecked = '';
						} else {
							$in_ischecked = 'checked';
						}
						echo "\n\t<tr valign=\"top\"><td>$user->user_login</td><td><input type=\"checkbox\" name=\"excluded_authors[]\" value=\"$user->ID\" $ex_ischecked /></td><td><input type=\"checkbox\" name=\"included_authors[]\" value=\"$user->ID\" $in_ischecked /></td></tr>";
					}
				}	
			?>
			</table>
		</td> 
	</tr>
	<?php
}

function link_cf_display_cats($excluded_cats, $included_cats) {
	global $wpdb;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Рубирки скрыть/показать:', 'post_plugin_library') ?></th>
		<td>
			<table class="linkateposts-inner-table">	
			<?php 
				if (function_exists("get_categories")) {
					$categories = get_categories();//('&hide_empty=1');
				} else {
					//$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories WHERE category_count <> 0 ORDER BY cat_name");
					$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_name");
				}
				if ($categories) {
					echo "\n\t<tr valign=\"top\"><td><strong>Рубрика</strong></td><td><strong>Скрыть</strong></td><td><strong>Показать</strong></td></tr>";
					$excluded = explode(',', $excluded_cats);
					$included = explode(',', $included_cats);
					$level = 0;
					$cats_added = array();
					$last_parent = 0;
					$cat_parent = 0;
					foreach ($categories as $category) {
						$category->cat_name = esc_html($category->cat_name);
						if (false === in_array($category->cat_ID, $excluded)) {
							$ex_ischecked = '';
						} else {
							$ex_ischecked = 'checked';
						}
						if (false === in_array($category->cat_ID, $included)) {
							$in_ischecked = '';
						} else {
							$in_ischecked = 'checked';
						}
						$last_parent = $cat_parent;
						$cat_parent = $category->category_parent;
						if ($cat_parent == 0) {
							$level = 0;
						} elseif ($last_parent != $cat_parent) {
							if (in_array($cat_parent, $cats_added)) {
								$level = $level - 1;
							} else {
								$level = $level + 1;
							}
							$cats_added[] = $cat_parent;
						}
						if ($level < 0) {
							$level = 0;
						}
						$pad = str_repeat('&nbsp;', 3*$level);
						echo "\n\t<tr valign=\"top\"><td>$pad$category->cat_name</td><td><input type=\"checkbox\" name=\"excluded_cats[]\" value=\"$category->cat_ID\" $ex_ischecked /></td><td><input type=\"checkbox\" name=\"included_cats[]\" value=\"$category->cat_ID\" $in_ischecked /></td></tr>";
					}
				}
			?>
			</table>
		</td> 
	</tr>
	<?php
}


function link_cf_display_age($age) {
	?>
	<tr valign="top">
		<th scope="row"><label for="age-direction"><?php _e('Скрыть записи по возрасту:', 'post_plugin_library') ?></label></th>
		<td>
			
				<select name="age-direction" id="age-direction">
				<option <?php if($age['direction'] == 'before') { echo 'selected="selected"'; } ?> value="before">младше</option>
				<option <?php if($age['direction'] == 'after') { echo 'selected="selected"'; } ?> value="after">старше</option>
				<option <?php if($age['direction'] == 'none') { echo 'selected="selected"'; } ?> value="none">-----</option>
				</select>
				<input style="vertical-align: middle; width: 60px;" name="age-length" type="number" id="age-length" value="<?php echo $age['length']; ?>" size="4" />
                
				<select name="age-duration" id="age-duration">
				<option <?php if($age['duration'] == 'day') { echo 'selected="selected"'; } ?> value="day">дней</option>
				<option <?php if($age['duration'] == 'month') { echo 'selected="selected"'; } ?> value="month">месяцев</option>
				<option <?php if($age['duration'] == 'year') { echo 'selected="selected"'; } ?> value="year">лет</option>
				</select>
				

		</td>
	</tr>
	<?php
}

function link_cf_display_status($status) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Статус записей:', 'post_plugin_library') ?></th>
		<td>

				<label for="status-publish">Опубликованы</label>
				<select name="status-publish" id="status-publish" <?php if (!function_exists('get_post_type')) echo 'disabled="true"'; ?>>
				<option <?php if($status['publish'] == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
				<option <?php if($status['publish'] == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
				</select>

				<label for="status-private">Личные</label>
				<select name="status-private" id="status-private" <?php if (!function_exists('get_post_type')) echo 'disabled="true"'; ?>>
				<option <?php if($status['private'] == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
				<option <?php if($status['private'] == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
				</select>

				<label for="status-draft">Черновик</label>
				<select name="status-draft" id="status-draft" <?php if (!function_exists('get_post_type')) echo 'disabled="true"'; ?>>
				<option <?php if($status['draft'] == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
				<option <?php if($status['draft'] == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
				</select>

				<label for="status-future">Запланированные</label>
				<select name="status-future" id="status-future" <?php if (!function_exists('get_post_type')) echo 'disabled="true"'; ?>>
				<option <?php if($status['future'] == 'false') { echo 'selected="selected"'; } ?> value="false">Нет</option>
				<option <?php if($status['future'] == 'true') { echo 'selected="selected"'; } ?> value="true">Да</option>
				</select>

		</td>
	</tr>
	<?php
}

function link_cf_display_custom($custom) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Совпадающие по кастомному полю:', 'post_plugin_library') ?></th>
		<td>
			<table>
			<tr><td style="border-bottom-width: 0">Имя поля</td><td style="border-bottom-width: 0"></td><td style="border-bottom-width: 0">Значение</td></tr>
			<tr>
			<td style="border-bottom-width: 0"><input name="custom-key" type="text" id="custom-key" value="<?php echo $custom['key']; ?>" size="20" /></td>
			<td style="border-bottom-width: 0">
				<select name="custom-op" id="custom-op">
				<option <?php if($custom['op'] == '=') { echo 'selected="selected"'; } ?> value="=">=</option>
				<option <?php if($custom['op'] == '!=') { echo 'selected="selected"'; } ?> value="!=">!=</option>
				<option <?php if($custom['op'] == '>') { echo 'selected="selected"'; } ?> value=">">></option>
				<option <?php if($custom['op'] == '>=') { echo 'selected="selected"'; } ?> value=">=">>=</option>
				<option <?php if($custom['op'] == '<') { echo 'selected="selected"'; } ?> value="<"><</option>
				<option <?php if($custom['op'] == '<=') { echo 'selected="selected"'; } ?> value="<="><=</option>
				<option <?php if($custom['op'] == 'LIKE') { echo 'selected="selected"'; } ?> value="LIKE">LIKE</option>
				<option <?php if($custom['op'] == 'NOT LIKE') { echo 'selected="selected"'; } ?> value="NOT LIKE">NOT LIKE</option>
				<option <?php if($custom['op'] == 'REGEXP') { echo 'selected="selected"'; } ?> value="REGEXP">REGEXP</option>
				<option <?php if($custom['op'] == 'EXISTS') { echo 'selected="selected"'; } ?> value="EXISTS">EXISTS</option>			
				</select>
			</td>
			<td style="border-bottom-width: 0"><input name="custom-value" type="text" id="custom-value" value="<?php echo $custom['value']; ?>" size="20" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<?php
}

function link_cf_display_append($options) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Вывод после записи:', 'post_plugin_library') ?></th>
		<td>
			<table>
			<tr><td style="border-bottom-width: 0"><label for="append_on">Activate</label></td><td style="border-bottom-width: 0"><label for="append_priority">Priority</label></td><td style="border-bottom-width: 0"><label for="append_parameters">Parameters</label></td><td style="border-bottom-width: 0"><label for="append_condition">Condition</label></td></tr>
			<tr>
			<td style="border-bottom-width: 0">			
				<select name="append_on" id="append_on">
				<option <?php if($options['append_on'] == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
				<option <?php if($options['append_on'] == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
				</select>
			</td>
			<td style="border-bottom-width: 0"><input name="append_priority" type="number" id="append_priority" style="width: 60px;" value="<?php echo $options['append_priority']; ?>" size="3" /></td>
			<td style="border-bottom-width: 0"><textarea name="append_parameters" id="append_parameters" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes($options['append_parameters'])); ?></textarea></td>
			<td style="border-bottom-width: 0"><textarea name="append_condition" id="append_condition" rows="4" cols="20"><?php echo htmlspecialchars(stripslashes($options['append_condition'])); ?></textarea></td>
			</tr></table>
		</td> 
	</tr>
	<?php
}

function link_cf_display_content_filter($content_filter) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Output in content:<br />(<em>via</em> special tags)', 'post_plugin_library') ?></th>
		<td>
			<table>
			<tr><td style="border-bottom-width: 0"><label for="content_filter">Activate</label></td></tr>
			<tr>
			<td style="border-bottom-width: 0">			
			<select name="content_filter" id="content_filter">
			<option <?php if($content_filter == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($content_filter == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
			</td>
			</tr>
			</table>
		</td> 
	</tr>
	<?php
}

function link_cf_display_sort($sort) {
	global $wpdb;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Сортировать по:<br />можно оставить пустым для сортировки по умолчанию', 'post_plugin_library') ?></th>
		<td>
			<table>
			<tr><td style="border-bottom-width: 0"></td><td style="border-bottom-width: 0">Тег (типа {title})</td><td style="border-bottom-width: 0">Порядок</td><td style="border-bottom-width: 0">Заглавные буквы</td></tr>
			<tr>
			<td style="border-bottom-width: 0">Условие №1</td>
			<td style="border-bottom-width: 0"><input name="sort-by1" type="text" id="sort-by1" value="<?php echo $sort['by1']; ?>" size="20" /></td>
			<td style="border-bottom-width: 0">
				<select name="sort-order1" id="sort-order1">
				<option <?php if($sort['order1'] == SORT_ASC) { echo 'selected="selected"'; } ?> value="SORT_ASC">По возрастанию</option>
				<option <?php if($sort['order1'] == SORT_DESC) { echo 'selected="selected"'; } ?> value="SORT_DESC">По убыванию</option>
				</select>
			</td> 
			<td style="border-bottom-width: 0">
				<select name="sort-case1" id="sort-case1">
				<option <?php if($sort['case1'] == 'false') { echo 'selected="selected"'; } ?> value="false">чувствительный</option>
				<option <?php if($sort['case1'] == 'true') { echo 'selected="selected"'; } ?> value="true">без разницы</option>
				</select>
			</td> 
			</tr>
			<tr>
			<td style="border-bottom-width: 0">Условие №2</td>
			<td style="border-bottom-width: 0"><input name="sort-by2" type="text" id="sort-by2" value="<?php echo $sort['by2']; ?>" size="20" /></td>
			<td style="border-bottom-width: 0">
				<select name="sort-order2" id="sort-order2">
				<option <?php if($sort['order2'] == SORT_ASC) { echo 'selected="selected"'; } ?> value="SORT_ASC">По возрастанию</option>
				<option <?php if($sort['order2'] == SORT_DESC) { echo 'selected="selected"'; } ?> value="SORT_DESC">По убыванию</option>
				</select>
			</td> 
			<td style="border-bottom-width: 0">
				<select name="sort-case2" id="sort-case2">
				<option <?php if($sort['case2'] == 'false') { echo 'selected="selected"'; } ?> value="false">чувствительный</option>
				<option <?php if($sort['case2'] == 'true') { echo 'selected="selected"'; } ?> value="true">без разницы</option>
				</select>
			</td> 
			</tr>
			</table>
		</td>
	</tr>
	<?php
}

function link_cf_display_orderby($options) {
	global $wpdb;
	$limit = 30;
	$keys = $wpdb->get_col( "
		SELECT meta_key
		FROM $wpdb->postmeta
		WHERE meta_key NOT LIKE '\_%'
		GROUP BY meta_key
		ORDER BY meta_id DESC
		LIMIT $limit" );
	$metaselect = "<select id='orderby' name='orderby'>\n\t<option value=''></option>";
	if ( $keys ) {
		natcasesort($keys);
		foreach ( $keys as $key ) {
			$key = esc_attr( $key );
			if ($options['orderby'] == $key) {
				$metaselect .= "\n\t<option selected='selected' value='$key'>$key</option>";
			} else {
				$metaselect .= "\n\t<option value='$key'>$key</option>";
			}
		}
		$metaselect .= "</select>";
	}

	?>
	<tr valign="top">
		<th scope="row"><?php _e('Select output by custom field:', 'post_plugin_library') ?></th>
		<td>
			<table>
			<tr><td style="border-bottom-width: 0">Field</td><td style="border-bottom-width: 0">Order</td><td style="border-bottom-width: 0">Case</td></tr>
			<tr>
			<td style="border-bottom-width: 0">
			<?php echo $metaselect;	?>	
			</td>
			<td style="border-bottom-width: 0">
				<select name="orderby_order" id="orderby_order">
				<option <?php if($options['orderby_order'] == 'ASC') { echo 'selected="selected"'; } ?> value="ASC">ascending</option>
				<option <?php if($options['orderby_order'] == 'DESC') { echo 'selected="selected"'; } ?> value="DESC">descending</option>
				</select>
			</td> 
			<td style="border-bottom-width: 0">
				<select name="orderby_case" id="orderby_case">
				<option <?php if($options['orderby_case'] == 'false') { echo 'selected="selected"'; } ?> value="false">case-sensitive</option>
				<option <?php if($options['orderby_case'] == 'true') { echo 'selected="selected"'; } ?> value="true">case-insensitive</option>
				<option <?php if($options['orderby_case'] == 'num') { echo 'selected="selected"'; } ?> value="num">numeric</option>
				</select>
			</td> 
			</tr>
			</table>
		</td>
	</tr>
	<?php
}

// now for linkate_posts

function link_cf_display_num_term_length_limit($term_length_limit) {
	?>
	<tr valign="top">
		<th scope="row"><label for="term_length_limit"><?php _e('Не учитывать слова короче (кол-во букв, включительно):', 'post_plugin_library') ?></label></th>
		<td><input name="term_length_limit" type="number" id="term_length_limit" style="width: 60px;" value="<?php echo $term_length_limit; ?>" size="3"  min="0"/></td>
	</tr>
	<?php
}


function link_cf_display_num_terms($num_terms) {
	?>
	<tr valign="top">
		<th scope="row"><label for="num_terms"><?php _e('Количество ключевых слов для определения схожести:', 'post_plugin_library') ?></label></th>
		<td><input name="num_terms" type="number" id="num_terms" style="width: 60px;" value="<?php echo $num_terms; ?>" size="3" /></td>
	</tr>
	<?php
}

function link_cf_display_term_extraction($term_extraction) {
	?>
	<tr valign="top">
		<th scope="row" title=""><label for="term_extraction"><?php _e('Алгоритм поиска ключевых слов:', 'post_plugin_library') ?></label></th>
		<td>
			<select name="term_extraction" id="term_extraction">
			<option <?php if($term_extraction == 'frequency') { echo 'selected="selected"'; } ?> value="frequency">Частота использования</option>
			<option <?php if($term_extraction == 'pagerank') { echo 'selected="selected"'; } ?> value="pagerank">Алгоритм TextRank</option>
			</select>
		</td> 

	</tr>
		<tr valign="top"><td colspan="2">* Стоп-слова учитываются только при включенном алгоритме по частотности использования слов.</td></tr>
	<?php
}

function link_cf_display_weights($options) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Значимость полей:', 'post_plugin_library') ?></th>
		<td>
			<label for="weight_content">содержание записи:  </label><input name="weight_content" type="number" style="width: 60px;" id="weight_content" value="<?php echo round(100 * $options['weight_content']); ?>" size="3" /> %
			<label for="weight_title" style="margin-left:20px;">заголовок статьи:  </label><input name="weight_title" type="number" style="width: 60px;" id="weight_title" value="<?php echo round(100 * $options['weight_title']); ?>" size="3" /> %
			<label for="weight_tags" style="margin-left:20px;">метки:  </label><input name="weight_tags" type="number" style="width: 60px;" id="weight_tags" value="<?php echo round(100 * $options['weight_tags']); ?>" size="3" /> % ( суммарно до 100% )
		</td>
		
	</tr>
	<tr valign="top">
	<th scope="row"><?php _e('SEO Title:', 'post_plugin_library') ?></th>
		<td><input name="compare_seotitle" type="checkbox" id="compare_seotitle" value="cb_compare_seotitle" <?php echo $options['compare_seotitle']; ?>/><label for="compare_seotitle">Использовать SEO тайтл вместо заголовка статьи (берется из Yoast или AIOSEO)  </label></td>
	</tr>
	<?php
}

function link_cf_display_stopwords($custom_stopwords) {
	?>
	<tr valign="top">
		<th scope="row"><label for="custom_stopwords"><?php _e('Список стоп-слов:', 'post_plugin_library') ?></label></th>
		<td><textarea name="custom_stopwords" id="custom_stopwords" rows="6" cols="38" placeholder="слово1&#10;слово2"><?php echo htmlspecialchars(stripslashes($custom_stopwords)); ?></textarea></td>
	</tr>
	<?php
}

function link_cf_get_plugin_data($plugin_file) {
	if(!function_exists( 'get_plugin_data' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php');
	static $plugin_data;
	if(!$plugin_data) {
		$plugin_data = get_plugin_data($plugin_file);
		if (!isset($plugin_data['Title'])) {
			if ('' != $plugin_data['PluginURI'] && '' != $plugin_data['Name']) {
				$plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '" title="'. __('Посетите страницу плагина', 'post-plugin-library') . '">' . $plugin_data['Name'] . '</a>';
			} else {
				$plugin_data['Title'] = $name;
			}
		}
	}
	return $plugin_data;
}
