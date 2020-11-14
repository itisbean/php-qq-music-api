<?php

namespace qqMusic;

class ChartConfig {
    
    // 飙升榜
    const UP_CHART = '62'; 
    // 热歌榜
    const HOT_CHART = '26'; 
    // 新歌榜
    const NEW_CHART = '27';
    // 流行指数榜
    const POP_CHART = '4';
    // 内地榜
    const AREA_MAINLAND_CHART = '5';
    // 香港地区榜
    const AREA_HK_CHART = '59';
    // 台湾地区榜
    const AREA_TW_CHART = '61';
    // 欧美榜
    const AREA_EUAM_CHART = '3';
    // 韩国榜
    const AREA_KOREA_CHART = '16';
    // 日本榜
    const AREA_JP_CHART = '17';
    // Q音快手榜
    const KS_CHART = '74';
    // 抖音排行榜
    const DY_CHART = '60';
    // 综艺新歌榜
    const SHOW_CHART = '64';
    // 影视金曲榜
    const TV_CHART = '29';
    // 说唱榜
    const RAP_CHART = '58';
    // 电音榜
    const ELE_CHART = '57';
    // 动漫音乐榜
    const CARTOON_CHART = '72';
    // 游戏音乐榜
    const GAME_CHART = '73';
    // K歌金曲榜
    const KTV_CHART = '36';
    // 美国公告牌榜
    const BILLBOARD_CHART = '108';
    // 美国iTunes榜
    const ITUNES_CHART = '123';
    // 韩国Melon榜
    const MELON_CHART = '129';
    // 英国UK榜
    const UK_CHART = '107';
    // 日本公信榜
    const ORICON_CHART = '105';
    // JOOX本地音乐热播榜
    const JOOX_CHART = '126';
    // 香港商台榜
    const FM903_CHART = '114';
    // 台湾KKBOX榜
    const KKBOX_CHART = '127';
    // Youtube音乐排行榜
    const YOUTUBE_CHART = '128';


    static $dayCharts = [
        // 100首
        self::UP_CHART,
        self::NEW_CHART,
        self::POP_CHART,

    ];

    static $weekCharts = [
        // 周四更新，300首
        self::HOT_CHART,
        // 周四更新，100首
        self::AREA_MAINLAND_CHART,
        self::AREA_HK_CHART,
        self::AREA_TW_CHART,
        // self::AREA_EUAM_CHART, 
        // self::AREA_KOREA_CHART, 
        // self::AREA_JP_CHART, 
        self::SHOW_CHART,
        self::TV_CHART,
        // self::CARTOON_CHART,
        // self::GAME_CHART,
        // 周四更新，50首
        self::RAP_CHART,
        self::ELE_CHART,
        self::KTV_CHART,
        // 周日更新，100首
        // self::BILLBOARD_CHART,
        // 周一更新，
        // self::ITUNES_CHART,
        // self::MELON_CHART,
        // 周一更新，40首
        // self::UK_CHART,
        // 周三更新，
        // self::ORICON_CHART,
        // 周四更新，
        self::JOOX_CHART,
        // 周六更新，20首
        self::FM903_CHART,
        // 周五更新，
        self::KKBOX_CHART,
        // 周一更新，
        self::YOUTUBE_CHART
    ];

}