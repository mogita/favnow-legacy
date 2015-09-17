<?php
require_once 'config.php';
include 'function.php';
include 'fav_query.php';
require_once 'cat_query.php';

if (!isset($_SESSION['username']) or empty($_SESSION['username']) or !isset($_SESSION['userid']) or empty($_SESSION['userid']) or !isset($_SESSION['userid']) or empty($_SESSION['loggedin']) or !$_SESSION['loggedin']) header("Location: logout.php");

$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$title_pattern = text('Home');

// category CRUD

if (isset($_POST['add-cat']) and !empty($_POST['add-cat'])) {
    $msg = addCategory($userid, $_POST['add-cat']);
}

if (isset($_POST['edit-name']) and isset($_POST['edit-catid']) and !empty($_POST['edit-catid'])) {
    $msg = editCategory($userid, $_POST['edit-catid'], $_POST['edit-name']);
}

if (isset($_POST['delete-cat-confirm']) and !empty($_POST['delete-cat-confirm'])) {
    $msg = deleteCategory($_POST['delete-cat-confirm'], $userid);
}

// Bookmark CRUD

if (isset($_POST['add-url']) and !empty($_POST['add-url'])) {
    if (isset($_POST['add-title']) and !empty($_POST['add-title'])) {
        $msg = addBookmark($userid, '', $_POST['add-url'], $_POST['add-title']);
    } else {
        $msg = addBookmark($userid, '', $_POST['add-url'], '');
    }
}

if (isset($_POST['edit-title']) and isset($_POST['edit-favid']) and !empty($_POST['edit-favid'])) {
    $msg = editBookmark($userid, $_POST['edit-favid'], $_POST['edit-title']);
}

if (isset($_POST['delete-confirm']) and !empty($_POST['delete-confirm'])) {
    $msg = deleteBookmark($_POST['delete-confirm'], $userid);
}

// Reading bookmarks
if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $cat = $_GET['cat'];
} else {
    $cat = '';
}

$bookmarks = readBookmark($userid, $cat);
$count = $bookmarks[1];
$bookmark = $bookmarks[2];

$cats_result = readCategory($userid);
$cats_count = $cats_result[1];
$cats = $cats_result[2];

// Loading home page now
include('head.php');
?>
<?php // About Dialogue ?>
<div class="modal fade" id="about" tabindex="-1" role="dialog" aria-labelledby="aboutLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only"><?php echo text('Close'); ?></span></button>
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

