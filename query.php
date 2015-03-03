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
	
	$mysqli->set_charset("utf8");
	
	return $mysqli;
}

/***************** BOOKMARK DATA *****************/

function readBookmark($userid, $favID = '', $limit = '') {
	$msg = '';
	$count = 0;
	$results = array();
	
	$mysqli = newDBConn();
	
	if ($favID <> '') {
		$sql = "SELECT * FROM Favs WHERE userid='$userid' AND id='$favID'";
	} else {
		if (isset($limit) and !empty($limit)) {
			$sql = "SELECT * FROM Favs WHERE userid='".$userid."' ORDER BY timepoint DESC LIMIT ".$limit;
		} else {
			$sql = "SELECT * FROM Favs WHERE userid='".$userid."' ORDER BY timepoint DESC";
		}
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


function addBookmark($userid, $url, $title) {
	
	$mysqli = newDBConn();
	if (isset($url) and !empty($url) and isset($userid) and !empty($userid)) {
		if (strlen($url) > 500) {
			$return = array(
				"code" => 233,
				"message" => text('URL too long. Use URL shorteners (e.g. <a href="http://is.gd" target="_blank">http://is.gd</a>) please')
			);
		} /*elseif (!preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $url)) {
			$msg = "URL 格式可能有误。如果你认为是 FavNow 的误判，请将 URL 发送给管理员进行检查。";
		}*/ else {

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

			$url = sanitize($mysqli->real_escape_string($url));
			$title = sanitize($mysqli->real_escape_string($title));
			$hash = md5($url);

			$sql = "INSERT INTO Favs (hash, userid, url, title, timepoint) VALUES ('".$hash."', '".$userid."', '".$url."', '".$title."', '".$time."')";
			$result = $mysqli->query($sql);
			$affectedRows = $mysqli->affected_rows;

			if ($affectedRows != 1) {
				$return = array(
					"code" => 233,
					"message" => text('This URL already exists')
				);
			} elseif ($result) {
				$return = array(
					"code" => 200,
					"message" => array(
						"time" => date(text('H:i:s M d, Y'), $time),
						"title" => $title,
						"url" => $url,
						"favid" => $mysqli->insert_id
					)
				);
			} else {
				$return = array(
					"code" => 233,
					"message" => text('There was an error, please try again')
				);
			}
		}
	} else {
		$return = array(
			"code" => 233,
			"message" => text('There was an error, please try again')
		);
	}
	
	echo json_encode($return);
	exit();
}

function editBookmark($userid, $favID, $title) {
	
	$mysqli = newDBConn();
	$favID = sanitize($mysqli->real_escape_string($favID));
	$resultRead = readBookmark($userid, $favID);
	
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
		
		if (!isset($title) or $title == '') {

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

		$sql = "UPDATE Favs SET title = '$title' WHERE id = '$favID'";
		$result = $mysqli->query($sql);
		$affectedRows = $mysqli->affected_rows;

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
					"favid" => $favID
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


function deleteBookmark($favID, $userid) {	
	$mysqli = newDBConn();
	$sql = "DELETE FROM Favs WHERE id=\"".$favID."\" AND userid=\"".$userid."\"";
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

function getUserFromID($userid) {
	if (!isset($userid) or empty($userid)) {
		// $getUserResult = text('Could not get user information, please reload the page and try again.')
		$getUserResult = false;
	} else {
		$mysqli = newDBConn();
		$sql = "SELECT * FROM Users WHERE id='".$userid."' LIMIT 1";
		
		$result = $mysqli->query($sql);
		
		if ($result->num_rows <> 0) {
			$getUserResult = $result->fetch_array(MYSQLI_NUM);
		} else {
			// $getUserResult = text('Could not get user information, please reload the page and try again.');
			$getUserResult = false;
		}
	}
	
	return $getUserResult;
}

function emailChange($email, $userid) {
	if (!isset($userid) or empty($userid) or !isset($email) or empty($email)) {
		$emailChangeResult = text('Could not change your Email, please reload the page and try again');
	} elseif (!preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $email)) {
		$emailChangeResult = text('Invalid Email format. <a href="mailto:favnow@mogita.com?Subject=Invalid Email format trouble">Contact us</a> if you meet any problem');
	} else {
		$mysqli = newDBConn();
		$sql = "UPDATE Users SET email='".$mysqli->real_escape_string($email)."' WHERE id='".$userid."'";
		
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
		$sql = "SELECT * FROM Users WHERE id='".$userid."' LIMIT 1";
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
				$pwcsql = "UPDATE Users SET password='".$mysqli->real_escape_string($safePassword2)."' WHERE id='".$userid."'";
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