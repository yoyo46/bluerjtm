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

	function getInfoTruck( $from_city_id = false, $to_city_id = false, $truck_id = false, $customer_id = false ) {
		$this->loadModel('UangKuli');
		$this->loadModel('UangJalan');
		$this->loadModel('Truck');
		$this->loadModel('Ttuj');
		$result = $this->Truck->getInfoTruck($truck_id);

		if( !empty($result) ) {
			$sjOutstanding = $this->Ttuj->getSJOutstanding( $result['Truck']['driver_id'] );
			$uangJalan = $this->UangJalan->getNopol( $from_city_id, $to_city_id, $result['Truck']['capacity'] );
			$uangKuli = $this->UangKuli->getUangKuli( $from_city_id, $to_city_id, $customer_id, $result['Truck']['capacity'] );
			$uangKuliMuat = !empty($uangKuli['UangKuliMuat'])?$uangKuli['UangKuliMuat']:false;
			$uangKuliBongkar = !empty($uangKuli['UangKuliBongkar'])?$uangKuli['UangKuliBongkar']:false;
		}

		$this->set(compact(
			'result', 'uangJalan', 'uangKuliMuat',
			'uangKuliBongkar', 'sjOutstanding'
		));
		$this->render('get_nopol');
	}

	function getInfoTtuj($ttuj_id, $is_payment = false){
		$this->loadModel('Ttuj');
		$this->loadModel('PartsMotor');

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
						),
						'contain' => array(
							'GroupMotor'
						)
					));
					$tipe_motor_list[$tipe_motor['TipeMotor']['id']] = sprintf('%s (%s)', $tipe_motor['TipeMotor']['name'], $tipe_motor['GroupMotor']['name']);
				}
			}
			$this->request->data = $data_ttuj;
		}
		
		$this->set('tipe_motor_list', $tipe_motor_list);

		$part_motors = $this->PartsMotor->getData('list', array(
            'conditions' => array(
                'PartsMotor.status' => 1
            ),
            'fields' => array(
                'PartsMotor.id', 'PartsMotor.name'
            )
        ));
        $this->set(compact('part_motors'));
	}

	function getInfoTtujKsu($ttuj_id, $atpm = false){
		$this->loadModel('Ttuj');
		$this->loadModel('Perlengkapan');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			),
			'contain' => array(
				'UangJalan'
			)
		));
		
		if(!empty($data_ttuj)){
			$this->request->data = $data_ttuj;
		}

		$this->request->data['Ksu']['kekurangan_atpm'] = (($atpm == 'true') ? true : false);

		$perlengkapans = $this->Perlengkapan->getListPerlengkapan(2);
        $this->set(compact('perlengkapans'));
	}

	function getColorTipeMotor($tipe_motor_id, $ttuj_id){
		$this->loadModel('Ttuj');
		$data_ttuj = $this->Ttuj->TtujTipeMotor->getData('first', array(
			'conditions' => array(
				'TtujTipeMotor.ttuj_id' => $ttuj_id,
				'TtujTipeMotor.tipe_motor_id' => $tipe_motor_id
			)
		));

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

	function getValuePerlengkapan($perlengkapan_id, $ttuj_id){
		$this->loadModel('Ttuj');
		$data_ttuj = $this->Ttuj->TtujPerlengkapan->getData('first', array(
			'conditions' => array(
				'TtujPerlengkapan.ttuj_id' => $ttuj_id,
				'TtujPerlengkapan.perlengkapan_id' => $perlengkapan_id
			)
		));

		$this->set(compact('data_ttuj'));
	}

	function getInfoLaka($ttuj_id = false){
		$this->loadModel('Ttuj');
		$this->loadModel('Driver');
		$this->loadModel('Truck');
		$this->loadModel('City');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id,
				'Ttuj.is_pool <>' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
			)
		), false);

		if( !empty($data_ttuj) ) {
			$driver_id = $data_ttuj['Ttuj']['driver_id'];

			if(!empty($data_ttuj['Ttuj']['driver_penganti_id'])){
				$driver_id = $data_ttuj['Ttuj']['driver_penganti_id'];
			}
			
			$data_ttuj = $this->Driver->getMerge($data_ttuj, $driver_id);

            if( !empty($data_ttuj['Ttuj']['from_city_id']) ) {
                $data_ttuj['Laka']['from_city_name'] = $this->City->getCity( $data_ttuj['Ttuj']['from_city_id'], 'name' );
            }

            if( !empty($data_ttuj['Ttuj']['to_city_id']) ) {
                $data_ttuj['Laka']['to_city_name'] = $this->City->getCity( $data_ttuj['Ttuj']['to_city_id'], 'name' );
            }
		}

		$this->set(compact('data_ttuj'));
	}

	function getTtujCustomerInfo($customer_id = false){
		$this->loadModel('Ttuj');
		$this->loadModel('Lku');
		$default_conditions = array(
			'Ttuj.customer_id' => $customer_id
		);

		$ttuj_id = $this->Ttuj->getData('list', array(
			'conditions' => $default_conditions,
			'group' => array(
				'Ttuj.customer_id'
			),
			'fields' => array(
				'Ttuj.id'
			)
		));

		$lku_condition = array(
			'Lku.ttuj_id' => $ttuj_id,
			'Lku.status' => 1,
			'Lku.complete_paid' => 0
			// 'Lku.type_lku' => $type_lku
		);
		if(!empty($ttuj_id)){
			if(!empty($this->request->data['Lku']['date_from']) || !empty($this->request->data['Lku']['date_to'])){
				if(!empty($this->request->data['Lku']['date_from'])){
					$lku_condition['DATE_FORMAT(Lku.tgl_lku, \'%Y-%m-%d\') >='] = $this->MkCommon->getDate($this->request->data['Lku']['date_from']);
				}
				if(!empty($this->request->data['Lku']['date_to'])){
					$lku_condition['DATE_FORMAT(Lku.tgl_lku, \'%Y-%m-%d\') <='] = $this->MkCommon->getDate($this->request->data['Lku']['date_to']);
				}
			}

			$lkus = $this->Lku->getData('all', array(
				'conditions' => $lku_condition,
				'contain' => array(
					'Ttuj'
				)
			));
		}

		if(!empty($lkus)){
			$this->loadModel('LkuPaymentDetail');
			foreach ($lkus as $key => $value) {
				$lku_has_paid = $this->LkuPaymentDetail->getData('first', array(
					'conditions' => array(
						'LkuPaymentDetail.lku_id' => $value['Lku']['id'],
						'LkuPaymentDetail.status' => 1
					),
					'fields' => array(
						'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
					)
				));

				 $lkus[$key]['lku_has_paid'] = $lku_has_paid[0]['lku_has_paid'];
			}
		}
		
		$this->set('lkus', $lkus);
		$data_change = 'browse-invoice';
		$data_action = 'getTtujCustomerInfo';
		$title = 'Pembayaran LKU Customer';
		$this->set(compact('customer_id', 'data_change', 'data_action', 'title'));
	}

	function getTtujCustomerInfoKsu($customer_id = false){
		$this->loadModel('Ttuj');
		$this->loadModel('Ksu');
		$default_conditions = array(
			'Ttuj.customer_id' => $customer_id
		);

		$ttuj_id = $this->Ttuj->getData('list', array(
			'conditions' => $default_conditions,
			'group' => array(
				'Ttuj.customer_id'
			),
			'fields' => array(
				'Ttuj.id'
			)
		));

		$ksu_condition = array(
			'Ksu.ttuj_id' => $ttuj_id,
			'Ksu.status' => 1,
			'Ksu.complete_paid' => 0,
			'Ksu.kekurangan_atpm' => 0
		);
		if(!empty($ttuj_id)){
			if(!empty($this->request->data['Ksu']['date_from']) || !empty($this->request->data['Ksu']['date_to'])){
				if(!empty($this->request->data['Ksu']['date_from'])){
					$ksu_condition['DATE_FORMAT(Ksu.tgl_ksu, \'%Y-%m-%d\') >='] = $this->MkCommon->getDate($this->request->data['Ksu']['date_from']);
				}
				if(!empty($this->request->data['Ksu']['date_to'])){
					$ksu_condition['DATE_FORMAT(Ksu.tgl_ksu, \'%Y-%m-%d\') <='] = $this->MkCommon->getDate($this->request->data['Ksu']['date_to']);
				}
			}

			$ksus = $this->Ksu->getData('all', array(
				'conditions' => $ksu_condition,
				'contain' => array(
					'Ttuj'
				)
			));
		}

		if(!empty($ksus)){
			$this->loadModel('KsuPaymentDetail');
			foreach ($ksus as $key => $value) {
				$ksu_has_paid = $this->KsuPaymentDetail->getData('first', array(
					'conditions' => array(
						'KsuPaymentDetail.ksu_id' => $value['Ksu']['id'],
						'KsuPaymentDetail.status' => 1
					),
					'fields' => array(
						'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
					)
				));
				
				$ksus[$key]['ksu_has_paid'] = $ksu_has_paid[0]['ksu_has_paid'];
			}
		}
		
		$this->set('ksus', $ksus);
		$data_change = 'browse-invoice';
		$data_action = 'getTtujCustomerInfoKsu';
		$title = 'Pembayaran KSU Customer';
		$this->set(compact('customer_id', 'data_change', 'data_action', 'title'));
	}

	function getTtujInfoLku($lku_id){
		$this->loadModel('Lku');
		$lku = $this->Lku->getData('first', array(
			'conditions' => array(
				'Lku.id' => $lku_id,
				// 'Lku.type_lku' => $type_lku
			),
			'contain' => array(
				'Ttuj'
			)
		));
		$this->set('lku', $lku);
	}

	function getTtujInfoKsu($ksu_id){
		$this->loadModel('Ksu');
		$Ksu = $this->Ksu->getData('first', array(
			'conditions' => array(
				'Ksu.id' => $ksu_id,
				'Ksu.kekurangan_atpm' => 0
			),
			'contain' => array(
				'Ttuj'
			)
		));
		
		$this->set('Ksu', $Ksu);
	}

	function getInfoTtujRevenue( $ttuj_id = false, $customer_id = false ){
		$this->loadModel('Ttuj');
		$this->loadModel('TarifAngkutan');
		$this->loadModel('Customer');
		$this->loadModel('City');
		$this->loadModel('GroupMotor');
		$this->loadModel('Revenue');
		// $this->loadModel('TtujTipeMotorUse');
		$data_revenue_detail = array();

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.status' => 1,
				'Ttuj.is_draft' => 0,
                'Ttuj.id' => $ttuj_id,
			),
		), false);

		if(!empty($data_ttuj)){
			$data_ttuj = $this->Ttuj->TtujTipeMotor->getMergeTtujTipeMotor( $data_ttuj, $ttuj_id );
            $tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $data_ttuj['Ttuj']['to_city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity']);

            if( !empty($tarif['jenis_unit']) && $tarif['jenis_unit'] == 'per_truck' ) {
                $tarifTruck = $tarif;
            }

			if(!empty($customer_id)){
				$data_ttuj['Ttuj']['customer_id'] = $customer_id;
			}

			$data_ttuj = $this->Ttuj->Customer->getMerge($data_ttuj, $data_ttuj['Ttuj']['customer_id']);
			$this->request->data = $data_ttuj;
			$this->request->data['Revenue']['customer_id'] = $data_ttuj['Ttuj']['customer_id'];
            $this->request->data['Revenue']['date_revenue'] = $this->MkCommon->customDate($data_ttuj['Ttuj']['ttuj_date'], 'd/m/Y');
			$toCities = array();

			if(!empty($data_ttuj['TtujTipeMotor'])){
				foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
					$price_unit = false;
					$group_motor_name = false;
					$qtyTtuj = !empty($value[0]['qty'])?$value[0]['qty']:0;
					$group_motor_id = !empty($value['TipeMotor']['group_motor_id'])?$value['TipeMotor']['group_motor_id']:false;
					$groupMotor = $this->GroupMotor->getMerge($value, $group_motor_id);
		            $revenue_id = $this->Revenue->find('list', array(
		                'conditions' => array(
		                    'Revenue.ttuj_id' => $ttuj_id,
		                    'Revenue.status' => 1,
		                ),
		            ));
		            // $qtyUsed = $this->TtujTipeMotorUse->find('first', array(
		            //     'conditions' => array(
		            //         'TtujTipeMotorUse.revenue_id' => $revenue_id,
		            //         'TtujTipeMotorUse.group_motor_id' => $value['TipeMotor']['group_motor_id'],
		            //     ),
		            //     'fields' => array(
		            //         'SUM(TtujTipeMotorUse.qty) as count_qty'
		            //     )
		            // ));
		            $qtyUsed = $this->Revenue->RevenueDetail->getData('first', array(
		                'conditions' => array(
		                    'RevenueDetail.revenue_id' => $revenue_id,
		                    'RevenueDetail.group_motor_id' => $value['TipeMotor']['group_motor_id'],
		                    'Revenue.status' => 1,
		                ),
		                'fields' => array(
		                    'SUM(RevenueDetail.qty_unit) as count_qty'
		                )
		            ));

		            if( !empty($qtyUsed[0]['count_qty']) ) {
		                $qtyUsed = $qtyUsed[0]['count_qty'];
		            } else {
		            	$qtyUsed = 0;
		            }

					if(!empty($groupMotor)){
						$group_motor_name = $groupMotor['GroupMotor']['name'];
						$tipe_motor_id = $groupMotor['GroupMotor']['id'];
					}

					if($data_ttuj['Ttuj']['is_retail']){
						$city = $this->City->getData('first', array(
							'conditions' => array(
								'City.id' => $value['TtujTipeMotor']['city_id']
							)
						));
						if(!empty($city['City']['name'])){
							$to_city_name = $city['City']['name'];
							$to_city_id = $city['City']['id'];
						}

						$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $value['TtujTipeMotor']['city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
					}else{
						$to_city_name = $data_ttuj['Ttuj']['to_city_name'];
						$to_city_id = $data_ttuj['Ttuj']['to_city_id'];

						$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $data_ttuj['Ttuj']['to_city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
					}

					$qtyUnit = $qtyTtuj - $qtyUsed;

					if( !empty($qtyUnit) ) {
						$data_revenue_detail[$key] = array(
							'TtujTipeMotor' => array(
								'qty' => $qtyTtuj,
							),
							'RevenueDetail' => array(
								'to_city_name' => $to_city_name,
								'price_unit' => $tarif,
								'qty_unit' => $qtyUnit,
								'group_motor_id' => $group_motor_id,
								'city_id' => $to_city_id,
								'GroupMotor' => array(
									'name' => $group_motor_name,
								),
							)
						);
					}
				}
			}
		}

		$customers = $this->Customer->getData('list', array(
			'conditions' => array(
				'Customer.status' => 1
			),
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
		));

		$toCities = $this->City->toCities();
		$groupMotors = $this->GroupMotor->getData('list', array(
			'conditions' => array(
				'GroupMotor.status' => 1
			)
		));
		// debug($data_revenue_detail);die();
		$this->set(compact(
			'data_revenue_detail', 'customers', 'toCities', 'groupMotors',
			'tarifTruck'
		));
	}

	public function event_add( $nopol = false, $date = false ) {
        $this->loadModel('CalendarEvent');
        $this->loadModel('Truck');
        $this->set('sub_module_title', 'Tambah Event');
		$truck = $this->Truck->getData('first', array(
			'conditions' => array(
				'Truck.nopol' => $nopol,
				'Truck.status' => 1
			)
		), false);

        $this->doEvent( $truck, $date );
	}

	public function event_edit( $id = false ) {
        $this->loadModel('CalendarEvent');
        $this->set('sub_module_title', 'Tambah Event');
		$calendarEvent = $this->CalendarEvent->getData('first', array(
			'conditions' => array(
				'CalendarEvent.id' => $id,
				'CalendarEvent.status' => 1
			)
		));
		$truck = false;

		if( !empty($calendarEvent['Truck']) ) {
			$truck['Truck'] = $calendarEvent['Truck'];
		}

        $this->doEvent( $truck, false, $calendarEvent, $id );
        $this->render('event_add');
	}

	function doEvent ( $truck = false, $date = false, $data_local = false, $id = false ) {
		$isAjax = $this->RequestHandler->isAjax();
        $msg = array(
			'class' => 'error',
			'text' => ''
		);

		if(!empty($this->request->data)){
            $data = $this->request->data;

            if( !empty($truck) ) {
            	$data['CalendarEvent']['truck_id'] = $truck['Truck']['id'];
            	$data['CalendarEvent']['nopol'] = $truck['Truck']['nopol'];
            }

            if( !empty($id) ){
                $this->CalendarEvent->id = $id;
                $msg = 'merubah';
            }else{
                $this->CalendarEvent->create();
                $msg = 'menambah';
            }

            if( !empty($data['CalendarEvent']['from_date']) ) {
                $data['CalendarEvent']['from_date'] = $this->MkCommon->getDate($data['CalendarEvent']['from_date']);

                if( !empty($data['CalendarEvent']['from_time']) ) {
                    $data['CalendarEvent']['from_time'] = date('H:i', strtotime($data['CalendarEvent']['from_time']));
                    $data['CalendarEvent']['from_date'] = sprintf('%s %s', $data['CalendarEvent']['from_date'], $data['CalendarEvent']['from_time']);
                }
            }

            if( !empty($data['CalendarEvent']['to_date']) ) {
                $data['CalendarEvent']['to_date'] = $this->MkCommon->getDate($data['CalendarEvent']['to_date']);

                if( !empty($data['CalendarEvent']['to_time']) ) {
                    $data['CalendarEvent']['to_time'] = date('H:i', strtotime($data['CalendarEvent']['to_time']));
                    $data['CalendarEvent']['to_date'] = sprintf('%s %s', $data['CalendarEvent']['to_date'], $data['CalendarEvent']['to_time']);
                }
            }

            $this->CalendarEvent->set($data);

            if($this->CalendarEvent->validates($data)){
                if($this->CalendarEvent->save($data)){
                	$msg = array(
						'class' => 'success',
						'text' => sprintf(__('Sukses %s Event'), $msg),
					);
                    $this->Log->logActivity( sprintf(__('Sukses %s Event #%s'), $msg, $this->CalendarEvent->id), $this->user_data, $this->RequestHandler, $this->params );
                }else{
                	$msg = array(
						'class' => 'error',
						'text' => sprintf(__('Gagal %s Event'), $msg),
					);
                    $this->Log->logActivity( sprintf(__('Gagal %s Event #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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
        } else if( !empty($data_local) ) {
        	$this->request->data = $data_local;

            if( !empty($data_local['CalendarEvent']['from_date']) ) {
                $this->request->data['CalendarEvent']['from_date'] = date('d/m/Y', strtotime($data_local['CalendarEvent']['from_date']));
                $this->request->data['CalendarEvent']['from_time'] = date('H:i', strtotime($data_local['CalendarEvent']['from_date']));
            }
            if( !empty($data_local['CalendarEvent']['to_date']) ) {
                $this->request->data['CalendarEvent']['to_date'] = date('d/m/Y', strtotime($data_local['CalendarEvent']['to_date']));
                $this->request->data['CalendarEvent']['to_time'] = date('H:i', strtotime($data_local['CalendarEvent']['to_date']));
            }
        } else if( !empty($date) ) {
        	$this->request->data['CalendarEvent']['from_date'] = date('d/m/Y', strtotime($date));
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

    function event_delete( $id = false ){
        // if( in_array('delete_cities', $this->allowModule) ) {
            $this->loadModel('CalendarEvent');
            $locale = $this->CalendarEvent->getData('first', array(
                'conditions' => array(
                    'CalendarEvent.id' => $id
                )
            ), false);

            if($locale){
                $this->CalendarEvent->id = $id;
                $this->CalendarEvent->set('status', 0);

                if($this->CalendarEvent->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses menghapus event.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses menghapus event ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menghapus cevent.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal menghapus cevent ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Event tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

	function getInfoRevenueDetail( $ttuj_id = false, $customer_id = false, $city_id = false, $group_motor_id = false, $is_charge = false, $to_city_id = false, $qty = 0 ){
		$this->loadModel('Ttuj');
		$this->loadModel('GroupMotor');
		$this->loadModel('TarifAngkutan');
		$detail = array();
		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
			),
		), false);

		if( !empty($group_motor_id) ) {
			$groupMotor = $this->GroupMotor->getData('first', array(
				'conditions' => array(
					'GroupMotor.id' => $group_motor_id,
	                'GroupMotor.status' => 1,
				),
			), false);

			if( !empty($groupMotor) ) {
				$group_motor_id = $groupMotor['GroupMotor']['id'];
			}
		}

		if(!empty($data_ttuj)){
			if( !empty($city_id) ) {
				$mainTarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $city_id, $customer_id, $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
			} else {
				$mainTarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $to_city_id, $customer_id, $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
			}

			$detail = array(
				'RevenueDetail' => array(
					'price_unit' => $mainTarif,
				)
			);
		}

		$this->set(compact(
			'detail', 'is_charge', 'mainTarif',
			'qty'
		));
	}

	function getInvoiceInfo( $customer_id = false, $tarif_type = 'angkut' ){
		$this->loadModel('Revenue');
		$this->loadModel('Bank');
		$this->loadModel('Customer');
		$conditions = array(
			'Revenue.customer_id' => $customer_id,
			'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
			// 'Revenue.type' => $tarif_type,
			'Revenue.status' => 1,
		);
		$customer = $this->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $customer_id
            ),
        ));

        $conditionsDetail = $conditions;
        $conditionsDetail['RevenueDetail.invoice_id'] = NULL;
        $conditionsDetail['RevenueDetail.tarif_angkutan_type'] = $tarif_type;
		$revenueDetail = $this->Revenue->RevenueDetail->getData('first', array(
			'conditions' => $conditionsDetail,
			'order' => array(
				'Revenue.date_revenue' => 'ASC'
			),
			'fields' => array(
				'SUM(RevenueDetail.total_price_unit) total',
				'Revenue.customer_id',
				'MAX(Revenue.date_revenue) period_to',
				'MIN(Revenue.date_revenue) period_from',
			),
			'group' => array(
				'Revenue.customer_id'
			),
			'contain' => array(
				'Revenue'
			),
		));
		$revenueId = $this->Revenue->RevenueDetail->getData('list', array(
			'conditions' => $conditionsDetail,
			'fields' => array(
				'RevenueDetail.revenue_id',
				'RevenueDetail.revenue_id',
			),
			'contain' => array(
				'Revenue'
			),
		));
        $conditions['Revenue.id'] = $revenueId;
        $conditions['Revenue.revenue_tarif_type'] = 'per_truck';
		$revenue = $this->Revenue->getData('first', array(
			'conditions' => $conditions,
			'fields' => array(
				'SUM(Revenue.total_without_tax) total',
				'Revenue.customer_id',
			),
			'group' => array(
				'Revenue.customer_id'
			),
		), false);
        $banks = $this->Bank->getData('list', array(
            'conditions' => array(
                'Bank.status' => 1,
            ),
        ));
        $msg = array(
        	'error' => 0,
        	'text' => '',
    	);

		if(!empty($revenueDetail) && !empty($customer)){
			$monthFrom = !empty($revenueDetail[0]['period_from'])?$this->MkCommon->customDate($revenueDetail[0]['period_from'], 'Y-m'):false;
			$monthTo = !empty($revenueDetail[0]['period_to'])?$this->MkCommon->customDate($revenueDetail[0]['period_to'], 'Y-m'):false;
			$this->request->data['Invoice']['bank_id'] = !empty($customer['Customer']['bank_id'])?$customer['Customer']['bank_id']:false;
			$this->request->data['Invoice']['period_from'] = !empty($revenueDetail[0]['period_from'])?$this->MkCommon->customDate($revenueDetail[0]['period_from'], 'd/m/Y'):false;
			$this->request->data['Invoice']['period_to'] = !empty($revenueDetail[0]['period_to'])?$this->MkCommon->customDate($revenueDetail[0]['period_to'], 'd/m/Y'):false;
			$this->request->data['Invoice']['total_revenue'] = !empty($revenue[0]['total'])?$revenue[0]['total']:0;
			$this->request->data['Invoice']['total'] = !empty($revenueDetail[0]['total'])?$revenueDetail[0]['total']:0;

			switch ($tarif_type) {
				case 'angkut':
					$this->request->data['Invoice']['total'] += $this->request->data['Invoice']['total_revenue'];
					break;
			}

			if( $monthFrom != $monthTo ) {
		        $msg = array(
		        	'error' => 1,
		        	'text' => sprintf(__('Revenue dengan periode bulan yang berbeda tidak bisa dibuatkan invoice( %s s/d %s ). Mohon cek kembali revenue Anda.'), $this->request->data['Invoice']['period_from'], $this->request->data['Invoice']['period_to']),
		    	);
	    	} else if( !empty($customer['CustomerGroup']['CustomerGroupPattern']) ) {
                $this->request->data['Invoice']['pattern'] = $this->MkCommon->getNoInvoice( $customer['CustomerGroup'] );
			}
			// } else if( !empty($customer['CustomerPattern']) ) {
   //              $this->request->data['Invoice']['pattern'] = $this->MkCommon->getNoInvoice( $customer );
			// }
		}

		$this->set(compact(
			'banks', 'msg'
		));
	}

	function getInvoicePaymentInfo($customer_id = false){
		$this->loadModel('Revenue');
		$revenues = $this->Revenue->getData('first', array(
			'conditions' => array(
				'Revenue.customer_id' => $customer_id,
				'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
				'Revenue.status' => 1,						
			),
			'order' => array(
				'Revenue.date_revenue' => 'ASC'
			),
			'fields' => array(
				'SUM(Revenue.total) total',
				'MAX(Revenue.date_revenue) period_to',
				'MIN(Revenue.date_revenue) period_from',
			),
			'group' => array(
				'Revenue.customer_id'
			),
		));

		if(!empty($revenues)){
			$this->request->data['Invoice']['period_from'] = !empty($revenues[0]['period_from'])?$this->MkCommon->customDate($revenues[0]['period_from'], 'd/m/Y'):false;
			$this->request->data['Invoice']['period_to'] = !empty($revenues[0]['period_to'])?$this->MkCommon->customDate($revenues[0]['period_to'], 'd/m/Y'):false;
			$this->request->data['Invoice']['total'] = !empty($revenues[0]['total'])?$revenues[0]['total']:0;;
		}
	}

	function previewInvoice($customer_id = false, $invoice_type = 'angkut', $action = false){
		$this->loadModel('Revenue');
		$this->loadModel('TipeMotor');
		$this->loadModel('City');
		$this->loadModel('TarifAngkutan');
		$this->loadModel('Ttuj');
		$conditions = array(
			'Revenue.customer_id' => $customer_id,
			'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
			'Revenue.status' => 1,
		);

		// if( !empty($invoice_type) ) {
		// 	$conditions['Revenue.type'] = $invoice_type;
		// }

		$revenue_id = $this->Revenue->getData('list', array(
			'conditions' => $conditions,
			'order' => array(
				'Revenue.date_revenue' => 'ASC'
			),
			'fields' => array(
				'Revenue.id', 'Revenue.id',
			),
		), false);
		$totalPPN = $this->Revenue->getData('first', array(
			'conditions' => $conditions,
			'group_id' => array(
				'Revenue.customer_id'
			),
			'fields' => array(
				'SUM(total_without_tax * (ppn / 100)) ppn',
			),
		), false);
		$totalPPh = $this->Revenue->getData('first', array(
			'conditions' => $conditions,
			'group_id' => array(
				'Revenue.customer_id'
			),
			'fields' => array(
				'SUM(total_without_tax * (pph / 100)) pph',
			),
		), false);

		if(!empty($revenue_id)){
            $revenue_detail = $this->Revenue->RevenueDetail->getPreviewInvoice($revenue_id, $invoice_type, $action);
		}

		$this->layout = 'ajax';
		$layout_css = array(
			'print',
		);

		$this->set(compact(
			'revenue_detail', 'action', 'layout_css',
			'invoice_type', 'totalPPN', 'totalPPh'
		));
	}

	function getDrivers ( $id = false ) {
		$this->loadModel('Driver');
		$title = __('Supir Truk');
		$data_action = 'browse-form';
		$data_change = 'driverID';
		$conditions = array(
            'Driver.status' => 1,
            'Truck.id' => NULL,
        );

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $conditions['Driver.name LIKE '] = '%'.$name.'%';
            }
            if(!empty($this->request->data['Driver']['alias'])){
                $alias = urldecode($this->request->data['Driver']['alias']);
                $conditions['Driver.alias LIKE '] = '%'.$alias.'%';
            }
            if(!empty($this->request->data['Driver']['identity_number'])){
                $identity_number = urldecode($this->request->data['Driver']['identity_number']);
                $conditions['Driver.identity_number LIKE '] = '%'.$identity_number.'%';
            }
            if(!empty($this->request->data['Driver']['phone'])){
                $phone = urldecode($this->request->data['Driver']['phone']);
                $conditions['Driver.phone LIKE '] = '%'.$phone.'%';
            }
        }

        if( !empty($id)) {
            unset($conditions['Truck.id']);
            $conditions['OR'] = array(
                'Truck.id' => NULL,
                'Driver.id' => $id,
            );
        }

		$this->paginate = $this->Driver->getData('paginate', array(
            'conditions' => $conditions,
            'order' => array(
                'Driver.status' => 'DESC',
                'Driver.name' => 'ASC',
            ),
            'contain' => array(
                'Truck'
            ),
            'limit' => 10,
        ), false);
        $drivers = $this->paginate('Driver');

        $this->set(compact(
        	'drivers', 'data_action', 'title',
        	'data_change', 'id'
    	));
	}

	function getTrucks ( $action_type = false, $action_id = false ) {
		$this->loadModel('Truck');
		$title = __('Data Truk');
		$data_action = 'browse-form';
		$data_change = 'truckID';
		$options = array(
            'conditions' => array(
	            'Truck.status' => 1
	        ),
            'limit' => 10,
            'contain' => array(
                'Driver'
            ),
            'order' => array(
                'Truck.nopol' => 'ASC',
            ),
        );

        switch ($action_type) {
        	case 'ttuj':
				$this->loadModel('Ttuj');
        		$truck_id = $this->Ttuj->getData('list', array(
                    'conditions' => array(
                        'Ttuj.status' => 1,
                        'Ttuj.is_pool' => 0,
                        'Ttuj.id <>' => $action_id,
                        'Ttuj.is_laka' => 0,
                    ),
                    'fields' => array(
                    	'Ttuj.id', 'Ttuj.truck_id',
                	),
		        ), false);

                $options['conditions']['Truck.id NOT'] = $truck_id;
        		break;
        	case 'laka':
        		$data_change = 'laka-driver-change';
        		break;
        }

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Truck']['nopol'])){
                $nopol = urldecode($this->request->data['Truck']['nopol']);
                $typeTruck = !empty($this->request->data['Truck']['type'])?$this->request->data['Truck']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }

                $truckSearch = $this->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $options['conditions']['Truck.id'] = $truckSearch;
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $options['conditions']['Driver.name LIKE '] = '%'.$name.'%';
            }
        }

		$this->paginate = $this->Truck->getData('paginate', $options);
        $trucks = $this->paginate('Truck');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $data = $truck['Truck'];

                $truck = $this->Truck->TruckCategory->getMerge($truck, $data['truck_category_id']);
                $truck = $this->Truck->TruckBrand->getMerge($truck, $data['truck_brand_id']);
                $truck = $this->Truck->Company->getMerge($truck, $data['company_id']);

                $trucks[$key] = $truck;
            }
        }

        $this->set(compact(
        	'trucks', 'data_action', 'title',
        	'data_change', 'action_type', 'action_id'
    	));
	}

	function getKirs () {
		$this->loadModel('Kir');
		$this->loadModel('Truck');
		$title = __('Data KIR');
		$data_action = 'browse-form';
		$data_change = 'truckID';
		$conditions = array(
            'Kir.status' => 1,
            'Kir.paid' => 0,
            'Kir.rejected' => 0,
        );

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Kir']['nopol'])){
                $nopol = urldecode($this->request->data['Kir']['nopol']);
                $typeTruck = !empty($this->request->data['Kir']['type'])?$this->request->data['Kir']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }

                $truckSearch = $this->Kir->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $conditions['Kir.truck_id'] = $truckSearch;
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $drivers = $this->Truck->Driver->getData('list', array(
                	'conditions' => array(
                		'Driver.name LIKE' => '%'.$name.'%',
            		),
            		'fields' => array(
            			'Driver.id', 'Driver.id'
        			),
            	));
                $conditions['Truck.driver_id'] = $drivers;
            }
        }

        $this->paginate = $this->Kir->getData('paginate', array(
            'conditions' => $conditions,
            'limit' => Configure::read('__Site.config_pagination'),
        ));
        $trucks = $this->paginate('Kir');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $truck = $this->Truck->getMerge($truck, $truck['Kir']['truck_id']);

                $driver_id = !empty($truck['Truck']['driver_id'])?$truck['Truck']['driver_id']:false;
                $truck = $this->Truck->Driver->getMerge($truck, $driver_id);
                $trucks[$key] = $truck;
            }
        }

        $this->set(compact(
        	'trucks', 'data_action', 'title',
        	'data_change'
    	));
	}

	function getStnks () {
		$this->loadModel('Stnk');
		$this->loadModel('Truck');
		$title = __('Data STNK');
		$data_action = 'browse-form';
		$data_change = 'truckID';
		$conditions = array(
            'Stnk.status' => 1,
            'Stnk.paid' => 0,
            'Stnk.rejected' => 0,
        );

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Stnk']['nopol'])){
                $nopol = urldecode($this->request->data['Stnk']['nopol']);
                $typeTruck = !empty($this->request->data['Stnk']['type'])?$this->request->data['Stnk']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }

                $truckSearch = $this->Stnk->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $conditions['Stnk.truck_id'] = $truckSearch;
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $drivers = $this->Truck->Driver->getData('list', array(
                	'conditions' => array(
                		'Driver.name LIKE' => '%'.$name.'%',
            		),
            		'fields' => array(
            			'Driver.id', 'Driver.id'
        			),
            	));
                $conditions['Truck.driver_id'] = $drivers;
            }
        }

        $this->paginate = $this->Stnk->getData('paginate', array(
            'conditions' => $conditions,
            'limit' => Configure::read('__Site.config_pagination'),
            'contain' => array(
            	'Truck'
        	),
        ));
        $trucks = $this->paginate('Stnk');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $truck = $this->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
                $trucks[$key] = $truck;
            }
        }

        $this->set(compact(
        	'trucks', 'data_action', 'title',
        	'data_change'
    	));
	}

	function getSiups () {
		$this->loadModel('Siup');
		$this->loadModel('Truck');
		$title = __('Data Ijin Usaha');
		$data_action = 'browse-form';
		$data_change = 'truckID';
		$conditions = array(
            'Siup.status' => 1,
            'Siup.paid' => 0,
            'Siup.rejected' => 0,
        );

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Siup']['nopol'])){
                $nopol = urldecode($this->request->data['Siup']['nopol']);
                $typeTruck = !empty($this->request->data['Siup']['type'])?$this->request->data['Siup']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }

                $truckSearch = $this->Siup->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $conditions['Siup.truck_id'] = $truckSearch;
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $drivers = $this->Truck->Driver->getData('list', array(
                	'conditions' => array(
                		'Driver.name LIKE' => '%'.$name.'%',
            		),
            		'fields' => array(
            			'Driver.id', 'Driver.id'
        			),
            	));
                $conditions['Truck.driver_id'] = $drivers;
            }
        }

        $this->paginate = $this->Siup->getData('paginate', array(
            'conditions' => $conditions,
            'limit' => Configure::read('__Site.config_pagination'),
            'contain' => array(
            	'Truck'
        	),
        ));
        $trucks = $this->paginate('Siup');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $truck = $this->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
                $trucks[$key] = $truck;
            }
        }

        $this->set(compact(
        	'trucks', 'data_action', 'title',
        	'data_change'
    	));
	}

	function getTtujs ( $action_type = false, $ttuj_id = false ) {
		$this->loadModel('Ttuj');
		$title = __('Data TTUJ');
		$data_action = 'browse-form';
		$data_change = 'no_ttuj';
		$conditions = array(
		 	'Ttuj.status' => 1,
            'Ttuj.is_draft' => 0,
            'Ttuj.is_laka' => 0,
        );
        $orders = array();

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Ttuj']['nottuj'])){
                $nottuj = urldecode($this->request->data['Ttuj']['nottuj']);
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($this->request->data['Ttuj']['nopol'])){
                $nopol = urldecode($this->request->data['Ttuj']['nopol']);
                $typeTruck = !empty($this->request->data['Ttuj']['type'])?$this->request->data['Ttuj']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }
                
                $truckSearch = $this->Ttuj->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $conditions['Ttuj.truck_id'] = $truckSearch;
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $conditions['Ttuj.driver_name LIKE '] = '%'.$name.'%';
            }
            if(!empty($this->request->data['Customer']['name'])){
                $name = urldecode($this->request->data['Customer']['name']);
                $customers = $this->Ttuj->Customer->getData('list', array(
                	'conditions' => array(
                		'Customer.customer_name LIKE' => '%'.$name.'%',
            		),
            		'fields' => array(
            			'Customer.id', 'Customer.id'
        			),
            	));
                $conditions['Ttuj.customer_id'] = $customers;
            }
            if(!empty($this->request->data['City']['name'])){
                $name = urldecode($this->request->data['City']['name']);
                $conditions['Ttuj.to_city_name LIKE '] = '%'.$name.'%';
            }
            if(!empty($this->request->data['Ttuj']['date'])){
                $date = urldecode($this->request->data['Ttuj']['date']);
                $date = explode('-', $date);

                if( !empty($date[0]) ) {
                	$from_date = trim($date[0]);
            		$from_date = $this->MkCommon->getDate($from_date);
                	$conditions['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $from_date;
                }

                if( !empty($date[1]) ) {
                	$to_date = trim($date[1]);
            		$to_date = $this->MkCommon->getDate($to_date);
                	$conditions['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $to_date;
                }
            }
        }

        switch ($action_type) {
            case 'bongkaran':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran <>'] = 1;
                break;

            case 'balik':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik <>'] = 1;
                break;

            case 'pool':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                $conditions['Ttuj.is_pool <>'] = 1;
                break;

            case 'revenues':
            	unset($conditions['Ttuj.is_revenue']);
            	$conditions['OR'] = array(
	                'Ttuj.is_revenue' => 0,
	                'Ttuj.id' => $ttuj_id,
	            );
				$data_change = 'getTtujInfoRevenue';
                break;

            case 'lku':
                $conditions['Ttuj.is_bongkaran'] = 1;
				$data_change = 'getTtujInfo';
                break;

            case 'ksu':
                $conditions['Ttuj.is_bongkaran'] = 1;
				$data_change = 'getTtujInfoKsu';
                break;

            case 'laka':
                $conditions['Ttuj.is_pool <>'] = 1;
                $conditions['Ttuj.truck_id'] = $ttuj_id;
				$data_change = 'laka-ttuj-change';
                break;

            case 'uang_jalan_payment':
                $conditions['Ttuj.is_laka'] = array( 0, 1 );
                $conditions['Ttuj.paid_uang_jalan'] = 0;
				$data_change = 'ttujID';
        		$orders = array(
	                'Ttuj.created' => 'ASC',
	                'Ttuj.id' => 'ASC',
	            );
                break;
            
            default:
                $conditions['Ttuj.is_arrive'] = 0;
                break;
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order' => $orders,
            'limit' => Configure::read('__Site.config_pagination'),
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set(compact(
        	'ttujs', 'data_action', 'title',
        	'data_change', 'action_type'
    	));
	}

	function getInfoInvoicePayment($id = false){
		$this->loadModel('Invoice');

		$invoice = $this->Invoice->InvoicePayment->getdata('first', array(
			'conditions' => array(
				'InvoicePayment.invoice_id' => $id
			),
			'fields' => array(
				'SUM(total_payment) as total_payment'
			)
		));
		
		$invoice_real = $this->Invoice->getdata('first', array(
			'conditions' => array(
				'Invoice.id' => $id
			),
		));

		if(!empty($invoice)){
			$this->request->data['InvoicePayment']['total_payment_before'] = (!empty($invoice[0]['total_payment'])) ? $invoice[0]['total_payment'] : 0;
		}

		$this->set(compact('invoice_real', 'invoice'));
	}

	function getInfoInvoicePaymentDetail($id = false){
		$this->loadModel('Invoice');
		$invoices = array();

		$default_conditions = array(
			'Invoice.customer_id' => $id,
			'Invoice.complete_paid' => 0,
			'Invoice.status' => 1,
		);

		if(!empty($this->request->data['Invoice']['date_from']) || !empty($this->request->data['Invoice']['date_to'])){
			if(!empty($this->request->data['Invoice']['date_from'])){
				$default_conditions['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m-%d\') >='] = $this->request->data['Invoice']['date_from'];
			}
			if(!empty($this->request->data['Invoice']['date_to'])){
				$default_conditions['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m-%d\') <='] = $this->request->data['Invoice']['date_to'];
			}
		}

		if(!empty($id)){
			$invoices = $this->Invoice->getdata('all', array(
				'conditions' => $default_conditions
			));

			if(!empty($invoices)){
				foreach ($invoices as $key => $value) {
					$invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
						'conditions' => array(
							'InvoicePaymentDetail.invoice_id' => $value['Invoice']['id'],
							'InvoicePaymentDetail.status' => 1
						),
						'fields' => array(
							'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
						)
					));

					 $invoices[$key]['invoice_has_paid'] = $invoice_has_paid[0]['invoice_has_paid'];
				}
			}
		}

		$data_action = 'browse-invoice';
		$title = __('Invoice Customer');
		$this->set(compact('invoices', 'id', 'data_action', 'title'));
	}

	function delete_laka_media($id = false){
		$this->loadModel('LakaMedias');
		$msg = array(
			'msg' => 'ID Media tidak ditemukan.',
			'type' => 'error'
		);
		if($id){
			$laka_media = $this->LakaMedias->getData('first', array(
				'conditions' => array(
					'LakaMedias.id' => $id
				)
			));

			if(!empty($laka_media)){
				$this->LakaMedias->delete($id);
				$this->MkCommon->deletePathPhoto(Configure::read('__Site.laka_photo_folder'), $laka_media['LakaMedias']['name']);

				$msg = array(
					'msg' => 'Media berhasil di hapus.',
					'type' => 'success'
				);
			}
		}

		$this->set('msg', $msg);
		if( !$this->RequestHandler->isAjax() ){
			$this->MkCommon->setCustomFlash($msg['msg'], $msg['type']);
			$this->redirect($this->referer());
		}
	}

	function getInfoDriver($id = false){
		$this->loadModel('Truck');
		$this->loadModel('Ttuj');

		$driver = $this->Truck->getData('first', array(
			'conditions' => array(
				'Truck.status' => 1,
				'Truck.id' => $id
			),
			'contain' => array(
				'Driver'
			)
		));

		$driver_name = '';
		$no_sim = '';
		if(!empty($driver)){
			$driver_name = $driver['Driver']['name'];
			$no_sim = $driver['Driver']['no_sim'];
		}

		$ttujs = $this->Ttuj->getData('list', array(
            'conditions' => array(
                'Ttuj.is_pool <>' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
                'Ttuj.is_laka' => 0,
                'Ttuj.truck_id' => $id
            ),
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
        ), false);

		$this->set(compact('driver_name', 'no_sim', 'ttujs'));
	}

	function getUserCashBank( $action_type = 'cash_bank' ){
		$data_action = 'browse-form';
		$data_change = 'receiver-id';
		$listReceivers = array(
        	'Customer' => __('Customer'),
        	'Vendor' => __('Vendor'),
        	'Employe' => __('karyawan')
        );

		switch ($action_type) {
			case 'ttuj':
				$model = 'Driver';
				$title = __('Dibayar Kepada');
				$listReceivers = array_merge(array(
					'Driver' => __('Supir'),
				), $listReceivers);
				break;

			case 'driver':
				$model = 'Driver';
				$title = __('Dibayar Kepada');
				$listReceivers = false;
				break;
			
			default:
				$model = 'Customer';
				$title = __('User Kas/Bank');
				break;
		}

		if(!empty($this->request->data['UserCashBank']['model'])){
			$model = ucwords($this->request->data['UserCashBank']['model']);
		}

		$this->loadModel($model);
		$default_conditions = array(
			$model.'.status' => 1
		);

		if(!empty($this->request->data['UserCashBank']['name'])){
			if( $model == 'Customer' ) {
				$default_conditions[$model.'.customer_name_code LIKE'] = '%'.$this->request->data['UserCashBank']['name'].'%';
			} else if( $model == 'Driver' ) {
				$default_conditions[$model.'.driver_name LIKE'] = '%'.$this->request->data['UserCashBank']['name'].'%';
			} else {
				$default_conditions[$model.'.name LIKE'] = '%'.$this->request->data['UserCashBank']['name'].'%';
			}
		}

		$list_result = $this->$model->getData('all', array(
			'conditions' => $default_conditions
		));

		$this->request->data['UserCashBank']['model'] = $model;

		$this->set(compact(
			'list_result', 'model', 'data_action', 
			'title', 'data_change', 'listReceivers',
			'action_type'
		));
	}

	function getInfoCoa(){
		$this->loadModel('Coa');

		$default_conditions = array(
            'Coa.status' => 1,
            'Coa.level' => 4
        );

        if(!empty($this->request->data['Coa']['name'])){
			$default_conditions['Coa.name LIKE'] = '%'.$this->request->data['Coa']['name'].'%';
		}
		if(!empty($this->request->data['Coa']['code'])){
			$code = trim($this->request->data['Coa']['code']);
			$default_conditions['OR']['Coa.code LIKE'] = '%'.$code.'%';
			$default_conditions['OR']['Coa.with_parent_code LIKE'] = '%'.$code.'%';
		}

		$coas = $this->Coa->getData('all', array(
            'conditions' => $default_conditions,
        ));
        
        $this->set('coas', $coas);

        $data_action = 'browse-cash-banks';
		$title = __('Detail Kas/Bank');

		$this->set(compact('data_action', 'title'));
	}

	function getUserEmploye($rel = false, $user_id = false){
		$this->loadModel('User');

		$default_conditions = array(
            'User.status' => 1
        );

        if(!empty($this->request->data['User']['full_name'])){
			$default_conditions['User.full_name LIKE'] = '%'.$this->request->data['User']['full_name'].'%';
		}
		if(!empty($this->request->data['User']['group_id'])){
			$default_conditions['User.group_id'] = $this->request->data['User']['group_id'];
		}

		if(!empty($user_id)){
			$default_conditions['User.id'] = $user_id;
			$users = $this->User->getData('first', array(
	            'conditions' => $default_conditions,
	            'contain' => array(
	            	'Group'
	            )
	        ));

	        echo !empty($users['Group']['name'])?$users['Group']['name']:false;
	        die();
		}else{
			$groups = $this->User->Group->find('list', array(
				'conditions' => array(
					'Group.status' => 1
				),
				'fields' => array(
					'Group.id', 'Group.name'
				)
			));

			$this->paginate = $this->User->getData('paginate', array(
	            'conditions' => $default_conditions,
	            'contain' => array(
	            	'Group'
	            )
	        ));

	        $users = $this->paginate('User');
	        $data_action = 'browse-form';
	        $data_change = 'cash-bank-auth-user-'.$rel;
			$title = __('Data Karyawan');

			$this->set(compact(
				'data_action', 'title', 'data_change', 
				'rel', 'user_id', 'users', 'groups'
			));
		}
	}

	function getPricePartMotor($id){
		$this->loadModel('PartsMotor');

		$part_motor = $this->PartsMotor->getData('first', array(
			'conditions' => array(
				'PartsMotor.id' => $id
			)
		));

		$this->set('part_motor', $part_motor);
	}

	function get_cashbank_doc ( $action_type = false ) {
		switch ($action_type) {
            case 'ppn_in':
				$this->loadModel('Revenue');
				$result = $this->Revenue->getDocumentCashBank();
				$urlBrowseDocument = array(
                    'controller'=> 'ajax', 
                    'action' => 'getCashBankPpnRevenue',
                );
                break;
            
            case 'prepayment_in':
				$this->loadModel('CashBank');
				$result = $this->CashBank->getDocumentCashBank();
				$urlBrowseDocument = array(
                    'controller'=> 'ajax', 
                    'action' => 'getCashBankPrepayment',
                );
                break;
        }

		if( !empty($result) ) {
			$docs = $result['docs'];
			$this->request->data['CashBank']['document_type'] = $result['docs_type'];
		}

		$this->set('docs', $docs);
		$this->set('urlBrowseDocument', $urlBrowseDocument);
		$this->render('get_cashbank_doc');
	}

	public function getCashBankPpnRevenue() {
        $this->loadModel('Customer');
		$this->loadModel('Revenue');
		$title = __('Data Revenue');
		$data_action = 'browse-form';
		$data_change = 'document-id';
		$options = array(
            'conditions' => array(
	            'Revenue.paid_ppn' => 0,
				'Revenue.transaction_status <>' => 'unposting',
				'Revenue.status' => 1,
	        ),
            'limit' => 10,
            'contain' => array(
                'CustomerNoType'
            ),
			'order' => array(
				'Revenue.id' => 'ASC'
			),
        );

        if(!empty($this->request->data)){
        	$refine = $this->request->data['Revenue'];

            if(!empty($refine['no_doc'])){
                $nodoc = urldecode($refine['no_doc']);
                $options['conditions']['Revenue.no_doc LIKE '] = '%'.$nodoc.'%';
            }
            if(!empty($refine['no_ttuj'])){
                $no_ttuj = urldecode($refine['no_ttuj']);
                $options['conditions']['Ttuj.no_ttuj LIKE '] = '%'.$no_ttuj.'%';
            }
            if(!empty($refine['customer_id'])){
                $customer = urldecode($refine['customer_id']);
                $options['conditions']['Revenue.customer_id'] = $customer;
            }
            if(!empty($refine['no_reference'])){
                $no_ref = urldecode($refine['no_reference']);

                if( is_numeric($no_ref) ) {
                    $no_ref = intval($no_ref);
                }

                $options['conditions']['LPAD(Revenue.id, 5, 0) LIKE'] = '%'.$no_ref.'%';
            }

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                    $options['conditions']['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') >='] = $dateFrom;
                    $options['conditions']['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') <='] = $dateTo;
                }
            }

            if(!empty($refine['nopol'])){
				$this->loadModel('Truck');
                $nopol = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['Ttuj']['type'])?$refine['Ttuj']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }

                $truckSearch = $this->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $options['conditions']['Ttuj.truck_id'] = $truckSearch;
            }

            if(!empty($refine['transaction_status'])){
                $status = urldecode($refine['transaction_status']);

                if( $status == 'paid' ) {
                    $revenueList = $this->Revenue->getData('list', array(
                        'conditions' => $options['conditions'],
                        'contain' => array(
                            'Ttuj',
                        ),
                        'fields' => array(
                            'Revenue.id', 'Revenue.id'
                        ),
                    ));
                    $paidList = $this->Revenue->InvoiceDetail->getInvoicedRevenueList($revenueList);
                    $options['conditions']['Revenue.id'] = $paidList;
                } else {
                    $options['conditions']['Revenue.transaction_status'] = $status;
                }
            }
        }

		$this->paginate = $this->Revenue->getData('paginate', $options);
        $revenues = $this->paginate('Revenue');

        if(!empty($revenues)){
            foreach ($revenues as $key => $value) {
                $value = $this->Revenue->InvoiceDetail->getInvoicedRevenue($value, $value['Revenue']['id']);
                $value = $this->Customer->getMerge($value, $value['Ttuj']['customer_id']);
                $revenues[$key] = $this->Customer->getMerge($value, $value['Ttuj']['customer_id']);
            }
        }

        $customers = $this->Customer->getData('list', array(
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set(compact(
        	'revenues', 'data_action', 'title',
        	'data_change', 'customers'
    	));
	}

	public function getCashBankPrepayment( $prepayment_out_id = false ) {
        $this->loadModel('CashBank');
		$title = __('Data Prepayment');
		$data_action = 'browse-form';
		$data_change = 'document-id';

		$options = array(
            'conditions' => array(
	            'CashBank.status' => 1,
                'CashBank.is_rejected' => 0,
                'CashBank.receiving_cash_type' => 'prepayment_out',
	        ),
            'limit' => 10,
			'order' => array(
				'CashBank.id' => 'ASC'
			),
        );

        if( !empty($prepayment_out_id) ) {
            $options['conditions']['OR'] = array(
                'CashBank.prepayment_status <>' => 'full_paid',
                'CashBank.id' => $prepayment_out_id,
            );
        } else {
            $options['conditions']['CashBank.prepayment_status <>'] = 'full_paid';
        }

        if(!empty($this->request->data)){
        	$refine = $this->request->data['CashBank'];

        	if(!empty($refine['nodoc'])){
                $nodoc = urldecode($refine['nodoc']);
                $options['conditions']['CashBank.nodoc LIKE '] = '%'.$nodoc.'%';
            }

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                    $options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') >='] = $dateFrom;
                    $options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') <='] = $dateTo;
                }
            }
        }

		$this->paginate = $this->CashBank->getData('paginate', $options);
        $cashBanks = $this->paginate('CashBank');

        if(!empty($cashBanks)){
            $this->loadModel('Vendor');
            $this->loadModel('Employe');
            $this->loadModel('Customer');

            foreach ($cashBanks as $key => $value) {
                $model = $value['CashBank']['receiver_type'];

                switch ($model) {
                    case 'Vendor':
                        $list_result = $this->Vendor->getData('first', array(
                            'conditions' => array(
                                'Vendor.status' => 1
                            )
                        ));
                        break;
                    case 'Employe':
                        $list_result = $this->Employe->getData('first', array(
                            'conditions' => array(
                                'Employe.status' => 1
                            )
                        ));

                        break;
                    default:
                        $list_result = $this->Customer->getData('first', array(
                            'conditions' => array(
                                'Customer.status' => 1
                            )
                        ));

                        break;
                }

                if(!empty($list_result)){
                    $cashBanks[$key]['name_cash'] = $list_result[$model]['name'];
                }
            }
        }

        $this->set(compact(
        	'cashBanks', 'data_action', 'title',
        	'data_change'
    	));
	}

	function getCustomer ( $cash_bank_id = false ) {
		$revenue_id = !empty($this->params['named']['revenue_id'])?$this->params['named']['revenue_id']:false;
		$prepayment_id = !empty($this->params['named']['prepayment_id'])?$this->params['named']['prepayment_id']:false;

		if( !empty($prepayment_id) ) {
			$this->loadModel('CashBank');
			$customer = $this->CashBank->getData('first', array(
				'conditions' => array(
					'CashBank.id' => $prepayment_id,
				),
				'contain' => array(
					'CashBankDetail' => array(
						'Coa'
					),
				),
			), false);

			if( !empty($customer) ) {
				$model = $customer['CashBank']['receiver_type'];

				if( !empty($customer['CashBankDetail']) ) {
					foreach ($customer['CashBankDetail'] as $key => $cashBankDetail) {
						$coa_id = !empty($cashBankDetail['coa_id'])?$cashBankDetail['coa_id']:false;
						$totalDibayar = $this->CashBank->CashBankDetail->totalPrepaymentDibayarPerCoa($prepayment_id, $coa_id, $cash_bank_id);
						$totalTagihan = !empty($customer['CashBankDetail'][$key]['total'])?$customer['CashBankDetail'][$key]['total']:0;
						$totalSisaTagihan = $totalTagihan - $totalDibayar;

						if( $totalSisaTagihan <= 0 ) {
							unset($customer['CashBankDetail'][$key]);
						} else {
							$customer['CashBankDetail'][$key]['total'] = $totalSisaTagihan;
						}
					}
				}

                switch ($model) {
                    case 'Vendor':
           		 		$this->loadModel('Vendor');
                        $list_result = $this->Vendor->getData('first', array(
                            'conditions' => array(
                                'Vendor.status' => 1
                            )
                        ));
                        break;
                    case 'Employe':
            			$this->loadModel('Employe');
                        $list_result = $this->Employe->getData('first', array(
                            'conditions' => array(
                                'Employe.status' => 1
                            )
                        ));

                        break;
                    default:
            			$this->loadModel('Customer');
                        $list_result = $this->Customer->getData('first', array(
                            'conditions' => array(
                                'Customer.status' => 1
                            )
                        ));

                        break;
                }

                if(!empty($list_result)){
                	$customer['CustomerNoType'] = $list_result[$model];
                }
            }
		} else {
        	$this->loadModel('CoaSetting');
			$this->loadModel('Revenue');

			$customer = $this->Revenue->getData('first', array(
				'conditions' => array(
					'Revenue.id' => $revenue_id,
				),
				'contain' => array(
					'CustomerNoType'
				),
			), false);

	        $this->CoaSetting->bindModel(array(
				'belongsTo' => array(
					'Coa' => array(
						'foreignKey' => 'ppn_coa_credit_id',
					),
				)
			), false);

	        $coaSetting = $this->CoaSetting->getData('first', array(
	            'conditions' => array(
	                'CoaSetting.status' => 1
	            ),
				'contain' => array(
					'Coa'
				),
	        ));
		}

        $this->set(compact(
        	'customer', 'coaSetting', 'revenue_id',
        	'prepayment_id'
    	));
		$this->render('get_customer');
	}

	function getInfoTtujPayment( $ttuj_id, $action_type = 'uang_jalan'){
		$this->loadModel('Ttuj');
		$result = $this->Ttuj->getTtujPayment($ttuj_id, $action_type);
		echo json_encode($result);
		$this->render(false);
	}

	function getInfoEmploye($id){
		$this->loadModel('Employe');
		$employes = $this->Employe->getData('first', array(
			'conditions' => array(
				'Employe.id' => $id,
				'Employe.status' => 1
			)
		));

		$first_name = '';
		$last_name = '';
		if(!empty($employes['Employe']['name'])){
			$arr_str = explode(' ', $employes['Employe']['name']);

			if(!empty($arr_str[0])){
				$first_name = $arr_str[0];
			}

			if(count($arr_str) > 1){
				unset($arr_str[0]);
				$last_name = implode(' ', $arr_str);
			}
		}

		$this->set(compact('first_name', 'last_name'));
	}

	function getBiayaTtuj( $action_type = false ){
		$this->loadModel('Ttuj');

		$document_type = false;
		$conditions = array(
            'Ttuj.is_draft' => 0,
            'Ttuj.status' => 1,
        );

        switch ($action_type) {
        	case 'biaya_ttuj':
				$title = __('Detail Biaya TTUJ');
        		$conditions['OR'] = array(
	            	array(
		            	'Ttuj.paid_uang_kuli_muat <>' => 'full',
		            	'Ttuj.uang_kuli_muat <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_uang_kuli_bongkar <>' => 'full',
		            	'Ttuj.uang_kuli_bongkar <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_asdp <>' => 'full',
		            	'Ttuj.asdp <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_uang_kawal <>' => 'full',
		            	'Ttuj.uang_kawal <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_uang_keamanan <>' => 'full',
		            	'Ttuj.uang_keamanan <>' => 0,
	            	),
	        	);
	        	$jenisBiaya = array(
	        		'uang_kuli_muat' => __('Uang Kuli Muat'),
	        		'uang_kuli_bongkar' => __('Uang Kuli Bongkar'),
	        		'asdp' => __('Uang Penyebrangan'),
	        		'uang_kawal' => __('Uang Kawal'),
	        		'uang_keamanan' => __('Uang Keamanan'),
        		);
        		break;
        	
        	default:
				$title = __('Detail Biaya Uang Jalan / Komisi');
        		$conditions['OR'] = array(
	            	array(
		            	'Ttuj.paid_commission <>' => 'full',
		            	'Ttuj.commission <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_uang_jalan <>' => 'full',
	            	),
	            	array(
		            	'Ttuj.paid_uang_jalan_2 <>' => 'full',
		            	'Ttuj.uang_jalan_2 <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_uang_jalan_extra <>' => 'full',
		            	'Ttuj.uang_jalan_extra <>' => 0,
	            	),
	            	array(
		            	'Ttuj.paid_commission_extra <>' => 'full',
		            	'Ttuj.commission_extra <>' => 0,
	            	),
	        	);
	        	$jenisBiaya = array(
	        		'uang_jalan' => __('Uang Jalan'),
	        		'uang_jalan_2' => __('Uang Jalan ke 2'),
	        		'uang_jalan_extra' => __('Uang Jalan Extra'),
	        		'commission' => __('Komisi'),
	        		'commission_extra' => __('Komisi Extra'),
        		);
        		break;
        }

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Ttuj']['nottuj'])){
                $nottuj = urldecode($this->request->data['Ttuj']['nottuj']);
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($this->request->data['Ttuj']['nopol'])){
                $nopol = urldecode($this->request->data['Ttuj']['nopol']);
                $typeTruck = !empty($this->request->data['Ttuj']['type'])?$this->request->data['Ttuj']['type']:1;

                if( $typeTruck == 2 ) {
                	$conditionsNopol = array(
	            		'Truck.id' => $nopol,
	        		);
                } else {
                	$conditionsNopol = array(
	            		'Truck.nopol LIKE' => '%'.$nopol.'%',
	        		);
                }

                $truckSearch = $this->Ttuj->Truck->getData('list', array(
                	'conditions' => $conditionsNopol,
            		'fields' => array(
            			'Truck.id', 'Truck.id',
        			),
            	));
                $conditions['Ttuj.truck_id'] = $truckSearch;
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $this->Ttuj->virtualFields['ttuj_driver_name'] = 'CASE WHEN DriverPenganti.name IS NULL THEN Ttuj.driver_name ELSE DriverPenganti.name END';
                $conditions['Ttuj.ttuj_driver_name LIKE'] = '%'.$name.'%';
            }
            if(!empty($this->request->data['Customer']['name'])){
                $name = urldecode($this->request->data['Customer']['name']);
                $customers = $this->Ttuj->Customer->getData('list', array(
                	'conditions' => array(
                		'Customer.customer_name LIKE' => '%'.$name.'%',
            		),
            		'fields' => array(
            			'Customer.id', 'Customer.id'
        			),
            	));
                $conditions['Ttuj.customer_id'] = $customers;
            }
            if(!empty($this->request->data['City']['name'])){
                $name = urldecode($this->request->data['City']['name']);
                $conditions['Ttuj.to_city_name LIKE '] = '%'.$name.'%';
            }
            if(!empty($this->request->data['Ttuj']['date'])){
                $date = urldecode($this->request->data['Ttuj']['date']);
                $date = explode('-', $date);

                if( !empty($date[0]) ) {
                	$from_date = trim($date[0]);
            		$from_date = $this->MkCommon->getDate($from_date);
                	$conditions['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $from_date;
                }

                if( !empty($date[1]) ) {
                	$to_date = trim($date[1]);
            		$to_date = $this->MkCommon->getDate($to_date);
                	$conditions['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $to_date;
                }
            }
            // if(!empty($this->request->data['Ttuj']['document_type'])){
            //     $document_type = urldecode($this->request->data['Ttuj']['document_type']);

            //     switch ($document_type) {
            //     	case 'uang_jalan':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_jalan <>'] = 'full';
            //     		break;
            //     	case 'uang_jalan_2':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_jalan_2 <>'] = 'full';
            //     		$conditions['Ttuj.uang_jalan_2 <>'] = 0;
            //     		break;
            //     	case 'uang_jalan_extra':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_jalan_extra <>'] = 'full';
            //     		$conditions['Ttuj.uang_jalan_extra <>'] = 0;
            //     		break;
            //     	case 'commission':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_commission <>'] = 'full';
            //     		$conditions['Ttuj.commission <>'] = 0;
            //     		break;
            //     	case 'commission_extra':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_commission_extra <>'] = 'full';
            //     		$conditions['Ttuj.commission_extra <>'] = 0;
            //     		break;
            //     	case 'uang_kuli_muat':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_kuli_muat <>'] = 'full';
            //     		$conditions['Ttuj.uang_kuli_muat <>'] = 0;
            //     		break;
            //     	case 'uang_kuli_bongkar':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_kuli_bongkar <>'] = 'full';
            //     		$conditions['Ttuj.uang_kuli_bongkar <>'] = 0;
            //     		break;
            //     	case 'asdp':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_asdp <>'] = 'full';
            //     		$conditions['Ttuj.asdp <>'] = 0;
            //     		break;
            //     	case 'uang_kawal':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_kawal <>'] = 'full';
            //     		$conditions['Ttuj.uang_kawal <>'] = 0;
            //     		break;
            //     	case 'uang_keamanan':
            //     		unset($conditions['OR']);
            //     		$conditions['Ttuj.paid_uang_keamanan <>'] = 'full';
            //     		$conditions['Ttuj.uang_keamanan <>'] = 0;
            //     		break;
            //     }
            // }

            if(!empty($this->request->data['Ttuj']['uang_jalan_1']) || !empty($this->request->data['Ttuj']['uang_jalan_2']) || !empty($this->request->data['Ttuj']['uang_jalan_extra']) || !empty($this->request->data['Ttuj']['commission']) || !empty($this->request->data['Ttuj']['commission_extra']) || !empty($this->request->data['Ttuj']['uang_kuli_muat']) || !empty($this->request->data['Ttuj']['uang_kuli_bongkar']) || !empty($this->request->data['Ttuj']['asdp']) || !empty($this->request->data['Ttuj']['uang_kawal']) || !empty($this->request->data['Ttuj']['uang_keamanan'])){
            	unset($conditions['OR']);
            	$idx = 0;
                $document_type = true;
        	}

            if(!empty($this->request->data['Ttuj']['uang_jalan_1'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_jalan <>'] = 'full';
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['uang_jalan_2'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_jalan_2 <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.uang_jalan_2 <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['uang_jalan_extra'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_jalan_extra <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.uang_jalan_extra <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['commission'])){
        		$conditions['OR'][$idx]['Ttuj.paid_commission <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.commission <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['commission_extra'])){
        		$conditions['OR'][$idx]['Ttuj.paid_commission_extra <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.commission_extra <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['uang_kuli_muat'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_kuli_muat <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.uang_kuli_muat <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['uang_kuli_bongkar'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_kuli_bongkar <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.uang_kuli_bongkar <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['asdp'])){
        		$conditions['OR'][$idx]['Ttuj.paid_asdp <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.asdp <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['uang_kawal'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_kawal <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.uang_kawal <>'] = 0;
        		$idx++;
        	}
            if(!empty($this->request->data['Ttuj']['uang_keamanan'])){
        		$conditions['OR'][$idx]['Ttuj.paid_uang_keamanan <>'] = 'full';
        		$conditions['OR'][$idx]['Ttuj.uang_keamanan <>'] = 0;
        		$idx++;
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order' => array(
                'Ttuj.created' => 'ASC',
                'Ttuj.id' => 'ASC',
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        ));
        $ttujs = $this->paginate('Ttuj');

        if( !empty($ttujs) ) {
        	$this->loadModel('Customer');
        	$this->loadModel('Driver');
        	$this->loadModel('TtujPaymentDetail');

        	foreach ($ttujs as $key => $ttuj) {
        		$customer_id = !empty($ttuj['Ttuj']['customer_id'])?$ttuj['Ttuj']['customer_id']:'';
            	$driver_id = !empty($ttuj['Ttuj']['driver_id'])?$ttuj['Ttuj']['driver_id']:'';
            	$ttuj_id = !empty($ttuj['Ttuj']['id'])?$ttuj['Ttuj']['id']:'';
        		$ttuj = $this->Customer->getMerge($ttuj, $customer_id);
            	$ttuj = $this->Driver->getMerge($ttuj, $driver_id);

            	switch ($action_type) {
		        	case 'biaya_ttuj':
            				$ttuj['uang_kuli_muat_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_kuli_muat');
            				$ttuj['uang_kuli_bongkar_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_kuli_bongkar');
            				$ttuj['asdp_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'asdp');
            				$ttuj['uang_keamanan_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_keamanan');
            				$ttuj['uang_kawal_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_kawal');
		        		break;
		        	
		        	default:
		            	$ttuj['uang_jalan_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_jalan');
		            	$ttuj['commission_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'commission');
		            	$ttuj['uang_jalan_2_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_jalan_2');
		            	$ttuj['uang_jalan_extra_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_jalan_extra');
		            	$ttuj['commission_extra_dibayar'] = $this->TtujPaymentDetail->getTotalPayment($ttuj_id, 'commission_extra');
		        		break;
		        }

            	$ttujs[$key] = $ttuj;
        	}
        }

        $data_action = 'browse-check-docs';

		$this->set(compact(
			'data_action', 'title', 'ttujs',
			'action_type', 'jenisBiaya', 'document_type'
		));
	}

	function getSjDriver( $driver_id ) {
		$this->loadModel('Ttuj');
		$sjOutstanding = $this->Ttuj->getSJOutstanding( $driver_id );

		$this->set(compact(
			'sjOutstanding', 'driver_id'
		));
	}
}
?>