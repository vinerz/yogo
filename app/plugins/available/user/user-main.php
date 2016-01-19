<?php
/**
  *  @name              User Adapter
  *  @description       Provides a getter and setter function for user data
  *  @author            Vinicius Tavares <vinerz@vinerz.net>
  *  @plugin_url        http://www.xenon-corporation.com/yogo/plugin/user
  *  @plugin_sysname    user
  *  @plugin_version    1.0.0
  */

class InfoGetter extends Core {
    private static $keys = array(), $tbl = 'users', $pri = 'id';
    
    private function __construct() {}
    
	public static function get($keys, $id = null) {
        $result = array();
        if( !is_array($keys) && !is_string($keys) ) // Bad data received
            return false;
            
        if(!is_array($keys))
            $keys = array($keys);
        
        if( empty($id) )
            $id = Session::get('uid');
            
        foreach($keys as &$key) {
            if( isset(self::$keys[$id][$key]) ) {
                $result[$key] = self::$keys[$id][$key];
                unset($key);
            }
        }
        
        if(count($keys) == 0) // All the keys are on the cache
            return $result;
        
        $query = DB::prepared('SELECT '.implode(',', $keys).' FROM users WHERE id = ?', $id);
        if($query) {
            foreach($query->fields as $field) {
                self::$keys[$id][$field] = $query->row[$field];
            }
            
            return array_merge($result, $query->row);
        } else {
            Core::log('Failed to get user information.', YG_NOTICE);
            return false;
        }
	}
    
    public static function set($keys, $value = false, $id = null) {
        if( is_array($keys) && $value !== false )
            return false;
        
        if( !is_array($keys) ) {
            $data = array();
            $data[$keys] = $value;
        } else {
            $data = $keys;
        }
        
        if( count($data) == 0 ) 
            return false;
        
        if( empty($id) )
            $id = Session::get('uid');
        
        $setData = '';
        $psvals = array();
        
        foreach($data as $key => $val) {
            if( !empty($setData) ) $setData .= ', ';
            $setData .= $key . ' = ?';
            $psvals[] = $val;
        }
        
        $psvals[] = $id;
        
        $query = DB::prepared('UPDATE users SET '.$setData.' WHERE id = ?', $psvals);
        if($query) {
            foreach($data as $key => $val) {
                self::$keys[$id][$key] = $val;
            }
            
            return true;
        } else {
            Core::log('Failed to get user information.', YG_NOTICE);
            return false;
        }
    }
    
    public static function setTbl($tbl) {
        $tbl = preg_replace( '|[^A-Za-z0-9-_]+|', '', $tbl );
        self::$tbl = $tbl;
    }
    
    public static function setPrimary($pri) {
        $pri = preg_replace( '|[^A-Za-z0-9-_]+|', '', $pri );
        self::$pri = $pri;
    }
}

class User {
    public static function get($keys, $id = null) {
        InfoGetter::setTbl('users');
        InfoGetter::setPrimary('id');
        return InfoGetter::get($keys, $id);
    }
    
    public static function set($keys, $value = false, $id = null) {
        InfoGetter::setTbl('users');
        InfoGetter::setPrimary('id');
        return InfoGetter::set($keys, $value, $id);
    }
}

class UserSettings {
    public static function get($keys, $id = null) {
        InfoGetter::setTbl('usettings');
        InfoGetter::setPrimary('userid');
        return InfoGetter::get($keys, $id);
    }
    
    public static function set($keys, $value = false, $id = null) {
        InfoGetter::setTbl('usettings');
        InfoGetter::setPrimary('userid');
        return InfoGetter::set($keys, $value, $id);
    }
}



