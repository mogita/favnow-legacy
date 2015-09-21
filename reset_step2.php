<?php
require_once 'config.php';
include 'fav_query.php';
// include 'function.php';

$title_pattern = text('Change Password');

$mysqli = newDBConn();

if (isset($_GET['code']) or isset($_POST['code'])) {
	
	if (isset($_POST['code']) and $_POST['code'] <> '') {
		$code = $_POST['code'];
	} elseif (isset($_GET['code']) and $_GET['code'] <> '') {
		$code = $_GET['code'];
	} else {
		header("Location: index.php");
		exit;
	}
	
	$sql = "SELECT * FROM users WHERE resetcode='".$mysqli->real_escape_string($code)."' LIMIT 1";
	$result = $mysqli->query($sql);
	
	if ($result) {
		if ($result->num_rows > 0) {
			$row = $result->fetch_array(MYSQLI_NUM);
			$userid = $row[0];
			$username = $row[1];
			$resettime = $row[6];
			
			$time = time();
			$timepassed = $time - $resettime;
			
			if ($timepassed > 48*60*60 ) {
				// echo "<script>alert('Time exceeded!')</script>";
				$sql = "UPDATE users SET resetcode = '', resetcodetime = '' WHERE id = '".$userid."'";
				$result = $mysqli->query($sql);
				
				$_SESSION['errcode'] = text('The link has expired, please send another link');
				header("Location: index.php");
				exit;
			} else {
				if (isset($_POST['newpwd1']) and isset($_POST['newpwd2']) and isset($_POST['captcha']) and $_POST['newpwd1'] <> '' and $_POST['newpwd2'] <> '' and $_POST['captcha'] <> '') {
					include_once 'lib/securimage/securimage.php';
					$securimage = new Securimage();

					if (!$securimage->check($_POST['captcha'])) {
						$msg = text('Captcha incorrect, please try again');
					} elseif ($_POST['newpwd1'] <> $_POST['newpwd2']) {
						$msg = text('The passwords you entered do not match');
					} else {
						$password = strip_tags(substr($_POST['newpwd1'], 0, 32));
						$safepwd = crypt(md5($password), md5($username).'romeoyjulieta');
						
						$sql = "UPDATE users SET password='".$mysqli->real_escape_string($safepwd)."' WHERE id='".$userid."'";
						$result = $mysqli->query($sql);
						
						if ($result) {
							$sql = "UPDATE users SET resetcode = '', resetcodetime = '' WHERE id = '".$userid."'";
							$result = $mysqli->query($sql);
							
							$_SESSION['warncode'] = text('Password successfully changed. Please login');
							header("Location: index.php");
							exit;
						}
					}
				}
			}
			
		} else {
			header("Location: logout.php");
			exit;
		}
	} else {
		header("Location: logout.php");
		exit;
	}
} else {
	header("Location: logout.php");
	exit;
}

include('head.php');
?>
		<div class="container">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<h1><a href="index.php">FavNow</a><sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup><br /><small><?php echo text('Your bookmarks in the cloud'); ?></small></h1>
										
					<form action="reset_step2.php" method="post" role="form">
						<h4 class="form-signin-heading"><?php echo text('Change Password'); ?></h4>
						<div class="alert alert-danger <?php if ($msg == '') echo 'hidden'; ?>" role="alert"><?php echo $msg; ?></div>
						<div class="alert alert-warning <?php if ($warncode == '') echo 'hidden'; ?>" role="alert"><?php echo $warncode; ?></div>
						
						<div class="form-group">
							<label for="newpwd1"><?php echo text('New password'); ?><small style="margin-left: 5px;"><?php echo text('(Must be between 6 and 32 chars long)'); ?></small></label>
							<input type="password" name="newpwd1" class="form-control" id="newpwd1" required autofocus />
						</div>
						<div class="form-group">
							<label for="newpwd2"><?php echo text('Confirm password'); ?></label>
							<input type="password" name="newpwd2" class="form-control" id="newpwd2" required />
						</div>
						<div class="form-group">
							<label for="captcha"><?php echo text('Captcha'); ?></label>
							<p>
								<a href="#" tabindex="-1" onclick="document.getElementById('captcha').src = 'lib/securimage/securimage_show.php?' + Math.random(); return false"><img style="height: 80px;" id="captcha" src="lib/securimage/securimage_show.php" alt="CAPTCHA Image" title="<?php echo text('Click to change a picture'); ?>" /></a>
							</p>
							<input type="text" name="captcha" class="form-control" id="captcha" placeholder="<?php echo text('Enter the word in the picture...'); ?>" required />
						</div>
						<input type="hidden" name="code" value="<?php echo $code; ?>" />
						<input type="submit" value="<?php echo text('Change Password'); ?>" class="btn btn-lg btn-warning btn-block" />
					</form>
					<br />
					<h6><a href="index.php"><?php echo text('Back to login'); ?>&nbsp;&nbsp;<i class="glyphicon glyphicon-log-in"></i></a></h6>
                    <br />
                    <br />
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</body>
</html>