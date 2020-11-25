<?php

namespace qqMusic;

use GuzzleHttp\Client;

class Api
{

    protected $_client;

    static $baseUrl = 'https://u.y.qq.com/cgi-bin/musicu.fcg';

    private $_errMsg = '';

    public function __construct()
    {
        $this->_client = new Client();
    }

    /**
     * 通过歌手姓名搜索歌手的歌曲列表
     * @param string $name
     * @return array
     */
    public function searchSinger($name)
    {
        $data = $this->_keywordSearch($name);
        if ($data === false) {
            return $this->_error();
        }
        if (empty($data['zhida']['zhida_singer'])) {
            return $this->_success();
        }
        $singer = $data['zhida']['zhida_singer'];
        return $this->_success([
            'singerID' => $singer['singerID'],
            'singerMID' => $singer['singerMID'],
            'singerName' => $singer['singerName'],
            'singerName_hilight' => $singer['singername_hilight'],
            'singerPic' => $singer['singerPic'],
            'songNum' => $singer['songNum'],
            'albumNum' => $singer['albumNum'],
            'mvNum' => $singer['mvNum']
        ]);
    }

    
    /**
     * 获取歌手粉丝数
     * @param string $singerMid
     * @return array
     */
    public function getSingerFanNum($singerMid)
    {
        $module = 'Concern.ConcernSystemServer';
        $method = 'cgi_qry_concern_num';
        $data = $this->_get($module, $method, [
            'vec_userinfo' => [
                ['usertype' => 1, 'userid' => $singerMid]
            ],
            'opertype' => 6
        ]);
        if ($data === false) {
            return $this->_error();
        }
        isset($data['map_singer_num']) && $data = $data['map_singer_num'];
        return $this->_success($data);
    }

    /**
     * 手机抓包接口（弃）
     */
    private function _keywordSearchBak($keyword)
    {
        $module = 'qqmusic.adaptor_all';
        $method = 'do_search_v2';
        return $this->_get($module, $method, [
            'query' => $keyword,
            'search_type' => 100
        ]);
    }

    /**
     * 搜索
     * @param string $keyword
     * @return array|bool
     */
    private function _keywordSearch($keyword)
    {
        $url = 'https://c.y.qq.com/soso/fcgi-bin/client_search_cp';
        $param = [
            'new_json' => 1,
            'catZhida' => 1,
            'w' => $keyword,
            'format' => 'json',
            'inCharset' => 'utf8',
            'outCharset' => 'utf8',
            'platform' => 'yqq.json',
            'needNewCode' => 0
        ];
        return $this->_get('url', $url, $param);
    }

    /**
     * 通过歌手Mid获取该歌手的歌曲列表
     * @param string $singerMid
     * @param integer $page 页码
     * @param integer $pageSize 每页数量（最大100）
     * @return array
     */
    public function getSonglistBySinger($singerMid, $page = 1, $pageSize = 100)
    {
        $page > 0 || $page = 1;
        $pageSize > 0 || $pageSize = 100;
        $module = 'musichall.song_list_server';
        $method = 'GetSingerSongList';
        $data = $this->_get($module, $method, [
            'singerMid' => $singerMid,
            'begin' => ($page - 1) *  $pageSize,
            'num' => $pageSize,
            'order' => 1
        ]);
        if ($data === false) {
            return $this->_error();
        }
        if (empty($data['songList'])) {
            return $this->_success();
        }
        foreach ($data['songList'] as &$val) {
            $val = $val['songInfo'];
        }
        return $this->_success($data);
    }

    /**
     * 通过歌手Mid获取该歌手的专辑列表
     * @param string $singerMid
     * @param integer $page
     * @param integer $pageSize 每页数量（最大80）
     * @return array
     */
    public function getAlbumlistBySinger($singerMid, $page = 1, $pageSize = 80)
    {
        $page > 0 || $page = 1;
        $pageSize > 0 || $pageSize = 80;
        $module = 'music.musichallAlbum.AlbumListServer';
        $method = 'GetAlbumList';
        $data = $this->_get($module, $method, [
            'singerMid' => $singerMid,
            'begin' => ($page - 1) *  $pageSize,
            'num' => $pageSize,
            'order' => 0
        ]);
        if ($data === false) {
            return $this->_error();
        }
        return $this->_success($data);
    }

