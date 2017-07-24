<?php

/***********************
        news类
***********************/
final class news{
 public $error;
  
  //类构造函数
 public function __construct(){
  
 }
  
 public static function del($dir){
  $arr = scandir($dir);
  foreach($arr as $val){
   if($val != '.' && $val != '..'){
    if(is_dir($val)){
     mydir::del($dir.$val.'/');
     unlink($dir.$val.'/');
    }else{
     unlink($dir.$val.'/');
    }
   }
  }
 }
 
 /*public function (){
  
 }
 public function (){
  
 }
 public function (){
  
 }
 public function (){
  
 }
 public function (){
  
 }*/
}
?>