<?php
include('./up.php');
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $path2code . "/index.php"); 
?>