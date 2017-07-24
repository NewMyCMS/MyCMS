<?php

/***********************
        mydir类
***********************/
final class mydir{
 public static $error = '';
  
  //类构造函数
 public function __construct(){
  
 }

 //删除文件(夹)
 public static function del($absdir, $dir, $isdel = false){
  if($dir){
   $dir = $absdir.$dir;
  }else{
   return;
  }
  if(!file_exists($dir)) {
   mydir::$error = $dir.'文件(夹)不存在！';
   return false;
  }
  set_time_limit(6000);
  if(is_dir($dir)){
   if(!$arr = scandir($dir)){
    mydir::$error = $dir.'文件夹没有读权限！';
    return false;
   }
   foreach($arr as $val){
    if($val != '.' && $val != '..'){
     self::del($dir, '/'.$val, true);
    }
   }
   if($isdel){
    if(!rmdir($dir)){
     mydir::$error = $dir.'文件夹没有写权限！';
     return false;
    }
   }
  }else{
   if(!unlink($dir)){
    mydir::$error = $dir.'文件没有写权限！';
    return false;
   }
  }
  return true;
 }
 
 //
 public static function filter($filename){
  if($filename == '.' || $filename == '..'){
   mydir::$error = '文件(夹)不能命名为.或..！';
   return false;
  }
  if(preg_match('@[/\\:*?<>|"]+@', $filename)){
   mydir::$error = '文件(夹)名称中不能包含/\:*?<>|"等ANSI字符！';
   return false;
  }
  return true;
 }
 
 //
 public static function _mkdir($dir){
  
 }
 
 //
 /*public function (){
  
 }*/
}
?>