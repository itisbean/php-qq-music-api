<?php

use qqMusic\Api;

include_once __DIR__.'/../src/Api.php';
// include_once __DIR__.'/../src/Ranks.php';
include_once __DIR__.'/../vendor/autoload.php';


$api = new Api();
$ret = $api->searchSong('Goodbye');
var_export($ret);