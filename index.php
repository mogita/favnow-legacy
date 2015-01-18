<?php
require_once('lib/favnow.lib.php');
$title_pattern = text('Login');

if (isset($_SESSION['loggedin']) and $_SESSION['loggedin']) header("Location: home.php");

if (isset($_POST['usn']) and isset($_POST['pwd']) and $_POST['usn'] <> '' and $_POST['pwd'] <> '') {
	
	if (preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $_POST['usn'])) {
		$email = $_POST['usn'];
		$sql = "SELECT * FROM Users WHERE email='".$mysqli->real_escape_string($email)."' LIMIT 1";
	} else {
		$username = strip_tags(substr($_POST['usn'], 0, 32));
		$sql = "SELECT * FROM Users WHERE user='".$mysqli->real_escape_string($username)."' LIMIT 1";
	}
	
	$result = $mysqli->query($sql);

	if ($result->num_rows <> 0) {
		$row = $result->fetch_array(MYSQLI_NUM);
		$userid = $row[0];
		$username = $row[1];
		$password = $row[2];
		
		$pass = strip_tags(substr($_POST['pwd'], 0, 32));
		$safepw = crypt(md5($pass), md5($username).'romeoyjulieta');
		
		if ($password == $safepw) {
			$_SESSION['loggedin'] = True;
			$_SESSION['username'] = $username;
			$_SESSION['userid'] = $userid;
	
			$mysqli->close();
	
			header("Location: home.php");
			exit;
		} else {
			$errcode = text('Username or password incorrect');
		}	
	} else {
		$errcode = text('Username or password incorrect');
	}
}

include('head.php');
?>
		<div class="container">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<h1><a href="index.php">FavNow</a><sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup><br /><small><?php echo text('Your bookmarks in the cloud'); ?></small></h1>
										
					<form action="index.php" method="post" role="form">
						<h2 class="form-signin-heading"><?php echo text('Login') ?></h2>
						<div class="alert alert-danger <?php if ($errcode == '') echo 'hidden'; ?>" role="alert"><?php echo $errcode; ?></div>
						<div class="alert alert-success <?php if (!isset($_SESSION['warncode']) or $_SESSION['warncode'] == '') echo 'hidden'; ?>" role="alert"><?php if (isset($_SESSION['warncode']) and $_SESSION['warncode'] <> '') { echo $_SESSION['warncode']; $_SESSION['warncode'] = ''; } ?></div>
						
						<div class="form-group">
							<label for="username"><?php echo text('Username'); ?> / <?php echo text('Email'); ?></label>
							<input type="text" name="usn" class="form-control" id="username" required autofocus />
						</div>
						<div class="form-group">
							<label for="password"><?php echo text('Password'); ?></label>
							<input type="password" name="pwd" class="form-control" id="password" required />
						</div>
						<input type="submit" value="<?php echo text('Login'); ?>" class="btn btn-lg btn-primary btn-block" />
					</form>
					<br />
					<p><a href="register.php"><?php echo text('Signup for FREE'); ?></a></p>
					<p><a href="reset.php"><?php echo text('Lost Password?'); ?></a></p>
					
					<form method="post" action="" role="form" class="pull-right">
						<input type="hidden" name="chlang" value="1">
						<label for="language-switch"></label>
						<select class="form-control" name="language-switch" id="language-switch" onchange="this.form.submit();">
							<option disabled selected="selected">Languages</option>
							<option value="zh_CN">简体中文</option>
							<option value="en_US">English</option>
						</select>
					</form>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</body>
</html>