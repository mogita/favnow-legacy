<?php
session_start();
// error_reporting(0); 

require_once(__DIR__.'/../config.php');
require_once('UI_text.php');

$errcode = "";

$mysqli = new Mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset("utf8");

