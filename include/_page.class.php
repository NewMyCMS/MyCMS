<?php

/***********************
        result类
***********************/
final class page{
 public $a;
  
  //类构造函数
 public function __construct(){
  
 }
  
 public function listtitle($conn, $cid, $length, $limit, $isfinal){
  $sql = 'select * from `article` where';
  if($isfinal){
   $sql .= " `cid`='".$cid."' order by aid desc limit ".$limit;
  }else{
   $sql1= "select `cid` from `class` where isfinal=1 and parentclass like '%|{$cid},%'";
   if(!$result = $conn->query($sql1)) return ERROR::err(303, 'SQL语句错误！');
   if($result->num_rows){
    for($i = 0; $i < $result->num_rows; ++$i){
     $class = $result->fetch_assoc();
     $sql .= ' cid=\''.$class['cid'].'\' or';
    }
    $sql = substr($sql, 0,strlen($sql) - 2);
   }else{
    return '暂无内容';
   }
   $sql .= "order by aid desc limit ".$limit;
  }
  echo $sql;
  if(!$result = $conn->query($sql)) return ERROR::err(304, 'SQL语句错误！1');
  if(!$result->num_rows) return '暂无内容';
  $listtitle = '';
  for($i = 0; $i < $result->num_rows; ++$i){
   $article = $result->fetch_assoc();
   $article['title'] = cutstr($article['title']);
   include(ABSPATH.TPLPATH.'listtitle.html');
   $listtitle .= $listtitle_html;
  }
  return $listtitle;
 }
 
 public function category($conn, $cid, $length = 31, $limit = TITLE_NUM){
  if(isset($cid)){
   $cid = trim($cid);
   $cid = (int) $cid;
  }else{
   $cid = 0;
  }
  $pagebody = '';
  $sql = "select * from `class` where `pcid`='".$cid."'";
  if(!$result = $conn->query($sql)) return ERROR::err(301, 'SQL语句错误！');
  $class = $this->sitepath($conn, $cid);
  if($result->num_rows){
   for($i = 0; $i < $result->num_rows; ++$i){
    $class = $result->fetch_assoc();
    $listtitle = $this->listtitle($conn, $class['cid'], $length, $limit, $class['isfinal']);
    include(ABSPATH.TPLPATH.'column.html');
    $pagebody .= $column_html;
   }
  }else{
   $listtitle = $this->listtitle($conn, $cid, $length, $limit, 1);
   include(ABSPATH.TPLPATH.'column.html');
   $pagebody = $column_html;
  }
  return $pagebody;
 }
 
 public function view($conn){
  if(isset($_GET['aid'])){
   $aid = trim($_GET['aid']);
   if(!$aid = (int) $aid) return ERROR::err('302', '非法访问！');
  }else{
   return ERROR::err('303', '非法访问！');
  }
  $sql = "select * from `article`, `content` where `article`.`aid`='".$aid."' and `content`.`aid`='".$aid."' order by content.page asc limit 1";
  if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！');
  $article = $result->fetch_assoc();
  $class = $this->sitepath($conn, $article['cid']);
  sitepath($article['title']);
  $paginations = '';
  for($i = 1; $i <= $article['page_num']; ++$i){
   include(ABSPATH.TPLPATH.'pagination.html');
   $paginations .= $pagination;
  }
  $comment = $this->comment($conn, $aid, $article['hascomment']);
  include(ABSPATH.TPLPATH.'article.html');
  return $item;
 }
 
 public function comment($conn, $aid, $hascomment, $start = 0, $limit = 20){
  if($hascomment){
   $sql = "select commentid, pid, aid, comment, uid, username, ip, posttime, ding from comment where aid=".$aid." order by commentid desc limit $start,$limit";
   if(!$result = $conn->query($sql)) return ERROR::err('sql');
   $comments = '';
   for($i = 1; $i <= $result->num_rows; ++$i){
    if(!$comment = $result->fetch_array()) return ERROR::err('读取结果集错误');
    $replies = '';
    if($comment['pid']) $replies = $this->comment_reply($conn, $comment['pid'], $aid, $hascomment);
    include(ABSPATH.TPLPATH.'comment_item.html');
    $comments .= $comment_item;
   }
  }else{
   $comment_num = 0;
   $comment_total = 0;
   $comment['pid'] = 0;
   $comment['aid'] = $aid;
   $comments = '暂无评论！';
  }
  $comment_login_html = '';
  if($GLOBALS['user']['uid'] == 1){
   include(ABSPATH.TPLPATH.'comment_login_html.html');
  }
  include(ABSPATH.TPLPATH.'comment.html');
  return $comment_html;
 }
  
 public function comment_reply($conn, $pid, $aid, $hascomment){
  $replies = '';
  static $floor = 1;
  $sql = "select commentid, pid, aid, comment, uid, username, ip, posttime, ding from comment where commentid={$pid} and aid=".$aid." limit 1";
  $result = $conn->query($sql);
  $reply = $result->fetch_array();
  if($reply['pid']){
   $replies = $this->comment_reply($conn, $reply['pid'], $aid, $hascomment);
   ++$floor;
  }else{
   $floor = 1;
  }
  include(ABSPATH.TPLPATH.'comment_reply.html');
  return $reply_item;
 }

 public function add_comment($conn){
  if(is_string($user = user::login($conn))) return $user;
  if(is_string($group = user::group($conn, $user['gid']))) return $group;
  if(!$group['iscomment']) return ERROR::err(305, $group['gname'].'不能发布评论！');
  if(isset($_POST['pid']) && isset($_POST['hascomment']) && isset($_POST['comment_content'])){
   $hascomment = trim($_POST['hascomment']);
   //if($hascomment !== '1' && $hascomment !== '0') return ERROR::err(305, '模板错误！');
   $aid = trim($_POST['aid']);
   if(!$aid = (int) $aid) return ERROR::err(305, '模板错误！');
   $pid = trim($_POST['pid']);
   $hascomment = (int) $hascomment;
   $pid = (int) $pid;
   $comment = filter($_POST['comment_content']);
   if($comment === '') return ERROR::err(305, '评论内容不能为空！');
  }else{
   return ERROR::err(305, '非法访问！');
  }
  if(!$hascomment){
   $sql = "update article set hascomment=1 where aid={$aid} limit 1";
   if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！1');
  }
  $uid = $user['uid'];
  $username = $user['username'];
  $ip = $_SERVER['REMOTE_ADDR'];
  $sql = "insert into comment (`pid`, `aid`, `comment`, `uid`, `username`, `ip`, `posttime`) values ('{$pid}', '{$aid}', '{$comment}', '{$uid}', '{$username}', '{$ip}', now())";
  if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！1');
 }
 
 public function sitepath($conn, $cid){
  if($cid){
   $sql = "select cid, classname, parentclass from `class` where `cid`='".$cid."' limit 1";
   if(!$result = $conn->query($sql)) return ERROR::err(305, 'SQL语句错误！');
   $class = $result->fetch_assoc();
   foreach(explode('|', $class['parentclass']) as $value){
    if($value){
     $arr = explode(',', $value);
     $url = "category.php?cid={$arr[0]}";
     sitepath($arr[1], $url);
    }
   }
   $url = "category.php?cid={$class['cid']}";
   sitepath($class['classname'], $url);
   return $class;
  }
 }
}
?>




