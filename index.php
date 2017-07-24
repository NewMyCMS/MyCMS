<?php
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}
$startime=get_microtime();
require('load.php');
/*require('config.inc.php');
require(ABSPATH.'/const.inc.php');
require(ABSPATH.'/include/sql.class.php');
require(ABSPATH.'/include/function.php');
require(ABSPATH.'/include/user.class.php');
require(ABSPATH.'/include/error.class.php');*/
require(ABSPATH.'/include/page.class.php');
if(isset($_GET['cid'])){
 $cid = $_GET['cid'];
}else{
 $cid = 0;
}
init_sitepath();
$p = new page();
$page_body = $p->category($db, $cid);
include(ABSPATH.'/template/default/blank.html');
echo $page;
echo get_microtime()-$startime;
?>