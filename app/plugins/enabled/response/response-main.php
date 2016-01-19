<?php
/**
  *  @name              Response
  *  @description       Send server response to client with JSON format
  *  @author            Vinicius Tavares <vinerz@vinerz.net>
  *  @plugin_url        http://www.xenon-corporation.com/yogo/plugin/response
  *  @plugin_sysname    response
  *  @plugin_version    1.0.0
  */

class Response extends Core {
    private static $sent = false, $stack = false;
    
    private function __construct() {}
    
	public static function send($content, $type = 'success', $nextAction = false) {
        if(self::$sent) {
            self::log("Cannot send response information - another response has already been sent at ".self::$stack.".", YG_WARNING);
            return false;
        }
		if($type == 'notice') {
			if($nextAction) echo json_encode(array("status" => $type, "content" => $content, "nextAction" => $nextAction));
			else echo json_encode(array("status" => $type, "content" => $content));
		} else {
			echo json_encode(array("status" => $type, "content" => $content));
		}
        
        self::$stack = self::getStack();
        self::$sent = true;
        
        return true;
	}
}
?>