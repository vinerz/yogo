<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
class Form extends Core {
    private $_form_data = array();
    private $_default_form_attributes = array(
                                             'name' => 'form',
                                             'id' => 'form',
                                             'method' => 'get',
                                             'action' => '#'
                                             );
    private $_default_element_attributes = array(
                                             'name' => 'field',
                                             'id' => 'field',
                                             );
                                             
    public function __construct($form_info, $form_content) {
        $this->_form_data['form'] = $form_info;
        $this->_form_data['content'] = $form_content;
    }
    
    public function load($registry_id) {
    }
    
    public function output() {
    }
}