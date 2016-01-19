<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

require_once( dirname(__FILE__) . "/modparser.class.php");

class PluginManager extends Core {
	private $_path;
	var $loadedPlugins;
	
	public function __construct() {
        $this->loadedPlugins = array();
		$this->_path = dirname( dirname(__FILE__)  ) . DS . 'plugins';
        if(!is_dir($this->_path)) $this->log('Plugin directory is missing!', YG_FATAL);
	}
    
    private function _listPluginsFiles($type) {
        $path = $this->_path . DS . $type;
        if(!is_dir($path)) $this->log('Plugins directory of type '.$type.' is missing!', YG_FATAL);
        return glob( $path . DS . '*' . DS . '*-main.php' ); /* Yogo only catches plugins by the main file *-main.php */
    }
    
    private function _getPluginInformation($file) {
        $ModParser = new ModParser();
        if(!$ModParser->loadString( file_get_contents($file) )->parse()) {
            $this->log("Plugin information parse error", YG_NOTICE);
            return false;
        } else {
            return $ModParser->getParams();
        }
    }
    
    public function loadEnabled() {
        $enPlugins = $this->_listPluginsFiles('enabled');
        foreach($enPlugins as $plugin) {
            $plugin = realpath($plugin);
            $info = $this->_getPluginInformation($plugin);
            $path = $this->_path . DS . 'enabled';
            $filename = str_replace($path, "", $plugin);
            if($info) {
                $sysname = $info['plugin_sysname'];
                if(!array_key_exists($sysname, $this->loadedPlugins)) {
                    if( !empty( $info['textdomain'] ) ) {
                        $relpath = 'enabled' . DS . trim(str_replace($path, '', dirname($plugin)), DS);
                        if( !empty( $info['domainpath'] ) ) 
                            $relpath .= DS . trim($info['domainpath'], '\/');
                        
                        if( !load_plugin_textdomain( $info['textdomain'], $relpath ) )
                            Core::log('Failed to get localization data for plugin \''.$filename.'\'.', YG_NOTICE);
                    }
                    if(require_once($plugin)) {
                        $this->loadedPlugins[$sysname] = $plugin;
                    } else {
                        $this->log("Unable to require plugin file ".$filename, YG_NOTICE);
                    }
                } else {
                    $dpfilename = str_replace($path, "", $this->loadedPlugins[$sysname]);
                    $this->log("Duplicated plugin sysname '".$sysname."' at file ".$filename.", previously declared at ".$dpfilename.". Ignoring this one.", YG_NOTICE);
                }
            } else {
                $this->log("Unable to load plugin file ".$filename.". Failed to parse the meta-data.", YG_NOTICE);
            }
        }
    }
}