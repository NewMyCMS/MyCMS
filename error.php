<?php
//error_reporting(0);
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}
$startime=get_microtime();
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__));
require(ABSPATH.'/const.inc.php');
require(ABSPATH.'/config.inc.php');
require(ABSPATH.'/include/sql.class.php');
require(ABSPATH.'/include/function.php');
require(ABSPATH.'/include/page.class.php');
if(isset($_GET['aid'])){
 $aid = (int)$_GET['aid'];
}else{
 $aid = 0;
}
$p = new page();
$page_body = $p->view($conn);
include(ABSPATH.'/template/default/blank.html');
echo $page;
echo get_microtime()-$startime;
?>