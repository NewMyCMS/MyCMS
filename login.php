<?php
//error_reporting(0);
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}
$startime=get_microtime();
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__));
require(ABSPATH.'/include/sql.class.php');
require(ABSPATH.'/include/function.php');
require(ABSPATH.'/include/error.class.php');
require(ABSPATH.'/include/user.class.php');
require(ABSPATH.'/include/page.class.php');
require(ABSPATH.'/config.inc.php');
$page_body = '';
if(is_string($user = user::login($conn)))$page_body = $user;
include(ABSPATH.'/template/default/blank.html');
echo $page;
echo get_microtime()-$startime;
?>