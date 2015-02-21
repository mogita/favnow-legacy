<?php
require_once 'config.php';
include 'function.php';
include 'query.php';

if (empty($_SESSION['loggedin'])) header("Location: logout.php");
if (!isset($_SESSION['username']) or $_SESSION['username'] == '' or !isset($_SESSION['userid']) or $_SESSION['userid'] == '') header("Location: logout.php");
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];

$title_pattern = text('Preference');

// Changing password
if (isset($_POST['pwd0']) and isset($_POST['pwd1']) and isset($_POST['pwd2']) and $_POST['pwd0'] <> '' and $_POST['pwd1'] <> '' and $_POST['pwd2']) {
	$msg = pwChange($_POST['pwd0'], $_POST['pwd1'], $_POST['pwd2'], $userid, $username);
}

// Loading home page now
include('head.php');
?>

<div class="modal" id="about" tabindex="-1" role="dialog" aria-labelledby="aboutLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
				<h4 class="modal-title" id="settings"><?php echo text('About'); ?></h4>
			</div>
			<div class="modal-body">
				<h3>FavNow</h3>
				<p><?php echo text('This is about dialog text'); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="password-change" tabindex="-1" role="dialog" aria-labelledby="passwordChangeLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
				<h4 class="modal-title" id="settings"><?php echo text('Change Password'); ?></h4>
			</div>
			
			<div class="modal-body">
				<form method="post" action="" role="form" onsubmit="return validatePreferenceForm()">
					<div class="form-group">
						<label for="password0"><?php echo text('Type current password to change it'); ?></label>
						<input type="password" name="pwd0" id="password0" class="form-control" />
					</div>
					<div id="new-password">
						<div class="form-group">
							<label for="password1"><?php echo text('New Password'); ?><small style="margin-left: 5px;"><?php echo text('(Must be between 6 and 32 chars long.)'); ?></small><small for="password1" id="password-invalid" class="password-info"></small></label>
							<input type="password" name="pwd1" id="password1" class="form-control" />
						</div>
						<div class="form-group">
							<label for="password2"><?php echo text('New Password Again'); ?></label>
							<small for="password2" class="password-info" id="password-mismatch"><?php echo text('Passwords mismatch'); ?></small>
							<input type="password" name="pwd2" id="password2" class="form-control" />
						</div>
					</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
							<button type="submit" class="btn btn-primary"><?php echo text('Save changes'); ?></button>
						</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div class="navbar navbar-default" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="home.php">FavNow<sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup></a>
        </div>
        <div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_SESSION['username']; ?> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu" style="min-width: 0px;">
						<li><a href="#" data-toggle="modal" data-target="#about"><?php echo text('About'); ?></a></li>
						<li class="divider"></li>
						<?php /*<li><a href="#" data-toggle="modal" data-target="#settings"><?php echo text('Setting'); ?></a></li>*/ ?>
						<li><a href="preference.php"><?php echo text('Preference'); ?></a></li>
						<li class="divider"></li>
						<li><a href="logout.php"><?php echo text('Logout'); ?></a></li>
					</ul>					
				</li>
			</ul>
        </div><!--/.navbar-collapse -->
      </div>
</div>

<div class="container">
	<div class="row">
		<div class="col-xs-3">
		</div>
		
		<div class="col-xs-6"  style="background-color: #f8f8f8; ">
			<h3 style="margin-bottom: 30px;"><?php echo text('Preference'); ?></h3>
		
		<div class="col-xs-12" style="padding-top: 7px; padding-bottom: 7px; margin-bottom: 10px; word-wrap: break-word; background-color: #fffdcb; <?php if (empty($msg)) echo 'display: none;';?>" role="alert">
			<?php if (!empty($msg)) echo $msg; ?>
		</div>
		
			<div class="col-xs-6 preference-left">
				<p>
					<form method="post" action="">
						<span>语言 / Language: </span>
						<span><?php echo $_SESSION['lang']; ?>
					</form>
				</p>
			</div>
			
			<div class="col-xs-6 preference-right">
				<p>
					<form method="post" action="">
						<input type="hidden" name="chlang" value="1">
						<select name="language-switch" id="language-switch" onchange="this.form.submit();">
							<option disabled selected="selected">Change language</option>
							<option value="zh_CN">简体中文</option>
							<option value="en_US">English</option>
						</select>
					</form>
				</p>
			</div>
			
			<div class="col-xs-6 preference-left">
				<p>
					<span><?php echo text('Password'); ?>: </span>
					<span>·······</span>
				</p>
			</div>
			
			<div class="col-xs-6 preference-right">
				<p>
					<a href="#" data-toggle="modal" data-target="#password-change"><?php echo text('Change Password'); ?></a>
				</p>
			</div>
			
		</div>
		
		<div class="col-xs-3">
		</div>
		
	</div>
</div>

</div>
<script language="javascript">
function checkPasswordValid() {
	if ($('#password1').val().length < 6 || $('#password1').val().length > 32) {
		$('#password-invalid').html('<?php echo text('Invalid length'); ?>');
		passwordValidation = false;
	} else {
		$('#password-invalid').html('');
		
		if ($('#password1').val() != $('#password2').val()) {
			$('#password-mismatch').show();
			passwordValidation = false;
		} else {
			$('#password-mismatch').hide();
			passwordValidation = true;
		}
	}
}

function changePassword() {
	if ($('#password0').val().length > 0) {
		$('#new-password').show();
		passwordValidation = false;
	} else {
		$('#new-password').hide();
		passwordValidation = true;
	}
}

function validatePreferenceForm() {
	if (passwordValidation == false) {
		return false;
	} else if (passwordValidation == true) {
		return true;
	}
}

$(document).ready(function(){
	$('#new-password').hide();
	$('#password-mismatch').hide();
	
	var passwordValidation = true;
				
	$('#password0').keyup(changePassword);
	$('#password1').keyup(checkPasswordValid);
	$('#password2').keyup(checkPasswordValid);
});

</script>
</body>
</html>