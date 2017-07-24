<?php

/***********************
        result类
***********************/
final class category{
 public $error = '';
  
  //类构造函数
 public function __construct(){
  
 }
 
 //列举栏目
 public function _list($db, $cid, $nbsp = ''){
  if(isset($cid)){
   $cid = trim($cid);
   $cid = (int) $cid;
  }else{
   $cid = 0;
  }
  $classes = '';
  $sql = "select * from `class` where `pcid`='".$cid."'";
  $result = $db->_query($sql);
  if($result->num_rows){
   for($i = 0; $i < $result->num_rows; ++$i){
    $class = $result->fetch();
    $class['classname'] = $nbsp . '|-' . '<a href="/category.php?cid='.$class['cid'].'" rel="external">'.$class['classname'].'</a>';
    include(ABSPATH.'/admin/template/class_html.html');
    $classes .= $class_html;
    $_nbsp = $nbsp . '&nbsp;&nbsp;&nbsp;';
    $classes .= $this->_list($db, $class['cid'], $_nbsp);
   }
  }
  return $classes;
 }
  
 //构造列表表单选项
 public function createoption($db, $cid, $curr = 0, $pcid = 0, $nbsp = ''){
  if(isset($cid)){
   $cid = trim($cid);
   $cid = (int) $cid;
  }else{
   $cid = 0;
  }
  if(!$cid){
   $options = '<option value="0">无</option>';
  }else{
   $options = '';
  }
  $sql = "select * from `class` where `pcid`='".$cid."'";
  $result = $db->_query($sql);
  if($result->num_rows){
   for($i = 0; $i < $result->num_rows; ++$i){
    $class = $result->fetch();
    if($pcid == $class['cid']){
     $selected = 'selected';
    }else{
     $selected = '';
    }
    if($curr != $class['cid']){
    $options .= '<option value="'.$class['cid'].'" '.$selected.'>'.$nbsp . '|-' .$class['classname'].'</option>';
    $_nbsp = $nbsp . '&nbsp;&nbsp;&nbsp;';
    $options .= $this->createoption($db, $class['cid'], $curr, $pcid, $_nbsp);
    }
   }
  }
  return $options;
 }
 
 //添加栏目
 public function add($db){
  if(!isset($_POST['name']) && $classname = trim($_POST['name']) == ''){
   return ERROR::err('请填写栏目名称！');
  }
  if(!isset($_POST['alias']) && $alias = trim($_POST['alias']) == ''){
   return ERROR::err('请填写栏目别名！');
  }
  if(!$this->filter_alias($_POST['alias'])) return $this->error;
  if(DIR_MODE){
   $absdir = ABSPATH;
   $dir = '';
  }else{
   $dir = filter($_POST['dir']);
   $absdir = SITEPATH;
   if($dir == ''){
   }else{
    if($dir == '/'){
     $dir = '';
    }elseif($dir = '.'){
     $dir = RELPATH;
    }else{
     //$dir = trim($dir);
     $dir = '/'.trim($dir, '/');
     if(!file_exists($absdir.$dir)) return ERROR::err("'".$absdir.$dir."'不是有效的目录！");
    }
   }
  }
  $parent = trim($_POST['parent']);
  $parent = (int) $parent;
  if($parent){
   $sql = "select classname, parentclass, dir, isfinal from class where cid={$parent}";
   $result = $db->_query($sql, 1);
   $class = $result->fetch();
   if(DIR_MODE) $dir = $class['dir'];
   //if(!$class['parentclass']) $class['parentclass'] = '|';
   $parentclass = '|'.$class['parentclass'].$_POST['parent'].','.$class['classname'];
  }else{
   $parentclass = '';
  }
  $thedir = $dir . '/'.$_POST['alias'];
  if(file_exists($absdir.$thedir)) return ERROR::err("'".$absdir.$thedir."'目录已经存在！");
  $sql = "insert into class (`pcid`, `classname`, `alias`, `describtion`, `parentclass`, `isfinal`, `dir`) values ('{$parent}', '{$_POST['name']}', '{$_POST['alias']}', '{$_POST['describtion']}', '{$parentclass}', '{$_POST['attr']}', '{$thedir}')";
  $db->insert($sql);
  if(isset($class) && $class['isfinal']){
   $sql = "update class set isfinal=0 where cid={$parent}";
   $db->update($sql, 1);
   $sql = "insert into recyclebin (`cid`, `name`, `thetime`) values ('{$parent}', '{$class['classname']}', now())";
   $db->insert($sql);
   if(DIR_MODE){
    if(!mydir::del($absdir, $dir)) return error::err(mydir::$error);
   }
  }
  mkdir($absdir.$thedir);
 }
 
