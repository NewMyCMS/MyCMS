<?php
echo $_SERVER['DOCUMENT_ROOT'];
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__));
require(ABSPATH.'/const.inc.php');
require(ABSPATH.'/include/sql.class.php');
$timestart=get_microtime();
$sql = "select * from user where uid='1' limit 1";
if(!$result = $conn->query($sql)) return ERROR::err('SQL语句有误！');
if(!$user = $result->fetch_assoc()){
 return '用户名或密码错误!';
}
$str = '$user = array(';
$str2 = '';
foreach($user as $key=>$val){
 if($str2 == ''){
  $str2 = '\''.$key.'\' => \''.$val.'\'';
 }else{
  $str2 .= ', \''.$key.'\' => \''.$val.'\'';
 }
}
/*
foreach($user as $key=>$val){
 if($str2 == ''){
  if(!is_numeric($val)){
   $val = '\''.$val.'\'';
  }else{
   $val = (int) $val;
  }
  $str2 = '\''.$key.'\' => '.$val;
 }else{
  if(!is_numeric($val)){
   $val = '\''.$val.'\'';
  }else{
   $val = (int) $val;
  }
  $str2 .= ', \''.$key.'\' => '.$val;
 }
}*/
$user = $str.$str2.');';
//print_r($user);
//$user = serialize($user);
file_put_contents('anonymous.txt', $user);
?>