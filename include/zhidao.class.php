<?php

/***********************
        result类
***********************/
final class user{
 public $;
  
  //类构造函数
 public function __construct(){
  
 }
 
 public function comment($conn, $aid, $hascomment, $start = 0, $limit = 20){
  if($hascomment){
   $sql = "select commentid, pid, aid, comment, uid, username, ip, posttime, hasreply, ding from comment where pid=0 and aid=".$aid." order by commentid desc limit $start,$limit";
   if(!$result = $conn->query($sql)) return ERROR::err('sql');
   $comments = '';
   for($i = 1; $i <= $result->num_rows; ++$i){
    if(!$comment = $result->fetch_array()) return ERROR::err('读取结果集错误');
    $replies = '';
    if($comment['hasreply']){
     $replies = $this->comment_reply($conn, $comment['commentid'], $aid);
    }
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
  include(ABSPATH.TPLPATH.'comment.html');
  return $comment_html;
 }
  
 public function comment_reply($conn, $pid, $aid){
  if(isset($_GET['aid'])){
   $aid = (int) $_GET['aid'];
  }else{
   $aid = 0;
  }
  $sql = "select commentid, pid, aid, comment, uid, username, ip, posttime, hasreply, ding from comment where pid={$pid} and aid=".$aid." order by commentid asc";
  $result = $conn->query($sql);
  $replies = '';
  for($i = 1; $i <= $result->num_rows; ++$i){
   $reply = $result->fetch_array();
   if($reply['hasreply']){
    $this->comment_retply($conn, $reply['comment_id'], $aid);
   }
   include(ABSPATH.TPLPATH.'comment_reply.html');
   $replies .= $reply_item;
  }
 }
}
?>