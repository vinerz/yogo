<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
function get_template_part( $slug ) {
	fire_hook("get_template_part_{$slug}");

	$templates = array();
	$templates[] = "{$slug}.php";

	locate_template($templates, true, false);
}

function get_query_template( $type, $templates = array() ) {
	$type = preg_replace( '|[^a-z0-9-]+|', '', $type );

	if ( empty( $templates ) )
		$templates = array("{$type}.php");

	return locate_template($templates);
}

function load_query_template( $type, $templates = array() ) {
    $located = get_query_template($type, $templates);
    
    if(!empty($located))
        load_template($located);
    else
        Core::log('Unable to locate template part \''.$type.'\'.');
}

function locate_template($template_names, $load = false, $require_once = true ) {
	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if (!$template_name)
			continue;
		if (file_exists(get_template_directory(true) . DS . $template_name)) {
			$located = get_template_directory(true) . DS . $template_name;
			break;
		}
	}

	if ($load && '' != $located)
		load_template($located, $require_once);

	return $located;
}

function load_template($_template_file, $require_once = true) {
	if ($require_once)
		require_once($_template_file);
	else
		require($_template_file);
}