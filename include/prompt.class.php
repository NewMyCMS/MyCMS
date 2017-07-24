<?php
//错误处理类
final class prompt{
 public $err_body;
  
 //类构造函数
 public function __construct(){
 }
 
 //
 public static function error($error, $errno = ''){
  include(ABSPATH.TPLPATH.'error.html');
  return $error_html;
 }
  
 public static function warning($str){
  include(ABSPATH.TPLPATH.'error.html');
  return $error_html;
 }
  
 public static function success($str){
  include(ABSPATH.TPLPATH.'error.html');
  return $error_html;
 }
 
 //
 public static function info($error){
  include(ABSPATH.TPLPATH.'error.html');
  return $error_html;
 }
  
 /*public static function ($){
 
 }*/
}
?>