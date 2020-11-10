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
    public function getMusicListBySinger()
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
    public function getMusicRank($rankId, $period = '')
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
        $queryUrl = self::$baseUrl . '?' . http_build_query(['data' => json_encode($data)]);
        try {
            $response = $this->_client->get($queryUrl);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->_error('request failed, [' . $response->getStatusCode().']'. $e->getMessage());
        }
        $result = $response->getBody()->getContents();
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