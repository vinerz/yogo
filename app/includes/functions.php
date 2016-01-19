<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
function get($id) {
    if(is_get($id))
        return $_GET[$id];
    else 
        return '';
}

function post($id) {
    if(is_form_post($id))
        return $_POST[$id];
    else 
        return '';
}

function is_get($id) {
    return array_key_exists($id, $_GET);
}

function is_form_post($id) {
    return array_key_exists($id, $_POST);
}

function makeValuesReferenced($arr){
    $refs = array();
    foreach($arr as $key => $value)
        $refs[$key] = &$arr[$key];
    return $refs;
}

function yg_shutdown() {
    DB::close();
    fire_hook('shutdown');
}

if(!function_exists('glob_recursive')) {
    function glob_recursive($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}

function closest_path($path, $paths) {
    $maxMatch = null;
    $maxMatchLength = 0;
    $maxMatchID = 0;

    foreach($paths as $k => $item) {
        $idomain = dirname(dirname($item->path));
        if(strlen($idomain) > $maxMatchLength && strpos($path, $idomain) === 0) {
            $maxMatch = $idomain;
            $maxMatchLength = strlen($idomain);
            $maxMatchID = $k;
        }
    }
    return $maxMatchID;
}

function trailingslashit($string) {
    return untrailingslashit($string) . '/';
}

function untrailingslashit($string) {
    return rtrim($string, '/');
}

function get_site_url($path = null) {
    $url = YG_SITE_URL;
    $scheme = ( is_ssl() ? 'https' : 'http' );

    if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false )
		$url .= '/' . ltrim( $path, '/' );
        
    if ( 'http' != $scheme )
		$url = str_replace( 'http://', "{$scheme}://", $url );
        
    return $url;
}

function plugins_url() {
    return get_site_url(YG_PLUGINDIR_REL);
}

function get_plugin_url( $file ) {
    $exerpt = str_replace(YG_PLUGIN_DIR, '', realpath(dirname($file)) );
    $exerpt = str_replace('\\' ,'/', $exerpt); // sanitize for Win32
    $exerpt = preg_replace('|/+|', '/', $exerpt);
    return trailingslashit( plugins_url() . $exerpt );
}

function site_url() {
    echo get_site_url();
}

function is_ssl() {
    return YG_ENABLE_SSL;
}

function is_home() {
    if(YG_SITE_HOME == rtrim(get_current_url(), '/')) return true;
    else return false;
}

function is_current_url($path) {
    if(get_site_url($path) == rtrim(get_current_url(), '/')) return true;
    else return false;
}

function is_login() {
    if(defined("YG_LOGIN_ENABLED")) return true;
    else return false;
}

function get_current_url() {
    $current_url = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on") ? "https://" : "http://";
    $current_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    return $current_url;
}

function esc_attr( $text ) {
    return htmlspecialchars( $text, ENT_QUOTES );
}

require_once(YG_INCLUDEPATH . DS . "hooks.php");
require_once(YG_INCLUDEPATH . DS . "usermessages.php");
require_once(YG_INCLUDEPATH . DS . "scriptloader.php");
require_once(YG_INCLUDEPATH . DS . "styleloader.php");