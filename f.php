
<!DOCTYPE html>
<html lang="en">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta ></meta>
<link rel="stylesheet" href="css/style_.css" type="text/css"/>
<script href=""></script>
</head>
<body>
<?php
function cutstr($string, $length = 20, $start = 0, $charset = 'gb2312'){
 if($charset == 'utf-8'){
  $tmpstr = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$start.'}'. 
'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$length.'}).*#s', 
'$1',$string);
 }else{
  $length = $length * 2; 
  $tmpstr = '';
  if(isset($string[$length-1])){
   $strlen = strlen($string);
   for($i=0; $i<$strlen; $i++){
    if($i>=$start && $i<$length){
     if(ord(substr($string, $i, 1))>129){
      $tmpstr.= substr($string, $i, 2);
      ++$i;
     }else{
      $tmpstr.= substr($string, $i, 1);
     }
    }
   }
  }else{
   $tmpstr = $string;
  }
 }
 return $tmpstr; 
}
echo cutstr('中华人民共和国,万岁,万岁,万万岁!中华人民共和国,万岁,万岁,万万岁!中华人民共和国,万岁,万岁,万万岁!');
function a(){
 
}
?>
</body>
</html>