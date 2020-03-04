<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache



define('WP_POST_REVISIONS', true );


 // Added by WP Rocket

/** Enable W3 Total Cache */

/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'tehnoguru');
/** Имя пользователя MySQL */
define('DB_USER', 'tehnoguru');
/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'Hvecj1Sz3$P@7]2p');
/** Имя сервера MySQL */
define('DB_HOST', 'localhost');
/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');
/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');
/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '<dKu5p7m#d|EXn{fr/fxqbMAl$8I1/MO$bfYG(-CW:FujRa|oa,r:O.LG+(kux_b');
define('SECURE_AUTH_KEY',  '.1.v9N:C?%8F@U?_yvtn^CHY2+vON+*P,_0 (9X|~o_{e-2:]45dCz&KF;#G4$X{');
define('LOGGED_IN_KEY',    '3!dPm@&pZAe7CI=KL5-A|Oyks_J>_hzT-}JTTOY8yVf]kZctGf9KrNbe)8rKpN!w');
define('NONCE_KEY',        'RscZR(}b+P6i{pj3<g_MevVG[+Z$WwwjmE)t{!lhL S@JC+/XdY MrD),;Qm|1?<');
define('AUTH_SALT',        '^#`#uI_Jn1fUyo^oMHaG$k1s@hFIB|&ZA}2[);NT+#.?qa8BDijJUKE2$IF7KSJ}');
define('SECURE_AUTH_SALT', '!fbqX_-&A=A(? hXa]vq;Adv~+4fvg=e +fF((V089%m%cZY,oO{rnQCu>?~Ztv*');
define('LOGGED_IN_SALT',   '^6JZd,ow5-T{cptT0K~Rm&y=o^,E0K>]4^M_huQY!e${W@-CQrJ!uPR[xqEI>b}1');
define('NONCE_SALT',       ':v[{>3vF#(<9mK]@:;U~BLe5,q0b]XBA, *%t?@ fG]Oin-aM?P^9V)gWv|IHwH_');
/**#@-*/
/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';
define( 'WP_MEMORY_LIMIT', '1024M' );
/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
/* Это всё, дальше не редактируем. Успехов! */
/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
add_filter('xmlrpc_enabled','__return_false');
define('DISABLE_WP_CRON', true);
