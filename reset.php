<?php
require_once 'config.php';
include 'fav_query.php';
// include 'function.php';
require_once 'lib/phpmailer/class.phpmailer.php';
require_once 'lib/phpmailer/class.smtp.php';

$title_pattern = text('Reset Password');

$mysqli = newDBConn();

if (isset($_POST['email']) and $_POST['email'] <> '' and isset($_POST['captcha']) and $_POST['captcha'] <> '') {
	include_once 'lib/securimage/securimage.php';
	$securimage = new Securimage();

	if (!$securimage->check($_POST['captcha'])) {
		$msg = text('Captcha incorrect, please try again');
	} else {
		
		if (strlen($_POST['email']) < 6 or !preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $_POST['email'])) {
			$msg = text('Email incorrect, please try again');
		} else {
			$sql = "SELECT email FROM users WHERE email='".$mysqli->real_escape_string($_POST['email'])."' LIMIT 1";
			$result = $mysqli->query($sql);
			
			if ($result) {
				if ($result->num_rows <> 0) {
					$myemail = "favnow@mogita.com";
					$time = time();
					$resetcode = crypt(md5($time), md5($_POST['email']).'romeoyjulieta');
					$sql = "UPDATE users SET resetcode = '".$resetcode."', resetcodetime='".$time."' WHERE email='".$_POST['email']."'";
					$result = $mysqli->query($sql);
					
					$mailer = new PHPMailer();
					$mailer->CharSet = "UTF-8";
					$mailer->Encoding = "base64";
					$mailer->IsSMTP();
					$mailer->SMTPAuth = true;
					$mailer->SMTPSecure = "ssl";
					$mailer->Host = "smtp.zoho.com";
					$mailer->Port = 465;
					$mailer->Username = $myemail;
					$mailer->Password = "nanoconmigo";
					$mailer->From = $myemail;
					$mailer->FromName = "FavNow";
					$mailer->AddAddress($_POST['email'], $_POST['email']);
					$mailer->AddReplyTo($myemail, "FavNow");
					$mailer->IsHTML(true);
					
					$mailer->Subject = text('FavNow Password reset');
					$mailer->Body = "
						<p>".text('Click the link below to reset your FavNow password')."</p>
						<h3><a href=\"http://0.0.0.0/favnow/reset_step2.php?code=".$resetcode."\" target=\"_blank\">http://0.0.0.0/favnow/reset_step2.php?code=".$resetcode."</a></h3>
						<p><a href=\"http://favnow.mogita.com\" target=\"_blank\">FavNow</a> | <small>".text('Your bookmarks in the cloud')."</small></p>";
					$mailer->AltBody = $mailer->Body;
						
					if (!$mailer->Send()) {
						$msg = text('There were problems sending the link, please try again')/*.$mailer->ErrorInfo*/;
					} else {
						$warncode = text('The link was sent to ').$_POST['email'].text(', only valid within 48 hours and once. Keep it safe and secret. Check your spam folder if you didn\'t see the email');
					}					
				} else {
					$msg = text('This Email does not match any records');
				}
			} else {
				$msg = text('There were problems inquiring, please try again');
			}
		}
	}
}

include('head.php');
?>
		<div class="container">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<h1><a href="index.php">FavNow</a><sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup><br /><small><?php echo text('Your bookmarks in the cloud'); ?></small></h1>
										
					<form action="reset.php" method="post" role="form">
						<h2 class="form-signin-heading"><?php echo text('Reset Password'); ?></h2>
						<div class="alert alert-danger <?php if ($msg == '') echo 'hidden'; ?>" role="alert"><?php echo $msg; ?></div>
						<div class="alert alert-danger <?php if (!isset($_SESSION['errcode']) or $_SESSION['errcode'] == '') echo 'hidden'; ?>" role="alert"><?php if (isset($_SESSION['errcode']) and $_SESSION['errcode'] <> '') { echo $_SESSION['errcode']; $_SESSION['errcode'] = ''; } ?></div>
						<div class="alert alert-warning <?php if ($warncode == '') echo 'hidden'; ?>" role="alert"><?php echo $warncode; ?></div>
						
						<div class="form-group">
							<label for="email"><?php echo text('Email'); ?></label>
							<input type="email" name="email" class="form-control" id="email" required autofocus />
						</div>
						<div class="form-group">
							<label for="captcha"><?php echo text('Captcha'); ?></label>
							<p>
								<a href="#" tabindex="-1" onclick="document.getElementById('captcha').src = 'lib/securimage/securimage_show.php?' + Math.random(); return false"><img style="height: 80px;" id="captcha" src="lib/securimage/securimage_show.php" alt="CAPTCHA Image" title="<?php echo text('Click to change a picture'); ?>" /></a>
							</p>
							<input type="text" name="captcha" class="form-control" id="captcha" placeholder="<?php echo text('Enter the word in the picture...'); ?>" required />
						</div>
						<input type="submit" value="<?php echo text('Reset Password'); ?>" class="btn btn-lg btn-warning btn-block" />
					</form>
					<br />
					<p><a href="index.php"><?php echo text('Back to login'); ?></a></p>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</body>
</html>