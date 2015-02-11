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

	function getInfoTtujRevenue( $ttuj_id = false, $customer_id = false ){
		$this->loadModel('Ttuj');
		$this->loadModel('TarifAngkutan');
		$this->loadModel('Customer');
		$this->loadModel('City');
		$this->loadModel('TipeMotor');
		$this->loadModel('Revenue');
		$this->loadModel('TtujTipeMotorUse');
		$data_revenue_detail = array();

		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id
			)
		));


		if(!empty($data_ttuj)){
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
				$tipe_motor_list = array();

				foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
					$tipe_motor = $this->TipeMotor->getData('first', array(
						'conditions' => array(
							'TipeMotor.id' => $value['tipe_motor_id']
						)
					));
		            $revenue_id = $this->Revenue->find('list', array(
		                'conditions' => array(
		                    'Revenue.ttuj_id' => $ttuj_id,
		                    'Revenue.status' => 1,
		                ),
		            ));
		            $qtyUsed = $this->TtujTipeMotorUse->find('first', array(
		                'conditions' => array(
		                    'TtujTipeMotorUse.revenue_id' => $revenue_id,
		                    'TtujTipeMotorUse.ttuj_tipe_motor_id' => $value['id'],
		                ),
		                'fields' => array(
		                    'SUM(TtujTipeMotorUse.qty) as count_qty'
		                )
		            ));

		            if( !empty($qtyUsed[0]['count_qty']) ) {
		                $qtyUsed = $qtyUsed[0]['count_qty'];
		            } else {
		            	$qtyUsed = 0;
		            }

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

						$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $value['city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity'], $tipe_motor['TipeMotor']['group_motor_id']);
					}else{
						$to_city_name = $data_ttuj['Ttuj']['to_city_name'];
						$to_city_id = $data_ttuj['Ttuj']['to_city_id'];

						$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $data_ttuj['Ttuj']['to_city_id'], $data_ttuj['Ttuj']['customer_id'], $data_ttuj['Ttuj']['truck_capacity'], $tipe_motor['TipeMotor']['group_motor_id']);
					}

					$data_revenue_detail[$key] = array(
						'TtujTipeMotor' => array(
							'qty' => $value['qty'],
						),
						'RevenueDetail' => array(
							'to_city_name' => $to_city_name,
							'price_unit' => $tarif,
							'qty_unit' => $value['qty'] - $qtyUsed,
							'tipe_motor_id' => $tipe_motor_id,
							'city_id' => $to_city_id,
							'TipeMotor' => array(
								'name' => $tipe_motor_name,
							),
							'ttuj_tipe_motor_id' => $value['id'],
                        	// 'max_qty_unit' => $value['qty'],
						)
					);
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
		$list_tipe_motor = $this->TipeMotor->getData('list', array(
			'conditions' => array(
				'TipeMotor.status' => 1
			)
		));
		// debug($data_revenue_detail);die();
		$this->set(compact(
			'data_revenue_detail', 'customers', 'toCities', 'list_tipe_motor',
			'tarifTruck'
		));
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

	function getInfoRevenueDetail( $ttuj_id = false, $customer_id = false, $city_id = false, $tipe_motor_id = false ){
		$this->loadModel('Ttuj');
		$this->loadModel('TipeMotor');
		$this->loadModel('TarifAngkutan');
		$detail = array();
		$data_ttuj = $this->Ttuj->getData('first', array(
			'conditions' => array(
				'Ttuj.id' => $ttuj_id,
				'Ttuj.is_pool' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
			),
		), false);
		$group_motor_id = false;

		if( !empty($tipe_motor_id) ) {
			$tipeMotor = $this->TipeMotor->getData('first', array(
				'conditions' => array(
					'TipeMotor.id' => $tipe_motor_id,
	                'TipeMotor.status' => 1,
				),
	            'contain' => array(
	                'ColorMotor',
	            ),
			), false);

			if( !empty($tipeMotor) ) {
				$group_motor_id = $tipeMotor['TipeMotor']['group_motor_id'];
			}
		}

		if(!empty($data_ttuj)){
			$tarif = $this->TarifAngkutan->findTarif($data_ttuj['Ttuj']['from_city_id'], $city_id, $customer_id, $data_ttuj['Ttuj']['truck_capacity'], $group_motor_id);
			$detail = array(
				'RevenueDetail' => array(
					'price_unit' => $tarif,
				)
			);
		}

		$this->set(compact('detail'));
	}

	function getInvoiceInfo($customer_id = false){
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

	function previewInvoice($customer_id, $action = false){
		$this->loadModel('Revenue');
		$this->loadModel('TipeMotor');
		$this->loadModel('City');
		$this->loadModel('TarifAngkutan');

		$revenues = $this->Revenue->getData('all', array(
			'conditions' => array(
				'Revenue.customer_id' => $customer_id,
				'Revenue.transaction_status' => 'posting',
				'Revenue.status' => 1,						
			),
			'order' => array(
				'Revenue.date_revenue' => 'ASC'
			),
		));

		if(!empty($revenues)){
			$revenue_id = Set::extract('/Revenue/id', $revenues);
			
			if($action == 'tarif'){
				$revenue_detail = $this->Revenue->RevenueDetail->getData('all', array(
					'conditions' => array(
						'RevenueDetail.revenue_id' => $revenue_id,
					),
					'order' => array(
						'RevenueDetail.price_unit' => 'DESC'
					)
				));
			}else{
				$revenue_detail = $this->Revenue->RevenueDetail->getData('all', array(
					'conditions' => array(
						'RevenueDetail.revenue_id' => $revenue_id,
					),
					'order' => array(
						'RevenueDetail.city_id'
					)
				));
			}

			foreach ($revenue_detail as $key => $value) {
				if(!empty($value['RevenueDetail'])){
					$value = $this->TipeMotor->getMerge($value, $value['RevenueDetail']['tipe_motor_id']);
					$value = $this->City->getMerge($value, $value['RevenueDetail']['city_id']);
					$value = $this->TarifAngkutan->getMerge($value, $value['RevenueDetail']['tarif_angkutan_id']);
					
					$revenue_detail[$key] = $value;
				}
			}
			
			if($action == 'tarif'){
				$result = array();
				foreach ($revenue_detail as $key => $value) {
					$result[$value['RevenueDetail']['price_unit']][] = $value;
				}
				$revenue_detail = $result;
			}else{
				$result = array();
				foreach ($revenue_detail as $key => $value) {
					$result[$value['City']['id']][] = $value;
				}
				$revenue_detail = $result;
			}
		}
// debug($revenue_detail);die();
		$this->set(compact('revenue_detail', 'action'));
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

	function getTrucks () {
		$this->loadModel('Truck');
		$title = __('Data Truk');
		$data_action = 'browse-form';
		$data_change = 'truckID';
		$conditions = array(
            'Truck.status' => 1
        );

        if(!empty($this->request->data)){
            if(!empty($this->request->data['Truck']['nopol'])){
                $nopol = urldecode($this->request->data['Truck']['nopol']);
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }
            if(!empty($this->request->data['Driver']['name'])){
                $name = urldecode($this->request->data['Driver']['name']);
                $conditions['Driver.name LIKE '] = '%'.$name.'%';
            }
        }

		$this->paginate = $this->Truck->getData('paginate', array(
            'conditions' => $conditions,
            'limit' => 10,
            'contain' => array(
                'Driver'
            ),
        ));
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
            'Ttuj.is_revenue' => 0,
            'OR' => array(
                'Ttuj.is_revenue' => 0,
                'Ttuj.id' => $ttuj_id,
            ),
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
                $conditions['Ttuj.is_pool'] = 1;
				$data_change = 'getTtujInfoRevenue';
                break;

            case 'lku':
                $conditions['Ttuj.is_pool'] = 1;
				$data_change = 'getTtujInfo';
                break;

            case 'laka':
                $conditions['Ttuj.is_pool <>'] = 1;
				$data_change = 'laka-driver-change';
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
}
?>