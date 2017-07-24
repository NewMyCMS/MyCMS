<?php

/***********************
        mydb类
***********************/
final class db{
 public $conn;
 public $cerrno;
 public $cerror;
 public $error;
 public $insertid;
  //类构造函数
 final public function __construct($host, $user, $pwd, $dbname){
  $conn = new mysqli($host, $user, $pwd, $dbname);
  $this->conn = $conn;
  $this->cerrno = $conn->connect_errno;
  $this->cerror = $conn->connect_error;
 }
 
 final public function selectdb($dbname){
  if($this->conn->select_db($dbname)){
   $this->error = '选择数据库出错！';
   return false;
  }
  return true;
 }
 
 
 final public function execute($sql){
  if(!$this->conn->query($sql)){
   $this->error = 'SQL语句错误！';
   return false;
  }
  return true;
 }
 
 final public function exists_table($tname, $dbname = ''){
  $subsql = '';
  if($dbname) $subsql = ' from '.$dbname;
  $sql = 'show tables'.$subsql.' like \''.$tname.'\'';
  $rs = $this->conn->query($sql);
  return $rs->num_rows;
 }
  
 final public function _query($sql, $limit = ''){
  if($limit) $sql .= ' limit '.$limit;
  if(!$result = $this->conn->query($sql)){
   $this->error = 'SQL语句错误！';
   return false;
  }
  return new result($result);
 }
 
 final public function pagination($table, $fields, $condition, $id, $start, $limit = TITLE_LIMIT){
  $index = intval($start/RECORD_SIZE);
  $start -= $index * RECORD_SIZE;
  if($index) $table .= '_'.$index;
  $sql = 'select '.$id.' from '.$table.$condition.' order by '.$id.' limit '.$start.','.$limit;
  $rs = $this->conn->query($sql);
  $idstr = '';
  while($arr = $rs->fetch_assoc()){
   $idstr .= ','.$arr[$id];
  }
  $idstr = substr($idstr, 1);
  $sql = 'select '.$fields.' from '.$table.' where '.$id.' in('.$idstr.')';
  $rs = $this->conn->query($sql);
  return new result($rs);
 }
 
 final public function insert($sql){
  if(!$this->conn->query($sql)){
   $this->error = 'SQL语句错误！';
   return false;
  }
  $this->insertid = $this->conn->insert_id;
  return true;
 }
 
 final public function update($sql, $limit = 0){
  if($limit) $sql .= ' limit '.$limit;
  if(!$this->conn->query($sql)){
   $this->error = 'SQL语句错误！';
   return false;
  }
  return true;
 }
 
 final public function del($sql, $limit = 0){ 
  if(!$this->conn->query($sql)){
   $this->error = 'SQL语句错误！';
   return false;
  }
  return true;
 }
 
 final public function escape($str){
  return $this->conn->real_escape_string($str);
 }
 
 final public function _autocommit(){
  
 }
 
 
 final public function _commit(){
  
 }
 

 final public function _rollback(){
  
 }
 
 final public function setcharset(){
  
 }
 
 
 final public function getversion(){
  
 }
 
 final public function _close(){
  $this->conn->close();
 }
 
 /*final public function (){
  
 }*/
}



/***********************
        myresult类
***********************/
final class result{
 public $num_rows;
 public $result;
 //public $error;
 
 //类构造函数
 final public function __construct($result){
  $this->result = $result;
  $this->num_rows = $result->num_rows;
 }
 
 final public function fetch(){
  return $this->result->fetch_assoc();
 }
  
 final public function free(){
  $this->result->free();
 }
 
 /*final public function (){
  
 }*/
}
?>
