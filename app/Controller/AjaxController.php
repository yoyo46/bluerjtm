<?php
App::uses('AppController', 'Controller');
class AjaxController extends AppController {

	public $name = 'Ajax';
	public $uses = array();
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->layout = false;
	}

	function getKotaAsal( $customer_id = false ) {
		$this->loadModel('UangJalan');
		$resultCity = $this->UangJalan->getKotaAsal($customer_id);

		$this->set(compact(
			'resultCity'
		));
	}

	function getKotaTujuan( $from_city_id = false, $customer_id = false ) {
		$this->loadModel('UangJalan');
		$resultCity = $this->UangJalan->getKotaTujuan($customer_id, $from_city_id);

		$this->set(compact(
			'resultCity'
		));
	}

	function getNopol( $from_city_id = false, $to_city_id = false, $customer_id = false ) {
		$this->loadModel('UangJalan');
		$data = $this->UangJalan->getNopol($customer_id, $from_city_id, $to_city_id);

		if( !empty($data) ) {
			$result = $data['result'];
			$uangJalan = $data['uangJalan'];
		}

		$this->set(compact(
			'result', 'uangJalan'
		));
	}

	function getInfoTruck( $truck_id = false ) {
		$this->loadModel('Truck');
		$result = $this->Truck->getData('first', array(
			'conditions' => array(
				'Truck.status' => 1,
				'Truck.id' => $truck_id,
			),
			'contain' => array(
				'Driver'
			),
		));

		$this->set(compact(
			'result'
		));
	}
}
?>