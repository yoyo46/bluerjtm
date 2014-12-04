<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();
	
	public function display() {
		$this->set('active_menu', 'dashboard');
		$this->set('title_for_layout', __('ERP RJTM | Dashboard'));
		$this->set('module_title', __('Dashboard'));
		$this->set('sub_module_title', __('Control panel'));
		$this->render('home');
	}
}
