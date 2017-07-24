<?php

/***********************
        category类
***********************/
final class category{
 public $prompt = [];
  
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
    include(ABSPATH.'/template/class_html.html');
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
  if(!isset($_POST['name']) || !isset($_POST['alias']) || !isset($_POST['parent']) || !isset($_POST['attr']) || !isset($_POST['describtion'])) return prompt::info('非法访问！');
  if(!DIR_MODE){
   if(!isset($_POST['dir'])) return prompt::info('非法访问！');
   $dir = filter($db, $_POST['dir']);
  }
  if(($classname = filter($db, $_POST['name'])) == ''){
   return prompt::info('请填写栏目名称！');
  }
  if(preg_match('@[\|,]+@', $classname)) return prompt::info('栏目名称中不能包含|,等字符！');
  if(($alias = filter($db, $_POST['alias'])) == ''){
   return prompt::info('请填写栏目别名！');
  }
  if(!$this->filter_alias($alias)) return prompt::info($this->error);
  $attr = filter($db, $_POST['attr']);
  $describtion = filter($db, $_POST['describtion']);
  $parent = trim($_POST['parent']);
  $parent = (int) $parent;
  if($parent){
   $sql = "select classname, parentclass, dir, isfinal, alias from class where cid={$parent}";
   $result = $db->_query($sql, 1);
   $class = $result->fetch();
   $parentclass = $class['parentclass'].'|'.$parent.','.$class['classname'];
  }else{
   $parentclass = '';
  }
  if(DIR_MODE){
   $dir = (isset($class))?$class['dir']:RELPATH;
  }else{
   if($dir == ''){
    $dir = (isset($class))?$class['dir']:RELPATH;
   }else{
    if($dir == '/'){
     $dir = '';
    }elseif($dir == '.'){
     $dir = RELPATH;
    }else{
     $dir = '/'.trim($dir, '/');
     if(!file_exists(SITEPATH.$dir)) return prompt::info(SITEPATH.$dir.'不是有效的目录！');
    }
   }
  }
  $thedir = $dir . '/'.$alias;
  if(file_exists(SITEPATH.$thedir)) return prompt::info(SITEPATH.$thedir.'目录已经存在！');
  if($attr){
   $tname = $this->createtable($db);
  }else{
   $tname = '';
  }
  $sql = "insert into class (`pcid`, tname, `classname`, `alias`, `describtion`, `parentclass`, `isfinal`, `dir`) values ('{$parent}', '{$tname}', '{$classname}', '{$alias}', '{$describtion}', '{$parentclass}', '{$attr}', '{$dir}')";
  $db->insert($sql);
  if(isset($class) && $class['isfinal']){
   $sql = "update class set isfinal=0 where cid={$parent}";
   $db->update($sql);
   if($class['records_num'] === 0){
    for($i = 0; $i < $class['tables_num']; $i++){
     if($i === 0){
      $index = '';
     }else{
      $index = '_' . $i;
     }
     $sql = 'drop table '.$class['tname'] . $index;
     $db->execute($sql);
    }
   }else{
    $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`, tables_num, records_num) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), '{$class['alias']}', '{$class['tables_num']}', '{$class['records_num']}')";
    $db->insert($sql);
   }
   $this->deldir($class['dir']);
  }
  mkdir(SITEPATH.$thedir, 0777, true);
 }
 
 //编辑栏目
 public function edit($db){
  $cid = trim($_GET['cid']);
  $cid = (int) $cid;
  if(!$cid) return prompt::info('非法访问！');
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
  $dir = dirname($class['dir']);
  $class_dir = '';
  if(!DIR_MODE) include(ABSPATH.'/admin/template/class_dir.html');
  include(ABSPATH.'/admin/template/class_form.html');
  return $class_form;
 }
 
 //修改栏目
 public function modify($db){
  if(!isset($_GET['cid'])) return prompt::warning('非法访问！');
  $cid = trim($_GET['cid']);
  $cid = (int) $cid;
  if(!isset($_POST['name']) || !isset($_POST['alias']) || !isset($_POST['parent']) || !isset($_POST['attr']) || !isset($_POST['describtion'])) return prompt::info('非法访问！');
  if(!DIR_MODE){
   if(!isset($_POST['dir'])) return prompt::info('非法访问！');
   $dir = filter($db, $_POST['dir']);
  }
  if(($classname = filter($db, $_POST['name'])) == ''){
   return prompt::info('请填写栏目名称！');
  }
  if(preg_match('@[\|,]+@', $classname)) return prompt::info('栏目名称中不能包含|,等字符！');
  if(($alias = filter($db, $_POST['alias'])) == ''){
   return prompt::info('请填写栏目别名！');
  }
  if(!$this->filter_alias($alias)) return prompt::info($this->error);
  $attr = filter($db, $_POST['attr']);
  $describtion = filter($db, $_POST['describtion']);
  $sql = 'select pcid, classname, describtion, alias, isfinal, dir, tname from class where cid=\''.$cid.'\'';
  $rs = $db->_query($sql);
  $class = $rs->fetch();
  $fields = '';
  $parent = trim($_POST['parent']);
  $parent = (int) $parent;
  if($class['pcid'] == $parent){
   if(DIR_MODE){
    $dir = '';
    if($class['alias'] != $_POST['alias']){
     $dir = ($parent)?$class['dir']:RELPATH;
     if(!file_exists(SITEPATH.$dir)) return $this->prompt = $dir.'目录不存在，请检查是否误删除！';
     $thedir = $dir.'/'.$alias;
     if(file_exists(SITEPATH.$thedir)) return prompt::info(SITEPATH.$thedir.'目录已经存在！');
     mydir::del(SITEPATH, $class['dir'].'/'.$class['alias'], true);
     mkdir(SITEPATH.$thedir, 0777, true);
     $fields .= ', dir=\''.$dir.'\'';
     $fields .= ', alias=\''.$alias.'\'';
    }
    if($class['isfinal']){
     if($class['isfinal'] != $attr){
      $fields .= ', isfinal=\'0\'';
      mydir::del(SITEPATH, $class['dir'].'/'.$class['alias']);
      $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`, tables_num, records_num) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), '{$class['alias']}', '{$class['tables_num']}', '{$class['records_num']}')";
      $db->insert($sql);
     }
    }else{
     if($class['isfinal'] != $attr){
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $cid, false);
      $tname = $this->createtable($db);
      $fields .= ', tname=\''.$tname.'\'';
     }
    }
   }else{
    if($dir == ''){
     if($parent){
      $sql = "select classname, parentclass, dir, isfinal from class where cid={$parent}";
      $result = $db->_query($sql, 1);
      $tclass = $result->fetch();
     }
     $dir = (isset($tclass) && $tclass['dir'])?$tclass['dir']:RELPATH;
    }else{
     if($dir == '/'){
      $dir = '';
     }elseif($dir == '.'){
      $dir = RELPATH;
     }else{
      $dir = '/'.trim($dir, '/');
      if(!file_exists(SITEPATH.$dir)) return prompt::info(SITEPATH.$dir.'不是有效的目录！');
     }
    }
    $thedir = dirname($class['dir']);
    if($thedir == $dir){
     if($class['alias'] != $alias){
      $dir .= '/'.$alias;
      if(file_exists(SITEPATH.$dir)) return prompt::info($dir.'目录已经存在！');
      mkdir(SITEPATH.$dir, 0777, true);
      $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
      $db->insert($sql);
      $fields .= ', dir=\''.$dir.'\'';
      $fields .= ', alias=\''.$_POST['alias'].'\'';
     }
    }else{
     $dir .= '/'.$alias;
     if(file_exists(SITEPATH.$dir)) return prompt::info($dir.'目录已经存在！');
     mkdir(SITEPATH.$dir, 0777, true);
     $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
     $db->insert($sql);
     $fields .= ', dir=\''.$dir.'\'';
     $fields .= ', alias=\''.$alias.'\'';
    }
    if($class['isfinal']){
     if($class['isfinal'] != $attr){
      $fields .= ', isfinal=\'0\'';
      $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), {$class['alias']})";
      $db->insert($sql);
     }
    }else{
     if($class['isfinal'] != $attr){
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $cid, false);
      $tname = $this->createtable($db);
      $fields .= ', tname=\''.$tname.'\'';
     }
    }
   }
   if($class['classname'] != $classname){
    $fields .= ', classname=\''.$classname.'\'';
    $parentclass = '';
   }else{
    $parentclass = '';
   }
   $this->modifysub($db, $cid, $parentclass, $dir);
   if($class['describtion'] != $describtion) $fields .= ',describtion=\''.$describtion.'\'';
  }else{
   if($parent){
    $sql = "select classname, parentclass, dir, isfinal, tname from class where cid={$parent}";
    $result = $db->_query($sql);
    $tclass = $result->fetch();
    $parentclass = $tclass['parentclass'].'|'.$parent.','.$tclass['classname'];
   }else{
    $parentclass = '';
   }
   if(DIR_MODE){
    $dir = (isset($tclass))?$tclass['dir']:RELPATH;
   }else{
    if($dir == ''){
     $dir = (isset($tclass))?$tclass['dir']:RELPATH;
    }else{
     if($dir == '/'){
      $dir = '';
     }elseif($dir == '.'){
      $dir = RELPATH;
     }else{
      $dir = '/'.trim($dir, '/');
      if(!file_exists(SITEPATH.$dir)) return prompt::info(SITEPATH.$dir.'不是有效的目录！');
     }
    }
   }
   if(isset($tclass) && $tclass['isfinal']){
    $sql = "update class set isfinal=0 where cid={$parent}";
    $db->update($sql);
    $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`) values ('{$parent}', '{$tclass['classname']}', '{$tclass['tname']}', now(), {$tclass['alias']})";
    $db->insert($sql);
    if(DIR_MODE){
     if(!mydir::del(SITEPATH, $dir)) $this->prompt[] = mydir::$error;
    }
   }
   $fields .= ', pcid=\''.$parent.'\'';
   $fields .= ', parentclass=\''.$parentclass.'\'';
   if(DIR_MODE){
    mydir::del(SITEPATH, $class['dir'], true);
    if(!file_exists(SITEPATH.$dir)) return prompt::info($dir.'目录不存在，请检查是否误删除！');
    $dir .= '/'.$alias;
    if(file_exists(SITEPATH.$dir)) return prompt::info(SITEPATH.$dir.'目录已经存在！');
    mkdir(SITEPATH.$dir, 0777, true);
    if($class['isfinal']){
     if($class['isfinal'] != $attr){
      $fields .= ', isfinal=\'0\'';
      $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), {$class['alias']})";
      $db->insert($sql);
     }
    }else{
     if($class['isfinal'] == $attr){
      $this->modifysub($db, $cid, $dir, $parentclass.'|'.$cid.','.$classname);
     }else{
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $cid, false);
      $tname = $this->createtable($db);
      $fields .= ', tname=\''.$tname.'\'';
     }
    }
    $fields .= ', dir=\''.$dir.'\'';
    $fields .= ', alias=\''.$alias.'\'';
   }else{
    if($class['isfinal']){
     if($class['isfinal'] != $attr){
      $fields .= ', isfinal=\'0\'';
      $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), {$class['alias']})";
      $db->insert($sql);
     }
    }else{
     if($class['isfinal'] == $attr){
      $this->modifysub($db, $class['cid'], '', $parentclass.'|'.$cid.','.$name);
     }else{
      $fields .= ', isfinal=\'1\'';
      $this->_delete($db, $cid, false);
      $tname = $this->createtable($db);
      $fields .= ', tname=\''.$tname.'\'';
     }
    }
    $thedir = dirname($class['dir']);
    if($thedir == $dir){
     if($class['alias'] != $alias){
      $dir .= '/'.$alias;
      if(file_exists(SITEPATH.$dir)) return prompt::info($dir.'目录已经存在！');
      mkdir(SITEPATH.$dir, 0777, true);
      $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
      $db->insert($sql);
      $fields .= ', dir=\''.$dir.'\'';
      $fields .= ', alias=\''.$alias.'\'';
     }
    }else{
     $dir .= '/'.$alias;
     if(file_exists(SITEPATH.$dir)) return prompt::info($dir.'目录已经存在！');
     mkdir(SITEPATH.$dir, 0777, true);
     $sql = 'insert into class_data (cid, old_dir, thetime) values (\''.$cid.'\', \''.$class['dir'].'\', now())';
     $db->insert($sql);
     $fields .= ', dir=\''.$dir.'\'';
     $fields .= ', alias=\''.$alias.'\'';
    }
   }
  }
  $fields = substr($fields, 1);
  $sql = 'update class set'.$fields.' where cid=\''.$cid.'\'';
  $db->update($sql);
 }
 
 //删除栏目
 public function _delete($db, $cid, $isdel = true){
  $cid = trim($cid);
  $cid = (int) $cid;
  if(!$cid) return prompt::info('非法访问！');
  $sql = "select * from class where cid='{$cid}'";
  $result = $db->_query($sql);
  if(!$class = $result->fetch()) return prompt::info('栏目不存在！');
  if($class['isfinal']){
   $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`, tables_num, records_num) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), '{$class['alias']}', '{$class['tables_num']}', '{$class['records_num']}')";
   $db->insert($sql);
  }else{
   $pcid = $class['parentclass'].'|'.$cid.',%';
   $sql = "select * from class where parentclass like '{$pcid}'";
   $result = $db->_query($sql);
   $num_rows = $result->num_rows;
   for($i = 0; $i < $num_rows; ++$i){
    $subclass = $result->fetch();
    if($subclass['isfinal']){
     $sql = "insert into recyclebin (cid, name, tname, `thetime`, `alias`, tables_num, records_num) values ('{$parent}', '{$class['classname']}', '{$class['tname']}', now(), '{$class['alias']}', '{$class['tables_num']}', '{$class['records_num']}')";
     $db->insert($sql);
    }
    $sql = "delete from class where cid='{$subclass['cid']}'";
    $db->del($sql);
   }
  }
  if($isdel){
   $sql = "delete from class where cid='{$cid}'";
   $db->del($sql);
  }
  $this->deldir($class['dir'], $isdel);
 }
 
 //修改子栏目
 public function modifysub($db, $pcid, $parentclass, $dir = '', $bool = false){
  if($bool){
   $sql = 'select cid, classname, alias, isfinal from class where pcid=\''.$pcid.'\'';
   $rs = $db->_query($sql);
   if(DIR_MODE){
    for($i = 0; $i < $rs->num_rows; ++$i){
     $class = $rs->fetch();
     $dir .= '/'.$class['alias'];
     $sql = 'update class set dir=\''.$dir.'\',parentclass=\''.$parentclass.'\' where cid=\''.$class['cid'].'\'';
     $db->update($sql);
     mkdir(SITEPATH.$dir, 0777, true);
     if(!$class['isfinal']){
      $parentclass .= '|'.$class['cid'].','.$class['classname'];
      $this->modifysub($db, $class['cid'], $parentclass, $dir, true);
     }
    }
   }else{
    for($i = 0; $i < $rs->num_rows; ++$i){
     $class = $rs->fetch();
     $sql = 'update class set parentclass=\''.$parentclass.'\' where cid=\''.$class['cid'].'\'';
     $db->update($sql);
     if(!$class['isfinal']){
      $parentclass .= '|'.$class['cid'].','.$class['classname'];
      $this->modifysub($db, $class['cid'], $parentclass, $dir, true);
     }
    }
   }
  }else{
   if(!DIR_MODE) $dir = '';
   if($parentclass || $dir){
    $sql = 'select cid, alias,parentclass, isfinal from class where pcid=\''.$pcid.'\'';
    $rs = $db->_query($sql);
    for($i = 0; $i < $rs->num_rows; ++$i){
     $class = $rs->fetch();
     $fields = '';
     if($parentclass){
      $fields .= ', parentclass=\''.$parentclass.'\'';
      $parentclass .= '|'.$class['cid'].','.$class['classname'];
     }
     if($dir){
      $dir .= '/'.$class['alias'];
      $fields .= ', dir=\''.$dir.'\'';
      mkdir(SITEPATH.$dir, 0777, true);
     }
     $fields = substr($fields, 1);
     $sql = 'update class set'.$fields.' where cid=\''.$class['cid'].'\'';
     $db->update($sql);
     if(!$class['isfinal']) $this->modifysub($db, $class['cid'], $parentclass, $dir);
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
 public function shift($db, $id, $cid){
  $sql = 'select * from class where cid=\''.$cid.'\'';
  $rs = $db->_query($sql);
  $class = $rs->fetch();
  if($class['isfinal']){
   $sql = 'update class set isfinal=0 where cid=\''.$cid.'\'';
   $db->update($sql);
   if(DIR_MODE) if(!mydir::del(SITEPATH, $dir)) $this->prompt[] = mydir::$error;
  }
  $sql = 'select * from recyclebin where id=\''.$id.'\'';
  $rs = $db->_query($sql);
  $rb = $rs->fetch();
  $parentclass = $class['parentclass'].'|'.$class['cid'].','.$class['classname'];
  if($rb['name']){
   $dir = $class['dir'].'/'.$rb['alias'];
   $sql = "insert into class (`pcid`, `classname`, `alias`, `parentclass`, `isfinal`, `dir`) values ('{$cid}', '{$rb['name']}', '{$rb['alias']}', '{$parentclass}', '1', '{$dir}')";
   $db->insert($sql);
   $insertid = $db->insertid;
   if(!DIR_MODE){
    $sql = 'update class_data set cid=\''.$insertid.'\' where cid=\''.$rb['cid'].'\'';
    $db->update($sql);
   }
   $sql = 'select count(*) as thenum from article where cid=\''.$rb['cid'].'\'';
   $rs = $db->_query($sql);
   $theclass = $rs->fetch();
   if($theclass['thenum']){
    set_time_limit($theclass['thenum'] + 30);
    $this->cshift($db, $rb['cid'], $insertid);
   }
  }else{
   $sql = 'select alias from class where cid=\''.$rb['cid'].'\'';
   $rs = $db->_query($sql);
   $theclass = $rs->fetch();
   $dir = $class['dir'].'/'.$theclass['alias'];
   $sql = "update class set pcid='{$cid}', parentclass='{$parentclass}', dir='{$dir}' where cid='{$rb['cid']}'";
   $db->update($sql);
  }
 }
 
 //
 public function repair(){
  
 }
 
 //
 public function clear($db){
  
 }
 
 //
 public function seerb($db, $start = 0, $limit = 30){
  $sql = 'select count(*) as num from recycle';
  $rs = $db->_query($sql);
  $theclass = $rs->fetch();
  $sql = 'select * from recyclebinorder by id';
  $limit = $start.','.$limit;
  $rs = $db->_query($sql, $limit);
  $theclass = $rs->fetch();
 }
 
 //
 public function cshift($db, $cid, $tcid, $limit = 0){
  $sql = 'update article set cid=\''.$tcid.'\' where cid=\''.$cid.'\' order by aid';
  $db->update($sql, $limit);
 }
 
 //
 public function createtable($db){
  $sql = 'select t_index from system';
  $rs = $db->_query($sql, 1);
  $system = $rs->fetch();
  $tname = 'news';
  if($system['t_index']){
   $tname .= $system['t_index'];
   $sql = 'create table '.$tname.'  like news';
   $db->execute($sql);
  }
  ++$system['t_index'];
  $sql = 'update system set t_index=\''.$system['t_index'].'\'';
  $db->update($sql, 1);
  return $tname;
 }
 
 //
 final public function delfclass($db, $class){
 }
 
 //
 final public function deldir($dir, $isdel = true){
  set_time_limit(6000);echo $dir;
  if(DIR_MODE){
   if(!mydir::del(SITEPATH, $dir, $isdel)) $this->prompt[] = mydir::$error;
  }else{
   if(!$arr = scandir(SITEPATH.$dir)) $this->prompt[] = $dir.'文件夹没有读权限！';
   foreach($arr as $val){
    if(is_dir(SITEPATH.$dir.'/'.$val)){
     if($val != '.' && $val != '..' && preg_match('@^[0-9]{4}$@', $val)){
      if(!mydir::del(SITEPATH.$dir, '/'.$val, true)) $this->prompt[] = mydir::$error;
     }
    }else{
     if(!unlink(SITEPATH.$dir.'/'.$val)) $this->prompt[] = $dir.'/'.$val.'文件没有写权限！';
    }
   }
   if($isdel) @rmdir(SITEPATH.$dir);
  }
 }
}
?>
