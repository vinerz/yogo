<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

final class DB extends Core {
    private static $initialized = false, $mysqli = false, $doCache = false;
	
	private function __construct() {}
    
    public static function initialize() {
        if(self::$initialized) return;
        self::$initialized = true;
    }
	
	public static function connect($host, $dbuser, $dbpass, $db) {
        self::initialize();
        if(@self::$mysqli = new mysqli($host, $dbuser, $dbpass, $db)) {
            if(mysqli_connect_errno()) {
                self::log("Error when establishing a connection to the database", YG_WARNING);
                return false;
            }
        } else {
            self::log("Error when creating a database object", YG_WARNING);
            return false;
        }
	}
	
	public static function query($query, $forceCache = false) {
		if(($result = self::$mysqli->query($query))) {
		    if(gettype($result) == 'object') {
		        return new ResultSet(self::$mysqli, $result, $query);
		    } else {
			    return true;
			}
		} else {
			self::log("Query ".$query." failed.", YG_WARNING);
			return false;
		}
	}
    
    public static function prepared($query, $bindvals) {
        $stmt = self::$mysqli->prepare($query);
        if($stmt) {
            $bindParam = new BindParam(); 
            if(is_array($bindvals)) {
                foreach($bindvals as &$val) {
                    $bindParam->add($val);
                }
            } else {
                $bindParam->add($bindvals);
            }
            
            if( count($bindvals) > 0 ) call_user_func_array( array($stmt, 'bind_param'), makeValuesReferenced($bindParam->get()));
            
            if($stmt->execute()) {
                if($stmt->affected_rows === -1) {
                    $result = $stmt->get_result();
                    return new ResultSet(self::$mysqli, $result, $query);
                } else {
                    return true;
                }
            }
        } else {
			self::log("Failed to prepare query.".self::error(), YG_WARNING);
			return false;
		}
    }
    
    public static function auto_insert($table, $data) {
        return self::_auto_query('insert', $table, $data);
    }
    
    public static function auto_update($table, $data, $pk = null) {
        return self::_auto_query('update', $table, $data, $pk);
    }
    
    public static function auto_select($table, $data, $pk = null) {
        $data = (array) $data;
        return self::_auto_query('select', $table, $data, $pk);
    }
    
    private static function _auto_query($mode, $table, $data, $pk = null) {
        if( empty( $table ) || !is_string( $table ) ) 
            return false;
            
        if( !is_array( $data ) ) 
            return false;
        
        if( $mode == 'insert' ) {
            $columnstr = '';
            $columnvalstr = '';
            $bindvals = array();
            foreach( $data as $column => $value ) {
                if( strlen($columnstr) > 0 ) $columnstr .= ', ';
                $column = preg_replace( '/[^0-9a-zA-Z-_]/' , '', $column );
                $columnstr .= '`'.$column.'`';
                $bindvals[] = $value;
                if( strlen($columnvalstr) > 0 ) $columnvalstr .= ', ';
                $columnvalstr .= '?';
            }
            $query = 'INSERT INTO `' . $table . '` (' . $columnstr . ') VALUES (' . $columnvalstr . ');';
        } else if( $mode == 'update' ) {
            $setsstr = '';
            $pkstr = '';
            $bindvals = array();
            foreach( $data as $column => $value ) {
                if(strlen($setsstr) > 0) $setsstr .= ', ';
                $column = preg_replace( '/[^0-9a-zA-Z-_]/' , '', $column );
                $setsstr .= '`' . $column . '` = ?';
                $bindvals[] = $value;
            }
            
            if( !empty( $pk ) && is_array( $pk ) ) {
                $pkstr = ' WHERE `' . preg_replace( '/[^0-9a-zA-Z-_]/' , '', key($pk) ) . '` = ?';
                $bindvals[] = current($pk);
            }
            
            $query = 'UPDATE `' . $table . '` SET ' . $setsstr . $pkstr;
        } else if( $mode == 'select' ) {
            $selectstr = '';
            $pkstr = '';
            $bindvals = array();
            foreach( $data as $column ) {
                if(strlen($selectstr) > 0) $selectstr .= ', ';
                $column = preg_replace( '/[^0-9a-zA-Z-_\*]/' , '', $column );
                if( $column == '*' ) 
                    $selectstr .= $column;
                else
                    $selectstr .= '`' . $column . '`';
            }
            
            if( !empty( $pk ) && is_array( $pk ) ) {
                $pkstr = ' WHERE `' . preg_replace( '/[^0-9a-zA-Z-_]/' , '', key($pk) ) . '` = ?';
                $bindvals[] = current($pk);
            }
            
            $query = 'SELECT ' . $selectstr . ' FROM `' . $table . '`' . $pkstr;
        }
        return self::prepared($query, $bindvals);
    }
	
