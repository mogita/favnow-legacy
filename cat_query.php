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
		if (strlen($name) > 254)
		{
			$return = array(
				"code"    => 233,
				"message" => text('URL too long. Use URL shorteners (e.g. <a href="http://is.gd" target="_blank">http://is.gd</a>) please')
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
						"time" => date(text('H:i:s M d, Y'), $time),
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