<?php
/*
 * TODO: SCRIPT LOCALIZATION
 *       AUTO-ENQUEUE DEPENDENT SCRIPTS
 */
class YG_Scripts extends Core {
    private static $_scripts = array(), $_queue = array(array(), array()), $_result = '';
    public static $do_concat = false, $print_html = '';
    
    private function __construct() { }
    
    public static function register( $handle, $src, $deps = array(), $group ) {
        if ( !is_string( $src ) || !is_string($handle) ) return false;
        if( !isset(self::$_scripts[$handle]) ) {
            self::$_scripts[$handle] = array( $src, $deps, $group );
        } else {
            Core::log('Attempt to override a script identifier \''.$handle.'\'.', YG_NOTICE);
        }
    }
    
    public static function deregister( $handle ) {
        if( self::is_enqueued( $handle ) ) self::dequeue( $handle );
        if( isset(self::$_scripts[$handle]) ) unset(self::$_scripts[$handle]);
    }
    
    public static function enqueue( $handle ) {
        if( !self::is_enqueued( $handle ) ) {
            $location = self::script_group( $handle );
            self::$_queue[$location][] = $handle;
            return true;
        } else {
            return false;
        }
    }
    
    public static function dequeue( $handle ) {
        if( self::is_enqueued( $handle ) ) {
            $location = self::script_group( $handle );
            unset(self::$_queue[$location][ array_search($handle, self::$_queue[$location]) ]);
            return true;
        } else {
            return false;
        }
    }
    
    private static function do_item( $handle ) {
        $hgroup = self::script_group( $handle );
        foreach( self::$_scripts[$handle][1] as $dep ) {
            if( self::is_enqueued( $dep ) ) {
                $dgroup = self::script_group( $dep );
                if( $dgroup > $hgroup ) {
                    return false; // Is enqueued but will load after this script
                }
            } else {
                return false;
            }
        }
        
        $src = self::$_scripts[$handle][0];
        
        if ( !preg_match('|^https?://|', $src) ) 
            $src = site_url() . '/' . $src;
            
        if ( self::$do_concat )
			self::$print_html .= "<script type='text/javascript' src='$src'></script>\n";
		else
			echo "<script type='text/javascript' src='$src'></script>\n";
            
        return true;
    }
    
    public static function print_scripts( $group ) {
        if( !array_key_exists( $group, self::$_queue ) ) return false;
        self::$print_html = '';

        foreach( self::$_queue[$group] as $handle ) {
            
            if(!self::do_item($handle)) return false; // Resolves dependencies and prints item if successful.
        }
        
        return true;
    }
    
    public static function is_registered( $handle ) {
        if( !isset(self::$_scripts[$handle]) ) return false;
        else return true;
    }
    
    public static function is_enqueued( $handle, $group = false ) {
        if( !self::is_registered($handle) ) return false;
        
        $location = ( self::$_scripts[$handle][2] == false ) ? 0 : 1;  // HEAD : FOOTER
        return in_array( $handle, self::$_queue[$location] );
    }
    
    public static function script_group( $handle ) {
        if( !self::is_registered($handle) ) return false;
        return (int) self::$_scripts[$handle][2];
    }
    
    public static function reset() { 
        self::$_scripts = array();
        self::$_queue = array(array(), array());
        self::$_result = '';
    }
}

function yg_register_script( $handle, $src, $deps = array(), $in_footer = false ) {
    $group = ($in_footer) ? 1 : 0;
    return YG_Scripts::register( $handle, $src, $deps, $group );
}

function yg_enqueue_script( $handle ) {
    return YG_Scripts::enqueue( $handle );
}

function yg_deregister_script( $handle ) {
    return YG_Scripts::deregister( $handle );
}

function yg_dequeue_script( $handle ) {
    return YG_Scripts::dequeue( $handle );
}

function print_head_scripts() {
    YG_Scripts::print_scripts(0);
}

function print_footer_scripts() {
    YG_Scripts::print_scripts(1);
}