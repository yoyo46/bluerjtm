<?php
App::uses('AppController', 'Controller');
class TrucksController extends AppController {
	public $uses = array();

	public function index() {
		$this->set('active_menu', 'trucks');
		$this->set('title_for_layout', __('ERP RJTM | Data Truk'));
		$this->set('module_title', __('Truk'));
		$this->set('sub_module_title', __('Data Truk'));
	}
}
