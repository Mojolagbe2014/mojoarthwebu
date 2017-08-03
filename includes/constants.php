<?php
/** Database Connection Strings */
define("SITE_URL","http://localhost/mojoarthwebu/");
define("SITE_URL1","http://localhost");
define("DB_NAME","arth"); 
define("DB_USER","root");
define("DB_PASSWORD","mysql");
define("DB_SERVER","localhost");
define("__ROOT__",dirname(dirname(__FILE__)));//
define("CLASSES_PATH", __ROOT__.'/classes/');
define("DB_CONFIG_FILE", __ROOT__.'/DbConfig/Database.php');
define("MEDIA_FILES_PATH", '../media/');
define("MEDIA_FILES_PATH1", SITE_URL.'media/');
date_default_timezone_set(timezone_name_from_abbr("CDT"));