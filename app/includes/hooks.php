<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
/* -==========================================================================================-
 *     System Hooks
 *     Will fire some actions in pre-defined steps of Yogo's execution
 *     You can use the function 'register_hook' to fire a custom-made hook
 *     System hooks types:
 *     init: Will be fired before modules load
 *     modload: Will be fired just after modules load and before any other interaction
 *     beforesend: Fired before send output to client
 *     shutdown: Will be executed after sending the output and before finish script execution
 
    TODO: CHECK CACHE FUNCTIONS
 * -==========================================================================================-
 */
$YG_SysHooks = array();
function register_hook($hook) {
    global $YG_SysHooks;
    if(!isset($YG_SysHooks[$hook])) $YG_SysHooks[$hook] = array();
}

function add_action($hook, $function, $args = false, $cache = false) { /* $cache here overrides the global option */
    global $YG_SysHooks;
    if(isset($YG_SysHooks[$hook])) $YG_SysHooks[$hook][] = array($function, $args, $cache);
}

function unregister_hook($hook, $function, $args) {
    global $YG_SysHooks;
}

function _do_fire_hook($element) {
    if(isset($element[0][0]) && gettype($element[0][0]) == 'object') {
        if(!method_exists($element[0][0], $element[0][1])) return 0;
    } else {
        if(!function_exists($element[0])) return 0;
    }
    
    if(is_array($element[1])) {
        return call_user_func_array($element[0], $element[1]);
    } else {
        return call_user_func($element[0], $element[1]);
    }
}

function _cache_hook($element) {
    $fingerprint = md5(json_encode($element));
    $cache_path = YG_CACHE_PATH . '/hooks/' . $fingerprint;
    
    if(is_file($cache_path)) {
        $cache_buffer = file_get_contents($cache_path);
        $cache_date = filemtime($cache_path);
        if( YG_CACHE_HOOKS_TIMEOUT > (time() - $cache_date)) {
            $response = unserialize($cache_buffer);
            echo $response[0];
            return $response[1];
        } else { /* Expired cache! */
            ob_start();
            $refresh_hook = _do_fire_hook($element);
            $return_contents = ob_get_flush();
            if(file_put_contents($cache_path, serialize(array($return_contents, $refresh_hook)))) {
                return $refresh_hook;
            } else {
                return false;
            }
        }
    } else {
        ob_start();
        $refresh_hook = _do_fire_hook($element);
        $return_contents = ob_get_flush();
        if(file_put_contents($cache_path, serialize(array($return_contents, $refresh_hook)))) {
            return $refresh_hook;
        } else {
            return false;
        }
    }
}

function _pre_fire_hook($element) {
    if(count($element) != 3) return -1;
    
    if($element[2] == false) {
        if( YG_CACHE_HOOKS ) {
            return _cache_hook($element);
        } else {
            return _do_fire_hook($element);
        }
    } else {
        return _cache_hook($element);
    }
}

function fire_hook($hook) {
    global $YG_SysHooks;
    if(!isset($YG_SysHooks[$hook])) return false;
    return array_map('_pre_fire_hook', $YG_SysHooks[$hook]);
}

require_once(YG_INCLUDEPATH . DS . "hooksinit.php");