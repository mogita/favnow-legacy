<?php
require_once 'config.php';
include 'function.php';
include 'fav_query.php';

if (!isset($_SESSION['username']) or empty($_SESSION['username']) or !isset($_SESSION['userid']) or empty($_SESSION['userid']) or !isset($_SESSION['userid']) or  empty($_SESSION['loggedin']) or !$_SESSION['loggedin']) header("Location: logout.php");

$userid = $_SESSION['userid'];
$username = $_SESSION['username'];

$authHash = getAuthById($userid);

$title_pattern = text('Bookmarklet');

// Loading home page now
include('head.php');
?>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="/home.php">FavNow<sup><span style="font-size: 0.4em; margin: 10px; color: #cccccc;">Alpha</span></sup></a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" data-toggle="modal" data-target="#about"><?php echo text('About'); ?></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="bookmarklet.php"><?php echo text('Bookmarklet'); ?></a></li>
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

        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="page-header">
                <h2 style="margin-bottom: 30px;"><?php echo text('Bookmarklet'); ?></h2>
                <?php // $msg = 'A quick fox jumped over a lazy dog. A quick fox jumped over a lazy dog.'; ?>
                <?php if (isset($msg) and !empty($msg)) {?>
                    <div class="row">
                        <div class="alert fade in alert-warning alert-dismissible col-xs-10 col-xs-offset-1" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $msg; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="row hidden-xs well well-lg">
                <div class="col-xs-12">
                    <h5><?php echo text('Drag this button to your bookmark bar'); ?></h5>
                </div>
                <div class="col-xs-12">
                    <a href="<?php echo "javascript:window.location='" . SITEURL . "/favnow.php?backto='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title)+'&user=" . $authHash . "';" ?>" class="btn btn-large btn-primary">FavNow!</a>
                </div>
            </div>

            <div class="row visible-xs well">
                <div class="col-xs-12">
                    <h6><strong><?php echo text('Drag this button to your bookmark bar'); ?></strong></h6>
                </div>
                <div class="col-xs-12">
                    <a href="<?php echo "javascript:document.location='" . SITEURL . "/favnow.php?backto='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title)+'&user=" . $authHash . "';" ?>" class="btn btn-large btn-primary">FavNow!</a>
                </div>
            </div>

            <div class="row hidden-xs">
                <div class="col-xs-12">
                    <h5><i class="glyphicon glyphicon-phone"></i>&nbsp;<?php echo text('If you\'re on a mobile device, where not possible to drag-and-drop, please:'); ?></h5>
                </div>
            </div>

            <div class="row visible-xs">
                <div class="col-xs-12">
                    <h6><i class="glyphicon glyphicon-phone"></i>&nbsp;<strong><?php echo text('If you\'re on a mobile device, where not possible to drag-and-drop, please:'); ?></strong></h6>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <h6><?php echo text('1. Select the text below and copy it as a whole'); ?></h6>
                </div>
                <div class="col-xs-12">
                    <pre id="favnow-bookmarklet" onclick="selectText('favnow-bookmarklet')" style="word-wrap: break-word;"><?php echo "javascript:document.location='" . SITEURL . "/favnow.php?backto='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title)+'&user=" . $authHash . "';" ?></pre>
                </div>
                <div class="col-xs-12">
                    <h6><?php echo text('2. Create a new bookmark, paste this text into the URL box'); ?></h6>
                </div>
                <div class="col-xs-12">
                    <h6><?php echo text('3. Tap the newly saved bookmark when you\'re browsing the web to FavNow!'); ?></h6>
                </div>
            </div>
            <br>
            <br>
        </div>
    </div>
</div>

<script type="text/javascript">
    function selectText(containerid) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
        }
    }
</script>