<?php
require_once 'config.php';
include 'function.php';
include 'query.php';

if (!$_SESSION['loggedin']) header("Location: logout.php");
if (!isset($_SESSION['username']) or $_SESSION['username'] == '' or !isset($_SESSION['userid']) or $_SESSION['userid'] == '') header("Location: logout.php");
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];

$title_pattern = text('Setting');

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
						<li><a href="setting.php"><?php echo text('Setting'); ?></a></li>
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
		<div class="col-xs-2" style="background-color: #dedef8; ">
			<h4>第一列</h4>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
		</div>
		
		<div class="col-xs-8"  style="background-color: #555; ">
			<h4>settings pane</h4>
			
			<div class="col-xs-6" style="background-color: #333; ">
				<p>Left</p>
			</div>
			
			<div class="col-xs-6" style="background-color: #777; ">
				<p>Right</p>
			</div>
		</div>
		
		<div class="col-xs-2" style="background-color: #dedef8; ">
			<h4>第三列</h4>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
		</div>
		
	</div>
</div>

</div>
</body>
</html>