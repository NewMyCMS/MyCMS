<?php
require('load.php');
//init_sitepath();
$c = new category();
if(isset($_GET['action'])){
 switch($_GET['action']){
  case 'add':
   $e = $c->add($db);
   break;
  case 'modify':
   $e = $c->modify($db);
   break;
  /*case 'edit':
   $e = $c->edit($db);
   break;*/
  case 'delete':
   if(!isset($_GET['cid'])){
    $e = ERROR::err('非法访问！');
    break;
   }
   $e = $c->_delete($db, $_GET['cid']);
   break;
  default:
   $e = '';
   break;
 }
 $classes = $c->_list($db, 0); 
 $options = $c->createoption($db, 0);
 $class_dir = '';
 if(!DIR_MODE) include(ABSPATH.'/template/class_dir.html');
 include(ABSPATH.'/template/table.html');
 $page_body .= $e;
 include(ABSPATH.'/template/blank.html');
 echo $page;
}elseif(isset($_GET['cid'])){
 $page_body = $c->edit($db);
 include(ABSPATH.'/template/blank.html');
 echo $page;
}else{
 $classes = $c->_list($db, 0);
 $options = $c->createoption($db, 0);
 $class_dir = '';
 if(!DIR_MODE) include(ABSPATH.'/template/class_dir.html');
 include(ABSPATH.'/template/table.html');
 include(ABSPATH.'/template/blank.html');
 echo $page;
}
?>