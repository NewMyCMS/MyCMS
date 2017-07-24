<?php
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}
require('load.php');

echo dirname('/hh');

//
/*$startime=get_microtime();
for($i=0;$i<100000;$i++){
 $sql = "insert into user (username,password,gid) values ('test{$i}','test',3)";
 $db->insert($sql);
}
echo '<br>',get_microtime()-$startime,'<br>';*/
?>