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
		$this->loadModel('City');

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

			$toCities = array();
			if(!empty($data_ttuj['TtujTipeMotor'])){
				$ttuj_city_id = Set::extract('/TtujTipeMotor/city_id', $data_ttuj);
                if(!empty($ttuj_city_id)){
                	if( !$data_ttuj['Ttuj']['is_retail'] ){
                		$toCities = $this->City->toCities($data_ttuj['Ttuj']['to_city_id']);
                	}else{
                		$toCities = $this->City->toCities($ttuj_city_id);
                	}
                }

				$this->loadModel('TipeMotor');
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
					}else{
						$to_city_name = $data_ttuj['Ttuj']['to_city_name'];
						$to_city_id = $data_ttuj['Ttuj']['to_city_id'];

						$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $data_ttuj['Ttuj']['to_city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity']);
					}

					$data_revenue_detail[$key] = array(
						'TtujTipeMotor' => array(
							
							'qty' => $value['qty']
						),
						'RevenueDetail' => array(
							'to_city_name' => $to_city_name,
							'price_unit' => $tarif,
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

		$customers = $this->Customer->find('list', array(
			'conditions' => array(
				'Customer.status' => 1
			)
		));

		// $toCities = $this->City->toCities();

		// debug($data_revenue_detail);die();
		$this->set(compact('data_revenue_detail', 'customers', 'toCities'));
	}

	public function event_add( $nopol = false, $date = false ) {
        $this->loadModel('CalendarEvent');
        $this->loadModel('Truck');
        $this->set('sub_module_title', 'Tambah Event');
		$isAjax = $this->RequestHandler->isAjax();
        $msg = array(
			'class' => 'error',
			'text' => ''
		);
		$truck = $this->Truck->getData('first', array(
			'conditions' => array(
				'Truck.nopol' => $nopol,
				'Truck.status' => 1
			)
		), false);

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['CalendarEvent']['date'] = $date;
            $data['CalendarEvent']['nopol'] = $nopol;

            if( !empty($truck) ) {
            	$data['CalendarEvent']['truck_id'] = $truck['Truck']['id'];
            }

            if( !empty($id) ){
                $this->CalendarEvent->id = $id;
                $msg = 'merubah';
            }else{
                $this->CalendarEvent->create();
                $msg = 'menambah';
            }
            $this->CalendarEvent->set($data);

            if($this->CalendarEvent->validates($data)){
                if($this->CalendarEvent->save($data)){
                	$msg = array(
						'class' => 'success',
						'text' => sprintf(__('Sukses %s Event'), $msg),
					);
                    $this->Log->logActivity( sprintf(__('Sukses %s Event'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    // $this->redirect(array(
                    //     'controller' => 'revenues',
                    //     'action' => 'monitoring_truck'
                    // ));
                }else{
                	$msg = array(
						'class' => 'error',
						'text' => sprintf(__('Gagal %s Event'), $msg),
					);
                    // $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Event'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Event'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
            	$msg = array(
					'class' => 'error',
					'text' => sprintf(__('Gagal %s Event'), $msg),
				);
            }

            if(!$isAjax){
				$this->MkCommon->setCustomFlash($msg['text'], $msg['class']);
				$this->redirect(array(
                    'controller' => 'revenues',
                    'action' => 'monitoring_truck'
                ));
			}
        }

        $this->loadModel('CalendarIcon');
        $this->loadModel('CalendarColor');
        $calendarIcons = $this->CalendarIcon->getData('all', array(
            'conditions' => array(
                'CalendarIcon.status' => 1
            ),
            'fields' => array(
                'CalendarIcon.id', 'CalendarIcon.name'
            )
        ));
        $calendarColors = $this->CalendarColor->getData('all', array(
            'conditions' => array(
                'CalendarColor.status' => 1
            ),
            'fields' => array(
                'CalendarColor.id', 'CalendarColor.name'
            )
        ));
        $optionIcons = array();
        $optionColors = array();

        if( !empty($calendarIcons) ) {
        	foreach ($calendarIcons as $key => $calendarIcon) {
        		$optionIcons[$calendarIcon['CalendarIcon']['id']] = $calendarIcon['CalendarIcon']['name'];
        	}
        }
        if( !empty($calendarColors) ) {
        	foreach ($calendarColors as $key => $calendarColor) {
        		$optionColors[$calendarColor['CalendarColor']['id']] = $calendarColor['CalendarColor']['name'];
        	}
        }

        $this->set(compact(
            'calendarIcons', 'calendarColors', 'optionIcons',
            'optionColors', 'msg', 'isAjax'
        ));
	}

	function getInfoRevenueDetail($ttuj_id, $city_id){
		$this->loadModel('Ttuj');
		$this->loadModel('TarifAngkutan');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			),
			'contain' => array(
				'TtujTipeMotor' => array(
					'conditions' => array(
						'TtujTipeMotor.city_id' => $city_id
					)
				)
			)
		));

		$detail = array();
		if(!empty($data_ttuj)){
			$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $city_id, $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity']);

			$detail = array(
				'RevenueDetail' => array(
					'price_unit' => $tarif,
				)
			);
		}

		$this->set(compact('detail'));
	}

	function getInvoiceInfo($customer_id){
		
	}
}
?>