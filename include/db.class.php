<?php

/***********************
        db类
***********************/
class db{
 public $conn;
 public $cerrno;
 public $cerror;
 public $insertid;
  //类构造函数
 public function __construct($host, $user, $pwd, $dbname){
  $conn = new mysqli($host, $user, $pwd, $dbname);
  $this->conn = $conn;
  $this->insertid = $conn->insert_id;
  $this->cerrno = $conn->connect_errno;
  $this->cerror = $conn->connect_error;
 }
 
 public function selectdb($dbname){
  if($this->conn->select_db($dbname)) return error::err('选择数据库出错！');
 }
  
 public function _query($sql, $limit = ''){
  if($limit) $sql .= ' limit '.$limit;
  if(!$result = $this->conn->query($sql)) return error::err('SQL语句错误！');
  return new myresult($result);
 }
 
 public function pagination(){
  
 }
 
 public function insert($sql){
  if(!$this->conn->query($sql)) return error::err('SQL语句错误！');
 }
 
 public function update($sql, $limit = 0){
  if($limit) $sql .= ' limit '.$limit;
  if(!$this->conn->query($sql)) return error::err('SQL语句错误！');
 }
 
 public function del(){ 
  if(!$this->conn->query($sql)) return error::err('SQL语句错误！');
 }
 
 public function _autocommit(){
  
 }
 
 
 public function _commit(){
  
 }
 

 public function _rollback(){
  
 }
 
 public function setcharset(){
  
 }
 
 
 public function getsversion(){
  
 }
 
 public function _close(){
  $this->conn->close();
 }
 
 /*public function (){
  
 }*/
}



/***********************
        myresult类
***********************/
class myresult extends result{
 public $num_rows;
 public $result;
  //类构造函数
 public function __construct($result){
  $this->result = $redult;
  $this->num_rows = $redult->num_rows;
 }
 
 public function fetch(){
  return $this->result->fetch_assoc();
 }
  
 public function free($sql){
  $this->result->free();
 }
 
 /*public function (){
  
 }*/
}
?>