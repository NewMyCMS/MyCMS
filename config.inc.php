<?php
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__));
define('TITLE_NUM', 6);
define('TITLE_LIMIT', 30);
define('TPLPATH', '/template/default/');
define('SITEPATH', $_SERVER['DOCUMENT_ROOT']);
define('RELPATH', str_replace(SITEPATH, '', ABSPATH));
define('CHARSET', 'utf-8');
define('SESSION_TYPE', 'session');
define('MOD_NAME', '文章中心');
define('MOD_URL', 'http://127.0.0.1/');
define('SITE_NAME', 'MyCMS');
define('SITE_URL', 'http://127.0.0.1/');
define('DIR_MODE', 1);//1-严谨模式 0-混乱模式
define('RECORD_SIZE', 1000000);
if(!isset($_SESSION))session_start();
$title_replacement = '正文';
$title = '';
$sitepath = '';
$user = array('uid' => '1', 'gid' => '1', 'username' => '匿名用户', 'password' => 'test', 'email' => 'test@test.cn', 'info' => '', 'registered' => '2013-07-24 23:14:51', 'lastlogintime' => '2013-07-24 23:14:51', 'ip' => '0', 'truename' => '', 'passport' => '', 'birthday' => '2013-07-24', 'favourite' => '', 'school' => '', 'virtual_coin' => '0.00');
//$group = '';
//$anonymous = '匿名用户';
?>