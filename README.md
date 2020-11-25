# php-qq-music-api

qq music's public api for php

## Composer Install

```php
composer require itisbean/php-qq-music-api
```

## Use

```php
// 引入autoload.php（框架中使用不需要）
include_once __DIR__.'/../vendor/autoload.php';
// 实例化Api类
$api = new \qqMusic\Api();
// 调用
$ret = $api->searchSinger('容祖儿');
```

## Function

### Search Singer

```php
/**
 * 通过歌手姓名搜索歌手的歌曲列表
 * @param string $name
 * @return array
 */
$api->searchSinger('容祖儿');
```

### Get the singer's songs

```php
/**
 * 通过歌手Mid获取该歌手的歌曲列表
 * @param string $singerMid
 * @param integer $page 当前页
 * @param integer $pageSize 每页数量（Max 100）
 * @return array
 */
$api->getSonglistBySinger('001uXFgt1kpLyI');
```

### Get the singer's albums

```php
/**
 * 通过歌手Mid获取该歌手的专辑列表
 * @param string $singerMid
 * @param integer $page 当前页
 * @param integer $pageSize 每页数量（Max 80）
 * @return array
 */
$api->getAlbumlistBySinger('001uXFgt1kpLyI');
```

### Get album songs

```php
/**
 * 通过QQ音乐专辑MID获取歌曲列表
 * @param string $albumMid
 * @return array
 */
$api->getAlbumSongs('00092YgX3cowE2');
```

### Get the number of song comments

```php
/**
 * 获取歌曲评论数量
 * @param integer $songId
 * @return array
 */
$api->getSongCommentCount(234276764);
```

### Get the number of album comments

```php
/**
 * 获取专辑评论数量
 * @param integer $albumId
 * @return array
 */
$api->getAlbumCommentCount(7144823);
```

### Get song popularity data

```php
/**
 * 通过QQ音乐歌曲MID获取歌曲流行指数
 * @param string|string[] $songMid
 * @return array
 */
$api->getMusicHitInfo('001Eo1rF3gYD09,003Inmwn25vHfD');
```

### Get the number of song collections

```php
/**
 * 通过QQ音乐歌曲ID获取歌曲收藏数
 * @param integer|integer[] $songId
 * @return void
 */
$ret = $api->getMusicFavNum('280494291,280494264');
```

### Get rank chart data

```php
/**
 * 获取QQ音乐排行榜数据（日榜：今日；周榜：本周）
 * @param mixed $rankId (see \qqMusic\ChartConfig)
 * @return array
 */
$api->getRankChart(\qqMusic\ChartConfig::BILLBOARD_CHART);
```

### Get ranking data of songs

```php
/**
 * 获取歌曲的排行榜数据
 * @param mixed $checkSongs songId
 * @param array $rankIds 排行榜ID，为空就用配置中默认的
 * @return array
 */
$api->getSongsRankInfo([280494291,280494264]);
```

### Get ranking data of singers

```php
/**
 * 获取歌手排行榜数据
 * @param string $singerName 歌手名
 * @param array $rankIds 排行榜ID，为空就用配置中默认的
 * @return array
 */
$api->getSingersRankInfo('容祖儿');
```

### Top gift rank

```php
/**
 * 巅峰人气榜
 * @param string $type day|week|year|all
 * @param mixed $checkSingers singerId|singerMid
 * @return array
 */
$api->getHitList('all', '000sVzZ83BIMjq');
```

### Best seller rank

```php
/**
 * 专辑热销榜
 * @param string $rankType day|week|year|all
 * @param string $albumType single|ep|album|all
 * @return array
 */
$api->getBestSeller();
```

### Yo rank

```php
/**
 * 由你音乐榜
 * @return array
 */
$api->getYoRank();
```
