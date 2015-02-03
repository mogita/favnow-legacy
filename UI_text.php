<?php

/**
 * @project DoubanCocktail
 * @file UI_text.php
 * @brief Language files hub for a more convenient include. Default language is Simplified Chinese (zh_CN)
 *
 * @author mogita <himogita@gmail.com>
 * @date 14-3-9
 */

require_once ("languages/zh_CN.php");
require_once ("languages/en_US.php");

if(isset($_GET['lang']) && isset($_GET['back'])) {

	$_SESSION['lang'] = $_GET['lang'];
	$_COOKIE['lang'] = $_GET['lang'];
	setcookie('lang', $_GET['lang']);

	header('Location: http://'.$_GET['back']);
}

if(!isset($_COOKIE['lang'])) {
	$_SESSION['lang'] = 'zh_CN';
	setcookie('lang', 'zh_CN');
	$_COOKIE['lang'] = 'zh_CN';
} else {
	$_SESSION['lang'] = $_COOKIE['lang'];
}

function text($string) {

	$lang = $_SESSION["lang"];

	if (isset($GLOBALS[$lang][$string])) {
		return $GLOBALS[$lang][$string];
	} else {
		// error_log("l10n error: locale: "."$lang, message:'$string'");
		return $string.' *L10N?';
	}
}
