<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

session_start();
ini_set('display_errors', 0);

require_once('UI_text.php');

$msg = "";

// Database credentials
define('DB_SERVER', 'favnow_db');
define('DB_USER', 'root');
define('DB_PASS', getenv('FAVNOW_DB_ROOT_PASSWORD'));
define('DB_NAME', 'favnow');

// Anyone can play "favnow"
define('REGISTER_OPEN', true);

// Debug mode on? This shows all error details in the browser.
define('DEBUGGING', false);

define('SITEURL', 'https://favnow.mogita.rocks');
?>