	public function GetFirst($query) {
	    $qRes = self::query($query);
	    if($qRes) {
	        if($qRes->count > 0) { 
	            return $qRes->row;
	        } else {
	            return false;
	        }
	    } else {
	        $this->log("Invalid query", YG_WARNING);
			return false;
	    }
	}
    
	
	public static function InsertID() {
		return self::$mysqli->insert_id;
	}
	
	public static function error() {
		return self::$mysqli->error;
	}
	
	public static function close() {
		if(self::$mysqli->close()) {
			return true;
		} else {
			return false;
		}
	}
}

class ResultSet extends Core {
    private $mysqli, $rs, $current, $query;
    
    public $rows, $count, $fields, $row;
    
    public function __construct($mysqli, $resultSet, $query) {
        if($resultSet !== false) {
            $this->mysqli = $mysqli;
            $this->rs = $resultSet;
            $this->query = $query;
            
            $this->count = $resultSet->num_rows;
            if($this->count > 0) { 
                $this->current = 1; /* Begin on the row 1 */
                $myData = $this->rs->fetch_assoc();
                $this->fields = array_keys($myData);
                $this->row = $myData;
            } else { /* If no rows returned */
                $this->current = 0; /* No rows to display */
                $this->fields = $this->row = array(); /* Empty resultset */
            }
        } else {
            self::log("Invalid Result Set for initialize class", YG_WARNING);
			return false;
        }
    }
    
    /* NAVIGATION FUNCTIONS */
    
    public function hasNext() {
        return ($this->current <= $this->count);
    }
    
    public function next() {
        if($this->current > $this->count) return false; /* Oops, we can't go ahead because there are no more rows */
        
        /* Cool, we can get the next result */
        $myData = $this->rs->fetch_assoc();
        $this->row = $myData;
        $this->current++;
        return true;
    }
    
    public function previous() {
        if($this->current <= 1) return false; /* Oops, we can't go back because we are already in the beginning */
        
        /* Cool, we can get the previous result */
        $this->current--;
        if($this->rs->data_seek($this->current)) {
            $myData = $this->rs->fetch_assoc();
            $this->row = $myData;
            return true;
        } else {
            return false;
        }
    }
    
    public function rewind() {
        if($this->count <= 0) return true; /* There is no need to reset an empty resultset */
        
        /* Otherwise, lets rewind it! */
        $this->current = 1;
        if($this->rs->data_seek(0)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function jump($to) {
        if($this->count <= 0) return false; /* We can't jump through results in an empty resultset */
        if($to <= 1 || $to > $this->count) return false; /* Oops, we about to jump in an out of bounds area! */
        
        /* Otherwise, jump! */
        $this->current = $to;
        if($this->rs->data_seek(($to-1))) {
            return true;
        } else {
            return false;
        }
    }
    
    public function fetchCol($colname) {
        if(!in_array($colname, $this->fields)) { $this->log("Invalid column name '".$coluname."'.", YG_NOTICE); return false; }
        
        if($this->rewind()) {
		    $allRes = array();
			while($this->hasNext()) {
				$this->next();
				$allRes[] = $this->row[$colname];
			}
			$this->rewind(); /* Gracefully reset the resultset */
			return $allRes;
		} else {
			$this->log("Error at pushing resultset data", YG_WARNING);
			return false;
		}
    }
    
    public function pushAll() {
		if($this->rewind()) {
		    $allRes = array();
			while($this->hasNext()) {
				$this->next();
				$allRes[] = $this->row;
			}
			$this->rewind(); /* Gracefully reset the resultset */
			return $allRes;
		} else {
			$this->log("Error at pushing resultset data", YG_WARNING);
			return false;
		}
	}
}

final class BindParam { 
    private $values = array(), $types = ''; 
    
    public function add( &$value ){ 
        $this->values[] = $value;
        if(gettype($value) == 'string') $this->types .= "s";
        else if(gettype($value) == 'integer') $this->types .= "i";
        else if(gettype($value) == 'double') $this->types .= "d";
        else $this->types .= "s";
    } 
    
    public function get(){ 
        return array_merge(array($this->types), $this->values); 
    } 
} 