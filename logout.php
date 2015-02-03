<?php
session_start();

$_SESSION['loggedin'] = False;
$_SESSION['username'] = '';
$_SESSION['userid'] = '';

header("Location: index.php");
?>