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

if (isset($_POST['add-url']) && !empty($_POST['add-url']) && isset($_POST['userid']) && !empty($_POST['userid'])) {
	$put_category = isset($_POST['category']) && !empty($_POST['category']) ? $_POST['category'] : 0;
	$title = isset($_POST['add-title']) && !empty($_POST['add-title']) ? $_POST['add-title'] : '';

	addBookmark($_POST['userid'], '', $_POST['add-url'], $title, $put_category);
}