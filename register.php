<?php
require_once('lib/favnow.lib.php');

if (!REGISTER_OPEN) {
	$title_pattern = text('Preserve');
} else {
	$title_pattern = text('Register');
}

if (isset($_POST['pre-email']) and isset($_POST['captcha'])) {
	include_once 'lib/securimage/securimage.php';
	$securimage = new Securimage();
	
	if ($_POST['pre-email'] == '' or $_POST['captcha'] == '') {
		$errcode = text('Please fill in all the required fields.');
	} elseif (!$securimage->check($_POST['captcha'])) {
		$errcode = text('Captcha incorrect, please try again.');
	} elseif (strlen($_POST['pre-email']) < 6 or !preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $_POST['pre-email'])) {
		$errcode = text('Email format incorrect, please try again.');
	} else {
		$email = strip_tags($_POST['pre-email']);
		$time = time();
		
		// 检查是否已是注册用户
		$sql = "SELECT email FROM Users WHERE email='".$mysqli->real_escape_string($email)."'";
		$result = $mysqli->query($sql);
		
		if ($result->num_rows <> 0) {
			$_SESSION['warncode'] = $email.text(' has already been a registered user, please login.');
			header("Location: index.php");
			exit;
		}
		
		// 检查是否已是预留的 email
		$sql = "SELECT email FROM PreRegister WHERE email='".$mysqli->real_escape_string($email)."'";
		$result = $mysqli->query($sql);
		
		if ($result->num_rows == 0) {
			$sql = "INSERT INTO PreRegister (email, preregtime, notified) VALUES ('".$mysqli->real_escape_string($email)."', '".$time."', 0)";
			$result = $mysqli->query($sql);
			
			if (!$result) {
				$errcode = text('There were problems processing your preservation, please try again.');
			} else {
				$_SESSION['warncode'] = text('Thank you! I\'ll make FavNow getting to you as soon as possible!');
				header("Location: index.php");
				exit;
			}
		} else {
			$_SESSION['warncode'] = text('Thank you! I\'ll make FavNow getting to you as soon as possible!');
			header("Location: index.php");
			exit;
		}
	}
}

if (isset($_POST['usn']) and isset($_POST['pwd1']) and isset($_POST['pwd2']) and isset($_POST['email']) and isset($_POST['captcha'])) {
	include_once 'lib/securimage/securimage.php';
	$securimage = new Securimage();
	
	if ($_POST['usn'] == '' or $_POST['pwd1'] == '' or $_POST['pwd2'] == '' or $_POST['email'] == '' or $_POST['captcha'] == '') {
		$errcode = text('Please fill in all the required fields.');
	} elseif (!$securimage->check($_POST['captcha'])) {
		$errcode = text('Captcha incorrect, please try again.');
	} elseif (strlen($_POST['usn']) < 3) {
		$errcode = text('Username must be between 3 and 32 chars long.');
	} elseif ($_POST['pwd1'] <> $_POST['pwd2']) {
		$errcode = text('The passwords you entered do not match.');
	} elseif (strlen($_POST['email']) < 6 or !preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $_POST['email'])) {
		$errcode = text('Email format incorrect, please try again.');
	} else {
		$username = strip_tags(substr($_POST['usn'], 0, 32));
		$password = strip_tags(substr($_POST['pwd1'], 0, 32));
		$email = strip_tags($_POST['email']);
		$time = time();
		
		$safepw = crypt(md5($password), md5($username).'romeoyjulieta');
		
		// 是否已有同 email 注册过检查
		$sql = "SELECT email FROM Users WHERE email='".$mysqli->real_escape_string($email)."'";
		$result = $mysqli->query($sql);
		
		if ($result->num_rows <> 0) {
			$errcode = text('This Email has already been a registered user, please <a href="index.php">login</a>. If you got difficulties logging in, please try to <a href="reset.php">reset</a> your password.');
		} else {
			$sql = "SELECT * FROM Users WHERE user='".$username."'";
			$result = $mysqli->query($sql);
			
			if ($result->num_rows <> 0) {
				$errcode = text('Username was taken by another user, please pick a different one.');
			} else {
				$sql = "INSERT INTO Users (user, password, email, jointime) VALUES ('".$mysqli->real_escape_string($username)."', '".$mysqli->real_escape_string($safepw)."', '".$mysqli->real_escape_string($email)."', '".$time."')";
				$result = $mysqli->query($sql);
		
				if ($result) {
					$sql = "SELECT id FROM Users WHERE user='".$mysqli->real_escape_string($username)."' LIMIT 1";
					$result = $mysqli->query($sql);
					$row = $result->fetch_array(MYSQLI_NUM);
					$userid = $row[0];
			
					$_SESSION['loggedin'] = True;
					$_SESSION['username'] = $username;
					$_SESSION['userid'] = $userid;
			
					$mysqli->close();
			
					header("Location: home.php");
					exit;
			
				} else {
					unset($mysqli);
					$errcode = text('There were problems processing your registration, please try again.');
				}
			}
		}
	}
}

