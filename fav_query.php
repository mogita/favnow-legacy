<?php
/*
 * Query functions for Favnow
 * Author: mogita 
 * 
*/

if (basename($_SERVER['PHP_SELF'] == basename(__FILE__))) die();

function newDBConn() {
	$mysqli = new Mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

	if ($mysqli->connect_errno > 0) {
		if (DEBUGGIN) {
			die(text('Unable to establish database connection: ').$mysqli->connect_error);
		} else {
			die(text('Unable to establish database connection'));
		}
	}

	$mysqli->set_charset("utf8mb4");

	return $mysqli;
}

/***************** BOOKMARK DATA *****************/

function readBookmark($userid, $catid = '', $favid = '', $amount = 25, $page = 1) {
	$msg = '';
	$results = array();

	$mysqli = newDBConn();

	if (!empty($favid)) {
		$sql = "SELECT * FROM favs WHERE userid='$userid' AND id='$favid'";
	} else {
        $sql = "SELECT * FROM favs";

        if (!empty($catid)) {
            $sql .= " JOIN cat_relation ON cat_relation.obj_id = favs.id";
        }

        $sql .= " WHERE favs.userid='" . $userid . "' ORDER BY favs.timepoint DESC LIMIT " . ($page - 1) * $amount . ", " . $amount;
	}

	if ($result = $mysqli->query($sql)) {

		while ($row = $result->fetch_assoc()) {
			$results[] = $row;
		}

		$count = $result->num_rows;

	} else {
		$msg = (DEBUGGING) ? $mysqli->error : '';
		$count = -1;
	}

	$result->free();
	return array($msg, $count, $results);
}


function addBookmark($userid = '', $authcode = '', $url, $title, $category = 0) {

	if (isset($authcode) and !empty($authcode)) {
		$id = getUserByAuth($authcode);

		if ($id) {
			$userid = $id[0];
		} else {
			$return = array(
				"code" => 233,
				"message" => text('Invalid User')
			);

			echo json_encode($return);
			exit ();
		}
	}


	$mysqli = newDBConn();
	if (isset($url) and !empty($url) /*and isset($userid) and !empty($userid)*/) {
		if (strlen($url) > 500) {
			$return = array(
				"code" => 233,
				"message" => text('URL too long. Use URL shorteners (e.g. <a href="http://is.gd" target="_blank">http://is.gd</a>) please')
			);
		} /*elseif (!preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $url)) {
			$msg = "URL 格式可能有误。如果你认为是 FavNow 的误判，请将 URL 发送给管理员进行检查。";
		}*/ else {

			if (!empty($userid) or !empty($userid)) {
				$time = time();
				if (!preg_match("~^(?:f|ht)tps?://~i", $url)) $url = "http://".$url;

				if (!isset($title) or $title == '') {
					$content = getHTML($url);

					if (strlen($content) > 0) {
						preg_match("/\<title\>(.*)\<\/title\>/", $content, $title);
						if (isset($title[1]) and $title[1] <> '') {
							$title = $title[1];
						} else {
							$title = $url;
						}
					} else {
						$title = $url;
					}

				}

				$url = $mysqli->real_escape_string($url);
				$title = $mysqli->real_escape_string($title);
				$hash = md5($url);

				$sql = "INSERT INTO favs (hash, userid, url, title, timepoint) VALUES ('".$hash."', '".$userid."', '".$url."', '".$title."', '".$time."')";

				if (!empty($userid)) {

				} elseif (!empty($authcode)) {
					$sql = "INSERT INTO favs (hash, userid, url, title, timepoint) VALUES ('".$hash."', '".$userid."', '".$url."', '".$title."', '".$time."')";
				}

				$result = $mysqli->query($sql);
				$affectedRows = $mysqli->affected_rows;

				if ($affectedRows != 1) {
					$return = array(
						"code" => 233,
						"message" => text('This URL already exists')
					);
				} elseif ($result) {
					$latest_favid = $mysqli->insert_id;
					$sql = "INSERT INTO cat_relation (userid, obj_id, cat_id, created_at) VALUES (" . $userid . ", " . $latest_favid . ", " . $category  . ", " . time() . ")";
					$mysqli->query($sql);

					$return = array(
						"code" => 200,
						"message" => array(
							"time" => date(text('H:i:s M d, Y'), $time),
							"title" => $title,
							"url" => $url,
							"favid" => $latest_favid
						)
					);
				} else {
					$return = array(
						"code" => 233,
						"message" => text('There was an error, please try again')
					);
				}

			} else {
				$return = array(
					"code" => 233,
					"message" => text('Invalid request')
				);
			}
		}
	} else {
		$return = array(
			"code" => 233,
			/*"message" => text('There was an error, please try again')*/
			"message" => text('Please provide a URL')
		);
	}

	echo json_encode($return);
	exit();
}

