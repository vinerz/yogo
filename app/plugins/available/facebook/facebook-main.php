<?php
/**
  *  @name              Facebook
  *  @description       Provides a wide library of Facebook adapters
  *  @author            Vinicius Tavares <vinerz@vinerz.net>
  *  @plugin_url        http://www.xenon-corporation.com/yogo/plugin/facebook
  *  @plugin_sysname    facebosok
  *  @plugin_version    1.0.0
  */

define("FB_ROOT",       dirname(__FILE__));
define("FB_APP_ID",     "");
define("FB_APP_SECRET", "");

require_once(FB_ROOT . DS . "facebook/facebook.php");

class FB {
    public static $obj;
    public static function get() {
        return self::$obj;
    }
}

FB::$obj = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_APP_SECRET));

function facebook_url_likes($url) {
    $qry = FB::get()->api(array('method' => 'fql.query',
                                'query' =>  'SELECT like_count FROM link_stat WHERE url="'.$url.'"'
                                ));
    return (int) $qry[0]["like_count"];
}

function facebook_page_fans($pid) {
    $qry = FB::get()->api(array('method' => 'fql.query',
                                'query' =>  'SELECT fan_count FROM page WHERE page_id = '.$pid
                               ));
    return (int) $qry[0]["fan_count"];
}
