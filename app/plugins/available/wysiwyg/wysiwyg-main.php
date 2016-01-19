<?php
/**
  *  @name              WYSIWYG Editor
  *  @description       Professional WYSIWYG editor. Usage: put the class beautyeditor in the textareas that you want to enhance.
  *  @author            Vinicius Tavares <vinerz@vinerz.net>
  *  @plugin_url        http://www.xenon-corporation.com/yogo/plugin/wysiywg
  *  @plugin_sysname    wysiywg
  *  @plugin_version    1.0.0
  *  @_textdomain       wysiywg
  *  @_domainpath       /languages/
  */

$urlpath = get_plugin_url(__FILE__);
yg_register_style('wysiwyg', $urlpath . 'lib/css/redactor.css');
yg_register_script('wysiwyg-core', $urlpath . 'lib/js/redactor.min.js', array(), true);
yg_register_script('wysiwyg-init', $urlpath . 'lib/js/redactor-init.js', array(), true);

yg_enqueue_style('wysiwyg');
yg_enqueue_script('wysiwyg-core');
yg_enqueue_script('wysiwyg-init');