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
		$fromCity = $this->UangJalan->getData('all', array(
			'conditions' => array(
				'UangJalan.status' => 1,
				'UangJalan.customer_id' => $customer_id,
			),
			'group' => array(
				'UangJalan.from_city_id'
			),
			'fields' => array(
				'UangJalan.from_city_id', 'FromCity.name'
			),
			'contain' => array(
				'FromCity'
			),
		));
		$resultCity = array();

		if( !empty($fromCity) ) {
			foreach ($fromCity as $key => $city) {
				$resultCity[$city['UangJalan']['from_city_id']] = $city['FromCity']['name'];
			}
		}

		$this->set(compact(
			'resultCity'
		));
	}

	function getKotaTujuan( $from_city_id = false, $customer_id = false ) {
		$this->loadModel('UangJalan');
		$toCity = $this->UangJalan->getData('all', array(
			'conditions' => array(
				'UangJalan.status' => 1,
				'UangJalan.customer_id' => $customer_id,
				'UangJalan.from_city_id' => $from_city_id,
			),
			'group' => array(
				'UangJalan.to_city_id'
			),
			'fields' => array(
				'UangJalan.to_city_id', 'ToCity.name'
			),
			'contain' => array(
				'ToCity'
			),
		));
		$resultCity = array();

		if( !empty($toCity) ) {
			foreach ($toCity as $key => $city) {
				$resultCity[$city['UangJalan']['to_city_id']] = $city['ToCity']['name'];
			}
		}

		$this->set(compact(
			'resultCity'
		));
	}

	function getNopol( $from_city_id = false, $to_city_id = false, $customer_id = false ) {
		$this->loadModel('UangJalan');
		$uangJalan = $this->UangJalan->getData('first', array(
			'conditions' => array(
				'UangJalan.status' => 1,
				'UangJalan.customer_id' => $customer_id,
				'UangJalan.from_city_id' => $from_city_id,
				'UangJalan.to_city_id' => $to_city_id,
			),
		));

		if( !empty($uangJalan) ) {
			$this->loadModel('Truck');
			$result = $this->Truck->getData('list', array(
				'conditions' => array(
					'Truck.status' => 1,
					'Truck.capacity' => $uangJalan['UangJalan']['capacity'],
				),
				'fields' => array(
					'Truck.id', 'Truck.nopol'
				),
			));
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