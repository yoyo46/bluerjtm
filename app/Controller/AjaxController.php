<?php
App::uses('AppController', 'Controller');
class AjaxController extends AppController {

	public $name = 'Ajax';
	public $uses = array();
	public $components = array(
		'RjLku', 'RjRevenue',
		'RjCashBank'
	);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->layout = false;
	}

    function search( $index = 'index' ){
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $named = $this->MkCommon->filterEmptyField($this->params, 'named');
            $params = array(
                'controller' => 'ajax',
                'action' => $index,
                'false' => false,
            );
            
            if( !empty($named) ) {
            	foreach ($named as $key => $value) {
            		switch ($key) {
            			case 'payment_id':
            				$params[$key] = $value;
            				break;
            			
            			default:
            				$params[] = $value;
            				break;
            		}
            	}
            }

            $result = $this->MkCommon->processFilter($data);
            $params = array_merge($params, $result);

            $this->redirect($params);
        } else {
            $this->redirect('/');
        }
    }

	function getKotaAsal() {
		$this->loadModel('UangJalan');
		$resultCity = $this->UangJalan->getKotaAsal();

		$this->set(compact(
			'resultCity'
		));
	}

	function getKotaTujuan( $from_city_id = false ) {
		$this->loadModel('UangJalan');
		$resultCity = $this->UangJalan->getKotaTujuan($from_city_id);

		$this->set(compact(
			'resultCity'
		));
	}

	function getInfoTruck( $from_city_id = false, $to_city_id = false, $truck_id = false, $customer_id = false ) {
		$this->loadModel('Ttuj');
		$this->loadModel('City');

        $plantCityId = Configure::read('__Site.Branch.Plant.id');
		$isAjax = $this->RequestHandler->isAjax();
		$result = $this->Ttuj->Truck->getInfoTruck($truck_id, $plantCityId);

		if( !empty($result) ) {
			$this->loadModel('UangKuli');

			if( !empty($result['Truck']['driver_id']) ) {
				$sjOutstanding = $this->Ttuj->getSJOutstanding( $result['Truck']['driver_id'] );
			}
			
			$uangJalan = $this->Ttuj->UangJalan->getNopol( $from_city_id, $to_city_id, $result['Truck']['capacity'] );
			$uangKuli = $this->UangKuli->getUangKuli( $from_city_id, $to_city_id, $customer_id, $result['Truck']['capacity'] );
			$converterUjs = $this->Ttuj->TtujTipeMotor->TipeMotor->GroupMotor->getData('all', array(
				'contain' => false,
			), true, array(
				'converter' => true,
			));

			$uangKuliMuat = !empty($uangKuli['UangKuliMuat'])?$uangKuli['UangKuliMuat']:false;
			$uangKuliBongkar = !empty($uangKuli['UangKuliBongkar'])?$uangKuli['UangKuliBongkar']:false;
		}

		$this->set(compact(
			'result', 'uangJalan', 'uangKuliMuat',
			'uangKuliBongkar', 'sjOutstanding',
			'converterUjs', 'isAjax'
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
				'UangJalan',
				'TtujTipeMotor' => array(
                    'City',
                    'ColorMotor',
                    'TipeMotor',
                ),
			)
		));
		
		if(!empty($data_ttuj)){
			if( !empty($data_ttuj['Ttuj']['driver_penganti_id']) ) {
				$this->loadModel('Driver');

				$driver_penganti_id = $data_ttuj['Ttuj']['driver_penganti_id'];
				$data_ttuj = $this->Driver->getMerge($data_ttuj, $driver_penganti_id, 'DriverPenganti');
			}

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

		$part_motors = $this->PartsMotor->getData('list', array(
            'conditions' => array(
                'PartsMotor.status' => 1
            ),
            'fields' => array(
                'PartsMotor.id', 'PartsMotor.name'
            )
        ));
        $this->set(compact(
        	'part_motors', 'tipe_motor_list'
    	));
	}

	function getInfoTtujKsu($ttuj_id, $atpm = false){
		$this->loadModel('Ttuj');
		$this->loadModel('Perlengkapan');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			),
			'contain' => array(
				'UangJalan',
			)
		));
		
		if(!empty($data_ttuj)){
			$this->loadModel('Driver');
			$driver_id = !empty($data_ttuj['Ttuj']['driver_penganti_id'])?$data_ttuj['Ttuj']['driver_penganti_id']:false;
			$data_ttuj = $this->Driver->getMerge($data_ttuj, $driver_id, 'DriverPenganti');
			$this->request->data = $data_ttuj;
		}

		$this->request->data['Ksu']['kekurangan_atpm'] = (($atpm == 'true') ? true : false);
		$perlengkapans = $this->Perlengkapan->getListPerlengkapan(2);
        $this->set(compact('perlengkapans'));
	}

	function getColorTipeMotor($tipe_motor_id, $ttuj_id){
		$this->loadModel('Ttuj');
		$this->loadModel('TtujTipeMotor');

		$data_ttuj = $this->TtujTipeMotor->getData('first', array(
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
		$this->loadModel('TtujPerlengkapan');
		
		$data_ttuj = $this->TtujPerlengkapan->getData('first', array(
			'conditions' => array(
				'TtujPerlengkapan.ttuj_id' => $ttuj_id,
				'TtujPerlengkapan.perlengkapan_id' => $perlengkapan_id
			)
		));

		$this->set(compact('data_ttuj'));
	}

	function getInfoLaka($ttuj_id = false){
		$this->loadModel('Ttuj');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id,
				'Ttuj.is_pool <>' => 1,
                'Ttuj.is_draft' => 0,
			),
			'contain' => false,
		), true, array(
			// 'plant' => true,
		));

		if( !empty($data_ttuj) ) {
			$this->loadModel('Driver');
			$this->loadModel('City');
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
		$this->loadModel('Lku');

        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');

		$options = array(
			'conditions' => array(
				'Ttuj.customer_id' => $customer_id,
				'Lku.complete_paid' => 0,
			),
			'contain' => array(
				'Ttuj',
			),
		);
		$lku_details = array();

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Lku->_callRefineParams($params, $options);
		$lku_id = $this->Lku->getData('list', $options);

		if(!empty($lku_id)){
			$this->loadModel('LkuDetail');
			$this->paginate = $this->LkuDetail->getData('paginate', array(
				'conditions' => array(
					'LkuDetail.lku_id' => $lku_id,
					'LkuDetail.complete_paid' => 0
				),
				'contain' => array(
					'Lku'
				),
            	'limit' => Configure::read('__Site.config_pagination'),
			));
			
			$lku_details = $this->paginate('LkuDetail');
		
			if(!empty($lku_details)){
				$this->loadModel('PartsMotor');
				$this->loadModel('TipeMotor');
				$this->loadModel('LkuPaymentDetail');

				foreach ($lku_details as $key => $value) {
					$part_motor = array();
					$tipe_motor = array();
					$part_motor_id = $this->MkCommon->filterEmptyField($value, 'LkuDetail', 'part_motor_id');
					$tipe_motor_id = $this->MkCommon->filterEmptyField($value, 'LkuDetail', 'tipe_motor_id');

					$lku_has_paid = $this->LkuPaymentDetail->getData('first', array(
						'conditions' => array(
							'LkuPaymentDetail.lku_detail_id' => $value['LkuDetail']['id'],
							'LkuPaymentDetail.status' => 1,
							'LkuPaymentDetail.lku_payment_id <>' => $payment_id,
						),
						'fields' => array(
							'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
						),
					));
					$value['LkuDetail']['lku_has_paid'] = !empty($lku_has_paid[0]['lku_has_paid'])?$lku_has_paid[0]['lku_has_paid']:0;

					$ttuj = $this->Lku->Ttuj->getData('first', array(
						'conditions' => array(
							'Ttuj.id' => $value['Lku']['ttuj_id'],
						),
						'contain' => false,
					));
					$value['Ttuj'] = !empty($ttuj['Ttuj']) ? $ttuj['Ttuj'] : array();

					if(!empty($part_motor_id)){
						$part_motor = $this->LkuDetail->PartsMotor->getData('first', array(
							'conditions' => array(
								'PartsMotor.id' => $part_motor_id,
							)
						));
					}
					$value['LkuDetail']['PartsMotor'] = !empty($part_motor['PartsMotor']) ? $part_motor['PartsMotor'] : array();

					if(!empty($tipe_motor_id)){
						$tipe_motor = $this->LkuDetail->TipeMotor->getData('first', array(
							'conditions' => array(
								'TipeMotor.id' => $tipe_motor_id,
							)
						));
					}
					$value['LkuDetail']['TipeMotor'] = !empty($tipe_motor['TipeMotor']) ? $tipe_motor['TipeMotor'] : array();
					$lku_details[$key] = $value;
				}
			}
		}
		
		$data_change = 'browse-invoice';
		$data_action = 'getTtujCustomerInfo';
		$title = 'Pembayaran LKU Customer';
		$this->set(compact(
			'customer_id', 'data_change', 'data_action', 
			'title', 'lku_details', 'lkus'
		));
	}

	function getTtujCustomerInfoKsu($customer_id = false){
		$this->loadModel('Ksu');

        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');

		$options = array(
			'conditions' => array(
				'Ttuj.customer_id' => $customer_id,
				'Ksu.complete_paid' => 0,
				'Ksu.kekurangan_atpm' => 0,
			),
			'contain' => array(
				'Ttuj',
			),
		);
		$ksu_details = array();

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Ksu->_callRefineParams($params, $options);
		$ksu_id = $this->Ksu->getData('list', $options);
		
		if(!empty($ksu_id)){
			$this->loadModel('KsuDetail');

			$this->paginate = $this->KsuDetail->getData('paginate', array(
				'conditions' => array(
					'KsuDetail.ksu_id' => $ksu_id,
					'KsuDetail.complete_paid' => 0
				),
				'contain' => array(
					'Ksu'
				)
			));
			$ksu_details = $this->paginate('KsuDetail');
		
			if(!empty($ksu_details)){
				$this->loadModel('KsuPaymentDetail');
				$this->loadModel('Perlengkapan');

				foreach ($ksu_details as $key => $value) {
					$Perlengkapan = array();

					$ksu_has_paid = $this->KsuPaymentDetail->getData('first', array(
						'conditions' => array(
							'KsuPaymentDetail.ksu_detail_id' => $value['KsuDetail']['id'],
							'KsuPaymentDetail.status' => 1,
							'KsuPaymentDetail.ksu_payment_id <>' => $payment_id,
						),
						'fields' => array(
							'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
						),
					));
					$ttuj = $this->Ksu->Ttuj->getData('first', array(
						'conditions' => array(
							'Ttuj.id' => $value['Ksu']['ttuj_id']
						),
						'contain' => false,
					));

					if(!empty($value['KsuDetail']['perlengkapan_id'])){
						$Perlengkapan = $this->Perlengkapan->getData('first', array(
							'conditions' => array(
								'Perlengkapan.id' => $value['KsuDetail']['perlengkapan_id']
							)
						));
					}

					$value['Ttuj'] = !empty($ttuj['Ttuj']) ? $ttuj['Ttuj'] : array();
					$value['KsuDetail']['ksu_has_paid'] = !empty($ksu_has_paid[0]['ksu_has_paid'])?$ksu_has_paid[0]['ksu_has_paid']:0;
					$value['KsuDetail']['Perlengkapan'] = !empty($Perlengkapan['Perlengkapan']) ? $Perlengkapan['Perlengkapan'] : array();
					$ksu_details[$key] = $value;
				}
			}
		}
		
		$data_change = 'browse-invoice';
		$data_action = 'getTtujCustomerInfo';
		$title = 'Pembayaran LKU Customer';
		$this->set(compact(
			'customer_id', 'data_change', 
			'data_action', 'title', 'ksu_details'
		));
	}

	function getTtujInfoLku($lku_id){
		$this->loadModel('Lku');
		$lku = $this->Lku->getData('first', array(
			'conditions' => array(
				'Lku.id' => $lku_id,
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
		$this->loadModel('City');
		$this->loadModel('GroupMotor');
		$data_revenue_detail = array();

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
                'Ttuj.id' => $ttuj_id,
				'Ttuj.is_draft' => 0,
			),
			'contain' => false,
		));

		if(!empty($data_ttuj)){
			$this->loadModel('TtujTipeMotor');

			$data_ttuj = $this->TtujTipeMotor->getMergeTtujTipeMotor( $data_ttuj, $ttuj_id );
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
				$this->loadModel('Revenue');

				foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
					$group_motor_name = false;
					$qtyTtuj = !empty($value['TtujTipeMotor']['qty'])?$value['TtujTipeMotor']['qty']:0;
					$group_motor_id = !empty($value['TipeMotor']['group_motor_id'])?$value['TipeMotor']['group_motor_id']:false;
					$groupMotor = $this->GroupMotor->getMerge($value, $group_motor_id);
        			$qtyReview = $this->Revenue->checkQtyUsed( $ttuj_id, false, $group_motor_id, false );
        			$qtyUsed = !empty($qtyReview['qtyUsed'])?$qtyReview['qtyUsed']:0;
					$qtyUnit = $qtyTtuj - $qtyUsed;

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

		$customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
		));
		$toCities = $this->City->getListCities();
		$groupMotors = $this->GroupMotor->getData('list');
        $this->MkCommon->_callSettingGeneral('Revenue', array( 'pph', 'ppn' ));

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
			),
		), true, array(
			'branch' => false,
		));

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
                $msgText = 'merubah';
            }else{
                $this->CalendarEvent->create();
                $msgText = 'menambah';
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
                	$transaction_id = $this->CalendarEvent->id;
					$this->params['old_data'] = $data_local;
					$this->params['data'] = $data;

                	$msg = array(
						'class' => 'success',
						'text' => sprintf(__('Sukses %s Event'), $msgText),
					);
                    $this->Log->logActivity( sprintf(__('Sukses %s Event #%s'), $msgText, $transaction_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $transaction_id );
                }else{
                	$msg = array(
						'class' => 'error',
						'text' => sprintf(__('Gagal %s Event'), $msg),
					);
                    $this->Log->logActivity( sprintf(__('Gagal %s Event #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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
                $this->Log->logActivity( sprintf(__('Sukses menghapus event ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus cevent.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus cevent ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Event tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

	function getInfoRevenueDetail( $ttuj_id = false, $customer_id = false, $detail_city_id = false, $group_motor_id = false, $is_charge = false, $main_city_id = false, $qty = 0, $jenis_unit = '', $from_city_id = false, $truck_id = false, $from_ttuj = false ){
		$this->loadModel('Ttuj');

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id,
			),
			'contain' => false,
		), true, array(
			'status' => 'all',
		));
		$from_city_id = $this->MkCommon->filterEmptyField($data_ttuj, 'Ttuj', 'from_city_id', $from_city_id);

		if( !empty($truck_id) ) {
			$truck = $this->Ttuj->Truck->getData('first', array(
                'conditions' => array(
                    'Truck.id' => $truck_id,
                ),
                'fields' => array(
                    'Truck.id', 'Truck.nopol',
                    'Truck.capacity'
                ),
            ));
			$truck_capacity = $this->MkCommon->filterEmptyField($truck, 'Truck', 'capacity');
		} else {
			$truck_capacity = $this->MkCommon->filterEmptyField($data_ttuj, 'Ttuj', 'truck_capacity');
		}

		if( !empty($group_motor_id) ) {
			$groupMotor = $this->Ttuj->Revenue->RevenueDetail->GroupMotor->getData('first', array(
				'conditions' => array(
					'GroupMotor.id' => $group_motor_id,
				),
			));

			if( !empty($groupMotor) ) {
				$group_motor_id = $groupMotor['GroupMotor']['id'];
			}
		}

		$tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $main_city_id, $detail_city_id, $customer_id, $truck_capacity, $group_motor_id );
		$this->set(compact(
			'is_charge', 'tarif',
			'qty', 'jenis_unit', 'truck',
			'ttuj_id', 'from_ttuj'
		));
	}

	function getInvoiceInfo( $customer_id = false, $tarif_type = 'angkut' ){
		$this->loadModel('Revenue');
		$this->loadModel('Bank');
		$this->loadModel('Customer');

        $head_office = Configure::read('__Site.config_branch_head_office');
		$conditions = array(
			'Revenue.customer_id' => $customer_id,
			'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
			'Revenue.status' => 1,
		);
		$customer = $this->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $customer_id
            ),
        ), true, array(
            'status' => 'all',
        ));

        $elementRevenue = false;
        $conditionsDetail = $conditions;
        $conditionsDetail['RevenueDetail.invoice_id'] = NULL;
        $conditionsDetail['RevenueDetail.tarif_angkutan_type'] = $tarif_type;

        if( !empty($head_office) ) {
        	$elementRevenue = array(
                'branch' => false,
            );
        }

		$revenueDetail = $this->Revenue->RevenueDetail->getData('first', array(
			'conditions' => array_merge($conditionsDetail, array(
				'Revenue.revenue_tarif_type' => 'per_unit',
			)),
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
		), $elementRevenue);
		$revenueId = $this->Revenue->RevenueDetail->getData('list', array(
			'conditions' => $conditionsDetail,
			'fields' => array(
				'RevenueDetail.revenue_id',
				'RevenueDetail.revenue_id',
			),
		), $elementRevenue);

        $conditions['Revenue.id'] = $revenueId;
        $conditionRevenue = $conditions;

		$revenue = $this->Revenue->getData('first', array(
			'conditions' => $conditionRevenue,
			'fields' => array(
				'SUM(Revenue.total_without_tax * (Revenue.pph / 100)) total_pph',
				'Revenue.customer_id',
			),
			'group' => array(
				'Revenue.customer_id'
			),
		), true, $elementRevenue);

        $conditionRevenueTruck = $conditionRevenue;
        $conditionRevenueTruck['Revenue.revenue_tarif_type'] = 'per_truck';
		$revenuePerTruck = $this->Revenue->getData('first', array(
			'conditions' => $conditionRevenueTruck,
			'fields' => array(
				'SUM(Revenue.tarif_per_truck) total',
				'Revenue.customer_id',
			),
			'group' => array(
				'Revenue.customer_id'
			),
		), true, $elementRevenue);

        $banks = $this->Bank->getData('list', array(
            'conditions' => array(
                'Bank.status' => 1,
            ),
        ));
        $msg = array(
        	'error' => 0,
        	'text' => '',
    	);

		if( !empty($customer) ){
			$monthFrom = !empty($revenueDetail[0]['period_from'])?$this->MkCommon->customDate($revenueDetail[0]['period_from'], 'Y-m'):false;
			$monthTo = !empty($revenueDetail[0]['period_to'])?$this->MkCommon->customDate($revenueDetail[0]['period_to'], 'Y-m'):false;
			
			$this->request->data['Invoice']['bank_id'] = !empty($customer['Customer']['bank_id'])?$customer['Customer']['bank_id']:false;
			$this->request->data['Invoice']['period_from'] = !empty($revenueDetail[0]['period_from'])?$this->MkCommon->customDate($revenueDetail[0]['period_from'], 'd/m/Y'):false;
			$this->request->data['Invoice']['period_to'] = !empty($revenueDetail[0]['period_to'])?$this->MkCommon->customDate($revenueDetail[0]['period_to'], 'd/m/Y'):false;
			$this->request->data['Invoice']['total_revenue'] = !empty($revenuePerTruck[0]['total'])?$revenuePerTruck[0]['total']:0;
			$this->request->data['Invoice']['total_pph'] = !empty($revenue[0]['total_pph'])?$revenue[0]['total_pph']:0;
			$this->request->data['Invoice']['total'] = !empty($revenueDetail[0]['total'])?$revenueDetail[0]['total']:0;

			$customer_group_id = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_group_id');
			$customer = $this->Customer->CustomerGroup->getMerge($customer, $customer_group_id);

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
	    	} else if( !empty($customer['CustomerGroupPattern']) ) {
                $this->request->data['Invoice']['pattern'] = $this->MkCommon->getNoInvoice( $customer );
			}
		}

		$this->set(compact(
			'banks', 'msg'
		));
	}

	// function getInvoicePaymentInfo($customer_id = false){
	// 	$this->loadModel('Revenue');
	// 	$revenues = $this->Revenue->getData('first', array(
	// 		'conditions' => array(
	// 			'Revenue.customer_id' => $customer_id,
	// 			'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
	// 			'Revenue.status' => 1,						
	// 		),
	// 		'order' => array(
	// 			'Revenue.date_revenue' => 'ASC'
	// 		),
	// 		'fields' => array(
	// 			'SUM(Revenue.total) total',
	// 			'MAX(Revenue.date_revenue) period_to',
	// 			'MIN(Revenue.date_revenue) period_from',
	// 		),
	// 		'group' => array(
	// 			'Revenue.customer_id'
	// 		),
	// 	));

	// 	if(!empty($revenues)){
	// 		$this->request->data['Invoice']['period_from'] = !empty($revenues[0]['period_from'])?$this->MkCommon->customDate($revenues[0]['period_from'], 'd/m/Y'):false;
	// 		$this->request->data['Invoice']['period_to'] = !empty($revenues[0]['period_to'])?$this->MkCommon->customDate($revenues[0]['period_to'], 'd/m/Y'):false;
	// 		$this->request->data['Invoice']['total'] = !empty($revenues[0]['total'])?$revenues[0]['total']:0;;
	// 	}
	// }

	function previewInvoice($customer_id = false, $invoice_type = 'angkut', $action = false){
		$this->loadModel('Revenue');

        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;
		$conditions = array(
			'Revenue.customer_id' => $customer_id,
			'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
		);

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

		$revenue_id = $this->Revenue->getData('list', array(
			'conditions' => $conditions,
			'order' => array(
				'Revenue.date_revenue' => 'ASC'
			),
			'fields' => array(
				'Revenue.id', 'Revenue.id',
			),
		), true, $elementRevenue);
		$totalPPN = $this->Revenue->getData('first', array(
			'conditions' => $conditions,
			'group' => array(
				'Revenue.customer_id'
			),
			'fields' => array(
				'SUM(total_without_tax * (ppn / 100)) ppn',
			),
		), true, $elementRevenue);
		$totalPPh = $this->Revenue->getData('first', array(
			'conditions' => $conditions,
			'group' => array(
				'Revenue.customer_id'
			),
			'fields' => array(
				'SUM(total_without_tax * (pph / 100)) pph',
			),
		), true, $elementRevenue);

		if(!empty($revenue_id)){
            $revenue_detail = $this->Revenue->RevenueDetail->getPreviewInvoice($revenue_id, $invoice_type, $action, 'preview');
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

	function getDrivers ( $id = false, $action_type = false ) {
		$this->loadModel('Driver');

		$title = __('Supir Truk');
		$data_action = 'browse-form';
		$data_change = 'driverID';
        $options = array(
        	'contain' => array(
        		'Truck',
    		),
            'limit' => Configure::read('__Site.config_pagination'),
    	);

        switch ($action_type) {
        	case 'pengganti':
        		$options['conditions'] = $this->Driver->getListDriverPenganti($id, true);
        		$options['contain'][] = 'Ttuj';
        		break;
        	
        	default:
		        if( !empty($id)) {
		            $options['conditions']['OR'] = array(
		                'Truck.id' => NULL,
		                'Driver.id' => $id,
		            );
		        } else {
					$options['conditions'] = array(
			            'Truck.id' => NULL,
			        );
		        }
        		break;
        }

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Driver->_callRefineParams($params, $options);

        if( $action_type == 'mutation' ) {
			$filterBranch = false;
		} else {
			$filterBranch = true;
		}

		$this->paginate = $this->Driver->getData('paginate', $options, true, array(
			'branch' => $filterBranch,
		));
        $drivers = $this->paginate('Driver');

        if( !empty($drivers) ) {
            $this->loadModel('City');

            foreach ($drivers as $key => $value) {
                // Custom Otorisasi
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Driver', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $drivers[$key] = $value;
            }
        }

        $this->set(compact(
        	'drivers', 'data_action', 'title',
        	'data_change', 'id'
    	));
	}

	function getTrucks ( $action_type = false, $action_id = false ) {
		$this->loadModel('Truck');
    	$this->loadModel('City');

		$title = __('Data Truk');
		$data_action = 'browse-form';
		$data_change = 'truckID';

		$options = array(
            'conditions' => array(),
            'contain' => array(
                'Driver'
            ),
            'order' => array(
                'Truck.nopol' => 'ASC',
            ),
            'limit' => 10,
        );
        $element = array();

        if( in_array($action_type, array( 'ttuj', 'revenue' )) ) {
    		$ttuj = $this->Truck->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $action_id,
                ),
	        ), true, array(
				'status' => 'all',
			));
    		$ttuj_truck_id = !empty($ttuj['Ttuj']['truck_id'])?$ttuj['Ttuj']['truck_id']:false;

            $options['contain'][] = 'Ttuj';

            // if( $action_type == 'ttuj' ) {
        		$plantCityId = Configure::read('__Site.Branch.Plant.id');

        		if( !empty($plantCityId) ) {
            		$options['conditions']['Truck.branch_id'] = $plantCityId;
            	}
            // } else {
            // 	$plantCityId = false;
            // }

			$addConditions = $this->Truck->getListTruck( $ttuj_truck_id, true, false, $plantCityId );
            $options['conditions'] = array_merge($options['conditions'], $addConditions);
		} else if( $action_type == 'laka' ) {
    		$data_change = 'laka-driver-change';
        	$options['conditions'] = $this->MkCommon->_callConditionPlant($options['conditions'], 'Truck');
            $options['conditions']['OR'] = array(
                array(
                    'Laka.id' => NULL
                ),
                array(
                    'Laka.truck_id' => $action_id,
                )
            );
            $options['contain'][] = 'Laka';
        } else if( $action_type == 'cashbank' ) {
    		$data_change = $action_id;
        	$element = array(
        		'branch' => false,
    		);
        }

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Truck->_callRefineParams($params, $options);

		$this->paginate = $this->Truck->getData('paginate', $options, true, $element);
        $trucks = $this->paginate('Truck');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $data = $truck['Truck'];
                $branch_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'branch_id');

                $truck = $this->Truck->TruckCategory->getMerge($truck, $data['truck_category_id']);
                $truck = $this->Truck->TruckBrand->getMerge($truck, $data['truck_brand_id']);
                $truck = $this->Truck->Company->getMerge($truck, $data['company_id']);
                $truck = $this->GroupBranch->Branch->getMerge($truck, $branch_id);

                $trucks[$key] = $truck;
            }
        }

        $this->set(compact(
        	'trucks', 'data_action', 'title',
        	'data_change', 'action_type', 'action_id'
    	));
	}

	function getDataTruck ( $truck_id = false ) {
		$this->loadModel('Truck');

		$options = array(
            'conditions' => array(
	            'Truck.id' => $truck_id,
	        ),
	        'contain' => array(
	        	'TruckCategory',
	        	'TruckFacility'
        	),
        );
        $result = $this->Truck->getData('first', $options, true, array(
        	'branch' => false,
    	));

        if( !empty($result) ) {
    		$branch_id = $this->MkCommon->filterEmptyField($result, 'Truck', 'branch_id');
    		$driver_id = $this->MkCommon->filterEmptyField($result, 'Truck', 'driver_id');

    		$result = $this->GroupBranch->Branch->getMerge($result, $branch_id);
    		$result = $this->Truck->Driver->getMerge($result, $driver_id);
    		$result = $this->Truck->TruckCustomer->getMergeTruckCustomer($result);
        }

        $this->set(compact(
        	'result'
    	));
    	$this->render('get_info_truck');
	}

	function getKirs () {
		$this->loadModel('Kir');
		$title = __('Data KIR');
		$data_action = 'browse-form';
		$data_change = 'truckID';
		$options = array(
			'conditions' => array(
	            'Kir.paid <>' => 'full',
	            'Kir.rejected' => 0,
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        );

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Kir->_callRefineParams($params, $options);

        $this->paginate = $this->Kir->getData('paginate', $options);
        $trucks = $this->paginate('Kir');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $truck = $this->Kir->Truck->getMerge($truck, $truck['Kir']['truck_id']);

                $driver_id = !empty($truck['Truck']['driver_id'])?$truck['Truck']['driver_id']:false;
                $truck = $this->Kir->Truck->Driver->getMerge($truck, $driver_id);
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
		$title = __('Data STNK');
		$data_action = 'browse-form';
		$data_change = 'truckID';
        $options = array(
			'conditions' => array(
	            'Stnk.status' => 1,
	            'Stnk.paid <>' => 'full',
	            'Stnk.rejected' => 0,
            ),
            'contain' => array(
            	'Truck',
        	),
            'limit' => Configure::read('__Site.config_pagination'),
        );

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Stnk->_callRefineParams($params, $options);

        $this->paginate = $this->Stnk->getData('paginate', $options);
        $trucks = $this->paginate('Stnk');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $truck = $this->Stnk->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
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
		$title = __('Data Ijin Usaha');
		$data_action = 'browse-form';
		$data_change = 'truckID';
        $options = array(
			'conditions' => array(
	            'Siup.paid <>' => 'full',
	            'Siup.rejected' => 0,
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        );

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Siup->_callRefineParams($params, $options);

        $this->paginate = $this->Siup->getData('paginate', $options);
        $trucks = $this->paginate('Siup');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $truck = $this->Siup->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
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
        $branchFlag = false;
        $options = array(
			'conditions' => array(
	            'Ttuj.is_draft' => 0,
	            'Ttuj.is_laka' => 0,
            ),
            'order' => array(
	            'Ttuj.created' => 'DESC',
	            'Ttuj.id' => 'DESC',
        	),
            'limit' => Configure::read('__Site.config_pagination'),
        );
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Ttuj->_callRefineParams($params, $options);

        switch ($action_type) {
            case 'bongkaran':
                $options['conditions']['Ttuj.is_arrive'] = 1;
                $options['conditions']['Ttuj.is_bongkaran <>'] = 1;
        		$options['conditions'] = $this->Ttuj->_callConditionBranch( $options['conditions'] );
                break;

            case 'balik':
                $options['conditions']['Ttuj.is_arrive'] = 1;
                $options['conditions']['Ttuj.is_bongkaran'] = 1;
                $options['conditions']['Ttuj.is_balik <>'] = 1;
        		$options['conditions'] = $this->Ttuj->_callConditionBranch( $options['conditions'] );
                break;

            case 'pool':
                $options['conditions']['Ttuj.is_arrive'] = 1;
                $options['conditions']['Ttuj.is_bongkaran'] = 1;
                $options['conditions']['Ttuj.is_balik'] = 1;
                $options['conditions']['Ttuj.is_pool <>'] = 1;
        		$options['conditions'] = $this->Ttuj->_callConditionTtujPool( $options['conditions'] );
                break;

            case 'revenues':
            	unset($options['conditions']['Ttuj.is_revenue']);
            	$options['conditions']['OR'] = array(
	                'Ttuj.is_revenue' => 0,
	                'Ttuj.id' => $ttuj_id,
	            );
				$data_change = 'getTtujInfoRevenue';
                break;

            case 'lku':
                $options['conditions'] = array_merge($options['conditions'], $this->RjLku->getTtujConditions());
				$data_change = 'getTtujInfo';
        		$branchFlag = true;
                break;

            case 'ksu':
                $options['conditions'] = array_merge($options['conditions'], $this->RjLku->getTtujConditions());
				$data_change = 'getTtujInfoKsu';
        		$branchFlag = true;
                break;

            case 'laka':
                $options['conditions']['Ttuj.is_pool <>'] = 1;
                $options['conditions']['Ttuj.truck_id'] = $ttuj_id;
				$data_change = 'laka-ttuj-change';
        		// $options['conditions'] = $this->MkCommon->_callConditionPlant($options['conditions'], 'Ttuj');
        		$branchFlag = true;
                break;

            case 'uang_jalan_payment':
                $options['conditions']['Ttuj.is_laka'] = array( 0, 1 );
                $options['conditions']['Ttuj.paid_uang_jalan'] = 0;
				$data_change = 'ttujID';
        		$options['order'] = array(
	                'Ttuj.created' => 'ASC',
	                'Ttuj.id' => 'ASC',
	            );
                break;
            
            default:
                $options['conditions']['Ttuj.is_arrive'] = 0;
        		$options['conditions'] = $this->Ttuj->_callConditionBranch( $options['conditions'] );
                break;
        }

        $this->paginate = $this->Ttuj->getData('paginate', $options, true, array(
        	'branch' => $branchFlag,
        ));
        $ttujs = $this->paginate('Ttuj');

        if( !empty($ttujs) ) {
        	$this->loadModel('Driver');

        	foreach ($ttujs as $key => $ttuj) {
				$ttuj_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'id');
				$to_city_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'to_city_id');

        		if( !empty($ttuj['Ttuj']['driver_penganti_id']) ) {
					$driver_penganti_id = $ttuj['Ttuj']['driver_penganti_id'];
					$ttuj = $this->Driver->getMerge($ttuj, $driver_penganti_id, 'DriverPenganti');
				}

				// if( $this->Ttuj->validateTtujAfterLeave( $to_city_id, $this->GroupBranch->Branch ) ) {
				// 	$ttujs[$key] = $ttuj;
    //             } else {
				// 	unset($ttujs[$key]);
				// }
        	}
        }

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
				'Invoice.id' => $id,
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
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

		$options = array(
			'conditions' => array(
				'Invoice.customer_id' => $id,
				'Invoice.complete_paid' => 0,
			),
		);

		$params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Invoice->_callRefineParams($params, $options);

		if(!empty($id)){
			$invoices = $this->Invoice->getdata('all', $options, true, $elementRevenue);

			if(!empty($invoices)){
				foreach ($invoices as $key => $value) {
					$invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
						'conditions' => array(
							'InvoicePaymentDetail.invoice_id' => $value['Invoice']['id'],
							'InvoicePaymentDetail.status' => 1,
							'InvoicePaymentDetail.invoice_payment_id <>' => $payment_id,
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
		$this->set(compact(
			'invoices', 'id', 'data_action', 'title'
		));
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
		$this->loadModel('Ttuj');

		$driver = $this->Ttuj->Truck->getData('first', array(
			'conditions' => array(
				'Truck.id' => $id
			),
			'contain' => array(
				'Driver'
			)
		), true, array(
            'plant' => true,
        ));
		$driver_name = $this->MkCommon->filterEmptyField($driver, 'Driver', 'name');
		$no_sim = $this->MkCommon->filterEmptyField($driver, 'Driver', 'no_sim');
		$ttujs = $this->Ttuj->getData('list', array(
            'conditions' => array(
                'Ttuj.truck_id' => $id,
                'Ttuj.is_pool <>' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.is_laka' => 0,
            ),
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'contain' => false,
        ), true, array(
        	// 'plant' => true,
        ));

		$this->set(compact('driver_name', 'no_sim', 'ttujs'));
	}

	function getUserCashBank( $action_type = 'cash_bank' ){
		$this->loadModel('Client');
		$data_action = 'browse-form';
		$data_change = 'receiver-id';
		
		$listReceivers = array(
        	'Customer' => __('Customer'),
        	'Vendor' => __('Vendor'),
        	'Employe' => __('karyawan'),
        	'Driver' => __('Supir'),
        );

		switch ($action_type) {
			case 'ttuj':
				$model = 'Driver';
				$title = __('Dibayar Kepada');
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

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Client->_callRefineParams($params, array(
			'limit' => 20,
    	));

		// if( !empty($this->request->data) ) {
		// 	$data = $this->request->data;

		// 	if(!empty($data['UserCashBank']['model'])){
		// 		$model = ucwords($data['UserCashBank']['model']);
		// 	} else if(!empty($this->params['named']['model'])){
		// 		$model = ucwords($this->params['named']['model']);
		// 	}
		// }

		$this->paginate = $options;
		$values = $this->paginate('Client');
		$this->request->data['UserCashBank']['model'] = $model;

		$this->set(compact(
			'values', 'model', 'data_action', 
			'title', 'data_change', 'listReceivers',
			'action_type'
		));
	}

	function getInfoCoa(){
		$this->loadModel('BranchCoa');

        $coaIdCache = $this->Session->read('Coa.CoaId');
        $coa = $this->BranchCoa->Coa->getData('first', array(
        	'conditions' => array(
        		'Coa.id' => $coaIdCache,
    		),
    	));
        $coa_code = $this->MkCommon->filterEmptyField($coa, 'Coa', 'coa_code');
        $params['named'] = $this->MkCommon->filterEmptyField($this->params, 'named');
        $param_code = $this->MkCommon->filterEmptyField($params, 'named', 'code');

        if( $param_code == 'none' ) {
        	$params['named']['code'] = false;
        }
        // else {
        // 	$params['named']['code'] = $this->MkCommon->filterEmptyField($params, 'named', 'code', $coa_code);
        // }

        $params = $this->MkCommon->_callRefineParams($params);
        $options =  $this->BranchCoa->_callRefineParams($params, array(
            'conditions' => array(
                'BranchCoa.branch_id' => Configure::read('__Site.config_branch_id'),
	        ),
	        'contain' => array(
	        	'Coa',
        	),
            'limit' => Configure::read('__Site.config_pagination'),
        ));

        if( empty($params['named']) && !empty($coa_code) && $param_code != 'none' ) {
        	$options['conditions']['Coa.code LIKE'] = '%'.$coa_code.'%';
        }

		$this->paginate = $this->BranchCoa->getData('paginate', $options);
		$coas = $this->paginate('BranchCoa');

        $data_action = 'browse-cash-banks';
		$title = __('Detail Kas/Bank');

		$this->set(compact(
			'data_action', 'title', 'coas'
		));
	}

	function getUserEmploye($rel = false, $user_id = false){
		$default_conditions = array(
            'User.status' => 1
        );

        if(!empty($this->request->data['Employe']['full_name'])){
			$default_conditions['CONCAT(Employe.first_name,\' \',Employe.last_name) LIKE'] = '%'.$this->request->data['Employe']['full_name'].'%';
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
			$groups = $this->User->Group->getData('list');

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
                $label = __('PPN IN');
                break;
            
            case 'prepayment_in':
				$this->loadModel('CashBank');
				$result = $this->CashBank->getDocumentCashBank();
				$urlBrowseDocument = array(
                    'controller'=> 'ajax', 
                    'action' => 'getCashBankPrepayment',
                );
                $label = __('Prepayment IN');
                break;
        }

		if( !empty($result) ) {
			$docs = $result['docs'];
			$this->request->data['CashBank']['document_type'] = $result['docs_type'];
		}

		$this->set('label', $label);
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
				'Revenue.ppn <>' => 0,
				'Revenue.transaction_status <>' => 'unposting',
	        ),
            'limit' => 10,
            'contain' => array(
                'Ttuj',
                'CustomerNoType',
            ),
			'order' => array(
				'Revenue.id' => 'ASC'
			),
        );

        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Revenue->_callRefineParams($params, $options);

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
                'CashBank.completed' => 1,
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

        $data = $this->request->data;
        $data = $this->MkCommon->dataConverter($data, array(
            'daterange' => array(
                'CashBank' => array(
                    'date',
                ),
            )
        ));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->CashBank->_callRefineParams($params, $options);

		$this->paginate = $this->CashBank->getData('paginate', $options);
        $cashBanks = $this->paginate('CashBank');

        if(!empty($cashBanks)){
            foreach ($cashBanks as $key => $value) {
                $model = $value['CashBank']['receiver_type'];
                $receiver_id = $value['CashBank']['receiver_id'];

				$id = !empty($value['CashBank']['id'])?$value['CashBank']['id']:false;
				$grand_total = !empty($value['CashBank']['grand_total'])?$value['CashBank']['grand_total']:0;
				$totalDibayar = $this->CashBank->CashBankDetail->totalPrepaymentDibayarPerCoa($id);

				$value['CashBank']['grand_total'] = $grand_total-$totalDibayar;
                $value['name_cash'] = $this->RjCashBank->_callReceiverName($receiver_id, $model);;
                $cashBanks[$key] = $value;
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
						'Coa',
						'Truck',
					),
				),
			), array(
				'status' => 'all',
			));

			if( !empty($customer) ) {
				$model = $customer['CashBank']['receiver_type'];
                $receiver_id = $customer['CashBank']['receiver_id'];
                
                $customer['CustomerNoType']['id'] = $receiver_id;
                $customer['CustomerNoType']['name'] = $this->RjCashBank->_callReceiverName($receiver_id, $model);

				if( !empty($customer['CashBankDetail']) ) {
					foreach ($customer['CashBankDetail'] as $key => $cashBankDetail) {
						$id = !empty($cashBankDetail['id'])?$cashBankDetail['id']:false;
						$coa_id = !empty($cashBankDetail['coa_id'])?$cashBankDetail['coa_id']:false;

						$totalDibayar = $this->CashBank->CashBankDetail->totalPrepaymentDibayarPerCoa($prepayment_id, $coa_id, $cash_bank_id, $id);
						$totalTagihan = !empty($customer['CashBankDetail'][$key]['total'])?$customer['CashBankDetail'][$key]['total']:0;
						$totalSisaTagihan = $totalTagihan - $totalDibayar;

						if( $totalSisaTagihan <= 0 ) {
							unset($customer['CashBankDetail'][$key]);
						} else {
							$customer['CashBankDetail'][$key]['total'] = $totalSisaTagihan;
						}
					}
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
			), true, array(
				'status' => 'all',
			));

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
        	'prepayment_id', 'model'
    	));
		$this->render('get_customer');
	}

	// function getInfoTtujPayment( $ttuj_id, $action_type = 'uang_jalan'){
	// 	$this->loadModel('Ttuj');
	// 	$result = $this->Ttuj->getTtujPayment($ttuj_id, $action_type);
	// 	echo json_encode($result);
	// 	$this->render(false);
	// }

	function getInfoEmploye($id){
		$this->loadModel('Employe');
		$employes = $this->Employe->getData('first', array(
			'conditions' => array(
				'Employe.id' => $id,
				'Employe.status' => 1
			)
		));

		$first_name = !empty($employes['Employe']['first_name'])?$employes['Employe']['first_name']:false;
		$last_name = !empty($employes['Employe']['last_name'])?$employes['Employe']['last_name']:false;

		$this->set(compact('first_name', 'last_name'));
	}

	function getBiayaTtuj( $action_type = false ){
		$this->loadModel('Ttuj');
		$this->loadModel('UangJalanKomisiPayment');
    	$this->loadModel('City');

		$document_type = false;
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');

        switch ($action_type) {
        	case 'biaya_ttuj':
				$title = __('Detail Biaya TTUJ');
	        	$jenisBiaya = array(
	        		'uang_kuli_muat' => __('Uang Kuli Muat'),
	        		'uang_kuli_bongkar' => __('Uang Kuli Bongkar'),
	        		'asdp' => __('Uang Penyebrangan'),
	        		'uang_kawal' => __('Uang Kawal'),
	        		'uang_keamanan' => __('Uang Keamanan'),
        		);
        		$conditions = array(
        			'UangJalanKomisiPayment.data_type' => array(
        				'uang_kuli_muat',
		        		'uang_kuli_bongkar',
		        		'asdp',
		        		'uang_kawal',
		        		'uang_keamanan',
    				),
    			);
        		break;
        	
        	default:
				$title = __('Detail Biaya Uang Jalan / Komisi');
	        	$jenisBiaya = array(
	        		'uang_jalan' => __('Uang Jalan'),
	        		'uang_jalan_2' => __('Uang Jalan ke 2'),
	        		'uang_jalan_extra' => __('Uang Jalan Extra'),
	        		'commission' => __('Komisi'),
	        		'commission_extra' => __('Komisi Extra'),
        		);
        		$conditions = array(
        			'UangJalanKomisiPayment.data_type' => array(
		        		'uang_jalan',
		        		'uang_jalan_2',
		        		'uang_jalan_extra',
		        		'commission',
		        		'commission_extra',
    				),
    			);
        		break;
        }

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->UangJalanKomisiPayment->_callRefineParams($params, array(
        	'conditions' => $conditions,
            'limit' => Configure::read('__Site.config_pagination'),
    	));

        if(!empty($this->params)){
            if(!empty($this->params['named']['uang_jalan_1']) || !empty($this->params['named']['uang_jalan_2']) || !empty($this->params['named']['uang_jalan_extra']) || !empty($this->params['named']['commission']) || !empty($this->params['named']['commission_extra']) || !empty($this->params['named']['uang_kuli_muat']) || !empty($this->params['named']['uang_kuli_bongkar']) || !empty($this->params['named']['asdp']) || !empty($this->params['named']['uang_kawal']) || !empty($this->params['named']['uang_keamanan'])){
                $document_type = true;
        	}
        }

        $this->paginate = $options;
        $ttujs = $this->paginate('UangJalanKomisiPayment');

        if( !empty($ttujs) ) {
        	foreach ($ttujs as $key => $ttuj) {
        		$customer_id = $this->MkCommon->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'customer_id');
            	$driver_id = $this->MkCommon->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'driver_id');
            	$ttuj_id = $this->MkCommon->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'id');
            	$driver_penganti_id = $this->MkCommon->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'driver_penganti_id');

        		$ttuj = $this->Ttuj->Customer->getMerge($ttuj, $customer_id);
            	$ttuj = $this->Ttuj->Truck->Driver->getMerge($ttuj, $driver_id);
            	$ttuj = $this->Ttuj->Truck->Driver->getMerge($ttuj, $driver_penganti_id, 'DriverPenganti');

            	switch ($action_type) {
		        	case 'biaya_ttuj':
            				$ttuj['uang_kuli_muat_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_kuli_muat', $payment_id);
            				$ttuj['uang_kuli_bongkar_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_kuli_bongkar', $payment_id);
            				$ttuj['asdp_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'asdp', $payment_id);
            				$ttuj['uang_keamanan_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_keamanan', $payment_id);
            				$ttuj['uang_kawal_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_kawal', $payment_id);
		        		break;
		        	
		        	default:
		            	$ttuj['uang_jalan_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_jalan', $payment_id);
		            	$ttuj['commission_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'commission', $payment_id);
		            	$ttuj['uang_jalan_2_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_jalan_2', $payment_id);
		            	$ttuj['uang_jalan_extra_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'uang_jalan_extra', $payment_id);
		            	$ttuj['commission_extra_dibayar'] = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, 'commission_extra', $payment_id);
		        		break;
		        }

            	$ttujs[$key] = $ttuj;
        	}
        }

        $data_action = 'browse-check-docs';
        $cities = $this->City->getData('list');
		$this->set(compact(
			'data_action', 'title', 'ttujs',
			'action_type', 'jenisBiaya', 'document_type',
			'cities', 'payment_id'
		));
	}

	function getSjDriver( $driver_id ) {
		$this->loadModel('Ttuj');
		$sjOutstanding = $this->Ttuj->getSJOutstanding( $driver_id );

		$this->set(compact(
			'sjOutstanding', 'driver_id'
		));
	}

	function auth_action_module($group_id = false, $branch_id = false, $checkall = false){
		if( !empty($branch_id) && !empty($group_id) ){
			$this->loadModel('BranchModule');
			$this->loadModel('BranchActionModule');
			$this->loadModel('BranchParentModule');

			$tmp_group_branch_id = !empty($this->params['named']['id'])?$this->params['named']['id']:false;
        	$parent_modules = $this->BranchParentModule->getData('all');

        	if(!empty($parent_modules)){
        		$GroupBranch = $this->GroupBranch->find('first', array(
					'conditions' => array(
						'GroupBranch.group_id' => $group_id,
						'GroupBranch.branch_id' => $branch_id,
					)
				));
				$group_branch_id = $this->MkCommon->filterEmptyField($GroupBranch, 'GroupBranch', 'id');

				if(empty($GroupBranch)){
					$this->GroupBranch->create();
					$this->GroupBranch->set(array(
						'group_id' => $group_id,
						'branch_id' => $branch_id,
					));
					
					if($this->GroupBranch->save()){
						$group_branch_id = $this->GroupBranch->id;
    					$this->Log->logActivity( sprintf(__('Berhasil menambahkan otorisasi module #%s utk group user #%s'), $group_branch_id, $group_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $group_branch_id );

						if( !empty($tmp_group_branch_id) ) {
							$this->GroupBranch->delete($tmp_group_branch_id);
						}
					} else {
    					$this->Log->logActivity( sprintf(__('Gagal menambahkan Group Cabang #%s utk group user #%s'), $branch_id, $group_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $group_id );
					}
				}

				foreach ($parent_modules as $key_parent => $value) {
					$parent_modules[$key_parent]['child'] = $branch_modules = $this->BranchModule->getData('all', array(
		                'conditions' => array(
		                	'BranchModule.branch_parent_module_id' => $value['BranchParentModule']['id'],
		                    'BranchModule.status' => 1,
		                    'BranchModule.parent_id' => 0
		                ),
		                'contain' => array(
                            'BranchChild' => array(
                                'conditions' => array(
                                    'BranchChild.status' => 1
                                ),
                                'order'=> array(
                                    'BranchChild.order' => 'ASC'
                                ),
                            )
                        ),
                        'order' => array(
                            'BranchModule.order' => 'ASC'
                        )
		            ));
					
					$data_auth = $this->BranchActionModule->getDataBranch($group_branch_id);

		            /*custom*/
		            if(!empty($branch_modules) && !empty($checkall)){
		            	$allow = 1;
		            	$flagSave = true;
		            	$default_msg = __('menambahkan otorisasi');

		            	if($checkall == 'uncheckall'){
		            		$allow = 0;
		            		$default_msg = __('menghilangkan otorisasi');
		            	}
		            	
						foreach ($branch_modules as $key_parent_1 => $value_module) {
							if(!empty($value_module['BranchChild'])){
								foreach ($value_module['BranchChild'] as $key => $value) {
									if(!empty($data_auth[$value['id']])){
										$this->BranchActionModule->id = $data_auth[$value['id']];
										$this->BranchActionModule->set('is_allow', $allow);
									}else{
										$this->BranchActionModule->create();
						            	$this->BranchActionModule->set(array(
						            		'group_branch_id' => $group_branch_id,
						            		'branch_module_id' => $value['id'],
						            		'is_allow' => $allow,
						            	));
									}

									if($this->BranchActionModule->save()){
										$id = $this->BranchActionModule->id;
										$parent_modules[$key_parent]['child'][$key_parent_1]['BranchChild'][$key]['is_allow'] = $allow;
									} else {
		            					$flagSave = false;
									}
								}
							}
						}
					}
		            /*end custom*/
				}

				if( !empty($default_msg) ) {
	        		if( !empty($flagSave) ) {
						$this->Log->logActivity( sprintf(__('Berhasil %s kpd group #%s utk semua module'), $default_msg, $group_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
	        		} else {
						$this->Log->logActivity( sprintf(__('Gagal %s kpd group #%s utk semua module'), $default_msg, $group_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $group_id );
	        		}
	        	}

				$branch_modules = $parent_modules;
				
	            $this->set(compact(
	            	'branch_modules', 'group_branch_id', 'msg'
            	));
        	}
		}
	}

	function auth_action_child_module($group_branch_id = false, $branch_module_id = false){
		if(!empty($group_branch_id) && !empty($branch_module_id)){
			$this->loadModel('BranchActionModule');
			$this->loadModel('BranchModule');

			$branch_modules = $this->BranchModule->getData('first', array(
                'conditions' => array(
                    'BranchModule.status' => 1,
                    'BranchModule.id' => $branch_module_id
                )
            ));

			$save = false;
			if(!empty($branch_modules)){
				$data_auth = $this->BranchActionModule->getData('first', array(
	                'conditions' => array(
	                    'BranchActionModule.group_branch_id' => $group_branch_id,
	                    'BranchActionModule.branch_module_id' => $branch_module_id,
	                )
	            ));
				
	            if(!empty($data_auth)){
	            	$this->BranchActionModule->id = $data_auth['BranchActionModule']['id'];

	            	$is_allow = true;
	            	if(!empty($data_auth['BranchActionModule']['is_allow'])){
	            		$is_allow = false;
	            	}

	            	$this->BranchActionModule->set('is_allow', $is_allow);

	            	if($this->BranchActionModule->save()){
	            		$id = $this->BranchActionModule->id;
	            		$save = true;
	            		$data_auth['BranchActionModule']['is_allow'] = $is_allow;
    					$this->Log->logActivity( sprintf(__('Berhasil menambahkan otorisasi module #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
	            	} else {
    					$this->Log->logActivity( sprintf(__('Gagal menambahkan Group Cabang #%s utk module #%s'), $group_branch_id, $branch_module_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $group_branch_id );
	            	}
	            }else{
	            	$this->BranchActionModule->create();
	            	$this->BranchActionModule->set(array(
	            		'group_branch_id' => $group_branch_id,
	            		'branch_module_id' => $branch_module_id,
	            		'is_allow' => 1,
	            	));

	            	if($this->BranchActionModule->save()){
	            		$id = $this->BranchActionModule->id;
	            		$save = true;
	            		$data_auth['BranchActionModule']['is_allow'] = 1;
    					$this->Log->logActivity( sprintf(__('Berhasil menambahkan otorisasi module #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
	            	} else {
    					$this->Log->logActivity( sprintf(__('Gagal menambahkan Group Cabang #%s utk module #%s'), $group_branch_id, $branch_module_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $group_branch_id );
	            	}
	            }

	            $this->set('data_auth', $data_auth);
			}

			$this->set(compact(
				'branch_modules', 'save',
				'group_branch_id'
			));
		}
	}

	function delete_branch_group($id){
		if(!empty($id)){
			$this->loadModel('GroupBranch');

			$group_branch = $this->GroupBranch->find('first', array(
				'conditions' => array(
					'GroupBranch.id' => $id
				)
			));

			$msg = array(
				'type' => 'error',
				'msg' => 'Gagal menghapus cabang'
			);
			
			if(!empty($group_branch)){
				if($this->GroupBranch->delete($id)){
					
					$this->loadModel('BranchActionModule');

					$this->BranchActionModule->deleteAll(array(
						'group_branch_id' => $id
					));

					$msg = array(
						'type' => 'success',
						'msg' => 'Berhasil menghapus cabang'
					);
                    $this->Log->logActivity( sprintf(__('Berhasil menghapus cabang #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
				} else {
                    $this->Log->logActivity( sprintf(__('Gagal menghapus cabang #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
				}
			}else{
				$msg = array(
					'type' => 'error',
					'msg' => 'Cabang tidak ditemukan'
				);
			}

			$this->set('msg', $msg);
		}
	}

	function check_per_branch($group_id = false, $branch_id = false, $parent_id = false, $type = false){
		if(!empty($group_id) && !empty($parent_id) && !empty($type)){
			$this->loadModel('BranchActionModule');
			$this->loadModel('BranchModule');
			$this->loadModel('GroupBranch');

			$GroupBranch = $this->GroupBranch->find('first', array(
				'conditions' => array(
					'GroupBranch.group_id' => $group_id,
					'GroupBranch.branch_id' => $branch_id,
				)
			));

			$group_branch_id = '';
			if(!empty($GroupBranch)){
				$group_branch_id = $GroupBranch['GroupBranch']['id'];
			}

            if(!empty($GroupBranch)){
            	$branch_modules = $this->BranchModule->getData('all', array(
	                'conditions' => array(
	                    'BranchModule.status' => 1,
	                    'BranchModule.branch_parent_module_id' => $parent_id
	                ),
	                'contain' => array(
                        'BranchChild' => array(
                            'conditions' => array(
                                'BranchChild.status' => 1
                            ),
                            'order'=> array(
                                'BranchChild.order' => 'ASC'
                            ),
                        )
                    ),
                    'order' => array(
                        'BranchModule.order' => 'ASC'
                    )
	            ));

            	$allow = 0;
        		$default_msg = __('menghilangkan otorisasi');
        		$flagSave = true;

	            if($type == 'checkall'){
	            	$allow = 1;
            		$default_msg = __('menambahkan otorisasi');
	            }

	            if(!empty($branch_modules)){
	            	foreach ($branch_modules as $key_parent => $value_act) {
						$branch_action_id = Set::extract('/BranchChild/id', $value_act);

		            	$data_auth = $this->BranchActionModule->getDataBranch($group_branch_id, false, $branch_action_id);

			            foreach ($value_act['BranchChild'] as $key => $value) {
			            	if( !empty($data_auth[$value['id']]) ){
								$this->BranchActionModule->id = $data_auth[$value['id']];
								$this->BranchActionModule->set('is_allow', $allow);
							}else{
								$this->BranchActionModule->create();
				            	$this->BranchActionModule->set(array(
				            		'group_branch_id' => $group_branch_id,
				            		'branch_module_id' => $value['id'],
				            		'is_allow' => $allow,
				            	));
							}

							if($this->BranchActionModule->save()){
								$branch_modules[$key_parent]['BranchChild'][$key]['is_allow'] = $allow;
							} else {
        						$flagSave = true;
							}
			            }
	            	}
		            
		            $this->set(compact(
		            	'branch_modules', 'group_branch_id'
	            	));
		        }

				if( !empty($default_msg) ) {
	        		if( !empty($flagSave) ) {
						$this->Log->logActivity( sprintf(__('Berhasil %s kpd group #%s utk semua module #%s di cabang #%s'), $default_msg, $group_id, $parent_id, $branch_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
	        		} else {
						$this->Log->logActivity( sprintf(__('Gagal %s kpd group #%s utk semua module #%s di cabang #%s'), $default_msg, $group_id, $parent_id, $branch_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $group_id );
	        		}
	        	}
            }
		}
	}

	function products ( $action_type = 'sq' ) {
        $this->loadModel('Product');

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->_callRefineParams($params, array(
        	'limit' => 10,
    	));

        switch ($action_type) {
        	case 'po':
        		$status = 'no-sq';
        		break;
        	
        	default:
        		$status = 'active';
        		break;
        }

        $this->paginate = $this->Product->getData('paginate', $options, array(
        	'status' => $status,
    	));
        $values = $this->paginate('Product');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Product', 'id');
                $product_unit_id = $this->MkCommon->filterEmptyField($value, 'Product', 'product_unit_id');
                $product_category_id = $this->MkCommon->filterEmptyField($value, 'Product', 'product_category_id');

                $value = $this->Product->ProductUnit->getMerge($value, $product_unit_id);
                $value = $this->Product->ProductCategory->getMerge($value, $product_category_id);
                $value['Product']['rate'] = $this->Product->SupplierQuotationDetail->SupplierQuotation->_callRatePrice($id, false, '-');
                $values[$key] = $value;
            }
        }

        $groups = $this->Product->ProductCategory->getData('list');
        $this->set('module_title', __('Barang'));
        $this->set(compact(
        	'values', 'groups', 'action_type'
    	));
	}

	function supplier_quotations () {
        $this->loadModel('SupplierQuotation');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->SupplierQuotation->_callRefineParams($params, array(
        	'limit' => 10,
    	));

        $this->paginate = $this->SupplierQuotation->getData('paginate', $options, array(
        	'status' => 'available',
    	));
        $values = $this->paginate('SupplierQuotation');

        $this->set('module_title', __('Supplier Quotation'));
        $this->set(compact(
        	'values'
    	));
	}

	function getSupplierQuotation ( $id = false ) {
        $this->loadModel('SupplierQuotation');
        $values = $this->SupplierQuotation->SupplierQuotationDetail->getData('all', array(
        	'conditions' => array(
        		'SupplierQuotationDetail.supplier_quotation_id' => $id,
    		),
    	), array(
        	'status' => 'available',
    	));

    	if( !empty($values) ) {
            $values = $this->SupplierQuotation->SupplierQuotationDetail->Product->getMerge($values, false, 'SupplierQuotationDetail', $id);
    	}

        $this->set('module_title', __('Supplier Quotation'));
        $this->set(compact(
        	'values'
    	));
    	$this->render('/Elements/blocks/ajax/purchases/purchase_orders/tables/detail_products');
	}

	function change_lead_time () {
		$isAjax = $this->RequestHandler->isAjax();
		$data = $this->request->data;
		$data = $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'Ttuj' => array(
                    'tgl_berangkat',
                ),
            )
        ));

		$tgl_berangkat = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'tgl_berangkat');
		$jam_berangkat = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'jam_berangkat');

        $tgl_jam_berangkat = sprintf('%s %s', $tgl_berangkat, $jam_berangkat);
		$this->request->data['Ttuj']['tgl_berangkat'] = false;

        $this->set(compact(
        	'isAjax', 'tgl_jam_berangkat'
    	));
    	$this->render('/Elements/blocks/ttuj/forms/ttuj_lanjutan');
	}

	function getDocumentTrucks(){
		$this->loadModel('DocumentTruck');

        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');

		$title = __('Dokumen Truk');
    	$jenisBiaya = array(
    		'kir' => __('KIR'),
    		'siup' => __('SIUP'),
    		'stnk' => __('STNK'),
		);

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->DocumentTruck->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
    	));

        if(!empty($this->params)){
            if(!empty($this->params['named']['kir']) || !empty($this->params['named']['siup']) || !empty($this->params['named']['stnk']) ){
                $document_type = true;
        	}
        }

        $this->paginate = $options;
        $values = $this->paginate('DocumentTruck');

        if( !empty($values) ) {
        	foreach ($values as $key => $value) {
        		$document_id = $this->MkCommon->filterEmptyField($value, 'DocumentTruck', 'id');
        		$document_type = $this->MkCommon->filterEmptyField($value, 'DocumentTruck', 'data_type');

				$value['DocumentTruck']['last_paid'] = $this->DocumentTruck->DocumentPaymentDetail->getTotalPayment($document_id, $document_type, $payment_id);

            	$values[$key] = $value;
        	}
        }

        $data_action = 'browse-check-docs';
		$this->set(compact(
			'data_action', 'title', 'values',
			'jenisBiaya', 'document_type',
			'payment_id'
		));
	}

	function getLakas(){
		$this->loadModel('Laka');

        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');
		$title = __('LAKA');

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Laka->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
    	));

        $this->paginate = $options;
        $values = $this->paginate('Laka');

        if( !empty($values) ) {
        	foreach ($values as $key => $value) {
        		$document_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'id');

				$value['Laka']['last_paid'] = $this->Laka->LakaPaymentDetail->getTotalPayment($document_id, $payment_id);

            	$values[$key] = $value;
        	}
        }

        $data_action = 'browse-check-docs';
		$this->set(compact(
			'data_action', 'title', 'values',
			'payment_id'
		));
	}

	function invoice_report ( $id = false ) {
		$this->loadModel('Invoice');
		$head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = array(
            'status' => 'all',
        );

        if( !empty($head_office) ) {
            $elementRevenue['branch'] = false;
        }
        
        $value = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
            ),
        ), true, $elementRevenue);
		$this->set(compact(
			'value', 'id'
		));
	}

	function saveCache ( $field_name = false, $coa_id = false ) {
		$cacheName = sprintf('%s.CoaId', $field_name);
        $this->Session->write($cacheName, $coa_id);
        die();
	}

	function cashbank_note(){
		$this->loadModel('CashBank');
		$values = array();
		$keyword = $this->MkCommon->filterEmptyField($this->request->data, 'query');
		
		if( !empty($keyword) ) {
			$values = $this->CashBank->getData('list', array(
				'conditions' => array(
					'CashBank.user_id' => $this->user_id,
					'CashBank.description LIKE' => '%'.$keyword.'%',
				),
				'fields' => array(
					'CashBank.id', 'CashBank.description',
				),
				'order' => array(
					'CashBank.id' => 'DESC',
				),
				'limit' => 10,
			));

			if( !empty($values) ) {
				$values = array_values($values);
			}
		}

		$this->autoRender = false;
		return json_encode($values);
	}
}
?>