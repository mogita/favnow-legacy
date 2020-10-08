<?php
session_start();
ini_set('display_errors', 0);

require_once('UI_text.php');

$msg = "";

// Database credentials
define('DB_SERVER', 'favnow_db');
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASS', getenv('MYSQL_PASSWORD'));
define('DB_NAME', 'favnow');

// Anyone can play "favnow"
define('REGISTER_OPEN', true);

// Debug mode on? This shows all error details in the browser.
define('DEBUGGING', false);

define('SITEURL', getenv('VIRTUAL_HOST'));
?>