include('head.php');
?>
		<div class="container">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<h1><a href="index.php">FavNow</a><sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup><br /><small><?php echo text('Your bookmarks in the cloud'); ?></small></h1>
					
					<form action="register.php" method="post" class="form">
						<h2 class="form-signin-heading"><?php echo $title_pattern; ?></h2>
						<div class="alert alert-danger <?php if ($errcode == '') echo 'hidden'; ?>" role="alert"><?php echo $errcode; ?></div>
						
						<?php if (!REGISTER_OPEN) { ?>
							<div class="alert alert-warning" role="alert"><?php echo text('FavNow still needs a little while to open register. Submit your Email to get notified right away about the public registration.'); ?></div>
							<div class="form-group">
								<label for="email"><?php echo text('Email'); ?></label>
								<input type="email" name="pre-email" id="email" class="form-control" required autofocus />
							</div>
						<?php } else { ?>
							<div class="form-group">
								<label for="email"><?php echo text('Email'); ?><small style="margin-left: 5px;"><?php echo text('(Please enter a valid Email address for receiving password reset links.)'); ?></small></label>
								<input type="email" name="email" id="email" class="form-control" required autofocus />
							</div>
							<div class="form-group">
								<label for="username"><?php echo text('Username'); ?><small style="margin-left: 5px;"><?php echo text('(Must be between 3 and 32 chars long.)'); ?></small></label>
								<input type="text" name="usn" class="form-control" id="username" required />
							</div>
							<div class="form-group">
								<label for="password1"><?php echo text('Password'); ?><small style="margin-left: 5px;"><?php echo text('(Must be between 6 and 32 chars long.)'); ?></small></label>
								<input type="password" name="pwd1" id="password1" class="form-control" required />
							</div>
							<div class="form-group">
								<label for="password2"><?php echo text('Confirm password'); ?></label>
								<input type="password" name="pwd2" id="password2" class="form-control" required />
							</div>
						<?php } ?>
						
						<div class="form-group">
							<label for="captcha"><?php echo text('Captcha'); ?></label>
							<p>
								<a href="#" tabindex="-1" onclick="document.getElementById('captcha').src = 'lib/securimage/securimage_show.php?' + Math.random(); return false"><img style="height: 80px;" id="captcha" src="lib/securimage/securimage_show.php" alt="CAPTCHA Image" title="<?php echo text('Click to change a picture'); ?>" /></a>
							</p>
							<input type="text" name="captcha" class="form-control" id="captcha" placeholder="<?php echo text('Enter the word in the picture...'); ?>" required />
						</div>
						<input type="submit" value="<?php echo $title_pattern; ?>" class="btn btn-lg btn-success btn-block" />
					</form>
					<br />
					<p><a href="index.php"><?php echo text('Have an account? Login now!'); ?></a></p>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</body>
</html>