<?php

namespace qqMusic;

use GuzzleHttp\Client;

class Api {

    protected $_client;

    static $baseUrl = 'https://u.y.qq.com/cgi-bin/musicu.fcg';

    private $_errMsg = '';

    public function __construct()
    {
        $this->_client = new Client();
    }

    /**
     * 通过歌手姓名搜索歌手的歌曲列表
     * @return array
     */
    public function searchSinger($keyword)
    {
        $data = $this->_keywordSearch($keyword);
        if ($data === false) {
            return $this->_error();
        }
        if (empty($data['body']['singer']['items'])) {
            return $this->_success();
        }
        $singer = $data['body']['singer']['items'][0];
        return $this->_success([
            'singerID' => $singer['singerID'],
            'singerMID' => $singer['singerMID'],
            'singerName' => $singer['singerName'],
            'singerName_hilight' => $singer['singerName_hilight'],
            'singerPic' => $singer['singerPic'],
            'songNum' => $singer['songNum'],
            'albumNum' => $singer['albumNum'],
            'mvNum' => $singer['mvNum']
        ]);
    }

    public function searchSong($keyword)
    {
        $data = $this->_keywordSearch($keyword);
        if ($data === false) {
            return $this->_error();
        }
    }

    private function _keywordSearch($keyword)
    {
        // TODO 搜索中文的问题
        $module = 'qqmusic.adaptor_all';
        $method = 'do_search_v2';
        return $this->_get($module, $method, [
            'query' => $keyword, 
            'search_type' => 100,
            // 'inCharset' => 'GB2312',
            // 'outCharset' => 'utf-8',
        ]);
    }

    public function test()
    {
        $module = 'music.liveShow.LiveShowOfficialRoomMedalSvr';
        $method = 'QuerySongExistMedal';
        $data = $this->_post($module, $method, ['songID' => 280494291, 'songType' => 1]);
        echo json_encode($data, JSON_UNESCAPED_UNICODE)."\n";die;
    }

    public function getSonglistBySinger()
    {
        
    }

    /**
     * 通过QQ音乐专辑MID获取歌曲列表
     * @param string $albumMid
     * @return array
     */
    public function getAlbumInfo($albumMid)
    {
        $module = 'music.musichallAlbum.AlbumSongList';
        $method = 'GetAlbumSongList';
        $data = $this->_get($module, $method, ['albumMid' => $albumMid]);
        if ($data === false) {
            return $this->_error();
        }
        return $this->_success($data);
    }

    /**
     * 通过QQ音乐歌曲MID获取歌曲流行指数
     * @param string|string[] $songMid
     * @return array
     */
    public function getMusicHitInfo($songMid)
    {
        if (is_string($songMid)) {
            $songMid = [$songMid];
        }
        $module = 'music.musicToplist.PlayTopInfoServer';
        $method = 'GetPlayTopData';
        $data = $this->_post($module, $method, ['songMidList' => $songMid, 'requireSongInfo' => 0]);
        if ($data === false) {
            return $this->_error();
        }
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
            $songId = [$songId];
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
     * 获取QQ音乐排行榜数据
     * @return array
     */
    public function getRankChart($rankId, $period = '')
    {
        $module = 'musicToplist.ToplistInfoServer';
        $method = 'GetDetail';
        $data = $this->_post($module, $method, ['topId' => $rankId, 'period' => $period, 'offset' => 0, 'num' => 500]);
        if ($data === false) {
            return $this->_error();
        }
        $data = !empty($data['data']) ? $data['data'] : [];
        return $this->_success($data);
    }

    /**
     * 巅峰人气榜
     * @return array
     */
    public function getHitList()
    {
        // 日榜
        // https://c.y.qq.com/rsc/fcgi-bin/fcg_global_gift_rank_list.fcg?g_tk=1974362392&uin=358008180&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&g_tk_new_20200303=478998249&ct=23&cv=0&reqtype=1&daystr=1605032384&_=1605032516716
        
        // 周榜
        // https://c.y.qq.com/rsc/fcgi-bin/fcg_global_gift_rank_list.fcg?g_tk=1974362392&uin=358008180&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&g_tk_new_20200303=478998249&ct=23&cv=0&reqtype=2&weeklist=0&year=2020&week=46&_=1605033084674

        // 年榜
        // https://c.y.qq.com/rsc/fcgi-bin/fcg_global_gift_rank_list.fcg?g_tk=1974362392&uin=358008180&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&g_tk_new_20200303=478998249&ct=23&cv=0&reqtype=3&year=2020&_=1605033159047

        // 总榜
        // https://c.y.qq.com/rsc/fcgi-bin/fcg_global_gift_rank_list.fcg?g_tk=1974362392&uin=358008180&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&g_tk_new_20200303=478998249&ct=23&cv=0&reqtype=0&weeklist=0&year=2020&week=46&daystr=1605032384&_=1605033181004
    }

    /**
     * 专辑热销榜
     * @return array
     */
    public function getBestSeller()
    {
        // AlbumType: all single ep album
        // RankType:  day week year all
        // 年榜
        // {"req_0":{"module":"music.musicMall.MallAlbumBestSellerSvr","method":"GetBestSellerRank","param":{"RankType":"year","Limit":20,"Index":0,"AlbumType":"all"}},"comm":{"g_tk":478998249,"uin":358008180,"format":"json","platform":"h5","ct":23,"cv":0}}
    }

    /**
     * 由你音乐榜
     * @return array
     */
    public function getYoRank()
    {
        // https://yobang.tencentmusic.com/unichartsapi/v1/songs/charts/dynamic?platform=qqyin&limit=10&offset=0&laiyuan=guanwang&source=006&t=1605034007011

        // 往期
        // https://yobang.tencentmusic.com/unichartsapi/v1/songs/charts/2020?sort=DESC&flag=history
        // https://yobang.tencentmusic.com/unichartsapi/v1/songs/charts/history/202044?platform=qqyin&limit=100&offset=0&laiyuan=guanwang&source=006
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
            return $this->_error('request failed, [' . $response->getStatusCode().']'. $e->getMessage());
        }
        $result = $response->getBody()->getContents();
        echo $result."\n";die;
        $result = json_decode($result, true);
        if ($result['code'] != 0) {
            return $this->_error('code: '.$result['code'], false);
        }
        if ($result['result']['code'] != 0) {
            return $this->_error('result code: '.$result['code'], false);
        }

        return $result['result']['data'];
    }

    private function _get($module, $method, $param)
    {
        $data = [
            'result' => [
                'module' => $module,
                'method' => $method,
                'param' => $param,
            ]
        ];
        $queryUrl = self::$baseUrl . '?' . http_build_query(['data' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
        try {
            $response = $this->_client->get($queryUrl, [
                'headers' => ['Content-Type' => 'application/json;charset=UTF-8'],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->_error('request failed, [' . $response->getStatusCode().']'. $e->getMessage());
        }
        $result = $response->getBody()->getContents();
        echo ($result);die;
        $result = json_decode($result, true);
        if ($result['code'] != 0) {
            return $this->_error('code: '.$result['code'], false);
        }
        if ($result['result']['code'] != 0) {
            return $this->_error('result code: '.$result['code'], false);
        }

        return $result['result']['data'];
    }
}