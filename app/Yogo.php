<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('DS',                  DIRECTORY_SEPARATOR);
define('YG_BASEPATH',         dirname(__FILE__));
define('YG_INCLUDEPATH',      YG_BASEPATH . DS . 'includes');
define('YG_TEMPLATEPATH',     dirname(__FILE__) . DS . 'themes');
define('YG_TEMPLATEPATH_REL', '/app/themes');
define('YG_LANG_DIR',         YG_BASEPATH . DS . 'languages');
define('YG_PLUGIN_DIR',       YG_BASEPATH . DS . 'plugins');
define('YG_PLUGINDIR_REL',    '/app/plugins');

require_once(YG_BASEPATH . DS .    'settings.inc.php');
require_once(YG_INCLUDEPATH . DS . 'bcrypt.class.php');
require_once(YG_INCLUDEPATH . DS . 'core.class.php');
require_once(YG_INCLUDEPATH . DS . 'session.class.php');
require_once(YG_INCLUDEPATH . DS . 'stringmanager.php');
require_once(YG_INCLUDEPATH . DS . 'functions.php');

if(!Session::isLogged() && (!defined('YG_SKIP_LOGINCHECK') || YG_SKIP_LOGINCHECK == false)) {
    header('Location: ' . get_site_url() . '/login.php?msg=notauthorized&returnpath='.rawurlencode(get_current_url()));
    exit();
}

require_once(YG_INCLUDEPATH . DS . 'pluginmanager.class.php');
require_once(YG_INCLUDEPATH . DS . 'database.class.php');
require_once(YG_INCLUDEPATH . DS . 'translation.class.php');
if(YG_ENABLE_THEME) require_once(YG_INCLUDEPATH . DS . 'templatemanager.php');
#require_once(YG_INCLUDEPATH . DS . 'formgen.php');

DB::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

fire_hook('init');
load_default_domain();

$PluginManager = new PluginManager();
$PluginManager->loadEnabled();

fire_hook('modload');

if( YG_ENABLE_THEME ) {
    $template = get_query_template('functions');
    if( !empty($template) ) load_template($template, true);
    else Core::log('Mandatory functions file missing for theme', YG_WARNING);
}

register_shutdown_function('yg_shutdown');