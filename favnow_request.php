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

if (isset($_POST['add-url']) and !empty($_POST['add-url'])) {
	if (isset($_POST['category']) && !empty($_POST['category']))
	{
		$put_category = $_POST['category'];
	}
	else
	{
		$put_category = 0;
	}
	if (isset($_POST['add-title']) and !empty($_POST['add-title'])) {
		addBookmark($_POST['userid'], '', $_POST['add-url'], $_POST['add-title'], $put_category);
	} else {
		addBookmark($_POST['userid'], '', $_POST['add-url'], '', $put_category);
	}
}