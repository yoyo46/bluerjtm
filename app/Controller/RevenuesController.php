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

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjRevenue->processRefine($this->request->data);
            $params = $this->RjRevenue->generateSearchURL($refine);
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
                $totalMuatan = 0;

                if( !empty($data['TtujTipeMotor']['qty']) ) {
                    foreach ($data['TtujTipeMotor']['qty'] as $key => $qty) {
                        if( !empty($qty) ) {
                            $totalMuatan += $qty;
                        }
                    }
                }

                if( empty($totalMuatan) ) {
                    $totalMuatan = 1;
                }

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

                                if( !empty($uangJalan['UangJalan']['uang_jalan_per_unit']) ) {
                                    $uang_jalan_1 = $uang_jalan_1*$totalMuatan;
                                    $uang_jalan_2 = 0;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_kuli_muat_per_unit']) ) {
                                    $uang_kuli_muat = $uang_kuli_muat*$totalMuatan;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_kuli_bongkar_per_unit']) ) {
                                    $uang_kuli_bongkar = $uang_kuli_bongkar*$totalMuatan;
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
                    break;
            }

            $data_local = $this->Ttuj->getData('first', array(
                'conditions' => $conditionsDataLocal
            ));

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

    public function ritase_report( $data_action = false ) {
        $this->loadModel('Truck');
        $this->loadModel('TruckCustomer');
        $this->loadModel('Ttuj');
        $this->loadModel('City');
        $this->set('active_menu', 'ritase_report');
        $dateFrom = date('Y-m-d', strtotime('-1 month'));
        $dateTo = date('Y-m-d');
        $conditions = array(
            'Truck.status'=> 1,
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

        $this->paginate = $this->Truck->getData('paginate', array(
            'conditions' => $conditions,
            'order' => array(
                'Truck.nopol' => 'ASC', 
            ),
            'contain' => array(
                'Driver',
                'TruckAlocation'
            ),
        ));
        $trucks = $this->paginate('Truck');

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $truck) {
                $truckCustomer = $this->TruckCustomer->getData('first', array(
                    'conditions' => array(
                        'TruckCustomer.truck_id'=> $truck['Truck']['id'],
                    ),
                    'order' => array(
                        'TruckCustomer.id' => 'ASC',
                    ),
                ));

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
                $total = $this->Ttuj->getData('count', array(
                    'conditions' => $conditionsTtuj
                ));
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

                $truck = array_merge($truck, $truckCustomer);
                $truck['Total'] = $total;
                $truck['City'] = $cities;
                $trucks[$key] = $truck;
            }
        }

        $module_title = __('Laporan Ritase Truk');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Ttuj']['date'] = sprintf('%s - %s', date('m/d/Y', strtotime($dateFrom)), date('m/d/Y', strtotime($dateTo)));
            $module_title .= sprintf(' Periode %s - %s', date('d M Y', strtotime($dateFrom)), date('d M Y', strtotime($dateTo)));
        }

        $this->set('sub_module_title', $module_title);
        $cities = $this->Ttuj->getData('list', array(
            'conditions' => array(
                'Ttuj.status' => 1,
                'Ttuj.is_pool' => 1,
                'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='=> $dateFrom,
                'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <=' => $dateTo,
            ),
            'order' => array(
                'Ttuj.to_city_name'
            ),
            'group' => array(
                'Ttuj.to_city_id'
            ),
            'fields' => array(
                'Ttuj.to_city_id', 'Ttuj.to_city_name'
            ),
        ), false);

        $this->set(compact(
            'trucks', 'cities', 'data_action'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function achievement_report( $data_action = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('Customer');
        $this->set('active_menu', 'achievement_report');
        $fromMonth = date('m');
        $fromYear = date('Y');
        $toMonth = 12;
        $toYear = date('Y');
        $conditions = array(
            'Ttuj.status'=> 1,
            'Ttuj.is_pool'=> 1,
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

        $conditions['DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') >='] = date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear));
        $conditions['DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') <='] = date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear));

        $ttujs = $this->Ttuj->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Ttuj.customer_name' => 'ASC', 
            ),
            'group' => array(
                'Ttuj.customer_id'
            ),
            'fields'=> array(
                'Ttuj.id', 
                'Ttuj.customer_id', 
                'COUNT(Ttuj.id) as cnt',
                'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') as dt',
            ),
        ), false);
        $cntPencapaian = array();

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $ttuj) {
                $ttuj = $this->Customer->getMerge($ttuj, $ttuj['Ttuj']['customer_id']);
                $cntPencapaian[$ttuj['Ttuj']['customer_id']][$ttuj[0]['dt']] = $ttuj[0]['cnt'];
                $ttujs[$key] = $ttuj;
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
            'toYear', 'toMonth'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }
}