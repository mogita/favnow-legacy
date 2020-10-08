<?php
/*
 * Functionalities definition for Favnow
 * Author: mogita
 * 
*/

if (basename($_SERVER['PHP_SELF'] == basename(__FILE__))) die();


// Trimming strings method from Discuz!
function cutstr($string, $length, $charset, $dot = '...') {
	if(strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	if(strtolower($charset) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}

// String sanitizer from tutorialzine.com
function sanitize($str)
{
	if(ini_get('magic_quotes_gpc'))
		$str = stripslashes($str);

	$str = strip_tags($str);
	$str = trim($str);
	$str = htmlspecialchars($str);
	// $str = mysql_real_escape_string($str);

	return $str;
}

// Password salter. Password max length is 32.
function safePassword ($pw, $username) {
	$pw = strip_tags(substr($pw, 0, 32));
	$salted = crypt(md5($pw), md5($username).'romeoyjulieta');
	
	return $salted;
}

// Auth code generator. For the use of bookmarklet etc.
function authCode($userid, $username, $pw) {
	$pw = strip_tags(substr($pw, 0, 32));
	$authCode = crypt(md5($pw), md5($username).'monmilk');
	
	return $authCode;
}

// Getting HTML content from URL
function getHTML($url) {
	$msg = '';
	
	if (isset($url) and !empty($url)) {
		$ch = curl_init();
		$timeout = 10;
		$ua = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36";
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		
		// curl_setopt($ch, CURLOPT_HEADER, 1);
		// curl_setopt($ch, CURLOPT_NOBODY, 1);
		
		$msg = curl_exec($ch);

		curl_close($ch);
		
	} else {
		$msg = false;
	}
	
	return $msg;
}

