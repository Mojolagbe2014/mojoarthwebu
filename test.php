<?php
date_default_timezone_set(timezone_name_from_abbr("CDT"));
$date = new DateTime();
echo $date->format('U = Y-m-d H:i:s') . "<br/>";

$date->setTimestamp(time());
echo $date->format('U = Y-m-d H:i:s') . "<br/>";