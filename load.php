<?php
include('config.inc.php');
require(ABSPATH.'/include/mydb.class.php');
include(ABSPATH.'/include/mydir.class.php');
//require(ABSPATH.'/admin/include/class.category.php');
require(ABSPATH.'/include/prompt.class.php');
require(ABSPATH.'/include/user.class.php');
require(ABSPATH.'/include/function.php');
$db = new db('localhost', 'root', '', 'mycms');
user::login($db);
?>