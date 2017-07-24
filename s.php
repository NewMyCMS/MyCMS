<?php
error_reporting(E_ALL);
echo '绝对路经:'.dirname(__FILE__);
session_start();
$_SESSION['A']=' SESSION 测试';
echo $_SESSION['A'];
?>