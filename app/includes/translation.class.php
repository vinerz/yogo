<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

require_once(YG_INCLUDEPATH . "/translation/streams.php");
require_once(YG_INCLUDEPATH . "/translation/gettext.php");

class MO {
    var $path;
    var $domain;
    var $obj;
}

class Translation extends Core {
    public static $domains = array();
    
    public static function loadMO($file, $domain) {
        $streamer = new FileReader($file);
        if( !$streamer ) return false;
        
        $translation = new gettext_reader($streamer);
        if( !$translation ) return false;
        
        $moFile = new MO();
        $moFile->path = $file;
        $moFile->obj = $translation;
        
        if( isset(self::$domains[$domain]) )
            return false;
            
        self::$domains[$domain] = $moFile;
        return true;
    }
}

function __( $single, $domain = 'default' ) {
    if( empty($domain) || !is_string($domain) ) 
        $domain = 'default';
    
    if( !isset(Translation::$domains[$domain]) || !is_object(Translation::$domains[$domain]) ) 
        return $single;
        
    $obj = Translation::$domains[$domain]->obj;
    return $obj->translate($single);
}

function _n( $single, $plural, $number, $domain = 'default' ) {
    if( empty($domain) || !is_string($domain) ) 
        $domain = 'default';
    
    if( !isset(Translation::$domains[$domain]) || !is_object(Translation::$domains[$domain]) )  {
        if ($number != 1)
            return $plural;
        else
            return $single;
    }
    
    $obj = Translation::$domains[$domain]->obj;
    return $obj->ngettext($single, $plural, $number);
}

function _e($single, $domain = 'default') {
    echo __($single);
}

function _ne($single, $plural, $number, $domain = 'default') {
    echo _n($single, $plural, $number, $domain);
}

function get_locale() {
	if ( defined( 'YG_LANG' ) )
		$locale = YG_LANG;

	if ( empty( $locale ) )
		$locale = 'en_US';

	return $locale;
}

function load_default_domain() {
	$locale = get_locale();
	load_textdomain( 'default', YG_LANG_DIR . DS . $locale . '.mo' );
}

function load_textdomain( $domain, $filepath ) {
    if ( !is_readable( $filepath ) ) return false;
    
    $translation = Translation::loadMO( $filepath, $domain );
    return $translation;
}

function load_plugin_textdomain( $domain, $plugin_rel_path = false ) {
	$locale = get_locale();

	if ( false !== $plugin_rel_path	) {
		$path = YG_PLUGIN_DIR . DS . trim( $plugin_rel_path, DS );
	} else {
		$path = YG_PLUGIN_DIR;
	}
    
	$mofile = $path . DS . $domain . '-' . $locale . '.mo';
	return load_textdomain( $domain, $mofile );
}

function load_theme_textdomain( $domain, $path = false ) {
	$locale = get_locale();

	$path = ( empty( $path ) ) ? get_template_directory() : $path;

	$mofile = $path . DS . $locale . '.mo';
	return load_textdomain($domain, $mofile);
}
?>