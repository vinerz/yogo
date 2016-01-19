<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
session_start();

class Session {
	public function __construct() {
	}
	
	public static function destroy() {
		session_destroy();
	}
	
	public static function exists($id) {
		return isset($_SESSION[$id]);
	}
	
	public static function get($id) {
        if(!self::exists($id)) return false;
		return $_SESSION[$id];
	}
	
	public static function set($id, $value) {
		$_SESSION[$id] = $value;
	}
	
	public static function isLogged($adm = false) {
		$key = ($adm) ? "aid" : "uid";
		if(!isset($_SESSION[$key]) or $_SESSION[$key] == "") {
			return false;
		}
		return true;
	}
}