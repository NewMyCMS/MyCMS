<?php
/*function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}*/

//字符切割函数
function cutstr($string, $length = 21, $start = 0, $charset = CHARSET){
 if($charset == 'utf-8'){
  $tmpstr = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$start.'}'. 
'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$length.'}).*#s', 
'$1',$string);
 }else{
  $length = $length * 2; 
  $tmpstr = '';
  if(isset($string[$length-1])){
   $strlen = strlen($string);
   for($i=0; $i<$strlen; $i++){
    if($i>=$start && $i<$length){
     if(ord(substr($string, $i, 1))>129){
      $tmpstr.= substr($string, $i, 2);
      ++$i;
     }else{
      $tmpstr.= substr($string, $i, 1);
     }
    }
   }
  }else{
   $tmpstr = $string;
  }
 }
 return $tmpstr; 
}

//
function filter($db, $str){
 if(get_magic_quotes_gpc()) $str = stripslashes($str);
 return trim($db->escape($str));
}


//登录相关函数
function session(){
 if(isset($_SESSION['admin'])){
  $GLOBALS['admin'] = $_SESSION['admin'];
  return $_SESSION['admin'];
 }else{
  return false;
 }
}

//
function cookie(){
 if(isset($_SESSION['uid'])){
  return '登录成功';
 }
}

//
function other(){
 if(isset($_SESSION['uid'])){
  return '登录成功';
 }
}

//
function session_l($admin){
 $GLOBALS['admin'] = $admin;
 $_SESSION['admin'] = $admin;
 return $admin;
}

//
function cookie_l($user){
}

//
function other_l($user){
}
//登录相关函数结束

/*//
function sitepath($label, $url = '', $sign = ' - '){
 if($url){
  include(ABSPATH.TPLPATH.'sitepath.html');
 }else{
  if($GLOBALS['title_replacement']) $sitepath = $GLOBALS['title_replacement'];
 }
 $GLOBALS['sitepath'] .= $sign.$sitepath;
 $label .= '_'.$GLOBALS['title'];
 $GLOBALS['title'] = $label;
}

//
function init_sitepath(){
 $label = SITE_NAME;
 $url = SITE_URL;
 include(ABSPATH.TPLPATH.'sitepath.html');
 $GLOBALS['sitepath'] = $sitepath;
 $label = MOD_NAME;
 $url = MOD_URL;
 include(ABSPATH.TPLPATH.'sitepath.html');
 $GLOBALS['sitepath'] .= ' - '.$sitepath;
 $GLOBALS['title'] = MOD_NAME.'_'.SITE_NAME.' - Powered by MyCMS';
}*/

//
function init_anonymous(){
 
}
?>