<?php // Add Category ?>
<div class="modal fade" id="add-category" tabindex="-1" role="dialog" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo text('Close'); ?></span>
                </button>
                <h4 class="modal-title" id="settings"><?php echo text('Add Category'); ?></h4>
            </div>
            <div class="modal-body">
                <form name="add-cat-form" id="add-cat-form" action="" method="POST">
                    <div class="form-group">
                        <input type="text" tabindex="0" id="add-cat" name="add-cat" size="100" class="form-control" placeholder="<?php echo text('Name'); ?>" autofocus required/>
                    </div>
                    <div class="modal-footer">
                        <span class="" id="add-cat-message"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
                        <button type="submit" id="add-cat-submit" class="btn btn-primary"><?php echo text('Add'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Edit Category ?>
<div class="modal fade" id="edit-category" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
                <h4 class="modal-title" id="settings"><?php echo text('Edit Category'); ?></h4>
            </div>
            <div class="modal-body">
                <form name="edit-cat-form" id="edit-cat-form" action="" method="post">
                    <input type="hidden" id="edit-catid" name="edit-catid" value="" class="form-control"/>

                    <div class="form-group">
                        <input type="text" id="edit-name" name="edit-name" size="100" class="form-control" placeholder="<?php echo text('Name'); ?>" required autofocus/>
                    </div>
                    <div class="modal-footer">
                        <span class="" id="edit-cat-message"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
                        <button type="submit" id="edit-cat-submit" class="btn btn-primary"><?php echo text('Save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Add Bookmark ?>
<div class="modal fade" id="add-bookmark" tabindex="-1" role="dialog" aria-labelledby="addBookmarkLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only"><?php echo text('Close'); ?></span></button>
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

                    <div class="modal-footer">
                        <span class="" id="add-url-message"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
                        <button type="submit" id="add-url-submit" class="btn btn-primary"><?php echo text('Add'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Edit Bookmark ?>
<div class="modal fade" id="edit-bookmark" tabindex="-1" role="dialog" aria-labelledby="editBookmarkLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo text('Close'); ?></span></button>
                <h4 class="modal-title" id="settings"><?php echo text('Edit Bookmark'); ?></h4>
            </div>
            <div class="modal-body">
                <form name="edit-url-form" id="edit-url-form" action="" method="post">
                    <input type="hidden" id="edit-favid" name="edit-favid" value="" class="form-control"/>

                    <div class="form-group">
                        <input type="text" id="edit-url" name="edit-url" size="100" class="form-control" placeholder="<?php echo text('URL'); ?>" required disabled/>
                    </div>

                    <div class="form-group">
                        <input type="text" id="edit-title" name="edit-title" size="100" class="form-control" placeholder="<?php echo text('Title (Optional)'); ?>" autofocus/>
                    </div>

                    <div class="modal-footer">
                        <span class="" id="edit-url-message"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo text('Close'); ?></button>
                        <button type="submit" id="edit-url-submit" class="btn btn-primary"><?php echo text('Save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Navigation Bar ?>
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
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="pull-right">
                        <button data-toggle="modal" data-target="#add-category" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;<?php echo text('Add Category'); ?>
                        </button>
                    </div>
                    <h6><?php echo text('Category'); ?></h6>
                </div>

                <ul class="list-group">
                    <li id="0" class="cat-list-cell list-group-item<?php echo (isset($_GET['cat']) && !empty($_GET['cat'])) ? '" onclick="showcat(this.id)' : ' active' ; ?>">
                        <a href="home.php" class="cat-list-item" id="cat-list-cell-0"<?php echo (isset($_GET['cat']) && !empty($_GET['cat'])) ? '' : ' style="color: #fff;"' ; ?>><strong><?php echo text('All Bookmarks'); ?></strong></a>
                        <?php if (1==0) : ?><span class="badge"><?php echo $count; ?></span><?php endif; ?>
                    </li>
                    <?php
                    if ($cats_count < 0) {
                        ?>
                        <strong><?php echo text('Unable to fetch categories: ') . $cats_result[0]; ?></strong>
                        <?php
                    } elseif ($cats_count == 0) {
                        ?>
                        <strong><?php // echo text('No category yet'); ?></strong>
                        <?php
                    } else {
                        foreach ($cats as $cat) {
                            $category = $cat['name'];
                            $catid = $cat['id'];
                            ?>
                            <li id="<?php echo $cat['id']; ?>" class="cat-list-cell list-group-item<?php echo (isset($_GET['cat']) && $_GET['cat'] == $catid) ? ' active' : '" onclick="showcat(this.id)' ; ?>">
                                <a href="/home.php?cat=<?php echo $catid; ?>" class="cat-list-item" id="cat-list-cell-<?php echo $catid; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat'] == $catid) ? ' style="color: #fff;"' : '' ; ?>><?php echo $category; ?></a>
                                <span class="pull-right list-group-item-text cat-list-cell-service<?php echo (isset($_GET['cat']) && $_GET['cat'] == $catid) ? '' : ' hide' ; ?>" style="line-height: 2;">
                                    <a class="edit-button" data-toggle="modal" data-target="#edit-category" data-edit-name="<?php echo $cat['name']; ?>" data-edit-catid="<?php echo $cat['id']; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat'] == $catid) ? ' style="color: #fff;"' : '' ; ?>>[&nbsp;&nbsp;<?php echo text('Edit'); ?></a>
                                    <a tabindex="0" class="delete-cat-button" data-toggle="popover" data-placement="right" data-content='<button class="btn btn-danger delete-button-confirm" id="<?php echo $cat['id']; ?>" onclick="deleteCategoryConfirm(this.id)"><?php echo text('Delete'); ?></button>'><i class="glyphicon glyphicon-trash"<?php echo (isset($_GET['cat']) && $_GET['cat'] == $catid) ? ' style="color: #fff;"' : '' ; ?>></i></a>&nbsp;&nbsp;]
                                </span>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="fav-list">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <div class="pull-right">
                            <button data-toggle="modal" data-target="#add-bookmark" class="btn btn-default"><i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;<strong><?php echo text('Add Bookmark'); ?></strong>
                            </button>
                        </div>
                        <h4>
                            <?php echo text('My Bookmarks'); ?>
                        </h4>
                    </div>

                    <div class="panel-body">
                        <?php // $msg = 'A quick fox jumped over a lazy dog. A quick fox jumped over a lazy dog.'; ?>
                        <?php if (isset($msg) and !empty($msg)) : ?>
                            <div class="row">
                                <div class="alert fade in alert-warning alert-dismissible col-xs-10 col-xs-offset-1"
                                     role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <?php echo $msg; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="fav-list-content">
                            <div id="before-first"></div>
                            <div class="no-bookmark-label text-right hide">
                                <h5>
                                    <span class="well">
                                        <?php echo text('No bookmarks yet? Start to save them right away!'); ?>
                                        <i class="glyphicon glyphicon-arrow-up"></i>
                                    </span>
                                </h5>
                            </div>
                            <?php
                            if ($count < 0) {
                                ?>
                                <h4>
                                    <span class="label label-error">
                                        <?php echo text('Unable to fetch bookmarks: ') . $bookmarks[0]; ?>
                                    </span>
                                </h4>
                            <?php } elseif ($count == 0) { ?>
                                <script>$('.no-bookmark-label').removeClass('hide');</script>
                            <?php } else {
                            foreach ($bookmark as $row) {
                            $favid = $row['id'];
                            $url = $row['url'];
                            $title = $row['title'];
                            $time = $row['timepoint'];
                            ?>
                                <article class="fav-list-cell" id="fav-list-cell-<?php echo $favid; ?>">
                                    <div class="fav-list-inner-item">
                                        <div class="fav-list-cell-top">
                                            <span class="fav-list-cell-title">
                                                <a class="fav-list-cell-link" href="<?php echo $url; ?>"
                                                   title="<?php echo $title; ?>"
                                                   target="_blank"><?php echo $title; ?></a>
                                            </span>
                                        </div>
                                        <div class="fav-list-cell-bottom">
                                            <span class="fav-list-cell-datetime">
                                                <?php echo date(text('H:i:s M d, Y'), $time); ?>
                                            </span>
                                            <span class="fav-list-cell-service">
                                                <a class="edit-button" data-toggle="modal" data-target="#edit-bookmark" data-editurl="<?php echo $url; ?>" data-edittitle="<?php echo $title; ?>" data-favid="<?php echo $favid; ?>"><?php echo text('Edit'); ?></a>
                                                <a tabindex="0" class="delete-button" data-toggle="popover" data-placement="top" data-content='<button class="btn btn-danger delete-button-confirm" id="<?php echo $favid; ?>" onclick="deleteConfirm(this.id)"><?php echo text('Delete'); ?></button>'><i class="glyphicon glyphicon-trash"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                                <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="javascript">
    $(function () {
        $('.fav-list-cell-service').hide();
        $('.fav-list-cell').hover(showCellService, hideCellService);

        $.notifyDefaults({
            newest_on_top: true,
            placement: {
                from: 'top',
                align: 'center'
            },
            z_index: 1031,
            timer: 100,
            offset: {
                x: 0,
                y: 8
            },
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
            template: '<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert>"' +
            '<span data-notify="message">{2}</span>' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button>' +
            '</div>'
        });
    });

    function showCellService() {
        $(this).find('.fav-list-cell-service').show();
    }

    function hideCellService(e) {
        $(this).find('.fav-list-cell-service').hide();
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    }

    function showcat(id)
    {
        if (id == 0)
        {
            window.location.replace('/home.php');
        }
        else
        {
            window.location.replace('/home.php?cat=' + id);
        }
    }

    function deleteCategoryConfirm(id) {
        $('.delete-cat-button').popover('hide');
        last_id = $('.cat-list-cell#' + id).prev().attr('id');
        $.ajax({
            type: 'POST',
            url: 'home.php',
            data: 'delete-cat-confirm=' + id,
            success: function (response) {
                response = $.parseJSON(response);
                if (response.code == 200) {
                    $('.cat-list-cell#' + id).addClass('animated zoomOut').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        $('.cat-list-cell#' + id).remove();
                        showcat(last_id);
                    });
                    // $.notify(response.message, {type: 'danger'});
                } else {
                    $.notify(response.message, {
                        type: 'danger'
                    });
                }
            }
        });
    }

    function deleteConfirm(id) {
        $('.delete-button').popover('hide');
        $.ajax({
            type: 'POST',
            url: 'home.php',
            data: 'delete-confirm=' + id,
            success: function (response) {
                response = $.parseJSON(response);
                if (response.code == 200) {
                    $('#fav-list-cell-' + id).addClass('animated zoomOut').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        $('#fav-list-cell-' + id).remove();
                        if ($('div.fav-list-content > article').length <= 0) {
                            $('.no-bookmark-label').removeClass('hide');
                        }
                    });
                    // $.notify(response.message, {type: 'danger'});
                } else {
                    $.notify(response.message, {
                        type: 'danger'
                    });
                }
            }
        });
    }

    $('#edit-cat-form').submit(function (e) {
        e.preventDefault();

        $('#edit-cat-submit').addClass('disabled');
        $('#edit-cat-submit').html('<span class="animated infinite flash edit-cat-indicator"><i class="glyphicon glyphicon-piggy-bank"></i></span>');
        $('#edit-cat-message').html('');

        var postData = $(this).serializeArray();
        $.ajax({
            url: 'home.php',
            type: 'POST',
            data: postData,
            success: function (response) {
                $('#edit-cat-submit').removeClass('disabled');
                $('#edit-cat-submit').html('<?php echo text('Save'); ?>');

                // alert(response);
                response = $.parseJSON(response);
                if (response.code == 200) {
                    $('#edit-category').modal('hide');
                    $('#edit-name').val('');
                    $('#edit-cat-message').fadeOut('fast');

                    name = response.message.name;
                    catid = response.message.catid;
                    $('a#cat-list-cell-' + catid).html(name);
                    $('a#cat-list-cell-' + catid + ' > span > a.edit-button').attr({
                        'data-editcat': name,
                        'data-catid': catid
                    });

                    $('a[data-toggle=popover]').popover({
                        html: 'true'
                    });

                    $('a#cat-list-cell-' + catid).addClass('animated zoomIn').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        $('a#cat-list-cell-' + catid).removeClass('animated zoomIn');
                    });
                } else {
                    $('#edit-cat-message').removeClass().addClass('text-danger').html(response.message);
                }
            },
            error: function (xhr, ajaxOptions, errorThrown) {
                $('#edit-cat-submit').removeClass('disabled');
                $('#edit-cat-submit').html('<?php echo text('Add'); ?>');
                $('#edit-cat-message').removeClass().addClass('text-danger').html('ERROR');
            }
        });
    });

    $('#edit-url-form').submit(function (e) {
        e.preventDefault();

        $('#edit-url-submit').addClass('disabled');
        $('#edit-url-submit').html('<span class="animated infinite flash edit-url-indicator"><i class="glyphicon glyphicon-piggy-bank"></i></span>');
        $('#edit-url-message').html('');

        var postData = $(this).serializeArray();
        $.ajax({
            url: 'home.php',
            type: 'POST',
            data: postData,
            success: function (response) {
                $('#edit-url-submit').removeClass('disabled');
                $('#edit-url-submit').html('<?php echo text('Save'); ?>');

                // alert(response);
                response = $.parseJSON(response);
                if (response.code == 200) {
                    $('.no-bookmark-label').addClass('hide');

                    $('#edit-bookmark').modal('hide');
                    $('#edit-url').val('');
                    $('#edit-title').val('');
                    $('#edit-url-message').fadeOut('fast');

                    url = response.message.url;
                    title = response.message.title;
                    favid = response.message.favid;
                    // alert(url + ' ' + title + ' ' + favid );
                    $('article#fav-list-cell-' + favid + ' > div > div > span > a.fav-list-cell-link').attr({
                        'href': url,
                        'title': title
                    }).html(title);
                    $('article#fav-list-cell-' + favid + ' > div > div > span > a.edit-button').attr({
                        'data-editurl': url,
                        'data-edittitle': title
                    })

                    $('.fav-list-cell-service').hide();
                    $('.fav-list-cell').hover(showCellService, hideCellService);
                    $('a[data-toggle=popover]').popover({
                        html: 'true'
                    })

                    $('article#fav-list-cell-' + favid + ' > div > div > span > a.fav-list-cell-link').addClass('animated zoomIn').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        $('article#fav-list-cell-' + favid + ' > div > div > span > a.fav-list-cell-link').removeClass('animated zoomIn');
                    });
                } else {
                    $('#edit-url-message').removeClass().addClass('text-danger').html(response.message);
                }
            },
            error: function (xhr, ajaxOptions, errorThrown) {
                $('#edit-url-submit').removeClass('disabled');
                $('#edit-url-submit').html('<?php echo text('Add'); ?>');
                $('#edit-url-message').removeClass().addClass('text-danger').html('ERROR');
            }
        });
    });

    $('#add-cat-form').submit(function (e) {
        e.preventDefault();

        $('#add-cat-submit').addClass('disabled');
        $('#add-cat-submit').html('<span class="animated infinite flash add-url-indicator"><i class="glyphicon glyphicon-piggy-bank"></i></span>');
        $('#add-cat-message').html('');

        var postData = $(this).serializeArray();
        $.ajax({
            url: 'home.php',
            type: 'POST',
            data: postData,
            success: function (response) {
                $('#add-cat-submit').removeClass('disabled');
                $('#add-cat-submit').html('<?php echo text('Add'); ?>');

                // alert(response);
                response = $.parseJSON(response);
                if (response.code == 200) {
                    $('#add-category').modal('hide');
                    $('#add-cat').val('');
                    $('#add-cat-message').fadeOut('fast');

                    name = response.message.name;
                    cat_id = response.message.cat_id;
                    url = 'home.php?cat=' + cat_id;
                    $('<li id="' + cat_id + '" class="cat-list-cell list-group-item" onclick="showcat(this.id)"><a href="/home.php?cat="' + cat_id + ' class="cat-list-item" id="cat-list-cell-' + cat_id + '">' + name + '</a><span class="pull-right list-group-item-text cat-list-cell-service hide" style="line-height: 2;"><a class="edit-button" data-toggle="modal" data-target="#edit-category" data-edit-name="' + name + '" data-edit-catid="' + cat_id + '">[&nbsp;&nbsp;<?php echo text('Edit'); ?></a><a tabindex="0" class="delete-cat-button" data-toggle="popover" data-placement="right" data-content="<button class=\"btn btn-danger delete-button-confirm\" id=\"' + cat_id + '\" onclick="deleteCategoryConfirm(this.id)"><?php echo text('Delete'); ?></button>"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;&nbsp;]</span></li>').insertAfter('.cat-list-cell:last');
                } else {
                    $('#add-cat-message').removeClass().addClass('text-danger').html(response.message);
                }
            },
            error: function (xhr, ajaxOptions, errorThrown) {
                $('#add-cat-submit').removeClass('disabled');
                $('#add-cat-submit').html('<?php echo text('Add'); ?>');
                $('#add-cat-message').removeClass().addClass('text-danger').html('ERROR');
            }
        });
    });

    $('#add-url-form').submit(function (e) {
        e.preventDefault();

        $('#add-url-submit').addClass('disabled');
        $('#add-url-submit').html('<span class="animated infinite flash add-url-indicator"><i class="glyphicon glyphicon-piggy-bank"></i></span>');
        $('#add-url-message').html('');

        var postData = $(this).serializeArray();
        $.ajax({
            url: 'home.php',
            type: 'POST',
            data: postData,
            success: function (response) {
                $('#add-url-submit').removeClass('disabled');
                $('#add-url-submit').html('<?php echo text('Add'); ?>');

                // alert(response);
                response = $.parseJSON(response);
                if (response.code == 200) {
                    $('#add-bookmark').modal('hide');
                    $('#add-url').val('');
                    $('#add-title').val('');
                    $('#add-url-message').fadeOut('fast');

                    url = response.message.url;
                    title = response.message.title;
                    time = response.message.time;
                    favid = response.message.favid;
                    $('<article class="fav-list-cell animated zoomIn" id="fav-list-cell-' + favid + '"><div class="fav-list-inner-item"><div class="fav-list-cell-top"><span class="fav-list-cell-title"><a class="fav-list-cell-link" href="' + url + '" title="' + title + '" target="_blank">' + title + '</a></span></div><div class="fav-list-cell-bottom"><span class="fav-list-cell-datetime">' + time + '</span><span class="fav-list-cell-service"><a class="edit-button" href="#" data-toggle="modal" data-target="#edit-bookmark" data-editurl="' + url + '" data-edittitle="' + title + '" data-favid="' + favid + '"><?php echo text('Edit'); ?></a><a tabindex="0" class="delete-button" data-toggle="popover" data-trigger="focus" data-placement="top" data-content=\'<button class="btn btn-danger delete-button-confirm" id="' + favid + '" onclick="deleteConfirm(this.id)"><?php echo text('Delete'); ?></button>\'><i class="glyphicon glyphicon-trash"></i></a></span></div></div></article>').insertAfter('#before-first');
                    $('.fav-list-cell-service').hide();
                    $('.fav-list-cell').hover(showCellService, hideCellService);
                    $('a[data-toggle=popover]').popover({
                        html: 'true'
                    });
                    if ($('div.fav-list-content > article').length > 0) {
                        $('.no-bookmark-label').addClass('hide');
                    }
                } else {
                    $('#add-url-message').removeClass().addClass('text-danger').html(response.message);
                }
            },
            error: function (xhr, ajaxOptions, errorThrown) {
                $('#add-url-submit').removeClass('disabled');
                $('#add-url-submit').html('<?php echo text('Add'); ?>');
                $('#add-url-message').removeClass().addClass('text-danger').html('ERROR');
            }
        });
    });

    $('#add-category').on('shown.bs.modal', function () {
        $('#add-cat').focus();
    });

    $('#add-bookmark').on('shown.bs.modal', function () {
        $('#add-url').focus();
    });

    $('#edit-category').on('shown.bs.modal', function () {
        $('#edit-name').focus();
    });

    $('#edit-bookmark').on('shown.bs.modal', function () {
        $('#edit-title').focus();
    });

    $('#edit-category').on('show.bs.modal', function (event) {
        var a = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#edit-catid').val(a.data('edit-catid'));
        modal.find('#edit-name').val(a.data('edit-name'));
    });

    $('#edit-bookmark').on('show.bs.modal', function (event) {
        var a = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#edit-url').val(a.data('editurl'));
        modal.find('#edit-title').val($('article#fav-list-cell-' + a.data('favid') + ' > div > div > span > a.edit-button').attr('data-edittitle'));
        modal.find('#edit-favid').val(a.data('favid'));
    });

    $('a[data-toggle=popover]').popover({
        html: 'true'
    });

    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
</script>
</body>
</html>