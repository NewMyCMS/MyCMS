<?php

/***********************
        result类
***********************/
final class page{
 public $a;
  
  //类构造函数
 public function __construct(){
  
 }
  
 public function listtitle($db, $cid, $length, $limit, $parentclass){
  $sql = 'select * from `article` where';
  if($parentclass){
   $sql1= 'select `cid` from `class` where isfinal=1 and parentclass like \''.$parentclass.'|'.$cid.',%\'';
   $result = $db->_query($sql1);
   if($result->num_rows){
    for($i = 0; $i < $result->num_rows; ++$i){
     $class = $result->fetch();
     $sql .= 'or cid=\''.$class['cid'].'\' ';
    }
    $sql = substr($sql, 2);
   }else{
    return '暂无内容';
   }
   $sql .= 'order by aid desc';
  }else{
   $sql .= ' `cid`=\''.$cid.'\' order by aid desc';
  }
  $result = $db->_query($sql, $limit);
  if(!$result->num_rows) return '暂无内容';
  $listtitle = '';
  for($i = 0; $i < $result->num_rows; ++$i){
   $article = $result->fetch();
   $article['title'] = cutstr($article['title']);
   include(ABSPATH.TPLPATH.'listtitle.html');
   $listtitle .= $listtitle_html;
  }
  return $listtitle;
 }
 
 public function category($db, $cid, $length = 31, $limit = TITLE_NUM){
  $cid = trim($cid);
  $cid = (int) $cid;
  $pagebody = '';
  $sql = "select * from `class` where `pcid`='".$cid."'";
  $result = $db->_query($sql);
  $class = $this->sitepath($db, $cid);
  if($result->num_rows){
   for($i = 0; $i < $result->num_rows; ++$i){
    $class = $result->fetch();
    $listtitle = $this->listtitle($db, $class['cid'], $length, $limit, $class['parentclass']);
    include(ABSPATH.TPLPATH.'column.html');
    $pagebody .= $column_html;
   }
  }else{
   $listtitle = $this->listtitle($db, $cid, $length, $limit, 1);
   include(ABSPATH.TPLPATH.'column.html');
   $pagebody = $column_html;
  }
  return $pagebody;
 }
 
 public function view($db){
  if(isset($_GET['aid'])){
   $aid = trim($_GET['aid']);
   if(!$aid = (int) $aid) return prompt::error('302', '非法访问！');
  }else{
   return prompt::error('303', '非法访问！');
  }
  $sql = "select * from `article`, `content` where `article`.`aid`='".$aid."' and `content`.`aid`='".$aid."' order by content.page asc";
  $result = $db->_query($sql, 1);
  $article = $result->fetch();
  $class = $this->sitepath($db, $article['cid']);
  sitepath($article['title']);
  $paginations = '';
  for($i = 1; $i <= $article['page_num']; ++$i){
   include(ABSPATH.TPLPATH.'pagination.html');
   $paginations .= $pagination;
  }
  $comment = $this->comment($db, $aid, $article['hascomment']);
  include(ABSPATH.TPLPATH.'article.html');
  return $item;
 }
 
 public function comment($db, $aid, $hascomment, $start = 0, $limit = 20){
  if($hascomment){
   $sql = "select commentid, pid, aid, comment, uid, username, ip, posttime, ding from comment where aid=".$aid." order by commentid desc";
   $result = $db->_query($sql, $start.','.$limit);
   $comments = '';
   for($i = 1; $i <= $result->num_rows; ++$i){
    $comment = $result->fetch_array();
    $replies = '';
    if($comment['pid']) $replies = $this->comment_reply($db, $comment['pid'], $aid, $hascomment);
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
  
 public function comment_reply($db, $pid, $aid, $hascomment){
  $replies = '';
  static $floor = 1;
  $sql = "select commentid, pid, aid, comment, uid, username, ip, posttime, ding from comment where commentid={$pid} and aid=".$aid;
  $result = $db->_query($sql,1);
  $reply = $result->fetch_array();
  if($reply['pid']){
   $replies = $this->comment_reply($db, $reply['pid'], $aid, $hascomment);
   ++$floor;
  }else{
   $floor = 1;
  }
  include(ABSPATH.TPLPATH.'comment_reply.html');
  return $reply_item;
 }

 public function add_comment($db){
  if(is_string($user = user::login($db))) return $user;
  if(is_string($group = user::group($db, $user['gid']))) return $group;
  if(!$group['iscomment']) return prompt::error(305, $group['gname'].'不能发布评论！');
  if(isset($_POST['pid']) && isset($_POST['hascomment']) && isset($_POST['comment_content'])){
   $hascomment = trim($_POST['hascomment']);
   //if($hascomment !== '1' && $hascomment !== '0') return prompt::error(305, '模板错误！');
   $aid = trim($_POST['aid']);
   if(!$aid = (int) $aid) return prompt::error(305, '模板错误！');
   $pid = trim($_POST['pid']);
   $hascomment = (int) $hascomment;
   $pid = (int) $pid;
   $comment = filter($_POST['comment_content']);
   if($comment === '') return prompt::error(305, '评论内容不能为空！');
  }else{
   return prompt::error(305, '非法访问！');
  }
  if(!$hascomment){
   $sql = "update article set hascomment=1 where aid={$aid}";
   $db->update($sql, 1);
  }
  $uid = $user['uid'];
  $username = $user['username'];
  $ip = $_SERVER['REMOTE_ADDR'];
  $sql = "insert into comment (`pid`, `aid`, `comment`, `uid`, `username`, `ip`, `posttime`) values ('{$pid}', '{$aid}', '{$comment}', '{$uid}', '{$username}', '{$ip}', now())";
  $db->insert($sql);
 }
 
 public function sitepath($db, $cid){
  if($cid){
   $sql = "select cid, classname, parentclass from `class` where `cid`='".$cid."'";
   $result = $db->_query($sql);
   $class = $result->fetch();
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




