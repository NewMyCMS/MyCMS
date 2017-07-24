
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
$str = 'pid, aid, comment, uid, username, ip, posttime, ding';
//$str = 'uid,username,password,gid,gname,power';
$arr = explode(', ', $str);
$fields = "";
$values = "";
foreach($arr as $val){
 $fields .= "`{$val}`,&nbsp;";
 //$values .= "'$val',&nbsp;";
 //$values .= "'\".\${$val}.\"',&nbsp;";
 $values .= "'{\${$val}}',&nbsp;";
 //$values .= "\${$val} = '$val';\n";
}
echo $fields,'<br>',$values,'<br>',get_microtime()-$startime,'<br>';

//g
/*$startime=get_microtime();
$str = '';
for($i=0;$i<50000;++$i){
$uid = 'uid'; 
$username = 'username'; $password = 'password'; $gid = 'gid'; $gname = 'gname'; $power = 'power'; 
 $str .= "好'".$uid."', '".$username."', '".$password."', '".$gid."', '".$gname."', '".$power;
 $str .= "<div style=\"display:none;\">如'{$uid}', '{$username}', '{$password}', '{$gid}', '{$gname}', '{$power}'</div>";
}
echo get_microtime()-$startime,'<br>';

//gg
$startime=get_microtime(); 
$str='';
for($i=0;$i<50000;++$i){
$uid = 'uid';
$username = 'username'; $password = 'password'; $gid = 'gid'; $gname = 'gname'; $power = 'power';
 $str .= "好'{$uid}', '{$username}', '{$password}', '{$gid}', '{$gname}', '{$power}'<br>";
 $str .= "<div style='display:none;'>如'".$uid."', '".$username."', '".$password."', '".$gid."', '".$gname."', '".$power."'</div>";
}
echo get_microtime()-$startime,'<br>';


//ggg
$startime=get_microtime();
$str='';
for($i=0;$i<10000;++$i){
$arr = array('uid','username','password','gid','gname','power');
$fields = "";
$values = "";
foreach($arr as $val){
 //$fields .= "`{$val}`,&nbsp;";
 //$values .= "'$val',&nbsp;";
 $values .= "'\".\${$val}.\"',&nbsp;";
 //$values .= "'{\${$val}}',&nbsp;";
 //$values .= "\${$val} = '$val';\n";
}
$uid = 'uid';
$username = 'username'; $password = 'password'; $gid = 'gid'; $gname = 'gname'; $power = 'power';
$str.=$values;
}
echo get_microtime()-$startime,'<br>';


//ggg
$startime=get_microtime();
$str='';
for($i=0;$i<10000;++$i){
$str = 'uid,username,password,gid,gname,power';
$arr = explode(',', $str);
$fields = "";
$values = "";
foreach($arr as $val){
 //$fields .= "`{$val}`,&nbsp;";
 //$values .= "'$val',&nbsp;";
 //$values .= "'\".\${$val}.\"',&nbsp;";
 $values .= "'{\${$val}}',&nbsp;";
 //$values .= "\${$val} = '$val';\n";
}
$uid = 'uid';
$username = 'username'; $password = 'password'; $gid = 'gid'; $gname = 'gname'; $power = 'power';
$str.=$values;
}
echo $str,get_microtime()-$startime,'<br>';*/
?>
</body>
</html>