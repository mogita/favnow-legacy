<?php
session_start();

$_SESSION['loggedin'] = False;
$_SESSION['username'] = '';

header("Location: index.php");
?>