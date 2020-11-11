<?php

use qqMusic\Api;

include_once __DIR__.'/../src/Api.php';
// include_once __DIR__.'/../src/Ranks.php';
include_once __DIR__.'/../vendor/autoload.php';


$api = new Api();
$ret = $api->searchSinger('容祖儿');
var_export($ret);