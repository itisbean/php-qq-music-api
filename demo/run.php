<?php

use qqMusic\Api;

include_once __DIR__.'/../src/Api.php';
include_once __DIR__.'/../src/ChartConfig.php';
include_once __DIR__.'/../vendor/autoload.php';


$api = new Api();

// 搜索歌手
// $ret = $api->searchSinger('容祖儿');
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 歌手歌曲列表
// $ret = $api->getSonglistBySinger('001uXFgt1kpLyI', 1, 100);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 歌手专辑列表
// $ret = $api->getAlbumlistBySinger('001uXFgt1kpLyI', 1, 10);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 专辑歌曲列表
// $ret = $api->getAlbumSongs('00092YgX3cowE2');
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 获取歌曲评论数
// $ret = $api->getSongCommentCount(234276764);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 获取专辑评论数
// $ret = $api->getAlbumCommentCount(7144823);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 获取歌曲流行指数
// $ret = $api->getMusicHitInfo('001Eo1rF3gYD09,003Inmwn25vHfD');
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 获取歌曲收藏数
// $ret = $api->getMusicFavNum('280494291,280494264');
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 榜单数据
// $ret = $api->getRankChart(\qqMusic\ChartConfig::BILLBOARD_CHART);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 歌曲排行榜数据
// $ret = $api->getSongsRankInfo([280494291,280494264]);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 歌手排行榜数据
// $ret = $api->getSingersRankInfo(['001uXFgt1kpLyI']);
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 巅峰人气榜
// $ret = $api->getHitList('all', '000sVzZ83BIMjq');
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 专辑热销榜
// $ret = $api->getBestSeller();
// echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";

// 由你音乐榜
$ret = $api->getYoRank();
echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";