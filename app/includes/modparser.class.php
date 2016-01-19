<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

class ModParser extends Core {
	private $string, $params;
    
    public function __construct() {
    }

	private function parseLines($lines) {
		foreach($lines as $line) $parsedLine = $this->parseLine($line); //Parse the line
        
        $optional_params = array('textdomain', 'domainpath', 'author', 'name', 'description');
        
        foreach($this->params as $key => &$param) 
            if($param == "" && !in_array($key, $optional_params)) return false;
        
        return true;
	}

	private function parseLine($line) {
		$line = trim($line);
		
		if(empty($line)) return false; //Empty line
		
		if(strpos($line, '@') === 0) {
			$param = substr($line, 1, strpos($line, ' ') - 1); //Get the parameter name
			$value = trim(substr($line, strlen($param) + 2)); //Get the value
			if($this->setParam($param, $value)) return false; //Parse the line and return false if the parameter is valid
		}
		
		return $line;
	}

	private function setupParams($type = "") {
		$params = array(
			'name'	            =>	'',
			'description'	    =>	'',
			'plugin_url'	    =>	'',
			'plugin_sysname'    =>	'',
			'author'	        =>	'',
			'plugin_version'	=>	'',
            'textdomain'        =>  '',
            'domainpath'        =>  ''
		);

		$this->params = $params;
	}

	private function setParam($param, $value) {
		if(!array_key_exists($param, $this->params)) return false;
		
		if(empty($this->params[$param])) {
			$this->params[$param] = $value;
		} else {
			$arr = array($this->params[$param], $value);
			$this->params[$param] = $arr;
		}
		return true;
	}

	public function loadString($string) {
		$this->string = $string;
		$this->setupParams();
        return $this;
	}

	public function parse() {
		if(preg_match('/\*\*(.*)\*/s', $this->string, $comment) === false) {
			$this->log("Bad plugin header received", YG_NOTICE);
            return false;
        }
        
        if(count($comment) == 0) { $this->log("Bad plugin header received", YG_NOTICE); return false; }

		$comment = trim($comment[1]);

		if(preg_match_all('/\s*\*?(.*)/m', $comment, $lines) === false) {
			$this->log("Bad plugin header received", YG_NOTICE);
            return false;
        }
        
        if(count($lines) == 0) { $this->log("Bad plugin header received", YG_NOTICE); return false; }
		
        $parseResult = $this->parseLines($lines[1]);
        
        if(!$parseResult) $this->log("Plugin header missing params", YG_NOTICE);
        
        return $parseResult;
	}
    
    public function getParams() {
        return $this->params;
    }
}