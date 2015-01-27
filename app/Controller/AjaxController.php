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
	// function getNopol( $from_city_id = false, $to_city_id = false ) {
	// 	$this->loadModel('UangJalan');
	// 	// $data = $this->UangJalan->getNopol($customer_id, $from_city_id, $to_city_id);
	// 	$data = $this->UangJalan->getNopol($from_city_id, $to_city_id);

	// 	if( !empty($data) ) {
	// 		$result = $data['result'];
	// 		$uangJalan = $data['uangJalan'];
	// 	}

	// 	$this->set(compact(
	// 		'result', 'uangJalan'
	// 	));
	// }

	function getInfoTruck( $from_city_id = false, $to_city_id = false, $truck_id = false ) {
		$this->loadModel('UangJalan');
		$this->loadModel('Truck');
		$result = $this->Truck->getInfoTruck($truck_id);

		if( !empty($result) ) {
			$uangJalan = $this->UangJalan->getNopol( $from_city_id, $to_city_id, $result['Truck']['capacity'] );
		}

		$this->set(compact(
			'result', 'uangJalan'
		));
		$this->render('get_nopol');
	}

	function getInfoTtuj($ttuj_id, $is_payment = false){
		$this->loadModel('Ttuj');
		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			),
			'contain' => array(
				'UangJalan'
			)
		));
		
		if(!empty($data_ttuj)){
			if(!empty($data_ttuj['TtujTipeMotor'])){
				$this->loadModel('TipeMotor');
				$tipe_motor_list = array();
				foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
					$tipe_motor = $this->TipeMotor->getData('first', array(
						'conditions' => array(
							'TipeMotor.id' => $value['tipe_motor_id']
						)
					));
					$tipe_motor_list[$tipe_motor['TipeMotor']['id']] = $tipe_motor['TipeMotor']['name'];
				}
			}
			$this->request->data = $data_ttuj;
		}
		
		$this->set('tipe_motor_list', $tipe_motor_list);

	}

	function getColorTipeMotor($tipe_motor_id, $ttuj_id){
		$this->loadModel('Ttuj');
		$data_ttuj = $this->Ttuj->TtujTipeMotor->getData('first', array(
			'conditions' => array(
				'TtujTipeMotor.ttuj_id' => $ttuj_id,
				'TtujTipeMotor.tipe_motor_id' => $tipe_motor_id
			)
		));

		$this->loadModel('ColorMotor');
		$this->loadModel('TipeMotor');

		if(!empty($data_ttuj['TtujTipeMotor']['tipe_motor_id'])){
			$tipe_motor = $this->TipeMotor->getData('first', array(
				'conditions' => array(
					'TipeMotor.id' => $data_ttuj['TtujTipeMotor']['tipe_motor_id']
				)
			));
			$data_ttuj = array_merge($data_ttuj, $tipe_motor);
			if(!empty($data_ttuj)){
				$this->set(compact('data_ttuj'));
			}
		}
	}

	function getInfoLaka($ttuj_id = false){
		$this->loadModel('Ttuj');
		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			)
		));

		$this->set(compact('data_ttuj'));
	}

	function getTtujCustomerInfo($customer_id){
		$this->loadModel('Ttuj');
		$this->loadModel('Lku');
		$ttuj_id = $this->Ttuj->getData('list', array(
			'conditions' => array(
				'Ttuj.customer_id' => $customer_id
			),
			'group' => array(
				'Ttuj.customer_id'
			),
			'fields' => array(
				'Ttuj.id'
			)
		));
		
		if(!empty($ttuj_id)){
			$lkus = $this->Lku->getData('all', array(
				'conditions' => array(
					'Lku.ttuj_id' => $ttuj_id
				),
				'contain' => array(
					'Ttuj'
				)
			));
		}

		$arr = array();
		if(!empty($lkus)){
			foreach ($lkus as $key => $value) {
				$arr[$value['Lku']['id']] = sprintf('%s (%s)', date('d F Y', strtotime($value['Ttuj']['ttuj_date'])), $value['Ttuj']['no_ttuj']);
			}
		}
		$lkus = $arr;
		
		$this->set('lkus', $lkus);
	}

	function getTtujInfoLku($lku_id){
		$this->loadModel('Lku');
		$lku = $this->Lku->getData('first', array(
			'conditions' => array(
				'Lku.id' => $lku_id
			),
			'contain' => array(
				'Ttuj'
			)
		));
		$this->set('lku', $lku);
	}

	function getInfoTtujRevenue($ttuj_id){
		$this->loadModel('Ttuj');
		$this->loadModel('TarifAngkutan');
		$this->loadModel('Customer');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			)
		));

		$data_revenue_detail = array();
		if(!empty($data_ttuj)){
			$data_ttuj = $this->Ttuj->Customer->getMerge($data_ttuj, $data_ttuj['Ttuj']['customer_id']);
			$this->request->data = $data_ttuj;
			$this->request->data['Revenue']['customer_id'] = $data_ttuj['Ttuj']['customer_id'];

			if(!empty($data_ttuj['TtujTipeMotor'])){
				$this->loadModel('TipeMotor');
				$this->loadModel('City');
				$tipe_motor_list = array();
				foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
					$tipe_motor = $this->TipeMotor->getData('first', array(
						'conditions' => array(
							'TipeMotor.id' => $value['tipe_motor_id']
						)
					));

					if(!empty($tipe_motor['TipeMotor']['name'])){
						$tipe_motor_name = sprintf('%s - %s', $tipe_motor['TipeMotor']['name'], $tipe_motor['ColorMotor']['name']);
						$tipe_motor_id = $tipe_motor['TipeMotor']['id'];
					}
					
					$price_unit = false;
					if($data_ttuj['Ttuj']['is_retail']){
						$city = $this->City->getData('first', array(
							'conditions' => array(
								'City.id' => $value['city_id']
							)
						));
						if(!empty($city['City']['name'])){
							$to_city_name = $city['City']['name'];
							$to_city_id = $city['City']['id'];
						}

						$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $value['city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity']);
						if(!empty($tarif['jenis_unit'])){
							if($tarif['jenis_unit'] != 'per_unit'){
								$tarif['tarif'] = false;
							}
						}
						$price_unit = $tarif['tarif'];
					}else{
						$to_city_name = $data_ttuj['Ttuj']['to_city_name'];
						$to_city_id = $data_ttuj['Ttuj']['to_city_id'];
					}

					$data_revenue_detail[$key] = array(
						'TtujTipeMotor' => array(
							
							'qty' => $value['qty']
						),
						'RevenueDetail' => array(
							'to_city_name' => $to_city_name,
							'price_unit' => $price_unit,
							'qty_unit' => $value['qty'],
							'tipe_motor_id' => $tipe_motor_id,
							'city_id' => $to_city_id,
							'TipeMotor' => array(
								'name' => $tipe_motor_name,
							),
							'ttuj_tipe_motor_id' => $value['id']
						)
					);
				}
			}
		}

		$tarif_angkutan = false;
		if(!$data_ttuj['Ttuj']['is_retail']){
			$tarif_angkutan = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $data_ttuj['Ttuj']['to_city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity']);
		}

		$customers = $this->Customer->find('list', array(
			'conditions' => array(
				'Customer.status' => 1
			)
		));

		// debug($data_revenue_detail);die();
		$this->set(compact('data_revenue_detail', 'tarif_angkutan', 'customers'));
	}
}
?>