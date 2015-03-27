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
						)
					));
					$tipe_motor_list[$tipe_motor['TipeMotor']['id']] = $tipe_motor['TipeMotor']['name'];
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
                'Customer.id', 'Customer.customer_name'
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
                    $this->Log->logActivity( sprintf(__('Sukses menghapus event ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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

	function getInfoRevenueDetail( $ttuj_id = false, $customer_id = false, $city_id = false, $group_motor_id = false, $is_charge = false, $to_city_id = false ){
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
			$mainTarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $to_city_id, $customer_id, $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
			$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $city_id, $customer_id, $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
			$detail = array(
				'RevenueDetail' => array(
					'price_unit' => $tarif,
				)
			);
		}

		$this->set(compact(
			'detail', 'is_charge', 'mainTarif'
		));
	}

	function getInvoiceInfo( $customer_id = false, $tarif_type = 'angkut' ){
		$this->loadModel('Revenue');
		$this->loadModel('Bank');
		$this->loadModel('Customer');
		$customer = $this->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $customer_id
            ),
        ));
		$revenues = $this->Revenue->getData('first', array(
			'conditions' => array(
				'Revenue.customer_id' => $customer_id,
				'Revenue.transaction_status' => 'posting',
				'Revenue.type' => $tarif_type,						
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
        $banks = $this->Bank->getData('list', array(
            'conditions' => array(
                'Bank.status' => 1,
            ),
        ));
        $msg = array(
        	'error' => 0,
        	'text' => '',
    	);

		if(!empty($revenues) && !empty($customer)){
			$monthFrom = !empty($revenues[0]['period_from'])?$this->MkCommon->customDate($revenues[0]['period_from'], 'Y-m'):false;
			$monthTo = !empty($revenues[0]['period_to'])?$this->MkCommon->customDate($revenues[0]['period_to'], 'Y-m'):false;
			$this->request->data['Invoice']['bank_id'] = !empty($customer['Customer']['bank_id'])?$customer['Customer']['bank_id']:false;
			$this->request->data['Invoice']['period_from'] = !empty($revenues[0]['period_from'])?$this->MkCommon->customDate($revenues[0]['period_from'], 'd/m/Y'):false;
			$this->request->data['Invoice']['period_to'] = !empty($revenues[0]['period_to'])?$this->MkCommon->customDate($revenues[0]['period_to'], 'd/m/Y'):false;
			$this->request->data['Invoice']['total'] = !empty($revenues[0]['total'])?$revenues[0]['total']:0;;

			if( $monthFrom != $monthTo ) {
		        $msg = array(
		        	'error' => 1,
		        	'text' => sprintf(__('Revenue dengan periode bulan yang berbeda tidak bisa dibuatkan invoice( %s s/d %s ). Mohon cek kembali revenue Anda.'), $this->request->data['Invoice']['period_from'], $this->request->data['Invoice']['period_to']),
		    	);
			} else if( !empty($customer['CustomerPattern']) ) {
                $this->request->data['Invoice']['pattern'] = $this->MkCommon->getNoInvoice( $customer );
			}
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
				'Revenue.transaction_status' => 'posting',
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

	function previewInvoice($customer_id = false, $action = false){
		$this->loadModel('Revenue');
		$this->loadModel('TipeMotor');
		$this->loadModel('City');
		$this->loadModel('TarifAngkutan');
		$this->loadModel('Ttuj');
		$conditions = array(
			'Revenue.customer_id' => $customer_id,
			'Revenue.transaction_status' => 'posting',
			'Revenue.status' => 1,
		);

		if( !empty($action) ) {
			$conditions['Revenue.type'] = $action;
		}

		$revenue_id = $this->Revenue->getData('list', array(
			'conditions' => $conditions,
			'order' => array(
				'Revenue.date_revenue' => 'ASC'
			),
			'fields' => array(
				'Revenue.id', 'Revenue.id',
			),
		), false);

		if(!empty($revenue_id)){
            $revenue_detail = $this->Revenue->RevenueDetail->getPreviewInvoice($revenue_id, $action);
		}

		$this->layout = 'ajax';
		$layout_css = array(
			'print'
		);

		$this->set(compact(
			'revenue_detail', 'action', 'layout_css'
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
	        	$this->Truck->bindModel(array(
		            'hasOne' => array(
		                'Ttuj' => array(
		                    'className' => 'Ttuj',
		                    'foreignKey' => 'truck_id',
		                    'conditions' => array(
		                        'Ttuj.status' => 1,
		                        'Ttuj.is_pool' => 0,
		                        'Ttuj.id <>' => $action_id,
		                    ),
		                )
		            )
		        ));

        		$options['conditions']['Ttuj.id'] = NULL;
        		$options['contain'][] = 'Ttuj';
        		break;
        	case 'laka':
        		$data_change = 'laka-driver-change';
        		break;
        }

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Truck']['nopol'])){
                $nopol = urldecode($this->request->data['Truck']['nopol']);
                $options['conditions']['Truck.nopol LIKE '] = '%'.$nopol.'%';
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
        	'data_change'
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
                $conditions['Kir.no_pol LIKE '] = '%'.$nopol.'%';
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
                $truck = $this->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
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
                $conditions['Stnk.no_pol LIKE '] = '%'.$nopol.'%';
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
		$title = __('Data SIUP');
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
                $conditions['Siup.no_pol LIKE '] = '%'.$nopol.'%';
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
        );

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Ttuj']['nottuj'])){
                $nottuj = urldecode($this->request->data['Ttuj']['nottuj']);
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($this->request->data['Ttuj']['nopol'])){
                $nopol = urldecode($this->request->data['Ttuj']['nopol']);
                $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
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
                $conditions['Ttuj.is_pool'] = 1;
				$data_change = 'getTtujInfo';
                break;

            case 'laka':
                $conditions['Ttuj.is_pool <>'] = 1;
				$data_change = 'laka-ttuj-change';
                break;
            
            default:
                $conditions['Ttuj.is_arrive'] = 0;
                break;
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
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

		$this->set(compact('invoices', 'id'));
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

		$this->set(compact('driver_name', 'no_sim'));
	}
}
?>