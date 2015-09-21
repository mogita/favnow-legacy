<?php
session_start();
ini_set('display_errors', 1);

require_once('UI_text.php');

$msg = "";

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'favnow');

// Register Open?
define('REGISTER_OPEN', true);

// Debug mode on? YES to show all error details in the browser.
define('DEBUGGING', true);

define('SITE_URL', 'http://fav.now')
?>