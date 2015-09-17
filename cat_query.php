<?php
/**
 * This file is part of the favnow package.
 *
 * @copyright   2015 mogita <me@mogita.com>
 * @link        https://github.com/mogita/favnow
 * @license     GNU General Public License, version 2 (GPL-2.0)
 */

if (basename($_SERVER['PHP_SELF'] == basename(__FILE__))) die();

/***************** CATEGORY DATA *****************/

function countItemsInCategory($catid)
{

}

function readCategory($userid = 0)
{
	$msg = '';
	$results = array();

	$mysqli = newDBConn();
	$sql = "SELECT id, catname AS `name` FROM cat_terms WHERE userid='" . $userid . "'";

	if ($result = $mysqli->query($sql))
	{
		while ($row = $result->fetch_assoc())
		{
			$results[] = $row;
		}
		$count = $result->num_rows;
	}
	else
	{
		$msg = (DEBUGGING) ? $mysqli->error : '';
		$count = -1;
	}

	$result->free();
	return array($msg, $count, $results);
}

function addCategory($userid = 0, $name = '')
{
	$mysqli = newDBConn();
	if (!empty($name))
	{
		if (strlen($name) > 60)
		{
			$return = array(
				"code"    => 233,
				"message" => text('Category name too long')
			);
		}
		else
		{
			$time = time();
			$name = $mysqli->real_escape_string($name);
			$sql = "INSERT INTO cat_terms (userid, catname, created_at) VALUES ('" . $userid . "', '" . $name . "', '" . $time . "')";
			$result = $mysqli->query($sql);

			if ($result)
			{
				$return = array(
					"code" => 200,
					"message" => array(
						"name" => $name,
						"cat_id" => $mysqli->insert_id
					)
				);
			}
			else
			{
				$return = array(
					"code" => 233,
					"message" => text('There was an error, please try again')
				);
			}
		}
	}
	else
	{
		$return = array(
			"code" => 233,
			"message" => text('Please provide a name')
		);
	}

	echo json_encode($return);
	exit();
}

function editCategory($userid = 0, $catid = 0, $name = '')
{
	$mysqli = newDBConn();
	if (!empty($name))
	{
		if (strlen($name) > 60)
		{
			$return = array(
				"code"    => 233,
				"message" => text('Category name too long')
			);
		}
		else
		{
			$name = $mysqli->real_escape_string($name);
			$sql = "UPDATE cat_terms SET catname='" . $name . "' WHERE id='" . $catid . "' AND userid='" . $userid . "'";
			$result = $mysqli->query($sql);

			if ($mysqli->affected_rows != 1) {
				$return = array(
					"code" => 200,
					"message" => text('Category not modified')
				);
			} elseif ($result) {
				$return = array(
					"code" => 200,
					"message" => array(
						"name" => $name,
						"catid" => $catid
					)
				);
			} else {
				$return = array(
					"code" => 233,
					"message" => text('There was an error saving your category, please try again')
				);
			}
		}
	}
	else
	{
		$return = array(
			"code" => 233,
			"message" => text('Please provide a name')
		);
	}

	echo json_encode($return);
	exit();
}

function deleteCategory($catid, $userid) {
	$mysqli = newDBConn();
	$sql = "DELETE FROM cat_terms WHERE id='".$catid."' AND userid='".$userid."'";
	$result = $mysqli->query($sql);
	$affectedRows = $mysqli->affected_rows;

	if ($affectedRows != 1) {
		$return = array(
			"code" => 233,
			"message" => text('This category does not exist')
		);
	} elseif ($result) {
		$sql = "DELETE FROM cat_relation WHERE cat_id='" . $catid . "'";
		$result = $mysqli->query($sql);

		$return = array(
			"code" => 200,
			"message" => text('Category deleted')
		);
	} else {
		$return = array(
			"code" => 233,
			"message" => text('There were problems deleting, please try again')
		);
	}

	echo json_encode($return);
	exit();
}