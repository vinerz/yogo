<?php
/**
  *  @name              cURL Module
  *  @description       Module for cURL support
  *  @author            Vinicius Tavares <vinerz@vinerz.net>
  *  @plugin_url        http://www.xenon-corporation.com/yogo/plugin/curl
  *  @plugin_sysname    curl
  *  @plugin_version    1.0.0
  */
  
class cURL extends Core {
    public $fetchData = false;
    private $_curl, $_isDown = false;
    
    public function __construct( $url, $opts = array() ) {
        $defaults = array(
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true
        );
        
        $settings = array_intersect_key($opts + $defaults, $defaults);
        
        $this->_curl = curl_init( $url );
        curl_setopt_array($this->_curl, $settings);
    }
    
    public function exec() {
        $fetchData = curl_exec($this->_curl);
        
        if($fetchData) {
            $this->fetchData = $fetchData;
            return true;
        } else {
            $this->fetchData = false;
            return false;
        }
    }
    
    public function httpCode() {
        return curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
    }
    
    public function getError() {
        return array(
            'TEXT' => curl_error($this->_curl),
            'CODE' => curl_errno($this->_curl)
        );
    }
}