 //编辑栏目
 public function edit($db){
  $cid = trim($_GET['cid']);
  $cid = (int) $cid;
  if(!$cid) return ERROR::err('非法访问！');
  $sql = 'select * from class where cid=\''.$cid.'\'';
  $result = $db->_query($sql);
  $class = $result->fetch();
  $options = $this->createoption($db, 0, $cid, $class['pcid']);
  if($class['isfinal']){
   $yes = 'checked';
   $not = '';
  }else{
   $yes = '';
   $not = 'checked';
  }
  $class_dir = '';
  if(!DIR_MODE) include(ABSPATH.'/admin/template/class_dir.html');
  include(ABSPATH.'/admin/template/class_form.html');
  return $class_form;
 }
 
 //修改栏目
 public function modify($db){
  if(!isset($_GET['cid'])) return ERROR::err('非法访问！');
  $cid = trim($_GET['cid']);
  $cid = (int) $cid;
  if(!$cid) return ERROR::err('非法访问！');
  if($_POST['name'] == ''){
   return ERROR::err('请填写栏目名称！');
  }
  if($_POST['alias'] == ''){
   return ERROR::err('请填写栏目别名！');
  }
  if(DIR_MODE){
   $absdir = ABSPATH;
   $dir = '';
  }else{
   $dir = filter($_POST['dir']);
   $absdir = SITEPATH;
   if($dir == ''){
    $dir = RELPATH;
   }else{
    if($dir == '/'){
     $dir = '';
    }else{
     $dir = trim($dir);
     $dir = '/'.trim($dir, '/');
     if(!file_exists($absdir.$dir)) return ERROR::err("'".$absdir.$dir."'不是有效的目录！");
    }
   }
  }
  $sql = 'select pcid, classname, describtion, alias, isfinal, dir from class where cid=\''.$cid.'\'';
  $rs = $db->_query($sql, 1);
  $class = $rs->fetch();
  $parent = trim($_POST['parent']);
  $parent = (int) $parent;
  if($class['pcid'] == $parent){
   $fields = '';
   if(DIR_MODE){
    //$dir = '';
    if($class['alias'] != $_POST['alias']){
     $dir = dirname($class['dir']);
     if(!file_exists(ABSPATH.$dir)) return error::err($dir.'目录不存在，请检查是否误删除！');
     $dir .= '/'.$_POST['alias'];
     if(file_exists($absdir.$dir)) return error::err($absdir.$dir.'目录已经存在！');
     mydir::del($absdir, $class['dir'], true);
     mkdir($absdir.$dir);
     $fields .= ', dir=\''.$dir.'\'';
     $fields .= ', alias=\''.$_POST['alias'].'\'';
    }
    if($class['isfinal']){
     if($class['isfinal'] != $_POST['attr']){
      $fields .= ', isfinal=\'0\'';
      mydir::del($absdir, $class['dir']);
     }
    }else{
     if($class['isfinal'] == $_POST['attr']){
      $this->modifysub($db, $class['cid'], $dir);
     }else{
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $class['cid'], false);
     }
    }
   }else{
    $thedir = dirname($class['dir']);
    if($thedir == $dir){
     if($class['alias'] != $_POST['alias']){
      $dir .= '/'.$_POST['alias'];
      if(file_exists(SITEPATH.$dir)) return error::err($dir.'目录已经存在！');
      mkdir(SITEPATH.$dir);
      $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
      $db->insert($sql);
      $fields .= ', dir=\''.$dir.'\'';
      $fields .= ', alias=\''.$_POST['alias'].'\'';
     }
    }else{
     $dir .= '/'.$_POST['alias'];
     if(file_exists(SITEPATH.$dir)) return error::err($dir.'目录已经存在！');
     mkdir(SITEPATH.$dir);
     $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
     $db->insert($sql);
     $fields .= ', dir=\''.$dir.'\'';
     $fields .= ', alias=\''.$_POST['alias'].'\'';
    }
    if($class['isfinal']){
     if($class['isfinal'] != $_POST['attr']){
      $fields .= ', isfinal=\'0\'';
     }
    }else{
     if($class['isfinal'] != $_POST['attr']){
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $class['cid'], false);
     }
    }
   }
   if($class['classname'] != $_POST['name']) $fields .= ', classname=\''.$_POST['name'].'\'';
   if($class['describtion'] != $_POST['describtion']) $fields .= ',describtion=\''.$_POST['describtion'].'\'';
  }else{
   if($parent){
    $sql = "select classname, parentclass, dir, isfinal from class where cid={$parent}";
    $result = $db->_query($sql);
    $tclass = $result->fetch();
    if(DIR_MODE) $dir = $tclass['dir'];
    //if(!$tclass['parentclass']) $tclass['parentclass'] = '|';
    $parentclass = '|'.$tclass['parentclass'].$_POST['parent'].','.$tclass['classname'];
   }else{
    $parentclass = '';
    //$tclass['classname'] = '|';
   }
   if(isset($tclass) && $tclass['isfinal']){
    $sql = "update class set isfinal=0 where cid={$parent}";
    $db->update($sql);
    $sql = "insert into recyclebin (`cid`, `name`, `thetime`) values ('{$parent}', '{$class['classname']}', now())";
    $db->insert($sql);
    if(DIR_MODE){
     if(!mydir::del($absdir, $dir)) return error::err(mydir::$error);
    }
   }
   if(DIR_MODE){
    mydir::del(ABSPATH, $class['dir'], true);
    //$dir = $tclass['dir'];
    if(!file_exists(ABSPATH.$dir)) return error::err($dir.'目录不存在，请检查是否误删除！');
    $dir .= '/'.$_POST['alias'];
    if(file_exists(ABSPATH.$dir)) return error::err(ABSPATH.$dir.'目录已经存在！');
    mkdir(ABSPATH.$dir);
    if($class['isfinal']){
     if($class['isfinal'] != $_POST['attr']){
      $fields .= ', isfinal=\'0\'';
     }
    }else{
     if($class['isfinal'] == $_POST['attr']){
      $this->modifysub($db, $class['cid'], $dir, '|'.$tclass['parentclass'].$cid.','.$_POST['name']);
     }else{
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $class['cid'], false);
     }
    }
    $fields .= ', dir=\''.$dir.'\'';
    $fields .= ', alias=\''.$_POST['alias'].'\'';
   }else{
    if($class['isfinal']){
     if($class['isfinal'] != $_POST['attr']){
      $fields .= ', isfinal=\'0\'';
     }
    }else{
     if($class['isfinal'] == $_POST['attr']){
      $this->modifysub($db, $class['cid'], '', '|'.$tclass['parentclass'].$cid.','.$_POST['name']);
     }else{
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $class['cid'], false);
     }
    }
    $thedir = dirname($class['dir']);
    if($thedir == $dir){
     if($class['alias'] != $_POST['alias']){
      $dir .= '/'.$_POST['alias'];
      if(file_exists(SITEPATH.$dir)) return error::err($dir.'目录已经存在！');
      mkdir(SITEPATH.$dir);
      $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
      $db->insert($sql);
      $fields .= ', dir=\''.$dir.'\'';
      $fields .= ', alias=\''.$_POST['alias'].'\'';
     }
    }else{
     $dir .= '/'.$_POST['alias'];
     if(file_exists(SITEPATH.$dir)) return error::err($dir.'目录已经存在！');
     mkdir($dir);
     $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
     $db->insert($sql);
     $fields .= ', dir=\''.$dir.'\'';
     $fields .= ', alias=\''.$_POST['alias'].'\'';
    }
   }
  }
  $fields = substr($fields, 1);
  $sql = 'update class set'.$fields;
  $db->update($sql);
 }
 
 //删除栏目
 public function _delete($db, $cid, $isdel = true)
  $cid = trim($cid);
  $cid = (int) $cid;
  if(!$cid) return ERROR::err('非法访问！');
  $sql = "select * from class where cid='{$cid}'";
  $result = $db->_query($sql, 1);
  if(!$class = $result->fetch()) return ERROR::err('栏目不存在！');
  if($class['isfinal']){
   $sql = "insert into recyclebin (`cid`, `name`, `thetime`) values ('{$class['cid']}', '{$class['classname']}', now())";
   $db->insert($sql);
  }else{
   $pcid = '%|'.$cid.',%';
   $sql = "select * from class where parentclass like '{$pcid}'";
   $result = $db->_query($sql);
   $num_rows = $result->num_rows;
   for($i = 0; $i < $num_rows; ++$i){
    $subclass = $result->fetch();
    if($subclass['isfinal']){
     $sql = "insert into recyclebin (`cid`, `name`, `thetime`) values ('{$subclass['cid']}', '{$subclass['classname']}', now())";
     $db->insert($sql);
    }
  echo $db->error,$sql;
    $sql = "delete from class where cid='{$subclass['cid']}'";
    $db->del($sql, 1);
   }
  }
  $bool = false;
  if($isdel){
   $sql = "delete from class where cid='{$cid}'";
   $db->del($sql, 1);
   $bool = true;
  }
  if(DIR_MODE){
   if(!mydir::del(ABSPATH, $class['dir'], $bool)) return error::err(mydir::$error);
  }
 }
 
 //修改子栏目
 public function modifysub($db, $pcid, $dir, $parentclass = ''){
  if($parentclass){
   if(DIR_MODE){
    $sql = 'select cid, classname, alias, isfinal from class where pcid=\''.$pcid.'\'';
    $rs = $db->_query($sql);
    for($i = 0; $i < $rs->num_rows; ++$i){
     $class = $rs->fetch();
     $dir .= '/'.$class['alias'];
     $sql = 'update class set dir=\''.$dir.'\',parentclass=\''.$parentclass.'\' where cid=\''.$class['cid'].'\'';
     $db->update($sql);
     mkdir(ABSPATH.$dir);
     if(!$class['isfinal']){
      $parentclass .= '|'.$class['cid'].','.$class['classname'];
      $this->modifysub($db, $class['cid'], $dir, $parentclass);
     }
    }
   }else{
    $sql = 'select cid, classname, isfinal from class where pcid=\''.$pcid.'\'';
    $rs = $db->_query($sql);
    for($i = 0; $i < $rs->num_rows; ++$i){
     $class = $rs->fetch();
     $sql = 'update class set parentclass=\''.$parentclass.'\' where cid=\''.$class['cid'].'\'';
     $db->update($sql);
     if(!$class['isfinal']){
      $parentclass .= '|'.$class['cid'].','.$class['classname'];
      $this->modifysub($db, $class['cid'], $dir, $parentclass);
     }
    }
   }
  }else{
   if(!$dir) return;
   if(DIR_MODE){
    $sql = 'select cid, alias, isfinal from class where pcid=\''.$pcid.'\'';
    $rs = $db->_query($sql);
    for($i = 0; $i < $rs->num_rows; ++$i){
     $class = $rs->fetch();
     $dir .= '/'.$class['alias'];
     $sql = 'update class set dir=\''.$dir.'\' where cid=\''.$class['cid'].'\'';
     $db->update($sql);
     mkdir(ABSPATH.$dir);
     if(!$class['isfinal']) $this->modifysub($db, $class['cid'], $dir);
    }
   }
  }
 }
 
 public function filter_alias($filename){
  if($filename == '.' || $filename == '..'){
   $this->error = '栏目别名不能命名为.或..！';
   return false;
  }
  if(preg_match('/[\/\\\:\*\?<>\|"]+/', $filename)){
   $this->error = '栏目别名中不能包含/\:*?<>|"等ANSI字符！';
   return false;
  }
  return true;  
 }
 
 //
 public function shift($db, $cid, $pcid){
 }
 
 //
 /*public function (){
  
 }*/
}
?>
