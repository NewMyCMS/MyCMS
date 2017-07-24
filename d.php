<!DOCTYPE html>
<html lang="en">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta ></meta>
<link rel="stylesheet" href="template/default/css/s.css" type="text/css"/>
<script href=""></script>
</head>
<body style="margin:400px 100px;font-size:80px;line-height:400px;">
<?php
function get_microtime(){
 list($usec,$sec) = explode(' ',microtime());
 return ((float)$usec + (float)$sec);
}

$startime=get_microtime();
for($i=0;$i<10000;$i++){
 echo $a,$b,$c,$d,$e,$f,$g;
}
echo '<br>',get_microtime()-$startime,'<br>';

//
$startime=get_microtime();
for($i=0;$i<10000;$i++){
 $h = $a.$b.$c.$d.$e.$f.$g;
 echo $h;
}
echo '<br>',get_microtime()-$startime,'<br>';

//
$startime=get_microtime();
for($i=0;$i<10000;$i++){
 echo "{$a}{$b}{$c}{$d}{$e}{$f}{$g}";
}
echo '<br>',get_microtime()-$startime;
?>
</body>
</html>