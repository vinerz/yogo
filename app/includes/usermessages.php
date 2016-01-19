<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
/* -============================================================================================-
 *   User messages
 *   Saves and displays messages at user level, useful for plugins and themes
 *   For use it properly you should fire the 'yg_messages' hook in your theme at desired location
 * -============================================================================================-
 */
 
register_hook('yg_messages');

class UserMessages extends Core {
    public static $messages = array('default' => array()), $templates = array('default' => '%s');
    
    private function __construct() { }
    
    public static function add_template($key, $string, $override = false) {
        if( empty($key) || !is_string($key) || ( !$override && array_key_exists($key, self::$templates ) ) )
            return false;
            
            if( !isset(self::$messages[$key]) ) self::$messages[$key] = array();
            self::$templates[$key] = $string;
        return true;
    }
    
    public static function override_template($key, $string) {
        return self::add_template($key, $string, true);
    }
    
    public static function remove_template($key) {
        if( empty($key) || !is_string($key) || array_key_exists($key, self::$templates) )
            return false;
        
        unset(self::$templates[$key]);
        return true;
    }
    
    public static function add($message, $type = null) {
        if( empty($type) || !is_string($type) )
            return false;
            
        if( !array_key_exists($type, self::$templates) )
            self::add_template($type, self::$templates['default'], true);
        
        if( empty($message) || !is_string($message) ) 
            return false;
            
        self::$messages[$type][] = $message;
        return true;
    }
    
    private static function _show_message($message, $template) {
        printf($template, $message);
    }
    
    public static function show($type = null) {
        if( !empty($type) && is_string($type) && array_key_exists($type, self::$templates) ) {
            foreach(self::$messages[$type] as $message) {
                self::_show_message($message, self::$templates[$type]);
            }
        } else {
            foreach(self::$messages as $type => $messages) {
                foreach($messages as $message) {
                    self::_show_message($message, self::$templates[$type]);
                }
            }
        }
    }
}

function yg_add_error($message) {
    return UserMessages::add($message, 'error');
}

function yg_add_notice($message) {
    return UserMessages::add($message, 'notice');
}

function yg_add_success($message) {
    return UserMessages::add($message, 'success');
}

function yg_add_info($message) {
    return UserMessages::add($message, 'info');
}

function yg_print_messages($type = null) {
    UserMessages::show($type);
}

function yg_messages($type = null) {
    add_action('yg_messages', 'yg_print_messages', $type);
    fire_hook('yg_messages');
}