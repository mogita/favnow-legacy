<?php
require_once('lib/favnow.lib.php');

if (!$_SESSION['loggedin']) header("Location: logout.php");
if (!isset($_SESSION['username']) or $_SESSION['username'] == '' or !isset($_SESSION['userid']) or $_SESSION['userid'] == '') header("Location: logout.php");
$userid = $_SESSION['userid'];

$title_pattern = text('Home');

if (isset($_POST['url']) and $_POST['url'] <> '') {
	$url = $_POST['url'];
	
	if (strlen($url) > 500) {
		$errcode = text('URL too long. Use URL shorteners (e.g. <a href="http://is.gd" target="_blank">http://is.gd</a>) please.');
	} /*elseif (!preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $url)) {
		$errcode = "URL 格式可能有误。如果你认为是 FavNow 的误判，请将 URL 发送给管理员进行检查。";
	}*/ else {
				
		$time = time();
		
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) $url = "http://".$url;
		
		$sql = "SELECT url FROM Favs WHERE userid='".$userid."' AND url='".$url."' LIMIT 1";
		$result = $mysqli->query($sql);
				
		if ($result->num_rows <> 0){
			$errcode = text('Bookmark already exists: ').$url;
		} else {
			
			if (!isset($_POST['title']) or $_POST['title'] == '') {
				
				$ctx = stream_context_create(array(
					'http' => array(
						'timeout' => 7
						)
					)
				);
				
				$content = file_get_contents($url, 0, $ctx);
				
				if (strlen($content) > 0) {
					preg_match("/\<title\>(.*)\<\/title\>/", $content, $title);
					if (isset($title[1]) and $title[1] <> '') {
						$title = $title[1];
					} else {
						$title = $url;
					}
				} else {
					$title = $url;
				}
				
			} else {
				$title = $_POST['title'];
			}
						
			$url = $mysqli->real_escape_string($url);
			$title = $mysqli->real_escape_string($title);

			$sql = "INSERT INTO Favs (userid, url, title, timepoint) VALUES ('".$userid."', '".$url."', '".$title."', '".$time."')";
			$result = $mysqli->query($sql);
				
			if (!$result) {
				$errcode = text('There was an error, please try again.');
			}
		}
	}
}

if (isset($_POST['fav-list-delete-item']) and $_POST['fav-list-delete-item'] <> '') {
	$fav_id = $_POST['fav-list-delete-item'];
	
	$sql = "DELETE FROM Favs WHERE id=\"".$fav_id."\" AND userid=\"".$userid."\"";
	$result = $mysqli->query($sql);
	
	if (!$result) {
		$errcode = text('There were problems deleting, please try again.');
	}
}

$sql = "SELECT * FROM Favs WHERE userid='".$userid."'";
$result = $mysqli->query($sql);

if ($result) {
	if ($result->num_rows > 0) {
		$fav_list = "";
		
		while ($row = $result->fetch_array()) {
			$fav_id = $row[0];
			$url = $row[2];
			$title = $row[3];
			$timestamp = $row[4];
			
			if (strlen($title) > 70) {
				$title = substr($title, 0, 46)." ... ".substr($title, -15);
			}
			
			$fav_list .= "<tr><td><a href=\"".$url."\" target=\"_blank\">".$title."</a></td><td>".date(text('H:i:s M d, Y'), $timestamp)."</td><td><form action=\"home.php\" method=\"post\"><input type=\"hidden\" name=\"fav-list-delete-item\" value=\"".$fav_id."\" /><input type=\"submit\" value=\"".text('Delete')."\" class=\"btn btn-xs\" /></form></td></tr>";
		}		
	} else {
		$fav_list = "<h4><span class=\"label label-default\">".text('No bookmarks yet? Start to save them right away!')."</span></h4>";
	}
}

include('head.php');
?>
		<div class="container">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<h1><a href="home.php">FavNow</a><sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup><br /><span style="font-size: 0.45em; color: #aaaaaa;"><?php echo text('Hello, '); ?><?php echo $_SESSION['username']; ?> <?php echo text('Kun'); ?><span style="margin-left: 5px;"><a class="btn btn-default btn-xs" href="logout.php" role="button"><?php echo text('Logout'); ?></a></span></span></h1>
					
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="panel-title"><?php echo text('New Bookmark'); ?></div>
						</div>
						<div class="panel-body">
							<form action="home.php" method="post" class="form">
								<div class="form-group">
									<input type="text" name="url" size="100" class="form-control" placeholder="<?php echo text('URL'); ?>" required autofocus />
								</div>
								<div class="form-group">
									<input type="text" name="title" size="100" class="form-control" placeholder="<?php echo text('Title (Optional. Leave it blank for FavNow to get it for you)'); ?>" />
								</div>
								<input type="submit" value="<?php echo text('Save'); ?>" class="btn btn-sm btn-primary " />
								<span class="label label-danger <?php if ($errcode == '') echo 'hidden'; ?>" role="alert" style="margin-left: 5px;"><?php echo $errcode; ?></span>
							</form>
						</div>
					</div>
							
					<div class="fav-list">
						<h2><?php echo text('My Bookmarks'); ?></h2>
						<table class="table table-hover">
							<tr>
								<p><?php echo $fav_list; ?></p>
							</tr>
						</table>
					</div>
				</div>
				<div class="col-md-1">
						<form method="post" action="" role="form">
							<input type="hidden" name="chlang" value="1">
							<label for="language-switch"></label>
							<select class="form-control" name="language-switch" id="language-switch" onchange="this.form.submit();">
								<option disabled selected="selected">Languages</option>
								<option value="zh_CN">简体中文</option>
								<option value="en_US">English</option>
							</select>
						</form>
				</div>
			</div>
		</div>
	</body>
</html>