<?php
App::uses('AppController', 'Controller');
class RevenuesController extends AppController {
	public $uses = array();

    public $components = array(
        'RjRevenue'
    );

    public $helper = array(
        'PhpExcel'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Revenue'));
        $this->set('module_title', __('Revenue'));
    }

    function search( $index = 'index', $id = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjRevenue->processRefine($this->request->data);
            $params = $this->RjRevenue->generateSearchURL($refine);
            if(!empty($id)){
                array_push($params, $id);
            }
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function ttuj() {
        $this->loadModel('Ttuj');
		$this->set('active_menu', 'ttuj');
		$this->set('sub_module_title', __('TTUJ'));

        $conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nottuj'])){
                $nottuj = urldecode($refine['nottuj']);
                $this->request->data['Ttuj']['nottuj'] = $nottuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
            }
            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['Ttuj.customer_name LIKE '] = '%'.$customer.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
	}

    function ttuj_add( $data_action = 'depo' ){
        $this->loadModel('Ttuj');
        $module_title = sprintf(__('Tambah TTUJ - %s'), strtoupper($data_action));
        $this->set('sub_module_title', trim($module_title));
        $this->doTTUJ( $data_action );
    }

    function ttuj_edit( $id ){
        $this->loadModel('Ttuj');
        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id
            )
        ));

        if(!empty($ttuj)){
            $data_action = false;

            if( !empty($ttuj['Ttuj']['is_retail']) ) {
                $data_action = 'retail';
            }

            $module_title = sprintf(__('Rubah TTUJ %s'), ucwords($data_action));
            $this->set('sub_module_title', trim($module_title));
            $this->doTTUJ($data_action, $id, $ttuj);
        }else{
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'ttuj'
            ));
        }
    }

    function saveTtujTipeMotor ( $data_action, $dataTtujTipeMotor = false, $data = false, $ttuj_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($dataTtujTipeMotor) ) {
            if( !empty($ttuj_id) ) {
                $this->Ttuj->TtujTipeMotor->updateAll( array(
                    'TtujTipeMotor.status' => 0,
                ), array(
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                ));
            }

            foreach ($dataTtujTipeMotor as $key => $tipe_motor_id) {
                $dataValidate['TtujTipeMotor']['tipe_motor_id'] = $tipe_motor_id;
                $dataValidate['TtujTipeMotor']['qty'] = !empty($data['TtujTipeMotor']['qty'][$key])?$data['TtujTipeMotor']['qty'][$key]:false;

                if( $data_action == 'retail' ) {
                    $dataValidate['TtujTipeMotor']['city_id'] = !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:false;
                }
                
                $this->Ttuj->TtujTipeMotor->set($dataValidate);

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujTipeMotor']['ttuj_id'] = $ttuj_id;
                    $this->Ttuj->TtujTipeMotor->create();
                    $this->Ttuj->TtujTipeMotor->save($dataValidate);
                } else {
                    if(!$this->Ttuj->TtujTipeMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveTtujPerlengkapan ( $dataTtujPerlengkapan = false, $data = false, $ttuj_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($dataTtujPerlengkapan) ) {
            if( !empty($ttuj_id) ) {
                $this->Ttuj->TtujPerlengkapan->updateAll( array(
                    'TtujPerlengkapan.status' => 0,
                ), array(
                    'TtujPerlengkapan.ttuj_id' => $ttuj_id,
                ));
            }

            foreach ($dataTtujPerlengkapan as $key => $qty) {
                $dataValidate['TtujPerlengkapan']['qty'] = $qty;
                $dataValidate['TtujPerlengkapan']['perlengkapan_id'] = !empty($data['TtujPerlengkapan']['id'][$key])?$data['TtujPerlengkapan']['id'][$key]:false;
                $this->Ttuj->TtujPerlengkapan->set($dataValidate);

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujPerlengkapan']['ttuj_id'] = $ttuj_id;

                    $this->Ttuj->TtujPerlengkapan->create();
                    $this->Ttuj->TtujPerlengkapan->save($dataValidate);
                } else {
                    if(!$this->Ttuj->TtujPerlengkapan->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function doTTUJ($data_action = false, $id = false, $data_local = false){
        $this->loadModel('City');
        $this->loadModel('UangJalan');
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');
        $step = false;
        $is_draft = isset($data_local['Ttuj']['is_draft'])?$data_local['Ttuj']['is_draft']:true;

        if( !empty($this->request->data) && $is_draft ){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Ttuj->id = $id;
                $msg = 'merubah';
            }else{
                $this->Ttuj->create();
                $msg = 'menambah';
            }

            $customer_id = !empty($data['Ttuj']['customer_id'])?$data['Ttuj']['customer_id']:false;
            $from_city_id = !empty($data['Ttuj']['from_city_id'])?$data['Ttuj']['from_city_id']:false;
            $to_city_id = !empty($data['Ttuj']['to_city_id'])?$data['Ttuj']['to_city_id']:false;
            $truck_id = !empty($data['Ttuj']['truck_id'])?$data['Ttuj']['truck_id']:false;

            $uangJalan = $this->UangJalan->getData('first', array(
                'conditions' => array(
                    'UangJalan.status' => 1,
                    // 'UangJalan.customer_id' => $customer_id,
                    'UangJalan.from_city_id' => $from_city_id,
                    'UangJalan.to_city_id' => $to_city_id,
                ),
            ));
            $customer = $this->Ttuj->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.status' => 1,
                    'Customer.id' => $customer_id,
                ),
                'contain' => array(
                    'CustomerType',
                ),
            ), false);
            $truck = $this->Truck->getData('first', array(
                'conditions' => array(
                    'Truck.status' => 1,
                    'Truck.id' => $truck_id,
                ),
                'fields' => array(
                    'Truck.id', 'Truck.nopol',
                    'Truck.capacity'
                ),
            ));

            $data['Ttuj']['from_city_name'] = !empty($uangJalan['FromCity']['name'])?$uangJalan['FromCity']['name']:0;
            $data['Ttuj']['to_city_name'] = !empty($uangJalan['ToCity']['name'])?$uangJalan['ToCity']['name']:0;
            $data['Ttuj']['customer_name'] = !empty($customer['Customer']['customer_name'])?$customer['Customer']['customer_name']:'';
            $data['Ttuj']['uang_jalan_id'] = !empty($uangJalan['UangJalan']['id'])?$uangJalan['UangJalan']['id']:false;
            $data['Ttuj']['nopol'] = !empty($truck['Truck']['nopol'])?$truck['Truck']['nopol']:false;
            $data['Ttuj']['ttuj_date'] = $this->MkCommon->getDate($data['Ttuj']['ttuj_date']);
            $data['Ttuj']['driver_penganti_id'] = !empty($data['Ttuj']['driver_penganti_id'])?$data['Ttuj']['driver_penganti_id']:0;
            $data['Ttuj']['commission'] = !empty($uangJalan['UangJalan']['commission'])?$uangJalan['UangJalan']['commission']:0;
            $data['Ttuj']['commission_per_unit'] = !empty($uangJalan['UangJalan']['commission_per_unit'])?$uangJalan['UangJalan']['commission_per_unit']:0;
            $data['Ttuj']['uang_jalan_1'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_jalan_1']);
            $data['Ttuj']['uang_jalan_2'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_jalan_2'], 0);
            $data['Ttuj']['uang_kuli_muat'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_kuli_muat'], 0);
            $data['Ttuj']['uang_kuli_bongkar'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_kuli_bongkar'], 0);
            $data['Ttuj']['asdp'] = $this->MkCommon->convertPriceToString($data['Ttuj']['asdp'], 0);
            $data['Ttuj']['uang_kawal'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_kawal'], 0);
            $data['Ttuj']['uang_keamanan'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_keamanan'], 0);
            $data['Ttuj']['uang_jalan_extra'] = $this->MkCommon->convertPriceToString($data['Ttuj']['uang_jalan_extra'], 0);
            $data['Ttuj']['min_capacity'] = $this->MkCommon->convertPriceToString($data['Ttuj']['min_capacity'], 0);
            $data['Ttuj']['arrive_lead_time'] = !empty($uangJalan['UangJalan']['arrive_lead_time'])?$uangJalan['UangJalan']['arrive_lead_time']:0;
            $data['Ttuj']['back_lead_time'] = !empty($uangJalan['UangJalan']['back_lead_time'])?$uangJalan['UangJalan']['back_lead_time']:0;
            $data['Ttuj']['tgljam_berangkat'] = '';

            if( !empty($data['Ttuj']['tgl_berangkat']) ) {
                $data['Ttuj']['tgl_berangkat'] = $this->MkCommon->getDate($data['Ttuj']['tgl_berangkat']);

                if( !empty($data['Ttuj']['jam_berangkat']) ) {
                    $data['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data['Ttuj']['jam_berangkat']));
                    $data['Ttuj']['tgljam_berangkat'] = sprintf('%s %s', $data['Ttuj']['tgl_berangkat'], $data['Ttuj']['jam_berangkat']);
                }
            }

            if( $data_action == 'retail' ) {
                $data['Ttuj']['is_retail'] = 1;
            }

            $this->Ttuj->set($data);

            if($this->Ttuj->validates($data)){
                if( !empty($data['TtujTipeMotor']['tipe_motor_id']) ) {
                    $dataTtujTipeMotor = array_filter($data['TtujTipeMotor']['tipe_motor_id']);

                    if( !empty($data['TtujPerlengkapan']['qty']) ) {
                        $dataTtujPerlengkapan = array_filter($data['TtujPerlengkapan']['qty']);
                    }

                    if( !empty($dataTtujTipeMotor) ) {
                        $result_data = array();
                        $validates = true;
                        $result_data_perlengkapan = array();
                        $validates_perlengkapan = true;

                        $resultTtujTipeMotor = $this->saveTtujTipeMotor($data_action, $dataTtujTipeMotor, $data);

                        if( !empty($dataTtujPerlengkapan) ) {
                            $resultTtujPerlengkapan = $this->saveTtujPerlengkapan($dataTtujPerlengkapan, $data);
                        }

                        if( !empty($resultTtujTipeMotor) ) {
                            $result_data = $resultTtujTipeMotor['data'];
                            $validates = $resultTtujTipeMotor['validates'];
                        }
                        if( !empty($resultTtujPerlengkapan) ) {
                            $result_data_perlengkapan = $resultTtujPerlengkapan['data'];
                            $validates_perlengkapan = $resultTtujPerlengkapan['validates'];
                        }
                        
                        if( !empty($validates) && !empty($validates_perlengkapan) ) {
                            if($this->Ttuj->save($data)){
                                $this->saveTtujTipeMotor($data_action, $dataTtujTipeMotor, $data, $this->Ttuj->id);

                                if( !empty($dataTtujPerlengkapan) ) {
                                    $this->saveTtujPerlengkapan($dataTtujPerlengkapan, $data, $this->Ttuj->id);
                                }

                                $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s TTUJ'), $msg), 'success');
                                $this->Log->logActivity( sprintf(__('Sukses %s TTUJ'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );  

                                $this->redirect(array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj'
                                ));
                            }else{
                                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error'); 
                                $this->Log->logActivity( sprintf(__('Gagal %s TTUJ'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                            }
                        } else {
                            $step = '#step2';
                            $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Mohon lengkapi muatan truk.'), $msg), 'error');  
                        }
                    } else {
                        $step = '#step2';
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Silahkan masukan muatan truk.'), $msg), 'error');  
                    }
                } else {
                    $step = '#step2';
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Silahkan masukan muatan truk.'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error');
                $step = '#step1';
            }

            // if( !empty($data['Ttuj']['customer_id']) ) {
            //     $fromCities = $this->UangJalan->getKotaAsal($data['Ttuj']['customer_id']);
                $fromCities = $this->UangJalan->getKotaAsal();

                if( !empty($data['Ttuj']['from_city_id']) ) {
                    // $toCities = $this->UangJalan->getKotaTujuan($data['Ttuj']['customer_id'], $data['Ttuj']['from_city_id']);
                    $toCities = $this->UangJalan->getKotaTujuan($data['Ttuj']['from_city_id']);

                    if( !empty($data['Ttuj']['to_city_id']) ) {
                        if( !empty($truck['Truck']['capacity']) ) {
                            $dataTruck = $this->UangJalan->getNopol($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $truck['Truck']['capacity']);

                            if( !empty($dataTruck) ) {
                                $uangJalan = $dataTruck;
                                $this->request->data['Ttuj']['uang_jalan_1_ori'] = $uang_jalan_1 = !empty($uangJalan['UangJalan']['uang_jalan_1'])?$uangJalan['UangJalan']['uang_jalan_1']:0;
                                $uang_jalan_2 = !empty($uangJalan['UangJalan']['uang_jalan_2'])?$uangJalan['UangJalan']['uang_jalan_2']:0;
                                $this->request->data['Ttuj']['uang_kuli_muat_ori'] = $uang_kuli_muat = !empty($uangJalan['UangJalan']['uang_kuli_muat'])?$uangJalan['UangJalan']['uang_kuli_muat']:0;
                                $this->request->data['Ttuj']['uang_kuli_bongkar_ori'] = $uang_kuli_bongkar = !empty($uangJalan['UangJalan']['uang_kuli_bongkar'])?$uangJalan['UangJalan']['uang_kuli_bongkar']:0;
                                $this->request->data['Ttuj']['asdp_ori'] = $asdp = !empty($uangJalan['UangJalan']['asdp'])?$uangJalan['UangJalan']['asdp']:0;
                                $this->request->data['Ttuj']['uang_kawal_ori'] = $uang_kawal = !empty($uangJalan['UangJalan']['uang_kawal'])?$uangJalan['UangJalan']['uang_kawal']:0;
                                $this->request->data['Ttuj']['uang_keamanan_ori'] = $uang_keamanan = !empty($uangJalan['UangJalan']['uang_keamanan'])?$uangJalan['UangJalan']['uang_keamanan']:0;
                                $this->request->data['Ttuj']['uang_jalan_extra_ori'] = $uang_jalan_extra = !empty($uangJalan['UangJalan']['uang_jalan_extra'])?$uangJalan['UangJalan']['uang_jalan_extra']:0;
                                $uang_jalan_tipe_motor = 0;
                                $uang_kuli_bongkar_tipe_motor = 0;
                                $uang_kuli_muat_tipe_motor = 0;
                                $totalMuatan = 0;
                                $uangJalanTipeMotor = array();

                                if( !empty($uangJalan['UangJalanTipeMotor']) ) {
                                    foreach ($uangJalan['UangJalanTipeMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['UangJalan'][$tipeMotor['tipe_motor_id']] = $tipeMotor['uang_jalan_1'];
                                        $uangJalanTipeMotor['UangKuliMuat'][$tipeMotor['tipe_motor_id']] = $tipeMotor['uang_kuli_muat'];
                                        $uangJalanTipeMotor['UangKuliBongkar'][$tipeMotor['tipe_motor_id']] = $tipeMotor['uang_kuli_bongkar'];
                                    }
                                }

                                if( !empty($data['TtujTipeMotor']['qty']) ) {
                                    foreach ($data['TtujTipeMotor']['qty'] as $key => $qty) {
                                        if( !empty($qty) ) {
                                            $tipe_motor_id = !empty($data['TtujTipeMotor']['tipe_motor_id'][$key])?$data['TtujTipeMotor']['tipe_motor_id'][$key]:false;
                                            $totalMuatan += $qty;

                                            if( !empty($uangJalanTipeMotor['UangJalan'][$tipe_motor_id]) ) {
                                                $uang_jalan_tipe_motor += $uangJalanTipeMotor['UangJalan'][$tipe_motor_id] * $qty;
                                            } else {
                                                $uang_jalan_tipe_motor += $uang_jalan_1 * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['UangKuliMuat'][$tipe_motor_id]) ) {
                                                $uang_kuli_muat_tipe_motor += $uangJalanTipeMotor['UangKuliMuat'][$tipe_motor_id] * $qty;
                                            } else {
                                                $uang_kuli_muat_tipe_motor += $uang_kuli_muat * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['UangKuliBongkar'][$tipe_motor_id]) ) {
                                                $uang_kuli_bongkar_tipe_motor += $uangJalanTipeMotor['UangKuliBongkar'][$tipe_motor_id] * $qty;
                                            } else {
                                                $uang_kuli_bongkar_tipe_motor += $uang_kuli_bongkar * $qty;
                                            }
                                        }
                                    }
                                }

                                if( empty($totalMuatan) ) {
                                    $totalMuatan = 1;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_jalan_per_unit']) ) {
                                    $uang_jalan_1 = $uang_jalan_tipe_motor;
                                    $uang_jalan_2 = 0;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_kuli_muat_per_unit']) ) {
                                    $uang_kuli_muat = $uang_kuli_muat_tipe_motor;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_kuli_bongkar_per_unit']) ) {
                                    $uang_kuli_bongkar = $uang_kuli_bongkar_tipe_motor;
                                }

                                if( !empty($uangJalan['UangJalan']['asdp_per_unit']) ) {
                                    $asdp = $asdp*$totalMuatan;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_kawal_per_unit']) ) {
                                    $uang_kawal = $uang_kawal*$totalMuatan;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_keamanan_per_unit']) ) {
                                    $uang_keamanan = $uang_keamanan*$totalMuatan;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_jalan_extra']) && !empty($uangJalan['UangJalan']['min_capacity']) ) {
                                    if( $totalMuatan > $uangJalan['UangJalan']['min_capacity'] ) {
                                        if( !empty($uangJalan['UangJalan']['uang_jalan_extra_per_unit']) ) {
                                            $capacityCost = $totalMuatan - $uangJalan['UangJalan']['min_capacity'];
                                            $uang_jalan_extra = $uang_jalan_extra*$capacityCost;
                                        }
                                    }
                                }

                                $this->request->data['Ttuj']['uang_jalan_1'] = number_format($uang_jalan_1, 0);
                                $this->request->data['Ttuj']['uang_kuli_muat'] = number_format($uang_kuli_muat, 0);
                                $this->request->data['Ttuj']['uang_kuli_bongkar'] = number_format($uang_kuli_bongkar, 0);
                                $this->request->data['Ttuj']['asdp'] = number_format($asdp, 0);
                                $this->request->data['Ttuj']['uang_kawal'] = number_format($uang_kawal, 0);
                                $this->request->data['Ttuj']['uang_keamanan'] = number_format($uang_keamanan, 0);
                                $this->request->data['Ttuj']['uang_jalan_extra'] = number_format($uang_jalan_extra, 0);

                                $this->request->data['Ttuj']['uang_jalan_per_unit'] = !empty($uangJalan['UangJalan']['uang_jalan_per_unit'])?$uangJalan['UangJalan']['uang_jalan_per_unit']:0;
                                $this->request->data['Ttuj']['uang_kuli_muat_per_unit'] = !empty($uangJalan['UangJalan']['uang_kuli_muat_per_unit'])?$uangJalan['UangJalan']['uang_kuli_muat_per_unit']:0;
                                $this->request->data['Ttuj']['uang_kuli_bongkar_per_unit'] = !empty($uangJalan['UangJalan']['uang_kuli_bongkar_per_unit'])?$uangJalan['UangJalan']['uang_kuli_bongkar_per_unit']:0;
                                $this->request->data['Ttuj']['asdp_per_unit'] = !empty($uangJalan['UangJalan']['asdp_per_unit'])?$uangJalan['UangJalan']['asdp_per_unit']:0;
                                $this->request->data['Ttuj']['uang_kawal_per_unit'] = !empty($uangJalan['UangJalan']['uang_kawal_per_unit'])?$uangJalan['UangJalan']['uang_kawal_per_unit']:0;
                                $this->request->data['Ttuj']['uang_keamanan_per_unit'] = !empty($uangJalan['UangJalan']['uang_keamanan_per_unit'])?$uangJalan['UangJalan']['uang_keamanan_per_unit']:0;
                                $this->request->data['Ttuj']['uang_jalan_extra_per_unit'] = !empty($uangJalan['UangJalan']['uang_jalan_extra_per_unit'])?$uangJalan['UangJalan']['uang_jalan_extra_per_unit']:0;

                                if( !empty($data['Ttuj']['truck_id']) ) {
                                    $truckInfo = $this->Truck->getInfoTruck($data['Ttuj']['truck_id']);
                                    $this->request->data['Ttuj']['driver_name'] = !empty($truckInfo['Driver']['name'])?$truckInfo['Driver']['name']:false;
                                    $this->request->data['Ttuj']['truck_capacity'] = !empty($truckInfo['Truck']['capacity'])?$truckInfo['Truck']['capacity']:false;
                                    $this->request->data['Ttuj']['truck_capacity'] = !empty($truckInfo['Truck']['capacity'])?$truckInfo['Truck']['capacity']:false;
                                }
                            }
                        }
                    }
                }
            // }

            $this->request->data['Ttuj']['ttuj_date'] = !empty($data['Ttuj']['ttuj_date'])?date('d/m/Y', strtotime($data['Ttuj']['ttuj_date'])):false;

            if( !empty($data['TtujPerlengkapan']['qty']) ) {
                $tempPerlengkapan = array();

                foreach ($data['TtujPerlengkapan']['qty'] as $key => $qty) {
                    if( !empty($qty) ) {
                        $tempPerlengkapan['TtujPerlengkapan'][$data['TtujPerlengkapan']['id'][$key]] = $qty;
                    }
                }

                if( !empty($tempPerlengkapan['TtujPerlengkapan']) ) {
                    $this->request->data['TtujPerlengkapan'] = $tempPerlengkapan['TtujPerlengkapan'];
                }
            }
        }else{
            if($id && $data_local){
                $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
                $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

                if( !empty($data_local['Ttuj']['tgljam_berangkat']) ) {
                    $data_local['Ttuj']['tgl_berangkat'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                    $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                }
                $this->request->data = $data_local;

                if( !empty($data_local['UangJalan']) ) {
                    $this->request->data['Ttuj']['uang_jalan_1_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_jalan_1'], 0);
                    $this->request->data['Ttuj']['uang_kuli_muat_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_kuli_muat'], 0);
                    $this->request->data['Ttuj']['uang_kuli_bongkar_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_kuli_bongkar'], 0);
                    $this->request->data['Ttuj']['asdp_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['asdp'], 0);
                    $this->request->data['Ttuj']['uang_kawal_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_kawal'], 0);
                    $this->request->data['Ttuj']['uang_keamanan_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_keamanan'], 0);
                    $this->request->data['Ttuj']['uang_jalan_extra_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_jalan_extra'], 0);
                }

                if( !empty($this->request->data['Ttuj']['ttuj_date']) && $this->request->data['Ttuj']['ttuj_date'] != '0000-00-00' ) {
                    $this->request->data['Ttuj']['ttuj_date'] = date('d/m/Y', strtotime($this->request->data['Ttuj']['ttuj_date']));
                } else {
                    $this->request->data['Ttuj']['ttuj_date'] = '';
                }
            }

            // if( !empty($this->request->data['Ttuj']['customer_id']) ) {
                // $fromCities = $this->UangJalan->getKotaAsal($this->request->data['Ttuj']['customer_id']);
                $fromCities = $this->UangJalan->getKotaAsal();

                if( !empty($this->request->data['Ttuj']['from_city_id']) ) {
                    // $toCities = $this->UangJalan->getKotaTujuan($this->request->data['Ttuj']['customer_id'], $this->request->data['Ttuj']['from_city_id']);
                    $toCities = $this->UangJalan->getKotaTujuan($this->request->data['Ttuj']['from_city_id']);
                }
            // }
        }

        $trucks = $this->Truck->getData('list', array(
            'conditions' => array(
                'Truck.driver_id <>' => 0,
                'Truck.status' => 1,
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
        ));
        $customers = $this->Ttuj->Customer->getData('list', array(
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name'
            )
        ));
        $driverPengantis = $this->Ttuj->Truck->Driver->getData('list', array(
            'conditions' => array(
                'Driver.status' => 1,
                'Truck.id <>' => NULL,
            ),
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
            'contain' => array(
                'Truck'
            )
        ));
        $perlengkapans = $this->Perlengkapan->getData('list', array(
            'fields' => array(
                'Perlengkapan.id', 'Perlengkapan.name',
            ),
            'conditions' => array(
                'Perlengkapan.status' => 1,
                'Perlengkapan.jenis_perlengkapan_id' => 2,
            ),
        ));
        $tipeMotorsTmp = $this->TipeMotor->getData('all', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.tipe_motor_color',
            ),
        ));
        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_asal' => 1,
            ),
        ));
        $tipeMotors = array();

        if( !empty($tipeMotorsTmp) ) {
            foreach ($tipeMotorsTmp as $key => $tipeMotor) {
                $tipeMotors[$tipeMotor['TipeMotor']['id']] = $tipeMotor['TipeMotor']['tipe_motor_color'];
            }
        }

        $this->set('active_menu', 'ttuj');
        $this->set(compact(
            'trucks', 'customers', 'driverPengantis',
            'fromCities', 'toCities', 'uangJalan',
            'tipeMotors', 'perlengkapans', 'step',
            'truckInfo', 'data_local', 'data_action',
            'cities'
        ));
        $this->render('ttuj_form');
    }

    function ttuj_toggle( $id, $action_type = 'status' ){
        $this->loadModel('Ttuj');
        $locale = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Ttuj']['status']){
                $value = false;
            }

            $this->Ttuj->id = $id;

            switch ($action_type) {
                case 'truk_tiba':
                    $this->Ttuj->set('is_arrive', 0);
                    break;

                case 'bongkaran':
                    $this->Ttuj->set('is_bongkaran', 0);
                    break;

                case 'balik':
                    $this->Ttuj->set('is_balik', 0);
                    break;
                
                default:
                    $this->Ttuj->set('status', 0);
                    break;
            }

            if($this->Ttuj->save()){
                $this->MkCommon->setCustomFlash(__('TTUJ berhasil dibatalkan.'), 'success');
                $this->Log->logActivity( sprintf(__('TTUJ ID #%s berhasil dibatalkan.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal membatalkan TTUJ.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal membatalkan TTUJ ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function truk_tiba() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'truk_tiba');
        $this->set('sub_module_title', __('Truk Tiba'));
        $conditions = array(
            'Ttuj.is_arrive' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nottuj'])){
                $nottuj = urldecode($refine['nottuj']);
                $this->request->data['Ttuj']['nottuj'] = $nottuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
            }
            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['Ttuj.customer_name LIKE '] = '%'.$customer.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function truk_tiba_add() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'truk_tiba');
        $this->doTTUJLanjutan();
    }

    function doTTUJLanjutan( $action_type = 'truk_tiba' ){
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');
        $module_title = __('Truk Tiba');
        $data_action = false;

        if( !empty($this->params['named']['no_ttuj']) ) {
            $conditionsDataLocal = array(
                'Ttuj.id' => $this->params['named']['no_ttuj'],
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
            );

            switch ($action_type) {
                case 'bongkaran':
                    $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                    $conditionsDataLocal['Ttuj.is_bongkaran <>'] = 1;
                    break;

                case 'balik':
                    $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                    $conditionsDataLocal['Ttuj.is_bongkaran'] = 1;
                    $conditionsDataLocal['Ttuj.is_balik <>'] = 1;
                    break;

                case 'pool':
                    $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                    $conditionsDataLocal['Ttuj.is_bongkaran'] = 1;
                    $conditionsDataLocal['Ttuj.is_balik'] = 1;
                    $conditionsDataLocal['Ttuj.is_pool <>'] = 1;
                    break;
                
                default:
                    $conditionsDataLocal['Ttuj.is_arrive'] = 0;
                    break;
            }
            $data_local = $this->Ttuj->getData('first', array(
                'conditions' => $conditionsDataLocal
            ));

            if( !empty($data_local['Ttuj']['is_retail']) ) {
                $module_title = __('Truk Tiba - RETAIL');
                $data_action = 'retail';
            }
        }

        $this->set('sub_module_title', trim($module_title));

        if( !empty($this->request->data) && !empty($data_local) ){
            $data = $this->request->data;

            $this->Ttuj->id = $data_local['Ttuj']['id'];

            switch ($action_type) {
                case 'bongkaran':
                    $dataTiba['Ttuj']['is_bongkaran'] = 1;
                    $dataTiba['Ttuj']['tgljam_bongkaran'] = '';
                    $dataTiba['Ttuj']['note_bongkaran'] = !empty($data['Ttuj']['note_bongkaran'])?$data['Ttuj']['note_bongkaran']:'';

                    if( !empty($data['Ttuj']['tgl_bongkaran']) ) {
                        $data['Ttuj']['tgl_bongkaran'] = $this->MkCommon->getDate($data['Ttuj']['tgl_bongkaran']);

                        if( !empty($data['Ttuj']['jam_bongkaran']) ) {
                            $data['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data['Ttuj']['jam_bongkaran']));
                            $dataTiba['Ttuj']['tgljam_bongkaran'] = sprintf('%s %s', $data['Ttuj']['tgl_bongkaran'], $data['Ttuj']['jam_bongkaran']);
                        }
                    }
                    $referer = 'bongkaran';
                    break;

                case 'balik':
                    $dataTiba['Ttuj']['is_balik'] = 1;
                    $dataTiba['Ttuj']['tgljam_balik'] = '';
                    $dataTiba['Ttuj']['note_balik'] = !empty($data['Ttuj']['note_balik'])?$data['Ttuj']['note_balik']:'';

                    if( !empty($data['Ttuj']['tgl_balik']) ) {
                        $data['Ttuj']['tgl_balik'] = $this->MkCommon->getDate($data['Ttuj']['tgl_balik']);

                        if( !empty($data['Ttuj']['jam_balik']) ) {
                            $data['Ttuj']['jam_balik'] = date('H:i', strtotime($data['Ttuj']['jam_balik']));
                            $dataTiba['Ttuj']['tgljam_balik'] = sprintf('%s %s', $data['Ttuj']['tgl_balik'], $data['Ttuj']['jam_balik']);
                        }
                    }
                    $referer = 'balik';
                    break;

                case 'pool':
                    $dataTiba['Ttuj']['is_pool'] = 1;
                    $dataTiba['Ttuj']['tgljam_pool'] = '';
                    $dataTiba['Ttuj']['note_pool'] = !empty($data['Ttuj']['note_pool'])?$data['Ttuj']['note_pool']:'';

                    if( !empty($data['Ttuj']['tgl_pool']) ) {
                        $data['Ttuj']['tgl_pool'] = $this->MkCommon->getDate($data['Ttuj']['tgl_pool']);

                        if( !empty($data['Ttuj']['jam_pool']) ) {
                            $data['Ttuj']['jam_pool'] = date('H:i', strtotime($data['Ttuj']['jam_pool']));
                            $dataTiba['Ttuj']['tgljam_pool'] = sprintf('%s %s', $data['Ttuj']['tgl_pool'], $data['Ttuj']['jam_pool']);
                        }
                    }
                    $referer = 'pool';
                    $fromTime = 'tgljam_berangkat';
                    $toTime = 'tgljam_pool';
                    $leadTime = 'back_lead_time';
                    $overTime = 'back_orver_time';
                    break;
                
                default:
                    $dataTiba['Ttuj']['is_arrive'] = 1;
                    $dataTiba['Ttuj']['tgljam_tiba'] = '';
                    $dataTiba['Ttuj']['note_tiba'] = !empty($data['Ttuj']['note_tiba'])?$data['Ttuj']['note_tiba']:'';

                    if( !empty($data['Ttuj']['tgl_tiba']) ) {
                        $data['Ttuj']['tgl_tiba'] = $this->MkCommon->getDate($data['Ttuj']['tgl_tiba']);

                        if( !empty($data['Ttuj']['jam_tiba']) ) {
                            $data['Ttuj']['jam_tiba'] = date('H:i', strtotime($data['Ttuj']['jam_tiba']));
                            $dataTiba['Ttuj']['tgljam_tiba'] = sprintf('%s %s', $data['Ttuj']['tgl_tiba'], $data['Ttuj']['jam_tiba']);
                        }
                    }
                    $referer = 'truk_tiba';
                    $fromTime = 'tgljam_berangkat';
                    $toTime = 'tgljam_tiba';
                    $leadTime = 'arrive_lead_time';
                    $overTime = 'arrive_over_time';
                    break;
            }

            if( !empty($fromTime) ) {
                $from_time = strtotime($data_local['Ttuj'][$fromTime]);
                $to_time = strtotime($dataTiba['Ttuj'][$toTime]);
                $diff = round(abs($to_time - $from_time) / 60, 2);
                $diff = round($diff/60, 2);

                if( $diff > $data_local['Ttuj'][$leadTime] ) {
                    $dataTiba['Ttuj'][$overTime] = $diff;
                }
            }

            $this->Ttuj->set($dataTiba);

            if($this->Ttuj->validates($dataTiba)){
                if($this->Ttuj->save($dataTiba)){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah TTUJ'), 'success');

                    $this->Log->logActivity( sprintf(__('Sukses merubah TTUJ ID #%s.'), $this->Ttuj->id), $this->user_data, $this->RequestHandler, $this->params, 1 );   

                    $this->redirect(array(
                        'controller' => 'revenues',
                        'action' => $referer
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah Ttuj'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah TTUJ.')), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                }
            }
        }

        if( !empty($data_local) ){
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

            if( !empty($data_local['Ttuj']['tgljam_berangkat']) && $data_local['Ttuj']['tgljam_berangkat'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_berangkat'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
            }
            if( !empty($data_local['Ttuj']['tgljam_tiba']) && $data_local['Ttuj']['tgljam_tiba'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_tiba'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_tiba']));
                $data_local['Ttuj']['jam_tiba'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_tiba']));
            }
            if( !empty($data_local['Ttuj']['tgljam_bongkaran']) && $data_local['Ttuj']['tgljam_bongkaran'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_bongkaran'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_bongkaran']));
                $data_local['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_bongkaran']));
            }
            if( !empty($data_local['Ttuj']['tgljam_balik']) && $data_local['Ttuj']['tgljam_balik'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_balik'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_balik']));
                $data_local['Ttuj']['jam_balik'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_balik']));
            }
            $this->request->data = $data_local;
        }

        if( !empty($this->params['named']['no_ttuj']) ) {
            $this->request->data['Ttuj']['no_ttuj'] = $this->params['named']['no_ttuj'];
        }

        $conditionsTtuj = array(
            'Ttuj.status' => 1,
            'Ttuj.is_draft' => 0,
        );

        switch ($action_type) {
            case 'bongkaran':
                $conditionsTtuj['Ttuj.is_arrive'] = 1;
                $conditionsTtuj['Ttuj.is_bongkaran <>'] = 1;
                break;

            case 'balik':
                $conditionsTtuj['Ttuj.is_arrive'] = 1;
                $conditionsTtuj['Ttuj.is_bongkaran'] = 1;
                $conditionsTtuj['Ttuj.is_balik <>'] = 1;
                break;

            case 'pool':
                $conditionsTtuj['Ttuj.is_arrive'] = 1;
                $conditionsTtuj['Ttuj.is_bongkaran'] = 1;
                $conditionsTtuj['Ttuj.is_balik'] = 1;
                $conditionsTtuj['Ttuj.is_pool <>'] = 1;
                break;
            
            default:
                $conditionsTtuj['Ttuj.is_arrive'] = 0;
                break;
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'conditions' => $conditionsTtuj,
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
        ));
        $perlengkapans = $this->Perlengkapan->getData('list', array(
            'fields' => array(
                'Perlengkapan.id', 'Perlengkapan.name',
            ),
            'conditions' => array(
                'Perlengkapan.status' => 1,
                'Perlengkapan.jenis_perlengkapan_id' => 2,
            ),
        ));
        $tipeMotorsTmp = $this->TipeMotor->getData('all', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.tipe_motor_color',
            ),
        ));
        $tipeMotors = array();

        if( !empty($tipeMotorsTmp) ) {
            foreach ($tipeMotorsTmp as $key => $tipeMotor) {
                $tipeMotors[$tipeMotor['TipeMotor']['id']] = $tipeMotor['TipeMotor']['tipe_motor_color'];
            }
        }

        $this->set(compact(
            'ttujs', 'data_local', 'perlengkapans', 
            'tipeMotors', 'action_type', 'data_action'
        ));
        $this->render('ttuj_lanjutan_form');
    }

    public function info_truk( $action_type = 'truk_tiba', $ttuj_id = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $conditions = array(
            'Ttuj.id' => $ttuj_id,
            'Ttuj.is_draft' => 0,
            'Ttuj.status' => 1,
        );

        switch ($action_type) {
            case 'bongkaran':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                break;

            case 'balik':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                break;

            case 'balik':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                $conditions['Ttuj.is_pool'] = 1;
                break;
            
            default:
                $conditions['Ttuj.is_arrive'] = 1;
                break;
        }

        $data_action = false;
        $module_title = __('Info Truk Tiba');
        $data_local = $this->Ttuj->getData('first', array(
            'conditions' => $conditions
        ));

        if( !empty($data_local) ){
            if( !empty($data_local['Ttuj']['is_retail']) ) {
                $module_title = __('Info Truk Tiba - RETAIL');
                $data_action = 'retail';
            }

            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

            if( !empty($data_local['Ttuj']['tgljam_berangkat']) && $data_local['Ttuj']['tgljam_berangkat'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_berangkat'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
            }
            if( !empty($data_local['Ttuj']['tgljam_tiba']) && $data_local['Ttuj']['tgljam_tiba'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_tiba'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_tiba']));
                $data_local['Ttuj']['jam_tiba'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_tiba']));
            }
            if( !empty($data_local['Ttuj']['tgljam_bongkaran']) && $data_local['Ttuj']['tgljam_bongkaran'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_bongkaran'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_bongkaran']));
                $data_local['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_bongkaran']));
            }
            if( !empty($data_local['Ttuj']['tgljam_balik']) && $data_local['Ttuj']['tgljam_balik'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_balik'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_balik']));
                $data_local['Ttuj']['jam_balik'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_balik']));
            }
            if( !empty($data_local['Ttuj']['tgljam_pool']) && $data_local['Ttuj']['tgljam_pool'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_pool'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_pool']));
                $data_local['Ttuj']['jam_pool'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_pool']));
            }
            $this->request->data = $data_local;
            $perlengkapans = $this->Perlengkapan->getData('list', array(
                'fields' => array(
                    'Perlengkapan.id', 'Perlengkapan.name',
                ),
                'conditions' => array(
                    'Perlengkapan.status' => 1,
                    'Perlengkapan.jenis_perlengkapan_id' => 2,
                ),
            ));
            $tipeMotorsTmp = $this->TipeMotor->getData('all', array(
                'fields' => array(
                    'TipeMotor.id', 'TipeMotor.tipe_motor_color',
                ),
            ));
            $tipeMotors = array();

            if( !empty($tipeMotorsTmp) ) {
                foreach ($tipeMotorsTmp as $key => $tipeMotor) {
                    $tipeMotors[$tipeMotor['TipeMotor']['id']] = $tipeMotor['TipeMotor']['tipe_motor_color'];
                }
            }

            $this->set('sub_module_title', $module_title);
            $this->set('active_menu', 'truk_tiba');
            $this->set(compact(
                'ttujs', 'data_local', 'perlengkapans', 
                'tipeMotors', 'ttuj_id', 'action_type',
                'data_action'
            ));
            $this->render('ttuj_lanjutan_form');
        } else {
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'truk_tiba'
            ));
        }
    }

    public function bongkaran() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'bongkaran');
        $this->set('sub_module_title', __('Bongkaran'));
        $conditions = array(
            'Ttuj.is_arrive' => 1,
            'Ttuj.is_bongkaran' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nottuj'])){
                $nottuj = urldecode($refine['nottuj']);
                $this->request->data['Ttuj']['nottuj'] = $nottuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
            }
            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['Ttuj.customer_name LIKE '] = '%'.$customer.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function bongkaran_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('Tambah Tiba'));
        $this->set('active_menu', 'bongkaran');
        $this->doTTUJLanjutan( 'bongkaran' );
    }

    public function balik() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'balik');
        $this->set('sub_module_title', __('Balik'));
        $conditions = array(
            'Ttuj.is_balik' => 1,
            'Ttuj.is_bongkaran' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nottuj'])){
                $nottuj = urldecode($refine['nottuj']);
                $this->request->data['Ttuj']['nottuj'] = $nottuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
            }
            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['Ttuj.customer_name LIKE '] = '%'.$customer.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function balik_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('Tambah TTUJ Balik'));
        $this->set('active_menu', 'balik');
        $this->doTTUJLanjutan( 'balik' );
    }

    public function pool() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'pool');
        $this->set('sub_module_title', __('Sampai di Pool'));
        $conditions = array(
            'Ttuj.is_balik' => 1,
            'Ttuj.is_bongkaran' => 1,
            'Ttuj.is_balik' => 1,
            'Ttuj.is_pool' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nottuj'])){
                $nottuj = urldecode($refine['nottuj']);
                $this->request->data['Ttuj']['nottuj'] = $nottuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
            }
            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['Ttuj.customer_name LIKE '] = '%'.$customer.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function pool_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('TTUJ Sampai Pool'));
        $this->set('active_menu', 'pool');
        $this->doTTUJLanjutan( 'pool' );
    }

    public function ritase_report( $data_type = 'depo', $data_action = false ) {
        $this->loadModel('Truck');
        $this->loadModel('TruckCustomer');
        $this->loadModel('Ttuj');
        $this->loadModel('UangJalan');
        $this->loadModel('Customer');
        $dateFrom = date('Y-m-d', strtotime('-1 month'));
        $dateTo = date('Y-m-d');
        $conditions = array(
            'Truck.status'=> 1,
            'TruckCustomer.primary'=> 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }

            if(!empty($refine['driver_name'])){
                $driver_name = urldecode($refine['driver_name']);
                $this->request->data['Ttuj']['driver_name'] = $driver_name;
                $conditions['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$driver_name.'%';
            }

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $dateFrom = date('Y-m-d', strtotime(urldecode($date[0])));
                    $dateTo = date('Y-m-d', strtotime(urldecode($date[1])));
                }
                $this->request->data['Ttuj']['date'] = $dateStr;
            }
        }

        $this->Truck->bindModel(array(
            'hasOne' => array(
                'TruckCustomer' => array(
                    'className' => 'TruckCustomer',
                    'foreignKey' => 'truck_id',
                )
            )
        ));

        $conditionCustomers = array(
            'Customer.status' => 1,
        );

        if( $data_type == 'retail' ) {
            $conditionCustomers['Customer.customer_type_id'] = 1;
        } else {
            $conditionCustomers['Customer.customer_type_id'] = 2;
        }

        $customer_id = $this->Customer->getData('list', array(
            'conditions' => $conditionCustomers,
            'fields' => array(
                'Customer.id', 'Customer.id'
            ),
        ));
        $conditions['TruckCustomer.customer_id'] = $customer_id;

        if( !empty($data_action) ) {
            $trucks = $this->TruckCustomer->getData('all', array(
                'conditions' => $conditions,
                'order' => array(
                    'Truck.nopol' => 'ASC', 
                ),
                'contain' => array(
                    'Truck',
                ),
            ));
        } else {
            $this->paginate = $this->TruckCustomer->getData('paginate', array(
                'conditions' => $conditions,
                'order' => array(
                    'Truck.nopol' => 'ASC', 
                ),
                'contain' => array(
                    'Truck',
                ),
                'limit' => 20,
            ));
            $trucks = $this->paginate('TruckCustomer');
        }

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $truck) {
                $conditionCustomers = array(
                    'TruckCustomer.truck_id'=> $truck['Truck']['id'],
                );

                $truckCustomer = $this->TruckCustomer->getData('first', array(
                    'conditions' => $conditionCustomers,
                    'order' => array(
                        'TruckCustomer.id' => 'ASC',
                    ),
                ));
                $truck = $this->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);

                if( !empty($truckCustomer) ) {
                    $truckCustomer = $this->TruckCustomer->Customer->getMerge($truckCustomer, $truckCustomer['TruckCustomer']['customer_id']);
                }

                $conditionsTtuj = array(
                    'Ttuj.is_pool'=> 1,
                    'Ttuj.status'=> 1,
                    'Ttuj.truck_id'=> $truck['Truck']['id'],
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='=> $dateFrom,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <=' => $dateTo,
                );

                if( $data_type == 'retail' ) {
                    $conditionsTtuj['Ttuj.is_retail'] = 1;
                } else {
                    $conditionsTtuj['Ttuj.is_retail'] = 0;
                }

                $total = $this->Ttuj->getData('count', array(
                    'conditions' => $conditionsTtuj
                ));
                $truck['Total'] = $total;

                $overTimeOptions = $conditionsTtuj;
                $overTimeOptions['Ttuj.arrive_over_time <>'] = 0;
                $overTime = $this->Ttuj->getData('count', array(
                    'conditions' => $overTimeOptions
                ));
                $truck['OverTime'] = $overTime;

                if( $data_type != 'retail' ) {
                    $cities = $this->Ttuj->getData('all', array(
                        'conditions' => $conditionsTtuj,
                        'group' => array(
                            'Ttuj.to_city_id'
                        ),
                        'fields'=> array(
                            'Ttuj.to_city_id', 
                            'COUNT(Ttuj.id) as cnt',
                            'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') as dt',
                        ),
                    ), false);
                    $truck['City'] = $cities;
                }

                $truck = array_merge($truck, $truckCustomer);
                $trucks[$key] = $truck;
            }
        }

        $module_title = __('Laporan Ritase Truk');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Ttuj']['date'] = sprintf('%s - %s', date('m/d/Y', strtotime($dateFrom)), date('m/d/Y', strtotime($dateTo)));
            $module_title .= sprintf(' Periode %s - %s', date('d M Y', strtotime($dateFrom)), date('d M Y', strtotime($dateTo)));
        }

        $this->set('sub_module_title', $module_title);

        if( $data_type == 'retail' ) {
            $this->set('active_menu', 'ritase_report_retail');
        } else {
            $this->set('active_menu', 'ritase_report');
            $cities = $this->UangJalan->getData('list', array(
                'conditions' => array(
                    'UangJalan.status' => 1,
                    // 'City.is_tujuan' => 1,
                ),
                'fields' => array(
                    'ToCity.id', 'ToCity.name'
                ),
                'contain' => array(
                    'ToCity'
                ),
                'order' => array(
                    'ToCity.name' => 'ASC',
                ),
                'group' => array(
                    'ToCity.id',
                )
            ), false);
        }

        $this->set(compact(
            'trucks', 'cities', 'data_action',
            'data_type'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function achievement_report( $data_action = false ) {
        $this->loadModel('CustomerTargetUnitDetail');
        $this->loadModel('Ttuj');
        $this->loadModel('TtujTipeMotor');
        $this->loadModel('Customer');
        $this->set('active_menu', 'achievement_report');
        $fromMonth = date('m');
        $fromYear = date('Y');
        $toMonth = 12;
        $toYear = date('Y');
        $conditions = array(
            'TtujTipeMotor.status'=> 1,
            'Ttuj.status'=> 1,
            'Ttuj.is_draft'=> 0,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Ttuj']['customer_name'] = $name;
                $conditions['Ttuj.customer_name LIKE '] = '%'.$name.'%';
            }

            if( !empty($refine['fromMonth']) && !empty($refine['fromYear']) ){
                $fromMonth = urldecode($refine['fromMonth']);
                $fromYear = urldecode($refine['fromYear']);
            }

            if( !empty($refine['toMonth']) && !empty($refine['toYear']) ){
                $toMonth = urldecode($refine['toMonth']);
                $toYear = urldecode($refine['toYear']);
            }
        }

        $conditions['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\') >='] = date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear));
        $conditions['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\') <='] = date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear));

        $customerTargetUnits = $this->CustomerTargetUnitDetail->find('all', array(
            'conditions' => array(
                'CustomerTargetUnit.status' => 1,
                'DATE_FORMAT(CONCAT(CustomerTargetUnit.year, \'-\', CustomerTargetUnitDetail.month, \'-\', 1), \'%Y-%m\') >=' => date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear)),
                'DATE_FORMAT(CONCAT(CustomerTargetUnit.year, \'-\', CustomerTargetUnitDetail.month, \'-\', 1), \'%Y-%m\') <=' => date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear)),
            ),
            'order' => array(
                'CustomerTargetUnit.customer_id' => 'ASC', 
            ),
            'contain' => array(
                'CustomerTargetUnit'
            ),
        ));

        $ttujs = $this->Customer->getData('all', array(
            'conditions' => array(
                'Customer.status' => 1,
            ),
        ));
        $cntPencapaian = array();
        $targetUnit = array();

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $ttuj) {
                $conditions['Ttuj.customer_id'] = $ttuj['Customer']['id'];
                $ttujTipeMotor = $this->TtujTipeMotor->find('first', array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Ttuj',
                    ),
                    'order' => array(
                        'Ttuj.customer_name' => 'ASC', 
                    ),
                    'group' => array(
                        'Ttuj.customer_id'
                    ),
                    'fields'=> array(
                        'Ttuj.id', 
                        'Ttuj.customer_id', 
                        'SUM(TtujTipeMotor.qty) as cnt',
                        'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\') as dt',
                    ),
                ), false);

                if( !empty($ttujTipeMotor) ) {
                    if( !empty($ttujTipeMotor) ) {
                        $ttujTipeMotor = $this->Customer->getMerge($ttujTipeMotor, $ttujTipeMotor['Ttuj']['customer_id']);
                        $cntPencapaian[$ttujTipeMotor['Ttuj']['customer_id']][$ttujTipeMotor[0]['dt']] = $ttujTipeMotor[0]['cnt'];
                    }

                    $ttuj = array_merge($ttuj, $ttujTipeMotor);
                }

                $ttujs[$key] = $ttuj;
            }
        }

        if( !empty($customerTargetUnits) ) {
            foreach ($customerTargetUnits as $key => $customerTargetUnit) {
                $idx = sprintf('%s-%s', $customerTargetUnit['CustomerTargetUnit']['year'], date('m', mktime(0, 0, 0, $customerTargetUnit['CustomerTargetUnitDetail']['month'], 10)));
                $targetUnit[$customerTargetUnit['CustomerTargetUnit']['customer_id']][$idx] = $customerTargetUnit['CustomerTargetUnitDetail']['unit'];
            }
        }

        $module_title = __('Laporan Pencapaian');

        $this->request->data['Ttuj']['from']['month'] = $fromMonth;
        $this->request->data['Ttuj']['from']['year'] = $fromYear;
        $this->request->data['Ttuj']['to']['month'] = $toMonth;
        $this->request->data['Ttuj']['to']['year'] = $toYear;
        $module_title .= sprintf(' Periode %s %s - %s %s', date('F', mktime(0, 0, 0, $fromMonth, 10)), $fromYear, date('F', mktime(0, 0, 0, $toMonth, 10)), $toYear);
        $totalCnt = $toMonth - $fromMonth;
        $totalYear = $toYear - $fromYear;

        if( !empty($totalYear) && $totalYear > 0 ) {
            $totalYear = 12 * $totalYear;
            $totalCnt += $totalYear;
        }

        $this->set('sub_module_title', $module_title);

        $this->set(compact(
            'ttujs', 'data_action', 'totalCnt',
            'fromMonth', 'fromYear', 'cntPencapaian',
            'toYear', 'toMonth', 'customerTargetUnit',
            'targetUnit'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function monitoring_truck( $data_action = false ) {
        $this->loadModel('Truck');
        $this->loadModel('Ttuj');
        $this->loadModel('TtujTipeMotor');
        $this->loadModel('CalendarEvent');
        $this->loadModel('Laka');
        $this->set('active_menu', 'monitoring_truck');
        $this->set('sub_module_title', __('Monitoring Truk'));

        if( !empty($this->params['named']) ) {
            $refine = $this->params['named'];

            if( !empty($refine['month']) ) {
                $refine['month'] = urldecode($refine['month']);
                $monthArr = explode('-', $refine['month']);

                if( !empty($monthArr[0]) && !empty($monthArr[1]) ) {
                    $monthNumber = date_parse($monthArr[0]);

                    if( !empty($monthArr[0]) ) {
                        $currentMonth = sprintf("%02s", $monthNumber['month']);
                    }

                    if( !empty($monthArr[1]) && !empty($currentMonth) ) {
                        $currentMonth = sprintf("%s-%s", $monthArr[1], $currentMonth);
                    }
                }
            }
        }

        $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
        $prevMonth = date('Y-m', mktime(0, 0, 0, date("m", strtotime($currentMonth))-1 , 1, date("Y", strtotime($currentMonth))));
        $nextMonth = date('Y-m', mktime(0, 0, 0, date("m", strtotime($currentMonth))+1 , 1, date("Y", strtotime($currentMonth))));
        $leftDay = date('N', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , 0, date("Y", strtotime($currentMonth))));
        $lastDay = date('t', strtotime($currentMonth));
        $conditions = array(
            'Ttuj.status'=> 1,
            'Ttuj.is_draft'=> 0,
            'OR' => array(
                'DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m\')' => $currentMonth,
                'DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m\')' => $currentMonth,
                'DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m\')' => $currentMonth,
                'DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m\')' => $currentMonth,
                'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\')' => $currentMonth,
            ),
        );
        $conditionEvents = array(
            'CalendarEvent.status'=> 1,
            'DATE_FORMAT(CalendarEvent.date, \'%Y-%m\')' => $currentMonth,
        );
        $conditionLakas = array(
            'Laka.status'=> 1,
            'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\')' => $currentMonth,
        );

        $this->paginate = $this->Truck->getData('paginate', array(
            'conditions' => array(
                'Truck.status' => 1
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
            'limit' => 20,
        ));
        $trucks = $this->paginate('Truck');
        $truckList = Set::extract('/Truck/id', $trucks);
        $conditions['Ttuj.truck_id'] = $truckList;

        $ttujs = $this->Ttuj->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Ttuj.customer_name' => 'ASC', 
            ),
            'group' => array(
                'Ttuj.customer_id'
            ),
        ));
        $events = $this->CalendarEvent->getData('all', array(
            'conditions' => $conditionEvents,
            'order' => array(
                'CalendarEvent.date' => 'ASC', 
            ),
        ));
        $lakas = $this->Laka->getData('all', array(
            'conditions' => $conditionLakas,
            'order' => array(
                'Laka.tgl_laka' => 'ASC', 
            ),
        ));
        $dataTtuj = array();
        $dataEvent = array();
        $dataLaka = array();

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $value) {
                $nopol = $value['Ttuj']['nopol'];
                $ttujTipeMotor = $this->TtujTipeMotor->find('first', array(
                    'conditions' => array(
                        'TtujTipeMotor.status' => 1,
                        'TtujTipeMotor.ttuj_id' => $value['Ttuj']['id'],
                    ),
                    'fields' => array(
                        'SUM(TtujTipeMotor.qty) cnt'
                    ),
                ));
                $totalMuatan = 0;

                if( !empty($ttujTipeMotor[0]['cnt']) ) {
                    $totalMuatan = $ttujTipeMotor[0]['cnt'];
                }

                $dataTmp = array(
                    'Tujuan' => $value['Ttuj']['to_city_name'],
                    'Driver' => $value['Ttuj']['driver_name'],
                    'Muatan' => $totalMuatan,
                );

                if( empty($value['Ttuj']['is_draft']) ) {
                    $tglBerangkat = date('d', strtotime($value['Ttuj']['tgljam_berangkat']));
                    $dataTtuj[$nopol]['Berangkat'][$tglBerangkat] = $dataTmp;
                    $dataTtuj[$nopol]['Berangkat'][$tglBerangkat]['datetime'] = date('d M Y H:i:s', strtotime($value['Ttuj']['tgljam_berangkat']));
                }

                if( !empty($value['Ttuj']['is_arrive']) ) {
                    $tglTiba = date('d', strtotime($value['Ttuj']['tgljam_tiba']));
                    $dataTtuj[$nopol]['Tiba'][$tglTiba] = $dataTmp;
                    $dataTtuj[$nopol]['Tiba'][$tglTiba]['datetime'] = date('d M Y H:i:s', strtotime($value['Ttuj']['tgljam_tiba']));
                }

                if( !empty($value['Ttuj']['is_bongkaran']) ) {
                    $tglBongkaran = date('d', strtotime($value['Ttuj']['tgljam_bongkaran']));
                    $dataTtuj[$nopol]['Bongkaran'][$tglBongkaran] = $dataTmp;
                    $dataTtuj[$nopol]['Bongkaran'][$tglBongkaran]['datetime'] = date('d M Y H:i:s', strtotime($value['Ttuj']['tgljam_bongkaran']));
                }

                if( !empty($value['Ttuj']['is_balik']) ) {
                    $tglBalik = date('d', strtotime($value['Ttuj']['tgljam_balik']));
                    $dataTtuj[$nopol]['Balik'][$tglBalik] = $dataTmp;
                    $dataTtuj[$nopol]['Balik'][$tglBalik]['datetime'] = date('d M Y H:i:s', strtotime($value['Ttuj']['tgljam_balik']));
                }

                if( !empty($value['Ttuj']['is_pool']) ) {
                    $tglPool = date('d', strtotime($value['Ttuj']['tgljam_pool']));
                    $dataTtuj[$nopol]['Pool'][$tglPool] = $dataTmp;
                    $dataTtuj[$nopol]['Pool'][$tglPool]['datetime'] = date('d M Y H:i:s', strtotime($value['Ttuj']['tgljam_pool']));
                }
            }
        }

        if( !empty($events) ) {
            foreach ($events as $key => $event) {
                $dataEvent[$event['CalendarEvent']['nopol']][date('d', strtotime($event['CalendarEvent']['date']))][] = array(
                    'time' => date('H:i', strtotime($event['CalendarEvent']['time'])),
                    'date' => $event['CalendarEvent']['date'],
                    'title' => $event['CalendarEvent']['name'],
                    'note' => $event['CalendarEvent']['note'],
                    'color' => !empty($event['CalendarColor']['hex'])?$event['CalendarColor']['hex']:false,
                    'icon' => !empty($event['CalendarIcon']['photo'])?$event['CalendarIcon']['photo']:false,
                );
            }
        }

        if( !empty($lakas) ) {
            foreach ($lakas as $key => $laka) {
                $dataLaka[date('d', strtotime($laka['Laka']['tgl_laka']))][] = array(
                    'tgl_laka' => $laka['Laka']['tgl_laka'],
                    'driver_name' => $laka['Laka']['driver_name'],
                    'lokasi_laka' => $laka['Laka']['lokasi_laka'],
                    'truck_condition' => $laka['Laka']['truck_condition'],
                );
            }
        }

        $this->set(compact(
            'data_action', 'lastDay', 'currentMonth',
            'trucks', 'prevMonth', 'nextMonth',
            'dataTtuj', 'dataEvent', 'dataLaka'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    function index(){
        $this->loadModel('Revenue');
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'revenue');
        $this->set('sub_module_title', __('Revenue'));

        $conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nodoc'])){
                $nodoc = urldecode($refine['nodoc']);
                $this->request->data['Revenue']['nodoc'] = $nodoc;
                $conditions['Revenue.nodoc LIKE '] = '%'.$nodoc.'%';
            }
            if(!empty($refine['no_ttuj'])){
                $no_ttuj = urldecode($refine['no_ttuj']);
                $this->request->data['Ttuj']['no_ttuj'] = $no_ttuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$no_ttuj.'%';
            }
        }

        $this->paginate = $this->Revenue->getData('paginate', array(
            'conditions' => $conditions,
            'contain' => array(
                'Ttuj'
            )
        ));
        $revenues = $this->paginate('Revenue');

        if(!empty($revenues)){
            foreach ($revenues as $key => $value) {
                $revenues[$key] = $this->Ttuj->Customer->getMerge($value, $value['Ttuj']['customer_id']);
            }
        }
        $this->set('revenues', $revenues); 
    }

    function revenue_add(){
        $this->loadModel('Revenue');
        $module_title = __('Tambah Revenue');
        $this->set('sub_module_title', trim($module_title));
        $this->doTTUJ();
    }

    function revenue_edit( $id ){
        $this->loadModel('Ttuj');
        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id
            )
        ));

        if(!empty($ttuj)){
            $data_action = false;

            if( !empty($ttuj['Ttuj']['is_retail']) ) {
                $data_action = 'retail';
            }

            $module_title = sprintf(__('Rubah TTUJ %s'), ucwords($data_action));
            $this->set('sub_module_title', trim($module_title));
            $this->doTTUJ($data_action, $id, $ttuj);
        }else{
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'ttuj'
            ));
        }
    }

    function add(){
        $this->loadModel('Revenue');
        $module_title = __('Tambah Revenue');
        $this->set('sub_module_title', trim($module_title));
        $this->doRevenue();
    }

    function edit( $id ){
        $this->loadModel('Revenue');
        $revenue = $this->Revenue->getData('first', array(
            'conditions' => array(
                'Revenue.id' => $id
            ),
            'contain' => array(
                'RevenueDetail'=> array(
                    'order' => array(
                        'RevenueDetail.ttuj_tipe_motor_id'
                    )
                ),
                'Ttuj'
            )
        ));

        if(!empty($revenue)){
            $module_title = __('Rubah Revenue');
            $this->set('sub_module_title', trim($module_title));
            $this->doRevenue($id, $revenue);
        }else{
            $this->MkCommon->setCustomFlash(__('Revenue tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'index'
            ));
        }
    }

    function doRevenue($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TarifAngkutan');
        $this->loadModel('City');
        $this->loadModel('TipeMotor');

        $data_revenue_detail = array();

        if(!empty($this->request->data)){
            $data = $this->request->data;
// debug($data);die();
            $data['Revenue']['date_sj'] = !empty($data['Revenue']['date_sj']) ? date('Y-m-d', strtotime($data['Revenue']['date_sj'])) : '';
            $data['Revenue']['date_revenue'] = !empty($data['Revenue']['date_revenue']) ? date('Y-m-d', strtotime($data['Revenue']['date_revenue'])) : '';

            if($id && $data_local){
                $this->Revenue->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Revenue');
                $this->Revenue->create();
                $msg = 'membuat';
            }

            /*validasi revenue detail*/
            $validate_detail = true;
            $validate_qty = true;
            $total_revenue = 0;
            $array_ttuj_tipe_motor = array();
            if(!empty($data['RevenueDetail'])){
                foreach ($data['RevenueDetail']['no_do'] as $key => $value) {
                    $data_detail['RevenueDetail'] = array(
                        'no_do' => $value,
                        'no_sj' => $data['RevenueDetail']['no_sj'][$key],
                        'qty_unit' => $data['RevenueDetail']['qty_unit'][$key],
                        'price_unit' => $data['RevenueDetail']['price_unit'][$key],
                        'tipe_motor_id' => $data['RevenueDetail']['tipe_motor_id'][$key],
                        'city_id' => $data['RevenueDetail']['city_id'][$key],
                        'ttuj_tipe_motor_id' => $data['RevenueDetail']['ttuj_tipe_motor_id'][$key],
                        'tarif_angkutan_id' => $data['RevenueDetail']['tarif_angkutan_id'][$key],
                    );

                    $this->Revenue->RevenueDetail->set($data_detail);
                    if( !$this->Revenue->RevenueDetail->validates() ){
                        $validate_detail = false;
                    }

                    if( empty($array_ttuj_tipe_motor[$data['RevenueDetail']['ttuj_tipe_motor_id'][$key]]) ){
                        $array_ttuj_tipe_motor[$data['RevenueDetail']['ttuj_tipe_motor_id'][$key]] = intval($data_detail['RevenueDetail']['qty_unit']);
                    }else{
                        $array_ttuj_tipe_motor[$data['RevenueDetail']['ttuj_tipe_motor_id'][$key]] += $data_detail['RevenueDetail']['qty_unit'];
                    }

                    if(!empty($data['RevenueDetail']['price_unit'][$key]) && $data['RevenueDetail']['qty_unit'][$key]){
                        if($data['RevenueDetail']['jenis_unit'][$key] == 'per_truck'){
                            $total_revenue += $data['RevenueDetail']['price_unit'][$key];
                        }else{
                            $total_revenue += $data['RevenueDetail']['price_unit'][$key] * $data['RevenueDetail']['qty_unit'][$key];
                        }
                    }
                }

                foreach ($array_ttuj_tipe_motor as $ttuj_tipe_motor_id => $value) {
                    $revenue_condition = array(
                        'TtujTipeMotorUse.ttuj_tipe_motor_id' => $ttuj_tipe_motor_id
                    );
                    if(!empty($data_local['Revenue']['id'])){
                        $revenue_condition['TtujTipeMotorUse.revenue_id <>'] = $data_local['Revenue']['id'];
                    }
                    
                    $qty = $this->Ttuj->TtujTipeMotor->TtujTipeMotorUse->find('first', array(
                        'conditions' => $revenue_condition,
                        'fields' => array(
                            'SUM(TtujTipeMotorUse.qty) as count_qty'
                        )
                    ));

                    $qty_real_tipe_ttuj = $this->Ttuj->TtujTipeMotor->getData('first', array(
                        'conditions' => array(
                            'TtujTipeMotor.id' => $ttuj_tipe_motor_id
                        )
                    ));

                    $validate_qty_real = false;
                    $qty_real = $qty_real_tipe_ttuj['TtujTipeMotor']['qty'];

                    if(!empty($qty_real_tipe_ttuj) && $qty_real_tipe_ttuj['TtujTipeMotor']['qty'] >= $value ){
                        $validate_qty_real = true;
                    }
                    
                    if($validate_qty_real){
                        if(empty($qty_real_tipe_ttuj)){
                            $validate_qty = true;
                        }else{
                            if( !empty($qty[0]['count_qty']) ){
                                $free_space = $qty_real - ($qty[0]['count_qty'] + $value);
                                if($free_space < 0){
                                    $validate_qty = false;
                                    break;
                                }
                            }
                        }
                    }else{
                        $validate_qty = false;
                        break;
                    }
                }
            }

            if( !empty($data['Revenue']['pph']) && $data['Revenue']['pph'] > 0 ){
                $pph = $total_revenue * ($data['Revenue']['pph'] / 100);
            }
            if( !empty($data['Revenue']['ppn']) && $data['Revenue']['ppn'] > 0 ){
                $ppn = $total_revenue * ($data['Revenue']['ppn'] / 100);
            }

            if( !empty($data['Revenue']['pph']) && $data['Revenue']['pph'] > 0 ){
                $total_revenue -= $pph;
            }
            if( !empty($data['Revenue']['ppn']) && $data['Revenue']['ppn'] > 0 ){
                $total_revenue += $ppn;
            }
            $data['Revenue']['total'] = $total_revenue;
            /*end validasi revenue detail*/

            $this->Revenue->set($data);

            if($this->Revenue->validates($data) && $validate_detail && $validate_qty){
                if($this->Revenue->save($data)){
                    $revenue_id = $this->Revenue->id;

                    if($id && $data_local){
                        $this->Revenue->RevenueDetail->deleteAll(array(
                            'revenue_id' => $revenue_id
                        ));

                        $this->Ttuj->TtujTipeMotor->TtujTipeMotorUse->deleteAll(array(
                            'revenue_id' => $revenue_id
                        ));
                    }

                    foreach ($array_ttuj_tipe_motor as $ttuj_tipe_motor_id => $value) {
                        $this->Ttuj->TtujTipeMotor->TtujTipeMotorUse->create();
                        $this->Ttuj->TtujTipeMotor->TtujTipeMotorUse->set(array(
                            'revenue_id' => $revenue_id,
                            'ttuj_tipe_motor_id' => $ttuj_tipe_motor_id,
                            'qty' => $value
                        ));
                        $this->Ttuj->TtujTipeMotor->TtujTipeMotorUse->save();
                    }

                    $getLastReference = intval($this->Revenue->RevenueDetail->getLastReference())+1;
                    foreach ($data['RevenueDetail']['no_do'] as $key => $value) {
                        $this->Revenue->RevenueDetail->create();
                        $data_detail['RevenueDetail'] = array(
                            'no_do' => $value,
                            'no_sj' => $data['RevenueDetail']['no_sj'][$key],
                            'qty_unit' => $data['RevenueDetail']['qty_unit'][$key],
                            'price_unit' => $data['RevenueDetail']['price_unit'][$key],
                            'revenue_id' => $revenue_id,
                            'tipe_motor_id' => $data['RevenueDetail']['tipe_motor_id'][$key],
                            'city_id' => $data['RevenueDetail']['city_id'][$key],
                            'ttuj_tipe_motor_id' => $data['RevenueDetail']['ttuj_tipe_motor_id'][$key],
                            'tarif_angkutan_id' => $data['RevenueDetail']['tarif_angkutan_id'][$key],
                            'no_reference' => str_pad ( $getLastReference++ , 10, "0", STR_PAD_LEFT)
                        );
                        $this->Revenue->RevenueDetail->set($data_detail);
                        $this->Revenue->RevenueDetail->save();
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Revenue'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Revenue'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'revenues',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Revenue'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Revenue'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $text = sprintf(__('Gagal %s Revenue'), $msg);
                if(!$validate_detail){
                    $text .= ', mohon lengkapi field-field yang kosong';
                }
                if(!$validate_qty){
                    $text .= ', qty tipe motor melebihi qty maksimum TTUJ';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['RevenueDetail'])){

                $ttuj_temp_data = $this->Ttuj->getData('first', array(
                    'conditions' => array(
                        'Ttuj.id' => $this->request->data['Revenue']['ttuj_id']
                    )
                ));
                foreach ($this->request->data['RevenueDetail'] as $key => $value) {
                    $ttuj_tipe_motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                        'conditions' => array(
                            'TtujTipeMotor.id' => $value['ttuj_tipe_motor_id']
                        ),
                        'contain' => array(
                            'Ttuj',
                        )
                    ));
                    $ttuj_tipe_motor = $this->TipeMotor->getMerge($ttuj_tipe_motor, $ttuj_tipe_motor['TtujTipeMotor']['tipe_motor_id']);
                    
                    if($ttuj_temp_data['Ttuj']['is_retail']){
                        if(!empty($ttuj_tipe_motor)){                            
                            $qty = $ttuj_tipe_motor['TtujTipeMotor']['qty'];

                            if($ttuj_tipe_motor['Ttuj']['is_retail']){
                                $city = $this->City->getData('first', array(
                                    'conditions' => array(
                                        'City.id' => $ttuj_tipe_motor['TtujTipeMotor']['city_id']
                                    )
                                ));
                                
                                if(!empty($city['City']['name'])){
                                    $to_city_name = $city['City']['name'];
                                    $to_city_id = $city['City']['id'];
                                }

                                $ttujTipeMotor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                                    'conditions' => array(
                                        'TtujTipeMotor.ttuj_id' => $this->request->data['Revenue']['ttuj_id'],
                                        'TtujTipeMotor.tipe_motor_id' => $ttuj_tipe_motor['TtujTipeMotor']['id'],
                                        'TtujTipeMotor.city_id' => $to_city_id
                                    )
                                ));

                                if(!empty($ttujTipeMotor)){
                                    $qty = $ttujTipeMotor['TtujTipeMotor']['qty'];
                                }
                            }else{
                                $to_city_name = $ttuj_tipe_motor['Ttuj']['to_city_name'];
                                $to_city_id = $ttuj_tipe_motor['Ttuj']['to_city_id'];                    
                            }
                        }

                        $tarif = $this->TarifAngkutan->findTarif($ttuj_temp_data['Ttuj']['from_city_id'], $value['city_id'], $ttuj_temp_data['Ttuj']['customer_id'], $ttuj_temp_data['Ttuj']['truck_capacity']);
                    }else{
                        $qty = $value['qty_unit'];
                        $to_city_name = $ttuj_temp_data['Ttuj']['to_city_name'];

                        $tarif = $this->TarifAngkutan->findTarif($ttuj_temp_data['Ttuj']['from_city_id'], $ttuj_temp_data['Ttuj']['to_city_id'], $ttuj_temp_data['Ttuj']['customer_id'], $ttuj_temp_data['Ttuj']['truck_capacity']);
                    }
                    
                    
                    $data_revenue_detail[$key] = array(
                        'TtujTipeMotor' => array(
                            'qty' => $qty
                        ),
                        'RevenueDetail' => array(
                            'no_do' => $value['no_do'],
                            'no_sj' => $value['no_sj'],
                            'to_city_name' => $to_city_name,
                            'price_unit' => $tarif,
                            'qty_unit' => $value['qty_unit'],
                            'ttuj_tipe_motor_id' => $value['ttuj_tipe_motor_id'],
                            'tipe_motor_id' => $ttuj_tipe_motor['TipeMotor']['id'],
                            'city_id' => $value['city_id'],
                            'TipeMotor' => array(
                                'name' => $ttuj_tipe_motor['TipeMotor']['name'],
                            ),
                        )
                    );
                }
            }
            // debug($data_revenue_detail);die();
        }

        $ttuj_retail = false;
        $ttuj_data = array();
        $tarif_angkutan = false;
        if(!empty($this->request->data['Revenue']['ttuj_id'])){
            $ttuj_data = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['Revenue']['ttuj_id']
                )
            ));

            if(!empty($ttuj_data) && !empty($this->request->data['RevenueDetail']['no_do'])){
                foreach ($this->request->data['RevenueDetail']['no_do'] as $key => $value) {
                    $tipe_motor_id = $this->request->data['RevenueDetail']['tipe_motor_id'][$key];

                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $tipe_motor_id
                        )
                    ));

                    $tipe_motor_name = '';
                    if(!empty($tipe_motor)){
                        $tipe_motor_name = $tipe_motor['TipeMotor']['name'];
                    }

                    $qty = 0;
                    if($ttuj_data['Ttuj']['is_retail']){
                        $city = $this->City->getData('first', array(
                            'conditions' => array(
                                'City.id' => $this->request->data['RevenueDetail']['city_id'][$key]
                            )
                        ));
                        // debug($city);die();
                        if(!empty($city['City']['name'])){
                            $to_city_name = $city['City']['name'];
                            $to_city_id = $city['City']['id'];
                        }

                        $ttujTipeMotor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Revenue']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $tipe_motor_id,
                                'TtujTipeMotor.city_id' => $to_city_id
                            )
                        ));

                        if(!empty($ttujTipeMotor)){
                            $qty = $ttujTipeMotor['TtujTipeMotor']['qty'];
                        }

                        $tarif = $this->TarifAngkutan->findTarif($ttuj_data['Ttuj']['from_city_id'], $this->request->data['RevenueDetail']['city_id'][$key], $ttuj_data['Ttuj']['customer_id'], $ttuj_data['Ttuj']['truck_capacity']);
                    }else{
                        $to_city_name = $ttuj_data['Ttuj']['to_city_name'];
                        $to_city_id = $ttuj_data['Ttuj']['to_city_id'];  

                        $tarif = $this->TarifAngkutan->findTarif($ttuj_data['Ttuj']['from_city_id'], $ttuj_data['Ttuj']['to_city_id'], $ttuj_data['Ttuj']['customer_id'], $ttuj_data['Ttuj']['truck_capacity']);
                    }

                    $data_revenue_detail[$key] = array(
                        'TtujTipeMotor' => array(
                            'qty' => $qty
                        ),
                        'RevenueDetail' => array(
                            'no_do' => $this->request->data['RevenueDetail']['no_do'][$key],
                            'no_sj' => $this->request->data['RevenueDetail']['no_sj'][$key],
                            'to_city_name' => $to_city_name,
                            'price_unit' => $tarif,
                            'qty_unit' => $this->request->data['RevenueDetail']['qty_unit'][$key],
                            'ttuj_tipe_motor_id' => $this->request->data['RevenueDetail']['ttuj_tipe_motor_id'][$key],
                            'tipe_motor_id' => $tipe_motor_id,
                            'city_id' => $to_city_id,
                            'TipeMotor' => array(
                                'name' => $tipe_motor_name,
                            ),
                        )
                    );
                }
            }
        }
        $this->set('data_revenue_detail', $data_revenue_detail);

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
        ));
        $this->set('ttujs', $ttujs);

        $this->loadModel('Customer');
        $customers = $this->Customer->find('list', array(
            'conditions' => array(
                'Customer.status' => 1
            )
        ));
        $this->set('customers', $customers);

        $toCities = $this->City->toCities();
        $list_tipe_motor = $this->TipeMotor->getData('list', array(
            'conditions' => array(
                'TipeMotor.status' => 1
            )
        ));

        $this->set(compact('toCities', 'list_tipe_motor'));
        $this->set('active_menu', 'revenues');
        $this->render('revenue_form');
    }

    function revenue_toggle( $id ){
        $this->loadModel('Revenue');
        $locale = $this->Revenue->getData('first', array(
            'conditions' => array(
                'Revenue.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Revenue']['status']){
                $value = false;
            }

            $this->Revenue->id = $id;

            if($this->Revenue->save()){
                $this->MkCommon->setCustomFlash(__('Revenue berhasil dibatalkan.'), 'success');
                $this->Log->logActivity( sprintf(__('Revenue ID #%s berhasil dibatalkan.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }else{
                $this->MkCommon->setCustomFlash(__('Revenue membatalkan TTUJ.'), 'error');
                $this->Log->logActivity( sprintf(__('Revenue membatalkan TTUJ ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Revenue tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function detail_ritase($id){
        if(!empty($id)){
            $this->loadModel('Truck');
            $this->loadModel('Ttuj');
            $this->loadModel('Customer');

            $this->Truck->bindModel(array(
                'hasOne' => array(
                    'TruckCustomer' => array(
                        'className' => 'TruckCustomer',
                        'foreignKey' => 'truck_id',
                    )
                )
            ));

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];
                if(!empty($refine['date'])){
                    $date = urldecode(rawurldecode($refine['date']));
                    $this->request->data['Ttuj']['date'] = $date;
                }
            }

            $truk = $this->Truck->getData('first', array(
                'conditions' => array(
                    'Truck.id' => $id,
                    'Truck.status' => 1
                ),
                'contain' => array(
                    'TruckBrand',
                    'TruckCategory',
                    'TruckFacility',
                    'Driver',
                    'TruckCustomer' => array(
                        'conditions' => array(
                            'TruckCustomer.primary'=> 1,
                        ),
                        'order' => array(
                            'TruckCustomer.primary' => 'DESC'
                        )
                    )
                )
            ));
            $truk = $this->Customer->getMerge($truk, $truk['TruckCustomer'][0]['customer_id']);
            
            if(!empty($truk)){
                $total_ritase = $this->Ttuj->getData('count', array(
                    'conditions' => array(
                        'Ttuj.truck_id' => $id,
                        'Ttuj.is_pool' => 1,
                        'Ttuj.status' => 1
                    )
                ));

                $total_unit = $this->Ttuj->getData('all', array(
                    'conditions' => array(
                        'Ttuj.truck_id' => $id,
                        'Ttuj.is_draft' => 0,
                        'Ttuj.status' => 1
                    )
                ));

                if(!empty($total_unit)){
                    $ttuj_id = Set::extract('/Ttuj/id', $total_unit);
                    $total_unit = $this->Ttuj->TtujTipeMotor->getData('first', array(
                        'conditions' => array(
                            'TtujTipeMotor.ttuj_id' => $ttuj_id
                        ),
                        'fields' => array(
                            'sum(TtujTipeMotor.qty) as total_qty'
                        )
                    ));

                    if(!empty($total_unit[0]['total_qty'])){
                        $total_unit = $total_unit[0]['total_qty'];
                    }else{
                        $total_unit = 0;
                    }
                }

                $default_conditions = array(
                    'Ttuj.truck_id' => $id,
                    'Ttuj.status' => 1,
                    'Ttuj.is_draft' => 0,
                );

                if(isset($this->request->data['Ttuj']['date']) && !empty($this->request->data['Ttuj']['date'])){
                    $date_explode = explode('-', trim($this->request->data['Ttuj']['date']));
                    $date_from = $date_explode[0];
                    $date_to = $date_explode[1];
                    $default_conditions['OR'] = array(
                        array(
                            'DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') >='=> $date_from,
                            'DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') <=' => $date_to,
                        ),
                        array(
                            'DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m-%d\') >='=> $date_from,
                            'DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m-%d\') <=' => $date_to,
                        ),
                        array(
                            'DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m-%d\') >='=> $date_from,
                            'DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m-%d\') <=' => $date_to,
                        ),
                        array(
                            'DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m-%d\') >='=> $date_from,
                            'DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m-%d\') <=' => $date_to,
                        ),
                        array(
                            'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='=> $date_from,
                            'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <=' => $date_to,
                        ),
                    );
                }

                $this->paginate = $this->Ttuj->getData('paginate', array(
                    'conditions' => $default_conditions,
                    'order' => array(
                        'Ttuj.tgljam_berangkat' => 'ASC',
                        'Ttuj.tgljam_tiba' => 'ASC',
                        'Ttuj.tgljam_bongkaran' => 'ASC',
                        'Ttuj.tgljam_balik' => 'ASC',
                        'Ttuj.tgljam_pool' => 'ASC'
                    )
                ));
                $truk_ritase = $this->paginate('Ttuj');

                if(!empty($truk_ritase)){
                    $this->loadModel('Lku');
                    $this->loadModel('TtujTipeMotor');

                    $total_lku = 0;
                    foreach ($truk_ritase as $key => $value) {
                        $qty_ritase = $this->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $value['Ttuj']['id'],
                                'TtujTipeMotor.status' => 1
                            ),
                            'fields' => array(
                                'sum(TtujTipeMotor.qty) as qty_ritase'
                            )
                        ));

                        $lkus = $this->Lku->getData('first', array(
                            'conditions' => array(
                                'Lku.status' => 1,
                                'Lku.ttuj_id' => $value['Ttuj']['id']
                            ),
                            'fields' => array(
                                'SUM(Lku.total_klaim) as qty_lku'
                            )
                        ));

                        $from_time = strtotime($value['Ttuj']['tgljam_berangkat']);
                        $to_time = strtotime($value['Ttuj']['tgljam_tiba']);
                        $diff = round(abs($to_time - $from_time) / 60, 2);
                        $truk_ritase[$key]['arrive_lead_time'] = round($diff/3600);
                        $diff = round($diff/60, 2);

                        if( $diff > $value['Ttuj']['arrive_over_time'] ) {
                            $truk_ritase[$key]['arrive_over_time'] = round($diff/3600);
                        }

                        $from_time = strtotime($value['Ttuj']['tgljam_balik']);
                        $to_time = strtotime($value['Ttuj']['tgljam_pool']);
                        $diff = round(abs($to_time - $from_time) / 60, 2);
                        $truk_ritase[$key]['back_lead_time'] = round($diff/3600);
                        $diff = round($diff/60, 2);

                        if( $diff > $value['Ttuj']['back_orver_time'] ) {
                            $truk_ritase[$key]['back_orver_time'] = round($diff/3600);
                        }

                        $truk_ritase[$key]['qty_ritase'] = $qty_ritase[0]['qty_ritase'];
                        $truk_ritase[$key]['qty_lku'] = $lkus[0]['qty_lku'];
                        $total_lku += $lkus[0]['qty_lku'];
                    }
                }

                $sub_module_title = __('Detail Ritase Truk');
                $this->set(compact('id', 'truk', 'truk_ritase', 'sub_module_title', 'total_ritase', 'total_unit', 'total_lku'));
            }else{
                $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function invoices(){
        $this->loadModel('Invoice');
        $this->set('active_menu', 'revenue');
        $this->set('sub_module_title', __('Invoice'));

        $conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nodoc'])){
                $nodoc = urldecode($refine['nodoc']);
                $this->request->data['Invoice']['nodoc'] = $nodoc;
                $conditions['Invoice.nodoc LIKE '] = '%'.$nodoc.'%';
            }
            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Invoice']['customer_id'] = $customer;
                $conditions['Invoice.customer_id LIKE '] = '%'.$customer.'%';
            }
        }

        $this->paginate = $this->Invoice->getData('paginate', array(
            'conditions' => $conditions,
        ));
        $invoices = $this->paginate('Invoice');

        if(!empty($invoices)){
            foreach ($invoices as $key => $value) {
                $invoices[$key] = $this->Invoice->Customer->getMerge($value, $value['Invoice']['customer_id']);
            }
        }
        $this->set('invoices', $invoices); 

        $this->loadModel('Customer');
        $customers = $this->Customer->find('list', array(
            'conditions' => array(
                'Customer.status' => 1
            )
        ));
        $this->set('customers', $customers);
    }

    function invoice_add(){
        $module_title = __('Tambah Invoice');
        $this->set('sub_module_title', trim($module_title));
        $this->doInvoice();
    }

    function invoice_edit( $id ){
        $this->loadModel('Invoice');
        $revenue = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id
            )
        ));

        if(!empty($revenue)){
            $module_title = __('Rubah Invoice');
            $this->set('sub_module_title', trim($module_title));
            $this->doInvoice($id, $revenue);
        }else{
            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'invoices'
            ));
        }
    }

    function doInvoice($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Invoice->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Invoice');
                $this->Invoice->create();
                $msg = 'membuat';
            }

            $this->Invoice->set($data);
            if($this->Invoice->validates()){
                if($this->Invoice->save()){
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil %s Invoice'), $msg), 'success'); 
                    $this->redirect(array(
                        'controller' => 'revenues',
                        'action' => 'invoices'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Invoice'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Invoice'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Invoice'), $msg), 'error'); 
                $this->Log->logActivity( sprintf(__('Gagal %s Invoice'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else if(!empty($id) && !empty($data_local)){
             $this->request->data = $data_local;
        }

        $this->loadModel('Customer');
        $customers = $this->Customer->find('list', array(
            'conditions' => array(
                'Customer.status' => 1
            )
        ));
        
        $this->set(compact('customers', 'id'));
        
        $this->render('invoice_form');
    }
}