    /**
     * 通过QQ音乐专辑MID获取歌曲列表
     * @param string $albumMid
     * @return array
     */
    public function getAlbumSongs($albumMid)
    {
        $module = 'music.musichallAlbum.AlbumSongList';
        $method = 'GetAlbumSongList';
        $data = $this->_get($module, $method, ['albumMid' => $albumMid]);
        if ($data === false) {
            return $this->_error();
        }
        foreach ($data['songList'] as &$val) {
            $val = $val['songInfo'];
        }
        return $this->_success($data);
    }

    /**
     * 获取歌曲评论数量
     * @param integer $songId
     * @return array
     */
    public function getSongCommentCount($songId)
    {
        $data = $this->_getComments($songId, 1);
        if ($data === false) {
            return $this->_error();
        }
        return $this->_success($data);
    }

    /**
     * 获取专辑评论数量
     * @param integer $albumId
     * @return array
     */
    public function getAlbumCommentCount($albumId)
    {
        $data = $this->_getComments($albumId, 2);
        if ($data === false) {
            return $this->_error();
        }
        return $this->_success($data);
    }

    /**
     * 获取评论数量
     * @param integer $id 歌曲ID或专辑ID
     * @param integer $bizType 1.歌曲 2.专辑
     * @return array|bool
     */
    private function _getComments($id, $bizType)
    {
        $url = 'https://c.y.qq.com/base/fcgi-bin/fcg_global_comment_h5.fcg';
        $param = [
            'inCharset' => 'utf8',
            'outCharset' => 'utf-8',
            'format' => 'json',
            'topid' => (int)$id,
            'cmd' => 4, // cmd=8 获取全部评论列表（分页）
            'biztype' => $bizType,
        ];
        $result = $this->_get('url', $url, $param);
        return $result['batch_commenttotal'];
    }

    /**
     * 通过QQ音乐歌曲MID获取歌曲流行指数
     * @param string|string[] $songMid
     * @return array
     */
    public function getMusicHitInfo($songMid)
    {
        if (!is_array($songMid)) {
            $songMid = explode(',', $songMid);
        }
        $module = 'music.musicToplist.PlayTopInfoServer';
        $method = 'GetPlayTopData';
        $data = $this->_post($module, $method, ['songMidList' => $songMid, 'requireSongInfo' => 0]);
        if ($data === false) {
            return $this->_error();
        }
        isset($data['data']) && $data = $data['data'];
        return $this->_success($data);
    }

    /**
     * 通过QQ音乐歌曲ID获取歌曲收藏数
     * @param integer|integer[] $songId
     * @return void
     */
    public function getMusicFavNum($songId)
    {
        if (!is_array($songId)) {
            $songId = explode(',', $songId);
        }
        foreach ($songId as &$sid) {
            $sid = (int)$sid;
        }
        $module = 'music.musicasset.SongFavRead';
        $method = 'GetSongFansNumberById';
        $data = $this->_post($module, $method, ['v_songId' => $songId]);
        if ($data === false) {
            return $this->_error();
        }
        $data = !empty($data['m_numbers']) ? $data['m_numbers'] : [];
        return $this->_success($data);
    }

    /**
     * 获取QQ音乐排行榜数据（日榜：今日；周榜：本周）
     * @param mixed $rankId
     * @return array
     */
    public function getRankChart($rankId, $period = '')
    {
        $isWeek = in_array($rankId, ChartConfig::$weekCharts);
        if (!$period && $isWeek) {
            $period = date('Y') . '_' . date('W'); // 周榜，周数
        } elseif (!$period) {
            $period = date('Y-m-d'); // 日榜，当天，也可以获取往期
        }
        $module = 'musicToplist.ToplistInfoServer';
        $method = 'GetDetail';
        $param = [
            'topId' => (int)$rankId,
            'period' => $period,
            'offset' => 0,
            'num' => 500
        ];
        $data = $this->_post($module, $method, $param);
        if ($data === false) {
            // 周榜 本周还没有更新，就获取上一周的数据
            if ($this->_errMsg == 'result code: 2000' && $isWeek) {
                $w = explode('_', $period)[1];
                if ($w - 1 > 0) {
                    $period = date('Y') . '_' . ($w - 1);
                } else {
                    $period = (date('Y') - 1)  . '_' . date('W', strtotime('-1 week'));
                }
                return $this->getRankChart($rankId, $period);
            }
            return $this->_error();
        }
        $data = !empty($data['data']) ? $data['data'] : [];
        return $this->_success($data);
    }

