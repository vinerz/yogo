<?php
class YG_Styles extends Core {
    private static $_styles = array(), $_inlines = array(), $_queue = array(), $_result = '';
    public static $do_concat = false, $print_html = '';
    
    private function __construct() { }
    
    public static function register( $handle, $src, $deps = array(), $media = 'all', $extra = array() ) {
        if ( !is_string( $src ) || !is_string( $handle ) ) return false;
        
        if( !isset(self::$_styles[$handle]) ) {
            self::$_styles[$handle] = array( $src, $deps, $media, $extra );
        } else {
            Core::log('Attempt to override a style identifier \''.$handle.'\'.', YG_NOTICE);
        }
    }
    
    public static function add_inline( $handle, $code ) {
        if ( !is_string( $code ) ) return false;
        
        $codes = array();
        
        if( isset(self::$_inlines[$handle]) && is_array( self::$_inlines[$handle] ) )
            $codes = self::$_inlines[$handle];
        
        $codes[] = $code;
        self::$_inlines[$handle] = $codes;
    }
    
    public static function print_inline( $handle, $echo = true ) {
        if( isset(self::$_inlines[$handle]) && is_array( self::$_inlines[$handle] ) ) {
            $output = implode( "\n", self::$_inlines[$handle] );

            if ( !$echo )
                return $output;

            echo "<style type='text/css'>\n";
            echo "$output\n";
            echo "</style>\n";

            return true;
        } else {
            return false;
        }
    }
    
    public static function deregister( $handle ) {
        if( self::is_enqueued( $handle ) ) self::dequeue( $handle );
        if( isset(self::$_styles[$handle]) ) unset(self::$_styles[$handle]);
    }
    
    public static function enqueue( $handle ) {
        if( !self::is_enqueued( $handle ) ) {
            self::$_queue[] = $handle;
            return true;
        } else {
            return false;
        }
    }
    
    public static function dequeue( $handle ) {
        if( self::is_enqueued( $handle ) ) {
            unset(self::$_queue[ array_search($handle, self::$_queue) ]);
            return true;
        } else {
            return false;
        }
    }
    
    private static function do_item( $handle ) {
        foreach( self::$_styles[$handle][1] as $dep ) {
            if( !self::is_enqueued( $dep ) ) {
                return false; // Is enqueued but will load after this style
            }
        }
        
        $src = self::$_styles[$handle][0];
        $media = self::$_styles[$handle][2];
        $extra = self::$_styles[$handle][3];
        $rel = isset($extra['alt']) && $extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
		$title = isset($extra['title']) ? "title='" . esc_attr( $extra['title'] ) . "'" : '';

		$end_cond = $tag = '';
		if ( isset($extra['conditional']) && $extra['conditional'] ) {
			$tag .= "<!--[if {$extra['conditional']}]>\n";
			$end_cond = "<![endif]-->\n";
		}
        
        if ( !preg_match('|^https?://|', $src) ) 
            $src = site_url() . '/' . $src;
        
        $tag .= "<link rel='$rel' id='$handle-css' $title href='$src' type='text/css' media='$media'>\n";
            
        $tag .= $end_cond;

		if ( self::$do_concat ) {
			self::$print_html .= $tag;
			self::$print_html .= self::print_inline( $handle, false );
		} else {
			echo $tag;
			self::print_inline( $handle );
		}
    }
    
    public static function print_styles() {
        self::$do_concat = false;
        self::$print_html = '';
        
        foreach( self::$_queue as $handle ) {
            if(!self::do_item($handle)) return false; // Resolves dependencies and prints item if successful.
        }
        
        return true;
    }
    
    public static function is_registered( $handle ) {
        if( !isset(self::$_styles[$handle]) ) return false;
        else return true;
    }
    
    public static function is_enqueued( $handle, $media = false ) {
        if( !self::is_registered($handle) ) return false;
        return in_array( $handle, self::$_queue );
    }
    
    public static function reset() { 
        self::$_styles = array();
        self::$_queue = array(array(), array());
        self::$_result = '';
    }
}

function yg_register_style( $handle, $src, $deps = array(), $media = 'all' ) {
    return YG_Styles::register( $handle, $src, $deps, $media );
}

function yg_enqueue_style( $handle ) {
    return YG_Styles::enqueue( $handle );
}

function yg_deregister_style( $handle ) {
    return YG_Styles::deregister( $handle );
}

function yg_dequeue_style( $handle ) {
    return YG_Styles::dequeue( $handle );
}

function yg_print_styles() {
    YG_Styles::print_styles();
}

function yg_add_inline_style( $handle, $code ) {
    YG_Styles::add_inline( $handle, $code );
}

function yg_print_inline_style( $handle ) {
    YG_Styles::print_inline( $handle );
}