<?php
session_start();
session_destroy();
//$_SESSION['loggedin'] = False;
//$_SESSION['username'] = '';
//$_SESSION['userid'] = '';

header("Location: index.php");
?>