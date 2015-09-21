<?php
/**
 * This file is part of the favnow package.
 *
 * @copyright   2015 mogita <me@mogita.com>
 * @link        https://github.com/mogita/favnow
 * @license     GNU General Public License, version 2 (GPL-2.0)
 */
require_once 'config.php';
require_once 'function.php';
require_once 'fav_query.php';
require_once 'cat_query.php';

if (!isset($_GET['backto']) || empty($_GET['backto'])) exit(text('<h1>Error</h1><p>No URL specified.</p><hr><p><strong>Favnow</strong></p>'));
if (!isset($_GET['user']) || empty($_GET['user'])) exit(text('<h1>Error</h1><p>Please provide your user credentials.</p><hr><p><strong>Favnow</strong></p>'));
if (!isset($_GET['title'])) $title = '';

$title = $_GET['title'];

// $url = parse_url($_GET['backto']);
$url = $_GET['backto'];

$user = getUserByAuth($_GET['user']);
if (!$user) exit(text('<h1>Error</h1><p>Bad user credentials.</p><hr><p><strong>Favnow</strong></p>'));
$userid = $user['id'];

$cats_result = readCategory($userid);
$cats_count = $cats_result[1];
$cats = $cats_result[2];

// Loading home page now
$title_pattern = text('FavNow!');
include('head.php');
?>
<div class="modal fade" id="add-bookmark">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Cancel and back'); ?></span></button>
				<h4 class="modal-title" id="settings"><?php echo text('Add Bookmark'); ?></h4>
			</div>
			<div class="modal-body">
				<form name="add-url-form" id="add-url-form" action="" method="POST">
					<div class="form-group">
						<input type="text" tabindex="0" id="add-url" name="add-url" size="100" class="form-control" placeholder="<?php echo text('URL'); ?>" autofocus required/>
					</div>
					<div class="form-group">
						<input type="text" id="add-title" name="add-title" size="100" class="form-control" placeholder="<?php echo text('Title (Optional)'); ?>"/>
					</div>
					<div class="form-group">
						<select name="category" id="category">
							<option value="0" selected="selected"><?php echo text('Not in category'); ?></option>
							<option disabled><?php echo text('Choose from categories:'); ?></option>
							<?php
							foreach($cats as $cat)
							{
								echo '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
							}
							?>
						</select>
					</div>

					<div class="modal-footer">
						<span class="" id="add-url-message"></span>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Cancel and back'); ?></button>
						<button type="submit" id="add-url-submit" class="btn btn-primary"><?php echo text('Add'); ?></button>
					</div>
					<input type="hidden" name="userid" value="<?php echo $userid; ?>">
				</form>
			</div>
		</div>
	</div>
</div>

<script language="javascript">
	$(window).load(function () {
		$("#add-bookmark").modal({
			show: true,
			backdrop : "static",
			keyboard: false
		});
	});

	var addBookmark = $('#add-bookmark');

	addBookmark.on('hide.bs.modal', function(e){
		// e.preventDefault(); // stops from closing
		window.location = "<?php echo $url; ?>";
	});

	addBookmark.on('show.bs.modal', function (event) {
		var a = $(event.relatedTarget);
		var modal = $(this);
		modal.find('#add-url').val("<?php echo $url; ?>");
		modal.find('#add-title').val("<?php echo $title; ?>");
		$('#add-title').focus();
	});

	$('#add-url-form').submit(function (e) {
		e.preventDefault();
		var addUrlSubmit = $('#add-url-submit');

		addUrlSubmit.addClass('disabled');
		addUrlSubmit.html('<span class="animated infinite flash add-url-indicator"><i class="glyphicon glyphicon-piggy-bank"></i></span>');
		$('#add-url-message').html('');

		var postData = $(this).serializeArray();
		$.ajax({
			url: 'favnow_request.php',
			type: 'POST',
			data: postData,
			success: function (response) {
				addUrlSubmit.removeClass('disabled');
				addUrlSubmit.html('<?php echo text('Add'); ?>');

				response = $.parseJSON(response);
				if (response.code == 200) {
					$('#add-bookmark').modal('hide');
					$('#add-url-message').fadeOut('fast');
					window.location = "<?php echo $url; ?>";

				} else {
					$('#add-url-message').removeClass().addClass('text-danger').html(response.message);
				}
			},
			error: function (xhr, ajaxOptions, errorThrown) {
				addUrlSubmit.removeClass('disabled');
				addUrlSubmit.html('<?php echo text('Add'); ?>');
				$('#add-url-message').removeClass().addClass('text-danger').html('ERROR');
			}
		});
	});
</script>