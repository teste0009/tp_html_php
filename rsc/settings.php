<?php

set_time_limit(0);
ini_set("memory_limit", "2048M");

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

session_start();

// * * * FIX * * *
// * * * Warning: strtotime(), date(), etc...: It is not safe to rely on the system's timezone settings.  * * *
// * * * You are *required* to use the date.timezone setting or the date_default_timezone_set() function. * * *
$timezone_identifier = "America/Argentina/Buenos_Aires";
date_default_timezone_set($timezone_identifier);


define("DIRTOARRAY_SORT_BY_NAME", 1);
define("DIRTOARRAY_SORT_BY_SIZE", 2);
define("DIRTOARRAY_SORT_BY_TIME", 3);

define("SHOW_MOVIES_FROM_TXT_FILE", 1);
define("SHOW_MOVIES_FROM_PATH_DIR", 2);


?>