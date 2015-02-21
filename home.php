<?php
require_once 'config.php';
include 'function.php';
include 'query.php';

if (empty($_SESSION['loggedin'])) header("Location: logout.php");
if (!isset($_SESSION['username']) or $_SESSION['username'] == '' or !isset($_SESSION['userid']) or $_SESSION['userid'] == '') header("Location: logout.php");
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];

$title_pattern = text('Home');

// Adding a bookmark
if (isset($_POST['url']) and $_POST['url'] <> '') {
	$msg = addBookmark($_POST['url'], $userid, $_POST['title']);
}

// Deleting a bookmark
if (isset($_GET['delete']) and $_GET['delete'] <> '') {
	$msg = deleteBookmark($_GET['delete'], $userid);
}

// Loading home page now
include('head.php');
?>

<script>
$(function(){
	var sideBarNavWidth=$('#leftColumn').width() - parseInt($('#sidePanel').css('paddingLeft')) - parseInt($('#sidePanel').css('paddingRight'));
	$('#sidePanel').css('width', sideBarNavWidth);
	});
</script>

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
				<div class="col-md-3" id="leftColumn">
					<?php //fixed side panel width problem with affix plugin by method provided on https://github.com/twbs/bootstrap/issues/6350#issuecomment-12173554 ?>
						<div class="panel panel-primary" data-spy="affix" id="sidePanel" data-offset-top="60">
							
							<div class="panel-body">
								
								<form action="home.php" method="post" class="form">
									<div class="form-group">
										<input type="text" name="url" size="100" class="form-control" placeholder="<?php echo text('URL'); ?>" required autofocus />
									</div>
									<div class="form-group">
										<input type="text" name="title" size="100" class="form-control" placeholder="<?php echo text('Title (Optional. Leave it blank for FavNow to get it for you)'); ?>" />
									</div>
									<input type="submit" value="<?php echo text('Add'); ?>" class="btn btn-sm btn-primary" />
								</form>
							</div>
							<div class="panel-footer <?php if ($msg == '') echo 'hidden'; ?>" role="alert"  style="margin-top: 15px; word-wrap: break-word;"><?php echo $msg; ?></div>
							
						</div>
				</div>
					
					<div class="col-md-9">
					<div class="fav-list">
						<div class="panel panel-primary">
						<div class="panel-heading"><?php echo text('My Bookmarks'); ?></div>
						<div class="panel-body">
						<table class="table table-hover" id="fav-list-table">
								<?php
								// Reading bookmarks
								$result = readBookmark($userid);
								$count = $result[1];
								$bookmark = $result[2];
								$favlist = '';
								
								if ($count < 0) {
								?>
									<h4>
										<span class="label label-error">
											<?php echo text('Unable to fetch bookmarks: ').$result[0]; ?>
										</span>
									</h4>
								<?php } elseif ($count == 0) { ?>
									<h2>
										<span class="label label-default">
											<?php echo text('No bookmarks yet? Start to save them right away!'); ?>
										</span>
									</h2>
								<?php } else {
										foreach ($bookmark as $row) {
										$favid = $row['id'];
										$url = $row['url'];
										$title = $row['title'];
										$time = $row['timepoint'];
										
										// Shortening the title if it's too long (Method from Discuz!)		
										$titleTrimmed = cutstr($title, 70, 'utf-8', $dot = ' ...');
										$titleHTML = ($title == $titleTrimmed) ? '' : $title;
								?>
								<tr>
									<td>
										<div class="fav-list-cell" id="<?php echo $favid; ?>">
											<div class="fav-list-cell-top">
												<span class="fav-list-cell-title">
													<a href="<?php echo $url; ?>" title="<?php echo $titleHTML; ?>" target="_blank"><?php echo $titleTrimmed; ?></a>
												</span>
												<span class="fav-list-cell-service">
													<a class="edit-button" href="#" data-toggle="modal" data-target="#edit"><?php echo text('Edit'); ?></a>
												</span>
											</div>
											<div class="fav-list-cell-bottom">
												<span class="fav-list-cell-datetime">
													<?php echo date(text('H:i:s M d, Y'), $time); ?>
												</span>
												<span class="fav-list-cell-service">
													<a class="delete-button" href="<?php echo $_SERVER['REQUEST_URI']; ?>?delete=<?php echo $favid; ?>"><?php echo text('Delete'); ?></a>
												</span>
											</div>
										</div>
									</td>
								</tr>
								<?php 
										}
									}
								?>
						</table>
					</div>
					</div>
				</div>
			</div>
		</div>
		<script language="javascript">
		function showCellService() {
			$(this).find('.fav-list-cell-service').show();
		}
		
		function hideCellService() {
			$(this).find('.fav-list-cell-service').hide();
		}
		
		$(document).ready(function(){
			$('.fav-list-cell-service').hide();						
			$('#fav-list-table tr td').hover(showCellService, hideCellService);
		});
		</script>
	</body>
</html>