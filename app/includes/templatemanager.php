<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */

require_once(YG_INCLUDEPATH . DS . "templateloaders.php");

function yg_title($sep = null) {
    $title = '';
    if(defined("YG_CUSTOM_TITLE")) {
        $title = __(YG_CUSTOM_TITLE);
    } else {
        if(is_home()) $title = __("Home");
    }
    
    if(empty($title))
        $sep = '';
    else
        if(empty($sep) || !is_string($sep)) $sep = " - ";
    
    echo YG_SITE_TITLE . $sep . $title;
}

function yg_header() {
    /* APPEND CUSTOM HEAD TAGS HERE */
    yg_meta();
    yg_print_styles();
}

function yg_meta() {
    fire_hook("yg_meta");
}

$yg_metas = array();
function add_meta($metakey, $metacontent, $httpequiv = false) {
    global $yg_metas;
    if(!isset($yg_metas[$metakey])) {
        $yg_metas[$metakey] = array($metacontent, $httpequiv);
        return true;
    } else {
        return false;
    }
}

function print_metas() {
    global $yg_metas;
    foreach($yg_metas as $metakey => $metacontent) {
        $metakey = preg_replace( '|[^A-Za-z0-9-]+|', '', $metakey );
        $metatype = ($metacontent[1] === true) ? 'http-equiv' : 'name';
        $metacontent = htmlspecialchars($metacontent[0]);
        echo '<meta '.$metatype.'="'.$metakey.'" content="'.$metacontent.'">'."\n";
    }
}

add_action('yg_meta', 'print_metas');

add_meta('generator', 'Yogo '.YG_VERSION_FULL);

function get_header() {
    $template = get_query_template('header');
    if(!empty($template)) load_template($template, true);
    else Core::log('Header file missing for theme', YG_WARNING);
}

function get_footer() {
    $template = get_query_template('footer');
    if(!empty($template)) load_template($template, true);
    else Core::log('Footer file missing for theme', YG_WARNING);
}

function get_template_directory($abs = false) {
    if(!$abs)  return get_site_url(YG_TEMPLATEPATH_REL . "/" . YG_THEME);
    else       return YG_TEMPLATEPATH . DS . YG_THEME;
}

function template_directory() {
    echo get_template_directory();
}