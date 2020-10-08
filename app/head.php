<?php
// 语言切换：表单 POST 请求，发送给原页面（即空 action），在此处下方判定，若有语言切换则跳转到 UI_text 页，用 GET 传送参数
if(isset($_POST['language-switch'])){
	header('Location:UI_text.php?lang='.$_POST['language-switch'].'&back='.$_SERVER['HTTP_HOST']
		.$_SERVER['REQUEST_URI']);
}
?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="个人云端书签服务，提供书签存储、管理等方便实用的功能">
		<meta name="keywords" content="书签, 网址, 工具, 实用, 小工具, 服务, 批量管理, URL, 浏览器">
		<meta name="author" content="mogita">
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
		<title><?php if(isset($title_pattern) && !empty($title_pattern)) echo $title_pattern.' - '; ?>FavNow</title>
		
		<link href="css/bootstrap.paper.min.css" rel="stylesheet">
		<link href="css/animate.min.css" rel="stylesheet">
		<link href="css/favnow.css" rel="stylesheet">
		
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-notify.min.js"></script>
	</head>
	
	<body>