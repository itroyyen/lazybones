<?php

require dirname(__FILE__).'/lib/debug.php';

define('XDEBUG_TRACE_SCRIPT', dirname($_SERVER['PHP_SELF']).'/debug/dev/xdebug-trace.php');
define('XDEBUG_XT_FILE', dirname(__FILE__).'/dev/data/xdebug-trace');

define('DB_DEBUG', 1);
define('DB_DEBUG_FILE', dirname(__FILE__).'/dev/data/db-debug.dat');
define('DB_DEBUG_SCRIPT', dirname($_SERVER['PHP_SELF']).'/dev/db-debug.php');

debug_start();

?>
