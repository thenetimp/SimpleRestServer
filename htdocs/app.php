<?php

echo "<pre>";

define("BASE_DIR", realpath(dirname(__FILE__) . '/../'));    
require_once(BASE_DIR . '/vendor/autoload.php');
$kernel = new \Component\Kernel(BASE_DIR, true);