    /**
     * 获取歌曲的排行榜数据
     * @param mixed $checkSongs 歌曲ID
     * @param array $rankIds 排行榜ID，为空就用配置中默认的
     * @return array
     */
    public function getSongsRankInfo($checkSongs, $rankIds = [])
    {
        if (!is_array($checkSongs)) {
            $checkSongs = explode(',', $checkSongs);
        }
        $checkSongs = array_flip($checkSongs);
        $rankInfo = [];
        if (!$rankIds) {
            // 配置中包含的榜单
            $rankIds = array_merge(ChartConfig::$dayCharts, ChartConfig::$weekCharts);
        }
        foreach ($rankIds as $rankId) {
            $data = $this->getRankChart($rankId);
            if ($data['ret']) {
                $data = $data['data'];
                $list = $data['song'];
                foreach ($list as $key => $song) {
                    if (!isset($checkSongs[$song['songId']])) {
                        unset($list[$key]);
                    }
                }
                $list = array_values($list);
                if ($list) {
                    $rankInfo[] = [
                        'topId' => $data['topId'],
                        'updateType' => $data['updateType'],
                        'title' => $data['title'],
                        'titleShare' => $data['titleShare'],
                        'period' => $data['period'],
                        'updateTime' => $data['updateTime'],
                        'song' => $list
                    ];
                }
            }
            usleep(500);
        }
        return $this->_success($rankInfo);
    }

    /**
     * 获取歌手排行榜数据
     * @param string $singerName 歌手名
     * @param array $rankIds 排行榜ID，为空就用配置中默认的
     * @return array
     */
    public function getSingersRankInfo($singerName, $rankIds = [])
    {
        $rankInfo = [];
        if (!$rankIds) {
            // 配置中包含的榜单
            $rankIds = array_merge(ChartConfig::$dayCharts, ChartConfig::$weekCharts);
        }
        foreach ($rankIds as $rankId) {
            $data = $this->getRankChart($rankId);
            if ($data['ret']) {
                $data = $data['data'];
                $list = $data['song'];
                foreach ($list as $key => $song) {
                    if (mb_strpos($song['singerName'], $singerName) === false) {
                        unset($list[$key]);
                    }
                }
                $list = array_values($list);
                if ($list) {
                    $rankInfo[] = [
                        'topId' => $data['topId'],
                        'updateType' => $data['updateType'],
                        'title' => $data['title'],
                        'titleShare' => $data['titleShare'],
                        'period' => $data['period'],
                        'updateTime' => $data['updateTime'],
                        'song' => $list
                    ];
                }
            }
            usleep(500);
        }
        return $this->_success($rankInfo);
    }

    /**
     * 巅峰人气榜
     * @param string $type day week year all
     * @param mixed $checkSingers 歌手ID或Mid
     * @return array
     */
    public function getHitList($type = 'all', $checkSingers = [])
    {
        if ($type == 'day') {
            $more = [
                'reqtype' =>  1,
                'daystr' => time()
            ];
        } elseif ($type == 'week') {
            $more = [
                'reqtype' =>  2,
                'year' => date('Y'),
                'week' => date('W')
            ];
        } elseif ($type == 'year') {
            $more = [
                'reqtype' =>  3,
                'year' => date('Y')
            ];
        } else {
            $more = [
                'reqtype' => 0
            ];
        }
        $url = 'https://c.y.qq.com/rsc/fcgi-bin/fcg_global_gift_rank_list.fcg';
        $param = array_merge([
            'format' => 'json',
            'inCharset' => 'utf-8',
            'outCharset' => 'utf-8',
        ], $more);
        $result = $this->_get('url', $url, $param);
        if ($result === false) {
            return $this->_error();
        }
        $result = $result['ranklist'];
        // 只要某歌手的数据
        if ($checkSingers) {
            if (!is_array($checkSingers)) {
                $checkSingers = explode(',', $checkSingers);
            }
            $checkSingers = array_flip($checkSingers);
            foreach ($result as $key => $val) {
                $f = false;
                foreach ($val['songinfo']['singer'] as $singer) {
                    if (isset($checkSingers[$singer['id']]) || isset($checkSingers[$singer['mid']])) {
                        $f = true;
                    }
                }
                if (!$f) {
                    unset($result[$key]);
                }
            }
            $result = array_values($result);
        }
        return $this->_success($result);
    }

