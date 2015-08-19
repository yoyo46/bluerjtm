<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();
	
	public function dashboard() {
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
            ),
        ));
        $invoiceUnPaid = $this->Invoice->getData('count', array(
            'conditions' => array(
                'Invoice.complete_paid' => 0,
                'Invoice.paid' => 0,
                'Invoice.is_canceled' => 0,
            ),
        ));

        $this->Truck->bindModel(array(
            'hasOne' => array(
                'Ttuj' => array(
                    'className' => 'Ttuj',
                    'foreignKey' => 'truck_id',
                    'conditions' => array(
                        'Ttuj.status' => 1,
                        'Ttuj.is_pool' => 0,
                        'Ttuj.completed' => 0,
                    ),
                ),
            )
        ));
        $truckAvailable = $this->Truck->getData('count', array(
            'conditions' => array(
                'Truck.sold' => 0,
                'Ttuj.id' => NULL,
            ),
            'contain' => array(
            	'Ttuj'
        	),
        ));

        $this->set(compact(
        	'ttujSJ', 'invoiceUnPaid', 'truckAvailable'
    	));
		$this->render('home');
	}

    function notifications(){
        $this->paginate = $this->Notification->getData('paginate', array(
            'conditions' => array(
                'Notification.user_id' => $this->user_id
            ),
            'limit' => 20
        ));

        $Notifications = $this->paginate('Notification');

        $want_to_read_id = array();
        if(!empty($Notifications)){
            foreach ($Notifications as $key => $value) {
                if(empty($value['Notification']['read'])){
                    array_push($want_to_read_id, $value['Notification']['id']);
                }
            }

            if(!empty($want_to_read_id)){
                $this->Notification->updateAll(
                    array(
                        'Notification.read' => 1
                    ),
                    array(
                        'Notification.id' => $want_to_read_id,
                        'Notification.user_id' => $this->user_id
                    )
                );
            }
        }

        $this->set('title_for_layout', __('ERP RJTM | Notifikasi'));
        $this->set('module_title', __('Notifikasi'));
        $this->set('Notifications', $Notifications);
    }
}
