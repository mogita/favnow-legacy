<?php
require_once 'config.php';
include 'function.php';
include 'query.php';

// if (empty($_SESSION['loggedin'])) header("Location: logout.php");
// if (!isset($_SESSION['username']) or $_SESSION['username'] == '' or !isset($_SESSION['userid']) or $_SESSION['userid'] == '') header("Location: logout.php");
if (empty($_SESSION['username']) or empty($_SESSION['userid']) or empty($_SESSION['loggedin'])) header("Location: logout.php");

$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$title_pattern = text('Home');

// Adding a bookmark
if (isset($_POST['add-url']) and !empty($_POST['add-url'])) {
	// echo "<script>alert('Add')</script>";
	$msg = addBookmark($userid, $_POST['add-url'], $_POST['add-title']);
}

// Editing a bookmark
if (isset($_POST['edit-title']) and isset($_POST['edit-favid']) and !empty($_POST['edit-favid'])) {
	// echo "<script>alert('Edit')</script>";
	$msg = editBookmark($userid, $_POST['edit-favid'], $_POST['edit-title']);
}

// Deleting a bookmark
if (isset($_POST['delete-favid']) and !empty($_POST['delete-favid'])) {
	// echo "<script>alert('Delete')</script>";
	$msg = deleteBookmark($_POST['delete-favid'], $userid);
}

// Loading home page now
include('head.php');
?>

<div class="modal fade" id="about" tabindex="-1" role="dialog" aria-labelledby="aboutLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
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

<div class="modal fade" id="add-bookmark" tabindex="-1" role="dialog" aria-labelledby="addBookmarkLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
				<h4 class="modal-title" id="settings"><?php echo text('Add Bookmark'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" method="post" role="form">
					<div class="form-group">
						<input type="text" id="add-url" name="add-url" size="100" class="form-control" placeholder="<?php echo text('URL'); ?>" required autofocus />
					</div>
					<div class="form-group">
						<input type="text" id="add-title" name="add-title" size="100" class="form-control" placeholder="<?php echo text('Title (Optional)'); ?>" />
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
						<button type="submit" class="btn btn-primary"><?php echo text('Add'); ?></button>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-bookmark" tabindex="-1" role="dialog" aria-labelledby="editBookmarkLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
				<h4 class="modal-title" id="settings"><?php echo text('Edit Bookmark'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" method="post" role="form">
					<input type="hidden" id="edit-favid" name="edit-favid" value="" class="form-control" />

					<div class="form-group">
						<input type="text" id="edit-url" name="edit-url" size="100" class="form-control" placeholder="<?php echo text('URL'); ?>" required disabled />
					</div>
					
					<div class="form-group">
						<input type="text" id="edit-title" name="edit-title" size="100" class="form-control" placeholder="<?php echo text('Title (Optional)'); ?>" autofocus />
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
						<button type="submit" class="btn btn-primary"><?php echo text('Save'); ?></button>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="delete-bookmark" tabindex="-1" role="dialog" aria-labelledby="deleteBookmarkLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
				<h4 class="modal-title" id="settings"><?php echo text('Delete Bookmark'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" method="post" role="form">
					<input type="hidden" id="delete-favid" name="delete-favid" value="" class="form-control" />
			
					<p><?php echo text('Are you sure you want to permanently delete this bookmark?')?></p>
					<p>
						<span><?php echo text('URL: ')?></span>
						<span><strong id="delete-url"></strong></span>
					</p>
					<p>
						<span><?php echo text('Title: ')?></span>
						<span id="delete-title"></span>
					</p>
			
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Cancel'); ?></button>
						<button type="submit" class="btn btn-danger"><?php echo text('Delete'); ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<a class="navbar-brand" href="home.php">FavNow<sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup></a>
			</div>
		
	        <div class="collapse navbar-collapse" id="navbar-menu">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" data-toggle="modal" data-target="#about"><?php echo text('About'); ?></a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<?php echo $_SESSION['username']; ?> <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="profile.php"><?php echo text('Profile'); ?></a></li>
							<li><a href="preference.php"><?php echo text('Preference'); ?></a></li>
							<li role="presentation" class="divider"></li>
							<li><a href="logout.php"><?php echo text('Logout'); ?></a></li>
						</ul>
					</li>
				</ul>
	        </div>
      </div>
</div>

	
		<div class="container user-page">
			<div class="row">
				<div class="col-xs-12 col-sm-10 col-sm-offset-1">
					<div class="fav-list">
						<div class="panel panel-default">
						
							<div class="panel-heading">
								<div class="pull-right">
									<button data-toggle="modal" data-target="#add-bookmark" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i> <strong><?php echo text('Add Bookmark'); ?></strong></button>
								</div>
								<h4>
									<?php echo text('My Bookmarks'); ?>
								</h4>
							</div>
						
							<div class="panel-body">
								<?php // $msg = 'A quick fox jumped over a lazy dog. A quick fox jumped over a lazy dog.'; ?>
								<?php if (isset($msg) and !empty($msg)) {?>
									<div class="row">
										<div class="alert fade in alert-warning alert-dismissible col-xs-10 col-xs-offset-1" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<?php echo $msg; ?>
										</div>
									</div>
								<?php } ?>
								
								<div class="table-responsive">
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
															<a class="edit-button" href="#" data-toggle="modal" data-target="#edit-bookmark" data-editurl="<?php echo $url; ?>" data-edittitle="<?php echo $title; ?>" data-favid="<?php echo $favid; ?>"><?php echo text('Edit'); ?></a>
														</span>
													</div>
													<div class="fav-list-cell-bottom">
														<span class="fav-list-cell-datetime">
															<?php echo date(text('H:i:s M d, Y'), $time); ?>
														</span>
														<span class="fav-list-cell-service">
															<a class="delete-button" href="#" data-toggle="modal" data-target="#delete-bookmark" data-deleteurl="<?php echo $url; ?>" data-deletetitle="<?php echo $title; ?>" data-favid="<?php echo $favid; ?>"><?php echo text('Delete'); ?></a>
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
		
		$('#add-bookmark').on('shown.bs.modal', function () {
		    $('#add-url').focus();
		})
		
		$('#edit-bookmark').on('shown.bs.modal', function () {
		    $('#edit-title').focus();
		})
		
		$('#edit-bookmark').on('show.bs.modal', function (event) {
			var a = $(event.relatedTarget);
			var modal = $(this);
			modal.find('#edit-url').val(a.data('editurl'));
			modal.find('#edit-title').val(a.data('edittitle'));
			modal.find('#edit-favid').val(a.data('favid'));
		})
		
		$('#delete-bookmark').on('show.bs.modal', function (event) {
			var a = $(event.relatedTarget);
			var modal = $(this);
			modal.find('#delete-url').html(a.data('deleteurl'));
			modal.find('#delete-title').html(a.data('deletetitle'));
			modal.find('#delete-favid').val(a.data('favid'));
		})

		</script>
	</body>
</html>