function editBookmark($userid, $favid, $title, $category = 0) {

	$mysqli = newDBConn();
	$favid = sanitize($mysqli->real_escape_string($favid));
	$resultRead = readBookmark($userid, '', $favid);

	$count = $resultRead[1];
	$bookmark = $resultRead[2];

	if ($count <= 0) {
		$return = array(
			"code" => 233,
			"message" => text('The bookmark could not be located, please try again')
		);
	} else {
		foreach ($bookmark as $row) {
			$url = $row['url'];
		}

		if (!isset($title) or $title == '')
		{
			$content = getHTML($url);

			if (!$content) {
				$title = $url;

			} elseif (strlen($content) > 0) {
				preg_match("/\<title\>(.*)\<\/title\>/", $content, $title);
				if (isset($title[1]) and $title[1] <> '') {
					$title = $title[1];
				} else {
					$title = $url;
				}
			} else {
				$title = $url;
			}
		}

		$title = sanitize($mysqli->real_escape_string($title));

		$sql = "UPDATE favs SET title = '$title' WHERE id = '$favid'";
		$result = $mysqli->query($sql);
		$affectedRows = $mysqli->affected_rows;

        if ($category == 0)
        {
            $sql = "DELETE FROM cat_relation WHERE obj_id='$favid'";
        }
        else
        {
            $sql = "INSERT INTO cat_relation (userid, obj_id, cat_id, created_at) VALUES (" . $userid . ", " . $favid . ", " . $category . ", " . time() . ") ON DUPLICATE KEY UPDATE cat_id=VALUES(cat_id)";
        }

		$mysqli->query($sql);

		if ($affectedRows != 1) {
			$return = array(
				"code" => 200,
				"message" => text('Bookmark not modified')
			);
		} elseif ($result) {
			$return = array(
				"code" => 200,
				"message" => array(
					"title" => $title,
					"url" => $url,
					"favid" => $favid
				)
			);
		} else {
			$return = array(
				"code" => 233,
				"message" => text('There was an error saving your bookmark, please try again')
			);
		}
	}

	echo json_encode($return);
	exit();
}

function deleteBookmark($favid, $userid) {
	$mysqli = newDBConn();
	$sql = "DELETE FROM favs WHERE id=\"".$favid."\" AND userid=\"".$userid."\"";
	$result = $mysqli->query($sql);
	$affectedRows = $mysqli->affected_rows;

	if ($affectedRows != 1) {
		$return = array(
			"code" => 233,
			"message" => text('This bookmark does not exist')
		);
	} elseif ($result) {
		$return = array(
			"code" => 200,
			"message" => text('Bookmark deleted')
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


/***************** USER DATA *****************/

function getUserByID($userid) {
	if (!isset($userid) or empty($userid)) {
		$getUserResult = false;
	} else {
		$mysqli = newDBConn();
		$sql = "SELECT * FROM users WHERE id='".$userid."' LIMIT 1";

		$result = $mysqli->query($sql);

		if ($result->num_rows <> 0) {
			$getUserResult = $result->fetch_array(MYSQLI_NUM);
		} else {
			$getUserResult = false;
		}
	}

	return $getUserResult;
}

function getUserByAuth($authcode) {
	if (!isset($authcode) or empty($authcode)) {
		$return = false;
	} else {
		$mysqli = newDBConn();
		$sql = "SELECT * FROM users WHERE authcode='".$authcode."' LIMIT 1";
		$result = $mysqli->query($sql);
		if ($result->num_rows != 0) {
			$return = $result->fetch_array(MYSQLI_NUM);
		} else {
			$return = false;
		}
	}

	return $return;
}

function emailChange($email, $userid) {
	if (!isset($userid) or empty($userid) or !isset($email) or empty($email)) {
		$emailChangeResult = text('Could not change your Email, please reload the page and try again');
	} elseif (!preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $email)) {
		$emailChangeResult = text('Invalid Email format. <a href="mailto:favnow@mogita.com?Subject=Invalid Email format trouble">Contact us</a> if you meet any problem');
	} else {
		$mysqli = newDBConn();
		$sql = "UPDATE users SET email='".$mysqli->real_escape_string($email)."' WHERE id='".$userid."'";

		$result = $mysqli->query($sql);

		if ($result) {
			$emailChangeResult = text('Email changed to ').$email;
		} else {
			$emailChangeResult = text('Could not change your Email, please try again later');
		}
	}

	return $emailChangeResult;
}

function pwChange($pwc1, $pwc2, $pwc3, $userid, $username) {
	// $pwcmsg = 'You wanted a change';

	// 先判断是否有 userid，若无就返回错误；若有则进行数据库操作逻辑。

	if ($userid == '' or !isset($userid) or $username == '' or !isset($username)) {

		// Something wrong with the session.
		$pwcmsg = text('Could not change your password, please reload the page and try again');

	} elseif ($pwc2 <> $pwc3) {

		// New passwords don't match
		$pwcmsg = text('New passwords mismatch, please try again');

	} else {

		$mysqli = newDBConn();
		$sql = "SELECT * FROM users WHERE id='".$userid."' LIMIT 1";
		$result = $mysqli->query($sql);

		if (!$result) {
			$pwcmsg = text('Could not change your password, please reload the page and try again');
		} else {
			$row = $result->fetch_array(MYSQLI_NUM);
			$oldPassword = $row[2];
			$safePassword1 = safePassword($pwc1, $username);
			$safePassword2 = safePassword($pwc2, $username);

			if ($oldPassword <> $safePassword1) {
				$pwcmsg = text('Current password incorrect, please try again');
			} else {
				$pwcsql = "UPDATE users SET password='".$mysqli->real_escape_string($safePassword2)."' WHERE id='".$userid."'";
				$pwcresult = $mysqli->query($pwcsql);

				if (!$pwcresult) {
					$pwcmsg = text('Could not change your password, please try again');
				} else {
					$_SESSION['warncode'] = text('Password successfully changed. Please login');
					header("Location: logout.php");
				}
			}
		}
	}


	return $pwcmsg;
}