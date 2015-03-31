<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();
	
	public function display() {
		$this->set('active_menu', 'dashboard');
		$this->set('title_for_layout', __('ERP RJTM | Dashboard'));
		$this->set('module_title', __('Dashboard'));
		$this->set('sub_module_title', __('Control panel'));
        $this->loadModel('Ttuj');
        $this->loadModel('Invoice');
        $this->loadModel('Truck');

        $ttujSJ = $this->Ttuj->getData('count', array(
            'conditions' => array(
                'Ttuj.is_sj_completed' => 0,
                'Ttuj.status' => 1,
            ),
        ), false);
        $invoiceUnPaid = $this->Invoice->getData('count', array(
            'conditions' => array(
                'Invoice.complete_paid' => 0,
                'Invoice.paid' => 0,
                'Invoice.is_canceled' => 0,
            ),
        ), false);

        $this->Truck->bindModel(array(
            'hasOne' => array(
                'Ttuj' => array(
                    'className' => 'Ttuj',
                    'foreignKey' => 'truck_id',
                    'conditions' => array(
                        'Ttuj.status' => 1,
                        'Ttuj.is_pool' => 0,
                    ),
                ),
            )
        ));
        $truckAvailable = $this->Truck->getData('count', array(
            'conditions' => array(
                'Truck.status' => 1,
                'Truck.sold' => 0,
                'Ttuj.id' => NULL,
            ),
            'contain' => array(
            	'Ttuj'
        	),
        ), false);

        $this->set(compact(
        	'ttujSJ', 'invoiceUnPaid', 'truckAvailable'
    	));
		$this->render('home');
	}
}
