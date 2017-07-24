<?php
//类
final class admin{
 public $a;
  
  //类构造函数
 public function __construct(){
  
 }
  //     
 public static function login($db, $sess_type = SESSION_TYPE){
  if($admin = $sess_type()) return $admin;
  if(isset($_POST['username']) && isset($_POST['password'])){
   $username = filter($_POST['username']);
   $password = filter($_POST['password']);
   if($username ==='' || $password === ''){
    return '用户名或密码不能为空!';
   }
   $sql = "select * from user where username='".$username."' and  password='".$password."' and gid=2  limit 1";
   if(!$result = $conn->query($sql)) return ERROR::err('SQL语句有误！');
   if(!$admin = $result->fetch_assoc()){
    return '用户名或密码错误!';
   }
   $sess_type .= '_l';
   return $sess_type($admin);
  }else{
   include(ABSPATH.TPLPATH.'login.html');
   return $login_html;
  }
 }
  
  //
 public function logout(){
  
 }
  
 public static function group($conn, $gid){
  $sql = "select * from `group` where gid='".$gid."' limit 1";
  if(!$conn) return '出错';
  if(!$result = $conn->query($sql))return ERROR::err('SQL语句有误！2');
  if(!$group = $result->fetch_assoc()){
   return '用户组不存在!';
  }
  if($group['isupload']){
   $_SESSION['KCFINDER'] = array();
   $_SESSION['KCFINDER']['disabled'] = false;
  }
  return $group;
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