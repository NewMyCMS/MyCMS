<?php
$dbhost = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mycms';
$conn = @new mysqli($dbhost,$username,$password,$dbname);
if(!$conn->select_db($dbname)) echo 'ok';
?>
