<?php
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__));
define('TITLE_NUM', 6);
define('TITLE_LIMIT', 30);
define('TPLPATH', '/template/');
define('SITEPATH', $_SERVER['DOCUMENT_ROOT']);
define('RELPATH', str_replace(SITEPATH, '', ABSPATH));
define('CHARSET', 'utf-8');
define('SESSION_TYPE', 'session');
define('SITE_NAME', 'MyCMS');
define('SITE_URL', 'http://127.0.0.1/');
define('DIR_MODE', 1);//1-严谨模式 0-混乱模式
?>