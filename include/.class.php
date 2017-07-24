<?php

/***********************
        db类
***********************/
final class book{
 public $;
  
  //类构造函数
 public function __construct(){
  
 }
 
 public function view($conn){
  if(isset($_GET['aid'])){
   $aid = trim($_GET['aid']);
   if(!$aid = (int) $aid) return ERROR::err('302', '非法访问！');
  }else{
   return ERROR::err('303', '非法访问！');
  }
  $sql = "select * from `article` where `aid`='".$aid."' limit 1";
  if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！');
  $article = $result->fetch_assoc();
  $class = $this->sitepath($conn, $article['cid']);
  sitepath($article['title']);
  $user = $GLOBALS['user'];
  if(is_string($group = user::group($conn, $user['gid']))) return $group;
  if($group['isvisit']){
   if(!$group['isread']){
    if($groups = $class['groups']){
     $price = $article['price'];
     if($price > 0){
      $ispay = false;
      if($user['hasgoods']){
       $sql = "select classes,articles from goods where uid={$user['uid']} limit 1";
       if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！');
       $goods = $result->fetch_assoc();
       $c_pos = strpos($goods['classes'], '|'.$article['cid'].'|');
       if(is_int($c_pos)){
        $ispay = true;
       }else{
        $a_pos = strpos($goods['articles'], ','.$aid.',');
        if(is_int($a_pos)) $ispay = true;
       }
      }
      $gid = '|'.$group['gid'].',';
      $pos = strpos($groups, $gid);
      if(is_int($pos)){
       $pos += strlen($gid);
       $percent = substr($groups, $pos, 3);
      }else{
       $percent = 0;
      }
      $payment = $price - $percent / 100 * $price;
      $virtual_coin = $user['virtual_coin'];
      if($virtual_coin < $payment){
       return ERROR::err(305, "对不起，您的金币不足，不能阅读本文，请充值！");
      }
      $virtual_coin -= $payment;
      $sql = "update user set virtual_coin={$virtual_coin} where uid = {$user['uid']} limit 1";
      if(!$conn->query($sql)) return ERROR::err(305, 'SQL语句错误！');
      $_SESSION['user'][''];
      $GLOBALS[''][''];
     }
    }
   }
  }else{
   if($group['gid'] == 1) return ERROR::err(305, "对不起，{$group['gname']}不能访问本站，请登录！");
   return ERROR::err(305, "对不起，{$group['gname']}不能访问本站！");
  }
  $sql = "select * from `content` where `aid`='".$aid."' order by page asc limit 1";
  if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！');
  $content = $result->fetch_assoc();
  $paginations = '';
  for($i = 1; $i <= $article['page_num']; ++$i){
   include(ABSPATH.TPLPATH.'pagination.html');
   $paginations .= $pagination;
  }
  $comment = $this->comment($conn, $aid, $article['hascomment']);
  include(ABSPATH.TPLPATH.'article.html');
  return $item;
 }
  
 public function (){
  
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