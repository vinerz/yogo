<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

/* GENERAL YOGO INFORMATIONS */
define('YG_SITE_TITLE',      'Default App');
define('YG_SITE_URL',        'http://app.com');
define('YG_SITE_HOME',       YG_SITE_URL . '/' . 'home.php');
define('YG_SITE_SHORT_NAME', 'DA');
define('YG_VERSION_SHORT',   '1.0.0');
define('YG_VERSION_FULL',    '1.0.0rev1');
define('YG_ENABLE_SSL',      false);
define('YG_LANG',            'pt_BR');
define('YG_ENABLE_THEME',    false);
define('YG_SKIP_LOGINCHECK', true);

/* YOGO HOOKS CACHING */
define('YG_CACHE_HOOKS',         false);
define('YG_CACHE_HOOKS_TIMEOUT', 300);

/* YOGO FILEPATHS */
define('YG_CORE_BASE_PATH', dirname(__FILE__) );
define('YG_SHARED_PATH',    YG_CORE_BASE_PATH . '/shared');
define('YG_CACHE_PATH',     YG_SHARED_PATH . '/cache');

/* YOGO DATABASE */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'app');

date_default_timezone_set('America/Sao_Paulo');