    /**
     * 专辑热销榜
     * @param string $rankType day week year all
     * @param string $albumType single ep album all
     * @return array
     */
    public function getBestSeller($rankType = 'all', $albumType = 'all')
    {
        in_array($rankType, ['all', 'day', 'week', 'year']) || $rankType = 'all';
        in_array($albumType, ['all', 'single', 'ep', 'album']) || $albumType = 'all';
        $module = 'music.musicMall.MallAlbumBestSellerSvr';
        $method = 'GetBestSellerRank';
        $param = [
            'RankType' => $rankType,
            'AlbumType' => $albumType,
        ];
        $result = $this->_get($module, $method, $param);
        if ($result === false) {
            return $this->_error();
        }
        return $this->_success($result['Rank']);
    }

    /**
     * 由你音乐榜
     * @return array
     */
    public function getYoRank($page = 1, $pageSize = 100)
    {
        // 往期
        // https://yobang.tencentmusic.com/unichartsapi/v1/songs/charts/2020?sort=DESC&flag=history
        // https://yobang.tencentmusic.com/unichartsapi/v1/songs/charts/history/202044?platform=qqyin&limit=100&offset=0&laiyuan=guanwang&source=006
        $page > 0 || $page = 1;
        $pageSize > 0 || $pageSize = 100;
        $url = 'https://yobang.tencentmusic.com/unichartsapi/v1/songs/charts/dynamic';
        $param = [
            'platform' => 'qqyin',
            'offset' => ($page - 1) * $pageSize,
            'limit' => $pageSize
        ];
        $result = $this->_get('url', $url, $param);
        if ($result === false) {
            return $this->_error();
        }
        return $this->_success($result);
    }

    private function _success($data = [])
    {
        return ['ret' => true, 'data' => $data, 'msg' => ''];
    }

    private function _error($msg = '', $isArray = true)
    {
        if ($isArray) {
            return ['ret' => false, 'data' => null, 'msg' => $msg ?: $this->_errMsg];
        }
        $this->_errMsg = $msg;
        return false;
    }

    private function _post($module, $method, $param)
    {
        $data = [
            'result' => [
                'module' => $module,
                'method' => $method,
                'param' => $param
            ],
        ];
        try {
            $response = $this->_client->post(self::$baseUrl, ['json' => $data]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->_error('request failed, [' . $e->getCode() . ']' . $e->getMessage(), false);
        }
        $result = $response->getBody()->getContents();
        // echo $result."\n";die;
        $result = json_decode($result, true);
        if ($result['code'] != 0) {
            return $this->_error('code: ' . $result['code'], false);
        }
        if ($result['result']['code'] != 0) {
            return $this->_error('result code: ' . $result['result']['code'], false);
        }
        return $result['result']['data'];
    }

    private function _get($module, $method, $param)
    {
        if ($module == 'url') {
            $queryUrl =  $method. '?' . http_build_query($param);
        } else {
            $data = [
                'result' => [
                    'module' => $module,
                    'method' => $method,
                    'param' => $param,
                ]
            ];
            $queryUrl = self::$baseUrl . '?' . http_build_query([
                'data' => json_encode($data),
                'inCharset' => 'utf8',
                'outCharset' => 'utf-8',
                'format' => 'json'
            ]);
        }
        
        try {
            $response = $this->_client->get($queryUrl);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->_error('request failed, [' . $e->getCode() . ']' . $e->getMessage(), false);
        }
        $result = $response->getBody()->getContents();
        // echo ($result)."\n";die;
        $result = json_decode($result, true);
        isset($result['result']) && $result = $result['result'];
        if ($result['code'] != 0) {
            return $this->_error('code: ' . $result['code'], false);
        }
        isset($result['data']) && $result = $result['data'];

        return $result;
    }

    /**
     * 弹幕
     */
    public function getBullet()
    {
        $module = 'music.bullet.BulletSrv';
        $method = 'GetBulletsCnt';
        $param = [
            'subjectType' => 0,
            'subjectId' => '001Eo1rF3gYD09'
        ];
        $ret = $this->_post($module, $method, $param);
        echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";die;
    }
}
