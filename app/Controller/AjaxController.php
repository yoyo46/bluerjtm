<?php
App::uses('AppController', 'Controller');
class AjaxController extends AppController {

	public $name = 'Ajax';
	public $uses = array();
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->layout = false;
	}

	// function getKotaAsal( $customer_id = false ) {
	function getKotaAsal() {
		$this->loadModel('UangJalan');
		$resultCity = $this->UangJalan->getKotaAsal();

		$this->set(compact(
			'resultCity'
		));
	}

	// function getKotaTujuan( $from_city_id = false, $customer_id = false ) {
	function getKotaTujuan( $from_city_id = false ) {
		$this->loadModel('UangJalan');
		// $resultCity = $this->UangJalan->getKotaTujuan($customer_id, $from_city_id);
		$resultCity = $this->UangJalan->getKotaTujuan($from_city_id);

		$this->set(compact(
			'resultCity'
		));
	}

	// function getNopol( $from_city_id = false, $to_city_id = false, $customer_id = false ) {
	function getNopol( $from_city_id = false, $to_city_id = false ) {
		$this->loadModel('UangJalan');
		// $data = $this->UangJalan->getNopol($customer_id, $from_city_id, $to_city_id);
		$data = $this->UangJalan->getNopol($from_city_id, $to_city_id);

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
		$result = $this->Truck->getInfoTruck($truck_id);

		$this->set(compact(
			'result'
		));
	}
}
?>