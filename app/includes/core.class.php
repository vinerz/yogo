<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

define('YG_MESSAGE', 1);
define('YG_NOTICE', 2);
define('YG_WARNING', 3);
define('YG_FATAL', 4);

define('YG_PRINT_LOG',  false);
define('YG_SHOW_STACK', true);
 
abstract class Core {
    public static $version = '1.0.0';
    
    public static function getStack() {
        $e = new Exception();
        $trace = $e->getTrace();
        $last_call = $trace[1];
        $file_name = str_replace($_SERVER["DOCUMENT_ROOT"]."/", "/", $last_call["file"]);
        return $file_name." on line ".$last_call["line"].": ".$last_call["class"].$last_call["type"].$last_call["function"];
    }
    
    public static function log($message, $level = YG_MESSAGE, $show_stack = false) {
        $stack = "";
        if($show_stack) {
            $stack = self::getStack()." - ";
        }

        $message = '['.date('d/m/Y H:i:s').'] '.$stack.''.$message.PHP_EOL;
        
        switch($level) {
            case 1:
            case 2:
                $log_name = "message.log";
            break;
            
            case 3:
            case 4:
                $log_name = "error.log";
            break;
            
            default:
                $log_name = "error.log";
        }
        
        $log_path = dirname(__FILE__) . "/../log/".$log_name;
        
        if(filesize($log_path) > 1024*1024*200) { /* 200kb limit */
            $increment = 1;
            while(file_exists(dirname(__FILE__) . "/../log/".$log_name.".".$increment)) $increment++;
            
            if(!file_put_contents(dirname(__FILE__) . "/../log/".$log_name.".".$increment, file_get_contents($log_path))) {
                echo "Failed at logging data!";
                return false;
            }
            
            if(file_put_contents($log_path, $message)) {
                if($level >= 3) {
                    exit();
                }
                return true;
            } else {
                if($level >= 3) {
                    exit();
                }
                echo "Failed at logging data!";
                return false;
            }
        }
        
        if(YG_PRINT_LOG) self::printError($message);
        
        if(file_put_contents($log_path, $message, FILE_APPEND)) {
            if($level >= 3) {
                exit();
            }
            return true;
        } else {
            if($level >= 3) {
                exit();
            }
            echo "Failed at logging data!";
        }
    }
    
    public static function printError($message) {
        echo $message . '<br>';
    }
}