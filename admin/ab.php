<?php
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}
require('../load.php');
$c = new category();
$c->filter_alias('fgvbb');
//echo 'fg\ vbb';
echo $c->error,RELPATH;
/*$startime=get_microtime();
for($i=0;$i<100000;$i++){
 $c->filter('fg/ vbb'); 
}
echo '<br>',get_microtime()-$startime,'<br>';
$startime=get_microtime();
for($i=0;$i<100000;$i++){
 $c->filter('fg/ vbb'); 
}
echo '<br>',get_microtime()-$startime,'<br>';
$startime=get_microtime();
for($i=0;$i<100000;$i++){
 $c->filter_alias('fg/ vbb');
}
echo '<br>',get_microtime()-$startime,'<br>';
$startime=get_microtime();
for($i=0;$i<100000;$i++){
 $c->filter_alias('fg/ vbb');
}
echo '<br>',get_microtime()-$startime,'<br>';*/
?>