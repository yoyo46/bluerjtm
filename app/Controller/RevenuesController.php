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

    function search( $index = 'index', $id = false, $data_action = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjRevenue->processRefine($this->request->data);
            $params = $this->RjRevenue->generateSearchURL($refine);
            if(!empty($id)){
                array_push($params, $id);
            }
            if(!empty($data_action)){
                array_push($params, $data_action);
            }
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    public function ttuj() {
        if( in_array('view_ttuj', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->loadModel('SuratJalan');
            $this->set('active_menu', 'ttuj');
            $this->set('sub_module_title', __('TTUJ'));
            $this->set('label_tgl', __('Tanggal Berangkat'));

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
                if(!empty($refine['is_draft'])){
                    $is_draft = urldecode($refine['is_draft']);
                    $conditions['Ttuj.is_draft'] = 1;
                    $this->request->data['Ttuj']['is_draft'] = $is_draft;
                }
                if(!empty($refine['is_commit'])){
                    $is_commit = urldecode($refine['is_commit']);
                    $conditions['Ttuj.is_draft'] = 0;
                    $conditions['Ttuj.is_arrive'] = 0;
                    $conditions['Ttuj.is_bongkaran'] = 0;
                    $conditions['Ttuj.is_balik'] = 0;
                    $conditions['Ttuj.is_pool'] = 0;
                    $this->request->data['Ttuj']['is_commit'] = $is_commit;
                }
                if(!empty($refine['is_arrive'])){
                    $is_arrive = urldecode($refine['is_arrive']);
                    $conditions['Ttuj.is_arrive'] = 1;
                    $conditions['Ttuj.is_bongkaran'] = 0;
                    $conditions['Ttuj.is_balik'] = 0;
                    $conditions['Ttuj.is_pool'] = 0;
                    $this->request->data['Ttuj']['is_arrive'] = $is_arrive;
                }
                if(!empty($refine['is_bongkaran'])){
                    $is_bongkaran = urldecode($refine['is_bongkaran']);
                    $conditions['Ttuj.is_bongkaran'] = 1;
                    $conditions['Ttuj.is_balik'] = 0;
                    $conditions['Ttuj.is_pool'] = 0;
                    $this->request->data['Ttuj']['is_bongkaran'] = $is_bongkaran;
                }
                if(!empty($refine['is_balik'])){
                    $is_balik = urldecode($refine['is_balik']);
                    $conditions['Ttuj.is_balik'] = 1;
                    $conditions['Ttuj.is_pool'] = 0;
                    $this->request->data['Ttuj']['is_balik'] = $is_balik;
                }
                if(!empty($refine['is_pool'])){
                    $is_pool = urldecode($refine['is_pool']);
                    $conditions['Ttuj.is_pool'] = 1;
                    $this->request->data['Ttuj']['is_pool'] = $is_pool;
                }
                if(!empty($refine['is_sj_not_completed'])){
                    $is_sj_not_completed = urldecode($refine['is_sj_not_completed']);
                    $conditions['Ttuj.is_sj_completed'] = 0;
                    $this->request->data['Ttuj']['is_sj_not_completed'] = $is_sj_not_completed;
                }
                if(!empty($refine['is_sj_completed'])){
                    $is_sj_completed = urldecode($refine['is_sj_completed']);
                    $conditions['Ttuj.is_sj_completed'] = 1;
                    $this->request->data['Ttuj']['is_sj_completed'] = $is_sj_completed;
                }
                if(!empty($refine['is_revenue'])){
                    $is_revenue = urldecode($refine['is_revenue']);
                    $conditions['Ttuj.is_revenue'] = 1;
                    $this->request->data['Ttuj']['is_revenue'] = $is_revenue;
                }
                if(!empty($refine['is_not_revenue'])){
                    $is_not_revenue = urldecode($refine['is_not_revenue']);
                    $conditions['Ttuj.is_revenue'] = 0;
                    $this->request->data['Ttuj']['is_not_revenue'] = $is_not_revenue;
                }
            }

            $this->paginate = $this->Ttuj->getData('paginate', array(
                'conditions' => $conditions
            ));
            $ttujs = $this->paginate('Ttuj');

            if( !empty($ttujs) ) {
                foreach ($ttujs as $key => $ttuj) {
                    $ttujs[$key] = $this->SuratJalan->getSJ( $ttuj, $ttuj['Ttuj']['id'] );
                }
            }

            $this->set('ttujs', $ttujs);
        } else {
            $this->redirect($this->referer());
        }
    }

    function ttuj_add( $data_action = 'depo' ){
        if( in_array('insert_ttuj', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $module_title = sprintf(__('Tambah TTUJ - %s'), strtoupper($data_action));
            $this->set('sub_module_title', trim($module_title));
            $this->doTTUJ( $data_action );
        } else {
            $this->redirect($this->referer());
        }
    }

    function ttuj_edit( $id ){
        if( in_array('update_ttuj', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->loadModel('Revenue');
            $ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $id
                )
            ));

            if(!empty($ttuj)){
                $ttuj = $this->Revenue->getPaid( $ttuj, $ttuj['Ttuj']['id'] );
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function saveTtujTipeMotor ( $data_action, $dataTtujTipeMotor = false, $data = false, $dataRevenue = false, $ttuj_id = false, $revenue_id = false, $tarifDefault = false ) {
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
                $group_motor_id = 0;
                $dataValidate['TtujTipeMotor']['tipe_motor_id'] = $tipe_motor_id;
                $dataValidate['TtujTipeMotor']['color_motor_id'] = !empty($data['TtujTipeMotor']['color_motor_id'][$key])?$data['TtujTipeMotor']['color_motor_id'][$key]:false;
                $dataValidate['TtujTipeMotor']['qty'] = !empty($data['TtujTipeMotor']['qty'][$key])?$data['TtujTipeMotor']['qty'][$key]:false;

                if( $data_action == 'retail' ) {
                    $dataValidate['TtujTipeMotor']['city_id'] = !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:false;
                }
                
                $this->Ttuj->TtujTipeMotor->set($dataValidate);

                if( !empty($dataRevenue) ) {
                    if( !empty($tipe_motor_id) ) {
                        $groupMotor = $this->TipeMotor->getData('first', array(
                            'conditions' => array(
                                'TipeMotor.id' => $tipe_motor_id,
                                'TipeMotor.status' => 1,
                            ),
                        ));

                        if( !empty($groupMotor['GroupMotor']['id']) ) {
                            $group_motor_id = $groupMotor['GroupMotor']['id'];
                        }
                    }

                    if( !empty($dataRevenue['Revenue']['revenue_tarif_type']) && $dataRevenue['Revenue']['revenue_tarif_type'] == 'per_unit' ) {
                        if( $data_action == 'retail' ) {
                            $tarif = $this->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $dataValidate['TtujTipeMotor']['city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity'], $group_motor_id);
                        } else {
                            $tarif = $this->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity'], $group_motor_id);
                        }
                    }

                    $priceUnit = 0;
                    $jenis_unit = 'per_truck';
                    $tarif_angkutan_id = 0;
                    $tarif_angkutan_type = '';

                    if( !empty($tarif['tarif']) ) {
                        $priceUnit = $tarif['tarif'];
                        $jenis_unit = $tarif['jenis_unit'];
                        $tarif_angkutan_id = $tarif['tarif_angkutan_id'];
                        $tarif_angkutan_type = $tarif['tarif_angkutan_type'];
                    } else if( !empty($tarifDefault['tarif']) ) {
                        $priceUnit = $tarifDefault['tarif'];
                        $jenis_unit = $tarifDefault['jenis_unit'];
                        $tarif_angkutan_type = $tarifDefault['tarif_angkutan_type'];
                    }

                    $dataRevenue['RevenueDetail'] = array(
                        'revenue_id' => $revenue_id,
                        'group_motor_id' => $group_motor_id,
                        'qty_unit' => $dataValidate['TtujTipeMotor']['qty'],
                        'city_id' => !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:$data['Ttuj']['to_city_id'],
                        'price_unit' => $priceUnit,
                        'payment_type' => $jenis_unit,
                        'tarif_angkutan_id' => $tarif_angkutan_id,
                        'tarif_angkutan_type' => $tarif_angkutan_type,
                    );
                }

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujTipeMotor']['ttuj_id'] = $ttuj_id;
                    $this->Ttuj->TtujTipeMotor->create();
                    $this->Ttuj->TtujTipeMotor->save($dataValidate);

                    if( !empty($dataRevenue) ) {
                        $this->RevenueDetail->create();
                        $this->RevenueDetail->save($dataRevenue);
                    }
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
        $this->loadModel('ColorMotor');
        $this->loadModel('TarifAngkutan');
        $this->loadModel('Revenue');
        $this->loadModel('RevenueDetail');
        $this->loadModel('UangKuli');
        $is_draft = isset($data_local['Ttuj']['is_draft'])?$data_local['Ttuj']['is_draft']:true;

        if( !empty($this->request->data) && in_array('update_ttuj_commit', $this->allowModule) ) {
            $is_draft = true;
        }

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
            $data['Ttuj']['commission_extra'] = !empty($uangJalan['UangJalan']['commission_extra'])?$uangJalan['UangJalan']['commission_extra']:0;
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

            if( !empty($data['Ttuj']['getting_sj']) ) {
                $data['Ttuj']['date_sj'] = $this->MkCommon->getDate($data['Ttuj']['date_sj']);
            } else {
                $data['Ttuj']['date_sj'] = NULL;
            }

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
            $dataRevenue = array();

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

                        $resultTtujTipeMotor = $this->saveTtujTipeMotor($data_action, $dataTtujTipeMotor, $data, $dataRevenue);

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
                            if( empty($data['Ttuj']['is_draft']) && empty($data_local['Ttuj']['is_revenue']) ) {
                                $data['Ttuj']['is_revenue'] = 1;
                            }

                            if($this->Ttuj->save($data)){
                                $tarifDefault = false;
                                if( empty($data['Ttuj']['is_draft']) && empty($data_local['Ttuj']['is_revenue']) ) {
                                    $revenue = $this->Revenue->getData('first', array(
                                        'conditions' => array(
                                            'Revenue.ttuj_id' => $this->Ttuj->id,
                                            'Revenue.status' => 1,
                                        ),
                                    ));

                                    if( empty($revenue) ) {
                                        $tarifDefault = $this->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity']);

                                        $dataRevenue['Revenue'] = array(
                                            'transaction_status' => 'unposting',
                                            'ttuj_id' => $this->Ttuj->id,
                                            'date_revenue' => $data['Ttuj']['ttuj_date'],
                                            'customer_id' => $data['Ttuj']['customer_id'],
                                            'ppn' => 0,
                                            'pph' => 0,
                                            'revenue_tarif_type' => !empty($tarifDefault['jenis_unit'])?$tarifDefault['jenis_unit']:'per_unit',
                                        );

                                        if( !empty($tarifDefault['jenis_unit']) && $tarifDefault['jenis_unit'] == 'per_truck' ) {
                                            $dataRevenue['Revenue']['total'] = $tarifDefault['tarif'];
                                            $dataRevenue['Revenue']['total_without_tax'] = $tarifDefault['tarif'];
                                            $dataRevenue['Revenue']['tarif_per_truck'] = $tarifDefault['tarif'];
                                        }

                                        $this->Revenue->create();
                                        $this->Revenue->save($dataRevenue);
                                    }
                                }

                                $this->saveTtujTipeMotor($data_action, $dataTtujTipeMotor, $data, $dataRevenue, $this->Ttuj->id, $this->Revenue->id, $tarifDefault);

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
                            $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Mohon lengkapi muatan truk.'), $msg), 'error');  
                        }
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Silahkan masukan muatan truk.'), $msg), 'error');  
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Silahkan masukan muatan truk.'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error');
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
                            $uangKuli = $this->UangKuli->getUangKuli( $data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $truck['Truck']['capacity'] );
                            $uangJalan['UangJalan']['uang_kuli_muat_per_unit'] = 1;

                            if( !empty($dataTruck) ) {
                                $uangJalan = $dataTruck;

                                if( !empty($uangKuli) ) {
                                    $uangJalan['UangJalan']['uang_kuli_muat'] = !empty($uangKuli['UangKuliMuat']['UangKuli']['uang_kuli'])?$uangKuli['UangKuliMuat']['UangKuli']['uang_kuli']:0;
                                    $uangJalan['UangJalan']['uang_kuli_bongkar'] = !empty($uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli'])?$uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli']:0;

                                    if( !empty($uangKuli['UangKuliMuat']['UangKuli']['uang_kuli_type']) && $uangKuli['UangKuliMuat']['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
                                        $uangJalan['UangJalan']['uang_kuli_muat_per_unit'] = 1;
                                    }

                                    if( !empty($uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli_type']) && $uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
                                        $uangJalan['UangJalan']['uang_kuli_bongkar_per_unit'] = 1;
                                    }
                                }

                                $this->request->data['Ttuj']['uang_jalan_1_ori'] = $uang_jalan_1 = !empty($uangJalan['UangJalan']['uang_jalan_1'])?$uangJalan['UangJalan']['uang_jalan_1']:0;
                                $uang_jalan_2 = !empty($uangJalan['UangJalan']['uang_jalan_2'])?$uangJalan['UangJalan']['uang_jalan_2']:0;
                                $this->request->data['Ttuj']['uang_kuli_muat_ori'] = $uang_kuli_muat = !empty($uangJalan['UangJalan']['uang_kuli_muat'])?$uangJalan['UangJalan']['uang_kuli_muat']:0;
                                $this->request->data['Ttuj']['uang_kuli_bongkar_ori'] = $uang_kuli_bongkar = !empty($uangJalan['UangJalan']['uang_kuli_bongkar'])?$uangJalan['UangJalan']['uang_kuli_bongkar']:0;
                                $this->request->data['Ttuj']['asdp_ori'] = $asdp = !empty($uangJalan['UangJalan']['asdp'])?$uangJalan['UangJalan']['asdp']:0;
                                $this->request->data['Ttuj']['uang_kawal_ori'] = $uang_kawal = !empty($uangJalan['UangJalan']['uang_kawal'])?$uangJalan['UangJalan']['uang_kawal']:0;
                                $this->request->data['Ttuj']['uang_keamanan_ori'] = $uang_keamanan = !empty($uangJalan['UangJalan']['uang_keamanan'])?$uangJalan['UangJalan']['uang_keamanan']:0;
                                $this->request->data['Ttuj']['uang_jalan_extra_ori'] = $uang_jalan_extra = !empty($uangJalan['UangJalan']['uang_jalan_extra'])?$uangJalan['UangJalan']['uang_jalan_extra']:0;
                                $this->request->data['Ttuj']['commission_ori'] = $commission = !empty($uangJalan['UangJalan']['commission'])?$uangJalan['UangJalan']['commission']:0;
                                $this->request->data['Ttuj']['commission_extra_ori'] = $commission_extra = !empty($uangJalan['UangJalan']['commission_extra'])?$uangJalan['UangJalan']['commission_extra']:0;
                                $uang_jalan_tipe_motor = 0;
                                $uang_kuli_bongkar_tipe_motor = 0;
                                $uang_kuli_muat_tipe_motor = 0;
                                $asdp_tipe_motor = 0;
                                $uang_kawal_tipe_motor = 0;
                                $uang_keamanan_tipe_motor = 0;
                                $commission_tipe_motor = 0;
                                $totalMuatan = 0;
                                $uangJalanTipeMotor = array();

                                if( !empty($uangJalan['UangJalanTipeMotor']) ) {
                                    foreach ($uangJalan['UangJalanTipeMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['UangJalan'][$tipeMotor['group_motor_id']] = $tipeMotor['uang_jalan_1'];
                                    }
                                }
                                if( !empty($uangJalan['CommissionGroupMotor']) ) {
                                    foreach ($uangJalan['CommissionGroupMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['Commission'][$tipeMotor['group_motor_id']] = $tipeMotor['commission'];
                                    }
                                }
                                if( !empty($uangJalan['AsdpGroupMotor']) ) {
                                    foreach ($uangJalan['AsdpGroupMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['Asdp'][$tipeMotor['group_motor_id']] = $tipeMotor['asdp'];
                                    }
                                }
                                if( !empty($uangJalan['UangKawalGroupMotor']) ) {
                                    foreach ($uangJalan['UangKawalGroupMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['UangKawal'][$tipeMotor['group_motor_id']] = $tipeMotor['uang_kawal'];
                                    }
                                }
                                if( !empty($uangJalan['UangKeamananGroupMotor']) ) {
                                    foreach ($uangJalan['UangKeamananGroupMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['UangKeamanan'][$tipeMotor['group_motor_id']] = $tipeMotor['uang_keamanan'];
                                    }
                                }
                                if( !empty($uangKuli['UangKuliMuat']['UangKuliGroupMotor']) ) {
                                    foreach ($uangKuli['UangKuliMuat']['UangKuliGroupMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['UangKuliMuat'][$tipeMotor['group_motor_id']] = $tipeMotor['uang_kuli'];
                                    }
                                }
                                if( !empty($uangKuli['UangKuliBongkar']['UangKuliGroupMotor']) ) {
                                    foreach ($uangKuli['UangKuliBongkar']['UangKuliGroupMotor'] as $key => $tipeMotor) {
                                        $uangJalanTipeMotor['UangKuliBongkar'][$tipeMotor['group_motor_id']] = $tipeMotor['uang_kuli'];
                                    }
                                }

                                if( !empty($data['TtujTipeMotor']['qty']) ) {
                                    foreach ($data['TtujTipeMotor']['qty'] as $key => $qty) {
                                        if( !empty($qty) ) {
                                            $tipe_motor_id = !empty($data['TtujTipeMotor']['tipe_motor_id'][$key])?$data['TtujTipeMotor']['tipe_motor_id'][$key]:false;
                                            $group_motor_id = 0;
                                            $totalMuatan += $qty;
                                            $groupMotor = $this->TipeMotor->find('first', array(
                                                'conditions' => array(
                                                    'TipeMotor.id' => $tipe_motor_id,
                                                    'TipeMotor.status' => 1,
                                                ),
                                            ));

                                            if( !empty($groupMotor) ) {
                                                $group_motor_id = $groupMotor['TipeMotor']['group_motor_id'];
                                            }

                                            if( !empty($uangJalanTipeMotor['UangJalan'][$group_motor_id]) ) {
                                                $uang_jalan_tipe_motor += $uangJalanTipeMotor['UangJalan'][$group_motor_id] * $qty;
                                            } else {
                                                $uang_jalan_tipe_motor += $uang_jalan_1 * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['UangKuliMuat'][$group_motor_id]) ) {
                                                $uang_kuli_muat_tipe_motor += $uangJalanTipeMotor['UangKuliMuat'][$group_motor_id] * $qty;
                                            } else {
                                                $uang_kuli_muat_tipe_motor += $uang_kuli_muat * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['UangKuliBongkar'][$group_motor_id]) ) {
                                                $uang_kuli_bongkar_tipe_motor += $uangJalanTipeMotor['UangKuliBongkar'][$group_motor_id] * $qty;
                                            } else {
                                                $uang_kuli_bongkar_tipe_motor += $uang_kuli_bongkar * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['Asdp'][$group_motor_id]) ) {
                                                $asdp_tipe_motor += $uangJalanTipeMotor['Asdp'][$group_motor_id] * $qty;
                                            } else {
                                                $asdp_tipe_motor += $asdp * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['UangKawal'][$group_motor_id]) ) {
                                                $uang_kawal_tipe_motor += $uangJalanTipeMotor['UangKawal'][$group_motor_id] * $qty;
                                            } else {
                                                $uang_kawal_tipe_motor += $uang_kawal * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['UangKeamanan'][$group_motor_id]) ) {
                                                $uang_keamanan_tipe_motor += $uangJalanTipeMotor['UangKeamanan'][$group_motor_id] * $qty;
                                            } else {
                                                $uang_keamanan_tipe_motor += $uang_keamanan * $qty;
                                            }

                                            if( !empty($uangJalanTipeMotor['Commission'][$group_motor_id]) ) {
                                                $commission_tipe_motor += $uangJalanTipeMotor['Commission'][$group_motor_id] * $qty;
                                            } else {
                                                $commission_tipe_motor += $commission * $qty;
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
                                    $asdp = $asdp_tipe_motor;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_kawal_per_unit']) ) {
                                    $uang_kawal = $uang_kawal_tipe_motor;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_keamanan_per_unit']) ) {
                                    $uang_keamanan = $uang_keamanan_tipe_motor;
                                }

                                if( !empty($uangJalan['UangJalan']['commission_per_unit']) ) {
                                    $commission = $commission_tipe_motor;
                                }

                                if( !empty($uangJalan['UangJalan']['uang_jalan_extra']) && !empty($uangJalan['UangJalan']['min_capacity']) ) {
                                    if( $totalMuatan > $uangJalan['UangJalan']['min_capacity'] ) {
                                        if( !empty($uangJalan['UangJalan']['uang_jalan_extra_per_unit']) ) {
                                            $capacityCost = $totalMuatan - $uangJalan['UangJalan']['min_capacity'];
                                            $uang_jalan_extra = $uang_jalan_extra*$capacityCost;
                                        }
                                    } else {
                                        $uang_jalan_extra = 0;
                                    }
                                } else {
                                    $uang_jalan_extra = 0;
                                }

                                if( !empty($uangJalan['UangJalan']['commission_extra']) && !empty($uangJalan['UangJalan']['commission_min_qty']) ) {
                                    if( $totalMuatan > $uangJalan['UangJalan']['commission_min_qty'] ) {
                                        if( !empty($uangJalan['UangJalan']['commission_extra_per_unit']) ) {
                                            $capacityCost = $totalMuatan - $uangJalan['UangJalan']['commission_min_qty'];
                                            $commission_extra = $commission_extra*$capacityCost;
                                        }
                                    } else {
                                        $commission_extra = 0;
                                    }
                                } else {
                                    $commission_extra = 0;
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

                if( !empty($data_local['UangJalan']) ) {
                    $uangJalan = $data_local['UangJalan'];
                    $uangKuli = $this->UangKuli->getUangKuli( $data_local['Ttuj']['from_city_id'], $data_local['Ttuj']['to_city_id'], $data_local['Ttuj']['customer_id'], $data_local['Ttuj']['truck_capacity'] );
                }

                if( !empty($data_local['Ttuj']['tgljam_berangkat']) ) {
                    $data_local['Ttuj']['tgl_berangkat'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                    $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                }
                $this->request->data = $data_local;

                if( !empty($data_local['UangJalan']) ) {
                    $this->request->data['Ttuj']['uang_jalan_1_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_jalan_1'], 0);
                    $this->request->data['Ttuj']['asdp_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['asdp'], 0);
                    $this->request->data['Ttuj']['uang_kawal_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_kawal'], 0);
                    $this->request->data['Ttuj']['uang_keamanan_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_keamanan'], 0);
                    $this->request->data['Ttuj']['uang_jalan_extra_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['uang_jalan_extra'], 0);
                    $this->request->data['Ttuj']['commission_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['commission'], 0);
                    $this->request->data['Ttuj']['commission_extra_ori'] = $this->MkCommon->convertPriceToString($data_local['UangJalan']['commission_extra'], 0);

                    $this->request->data['Ttuj']['uang_jalan_per_unit'] = !empty($data_local['UangJalan']['uang_jalan_per_unit'])?$data_local['UangJalan']['uang_jalan_per_unit']:0;
                    $this->request->data['Ttuj']['asdp_per_unit'] = !empty($data_local['UangJalan']['asdp_per_unit'])?$data_local['UangJalan']['asdp_per_unit']:0;
                    $this->request->data['Ttuj']['uang_kawal_per_unit'] = !empty($data_local['UangJalan']['uang_kawal_per_unit'])?$data_local['UangJalan']['uang_kawal_per_unit']:0;
                    $this->request->data['Ttuj']['uang_keamanan_per_unit'] = !empty($data_local['UangJalan']['uang_keamanan_per_unit'])?$data_local['UangJalan']['uang_keamanan_per_unit']:0;
                    $this->request->data['Ttuj']['uang_jalan_extra_per_unit'] = !empty($data_local['UangJalan']['uang_jalan_extra_per_unit'])?$data_local['UangJalan']['uang_jalan_extra_per_unit']:0;

                    if( !empty($uangKuli) ) {
                        $this->request->data['Ttuj']['uang_kuli_muat_ori'] = !empty($uangKuli['UangKuliMuat']['UangKuli']['uang_kuli'])?$uangKuli['UangKuliMuat']['UangKuli']['uang_kuli']:0;
                        $this->request->data['Ttuj']['uang_kuli_bongkar_ori'] = !empty($uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli'])?$uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli']:0;

                        if( !empty($uangKuli['UangKuliMuat']['UangKuli']['uang_kuli_type']) && $uangKuli['UangKuliMuat']['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
                            $uangJalan['UangJalan']['uang_kuli_muat_per_unit'] = 1;
                        }

                        if( !empty($uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli_type']) && $uangKuli['UangKuliBongkar']['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
                            $uangJalan['UangJalan']['uang_kuli_bongkar_per_unit'] = 1;
                        }
                    }
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

        $customerConditions = array(
            'Customer.customer_type_id' => 2,
            'Customer.status' => 1,
        );

        if( $data_action == 'retail' ) {
            $customerConditions['Customer.customer_type_id'] = 1;
        }

        $this->Truck->bindModel(array(
            'hasOne' => array(
                'Ttuj' => array(
                    'className' => 'Ttuj',
                    'foreignKey' => 'truck_id',
                    'conditions' => array(
                        'Ttuj.status' => 1,
                        'Ttuj.is_pool' => 0,
                        'Ttuj.id <>' => $id,
                    ),
                )
            )
        ));

        $trucks = $this->Truck->getData('list', array(
            'conditions' => array(
                'Ttuj.id' => NULL,
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
            'contain' => array(
                'Ttuj'
            ),
            'order' => array(
                'Truck.nopol'
            ),
        ));

        $customers = $this->Ttuj->Customer->getData('list', array(
            'conditions' => $customerConditions,
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

        $tipeMotors = array();
        $groupTipeMotors = array();
        $tipeMotorTemps = $this->TipeMotor->getData('all', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.name', 'GroupMotor.id',
            ),
        ));

        if( !empty($tipeMotorTemps) ) {
            foreach ($tipeMotorTemps as $key => $tipeMotorTemp) {
                $tipeMotors[$tipeMotorTemp['TipeMotor']['id']] = $tipeMotorTemp['TipeMotor']['name'];
                $groupTipeMotors[$tipeMotorTemp['TipeMotor']['id']] = $tipeMotorTemp['GroupMotor']['id'];
            }
        }

        $colors = $this->ColorMotor->getData('list', array(
            'fields' => array(
                'ColorMotor.id', 'ColorMotor.name',
            ),
        ));
        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_asal' => 1,
            ),
        ));

        $this->set('active_menu', 'ttuj');
        $this->set(compact(
            'trucks', 'customers', 'driverPengantis',
            'fromCities', 'toCities', 'uangJalan',
            'tipeMotors', 'perlengkapans',
            'truckInfo', 'data_local', 'data_action',
            'cities', 'colors', 'tipeMotorTemps',
            'groupTipeMotors', 'uangKuli'
        ));
        $this->render('ttuj_form');
    }

    function ttuj_toggle( $id, $action_type = 'status' ){
        if( in_array('delete_ttuj', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    public function truk_tiba() {
        if( in_array('view_truk_tiba', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'truk_tiba');
            $this->set('sub_module_title', __('Truk Tiba'));
            $this->set('label_tgl', __('Tanggal Tiba'));
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

                if(!empty($refine['date'])){
                    $dateStr = urldecode($refine['date']);
                    $date = explode('-', $dateStr);

                    if( !empty($date) ) {
                        $date[0] = urldecode($date[0]);
                        $date[1] = urldecode($date[1]);
                        $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                        $dateFrom = $this->MkCommon->getDate($date[0]);
                        $dateTo = $this->MkCommon->getDate($date[1]);
                        $conditions['DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m-%d\') >='] = $dateFrom;
                        $conditions['DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Ttuj']['date'] = $dateStr;
                }
            }

            $this->paginate = $this->Ttuj->getData('paginate', array(
                'conditions' => $conditions
            ));
            $ttujs = $this->paginate('Ttuj');

            $this->set('ttujs', $ttujs);
            $this->render('ttuj');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function truk_tiba_add() {
        if( in_array('insert_truk_tiba', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'truk_tiba');
            $this->doTTUJLanjutan();
        } else {
            $this->redirect($this->referer());
        }
    }

    public function ttuj_lanjutan_edit( $action_type = 'truk_tiba', $id = false ) {
        if( in_array(sprintf('update_%s', $action_type), $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'truk_tiba');

            $ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $id
                )
            ));
            $this->doTTUJLanjutan( $action_type, $id, $ttuj );
        } else {
            $this->redirect($this->referer());
        }
    }

    function doTTUJLanjutan( $action_type = 'truk_tiba', $id = false, $ttuj = false ){
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');
        $this->loadModel('ColorMotor');
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

        if( !empty($this->request->data) && ( !empty($data_local) || $id ) ){
            $data = $this->request->data;

            if( !empty($id) ) {
                $this->Ttuj->id = $id;
                $data_local = $ttuj;
            } else {
                $this->Ttuj->id = $data_local['Ttuj']['id'];
            }

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
        } else if( !empty($ttuj) ) {
            $this->request->data = $data_local = $ttuj;
        }

        if( !empty($data_local) ){
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);
            $data_local = $this->MkCommon->generateDateTTUJ($data_local);
            $this->request->data = $data_local;

            if( !empty($id) ) {
                $ttuj_id = $id;
            }
        }

        if( !empty($this->params['named']['no_ttuj']) ) {
            $this->request->data['Ttuj']['no_ttuj'] = $this->params['named']['no_ttuj'];
        }

        $conditionsTtuj = array(
            'Ttuj.status' => 1,
            'Ttuj.is_draft' => 0,
            'Laka.id' => NULL,
        );

        $this->Ttuj->bindModel(array(
            'hasOne' => array(
                'Laka' => array(
                    'className' => 'Laka',
                    'foreignKey' => 'ttuj_id',
                    'conditions' => array(
                        'Laka.status' => 1,
                    ),
                )
            )
        ));

        switch ($action_type) {
            case 'bongkaran':
                $this->set('sub_module_title', __('Tambah Bongkaran'));
                if( !empty($id) ) {
                    $conditionsTtuj['OR'] = array(
                        array(
                            'Ttuj.id' => $id,
                        ),
                        array(
                            'Ttuj.is_arrive' => 1,
                            'Ttuj.is_bongkaran <>' => 1,
                        ),
                    );
                } else {
                    $conditionsTtuj['Ttuj.is_arrive'] = 1;
                    $conditionsTtuj['Ttuj.is_bongkaran <>'] = 1;
                }
                break;

            case 'balik':
                if( !empty($id) ) {
                    $conditionsTtuj['OR'] = array(
                        array(
                            'Ttuj.id' => $id,
                        ),
                        array(
                            'Ttuj.is_arrive' => 1,
                            'Ttuj.is_bongkaran' => 1,
                            'Ttuj.is_balik <>' => 1,
                        ),
                    );
                } else {
                    $conditionsTtuj['Ttuj.is_arrive'] = 1;
                    $conditionsTtuj['Ttuj.is_bongkaran'] = 1;
                    $conditionsTtuj['Ttuj.is_balik <>'] = 1;
                }
                break;

            case 'pool':
                if( !empty($id) ) {
                    $conditionsTtuj['OR'] = array(
                        array(
                            'Ttuj.id' => $id,
                        ),
                        array(
                            'Ttuj.is_arrive' => 1,
                            'Ttuj.is_bongkaran' => 1,
                            'Ttuj.is_balik' => 1,
                            'Ttuj.is_pool <>' => 1,
                        ),
                    );
                } else {
                    $conditionsTtuj['Ttuj.is_arrive'] = 1;
                    $conditionsTtuj['Ttuj.is_bongkaran'] = 1;
                    $conditionsTtuj['Ttuj.is_balik'] = 1;
                    $conditionsTtuj['Ttuj.is_pool <>'] = 1;
                }
                break;
            
            default:
                if( !empty($id) ) {
                    $conditionsTtuj['OR'] = array(
                        array(
                            'Ttuj.id' => $id,
                        ),
                        array(
                            'Ttuj.is_arrive' => 0,
                        ),
                    );
                } else {
                    $conditionsTtuj['Ttuj.is_arrive'] = 0;
                }
                break;
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'conditions' => $conditionsTtuj,
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'contain' => array(
                'Laka'
            ),
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
        $tipeMotors = $this->TipeMotor->getData('list', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.name',
            ),
        ));
        $colors = $this->ColorMotor->getData('list', array(
            'fields' => array(
                'ColorMotor.id', 'ColorMotor.name',
            ),
        ));

        $this->set(compact(
            'ttujs', 'data_local', 'perlengkapans', 
            'tipeMotors', 'action_type', 'data_action',
            'colors', 'ttuj', 'id'
        ));
        $this->render('ttuj_lanjutan_form');
    }

    public function info_truk( $action_type = 'truk_tiba', $ttuj_id = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('ColorMotor');
        $conditions = array(
            'Ttuj.id' => $ttuj_id,
            'Ttuj.is_draft' => 0,
            'Ttuj.status' => 1,
        );

        switch ($action_type) {
            case 'bongkaran':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $module_title = __('Info Bongkaran');
                $this->set('active_menu', 'bongkaran');
                break;

            case 'balik':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                $module_title = __('Info Truk Balik');
                $this->set('active_menu', 'balik');
                break;

            case 'pool':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                $conditions['Ttuj.is_pool'] = 1;
                $module_title = __('Info Sampai Pool');
                $this->set('active_menu', 'pool');
                break;
            
            default:
                $conditions['Ttuj.is_arrive'] = 1;
                $module_title = __('Info Truk Tiba');
                $this->set('active_menu', 'truk_tiba');
                break;
        }

        $data_action = false;
        $data_local = $this->Ttuj->getData('first', array(
            'conditions' => $conditions
        ));

        if( !empty($data_local) ){
            if( !empty($data_local['Ttuj']['is_retail']) ) {
                $module_title = __('Info Truk Tiba - RETAIL');
                $data_action = 'retail';
            }

            $data_local['Ttuj']['ttuj_date'] = date('d/m/Y', strtotime($data_local['Ttuj']['ttuj_date']));
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
            $tipeMotors = $this->TipeMotor->getData('list', array(
                'fields' => array(
                    'TipeMotor.id', 'TipeMotor.name',
                ),
            ));
            $colors = $this->ColorMotor->getData('list', array(
                'fields' => array(
                    'ColorMotor.id', 'ColorMotor.name',
                ),
            ));

            $this->set('sub_module_title', $module_title);
            $this->set(compact(
                'ttujs', 'data_local', 'perlengkapans', 
                'tipeMotors', 'ttuj_id', 'action_type',
                'data_action', 'colors'
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
        if( in_array('view_bongkaran', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'bongkaran');
            $this->set('sub_module_title', __('Bongkaran'));
            $this->set('label_tgl', __('Tanggal Bongkaran'));
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

                if(!empty($refine['date'])){
                    $dateStr = urldecode($refine['date']);
                    $date = explode('-', $dateStr);

                    if( !empty($date) ) {
                        $date[0] = urldecode($date[0]);
                        $date[1] = urldecode($date[1]);
                        $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                        $dateFrom = $this->MkCommon->getDate($date[0]);
                        $dateTo = $this->MkCommon->getDate($date[1]);
                        $conditions['DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m-%d\') >='] = $dateFrom;
                        $conditions['DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Ttuj']['date'] = $dateStr;
                }
            }

            $this->paginate = $this->Ttuj->getData('paginate', array(
                'conditions' => $conditions
            ));
            $ttujs = $this->paginate('Ttuj');

            $this->set('ttujs', $ttujs);
            $this->render('ttuj');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function bongkaran_add() {
        if( in_array('insert_bongkaran', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('sub_module_title', __('Bongkaran'));
            $this->set('active_menu', 'bongkaran');
            $this->doTTUJLanjutan( 'bongkaran' );
        } else {
            $this->redirect($this->referer());
        }
    }

    public function balik() {
        if( in_array('view_balik', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'balik');
            $this->set('sub_module_title', __('Balik'));
            $this->set('label_tgl', __('Tanggal Balik'));
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

                if(!empty($refine['date'])){
                    $dateStr = urldecode($refine['date']);
                    $date = explode('-', $dateStr);

                    if( !empty($date) ) {
                        $date[0] = urldecode($date[0]);
                        $date[1] = urldecode($date[1]);
                        $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                        $dateFrom = $this->MkCommon->getDate($date[0]);
                        $dateTo = $this->MkCommon->getDate($date[1]);
                        $conditions['DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m-%d\') >='] = $dateFrom;
                        $conditions['DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Ttuj']['date'] = $dateStr;
                }
            }

            $this->paginate = $this->Ttuj->getData('paginate', array(
                'conditions' => $conditions
            ));
            $ttujs = $this->paginate('Ttuj');

            $this->set('ttujs', $ttujs);
            $this->render('ttuj');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function balik_add() {
        if( in_array('insert_balik', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('sub_module_title', __('Tambah TTUJ Balik'));
            $this->set('active_menu', 'balik');
            $this->doTTUJLanjutan( 'balik' );
        } else {
            $this->redirect($this->referer());
        }
    }

    public function pool() {
        if( in_array('view_pool', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'pool');
            $this->set('sub_module_title', __('Sampai di Pool'));
            $this->set('label_tgl', __('Tanggal Sampai Pool'));
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

                if(!empty($refine['date'])){
                    $dateStr = urldecode($refine['date']);
                    $date = explode('-', $dateStr);

                    if( !empty($date) ) {
                        $date[0] = urldecode($date[0]);
                        $date[1] = urldecode($date[1]);
                        $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                        $dateFrom = $this->MkCommon->getDate($date[0]);
                        $dateTo = $this->MkCommon->getDate($date[1]);
                        $conditions['DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='] = $dateFrom;
                        $conditions['DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Ttuj']['date'] = $dateStr;
                }
            }

            $this->paginate = $this->Ttuj->getData('paginate', array(
                'conditions' => $conditions
            ));
            $ttujs = $this->paginate('Ttuj');

            $this->set('ttujs', $ttujs);
            $this->render('ttuj');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function pool_add() {
        if( in_array('insert_pool', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->set('sub_module_title', __('TTUJ Sampai Pool'));
            $this->set('active_menu', 'pool');
            $this->doTTUJLanjutan( 'pool' );
        } else {
            $this->redirect($this->referer());
        }
    }

    public function ritase_report( $data_type = 'depo' ) {
        if( $data_type == 'retail' ) {
            $module_name = 'view_ritase_retail_report';
        } else {
            $module_name = 'view_ritase_depo_report';
        }

        if( in_array($module_name, $this->allowModule) ) {
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
            $data_action = false;

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
                        $date[0] = urldecode($date[0]);
                        $date[1] = urldecode($date[1]);
                        $dateFrom = $this->MkCommon->getDate($date[0]);
                        $dateTo = $this->MkCommon->getDate($date[1]);
                    }
                    $this->request->data['Ttuj']['date'] = $dateStr;
                }

                if(!empty($refine['data_action'])){
                    $data_action = $refine['data_action'];
                }
            }

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
            $options = $this->TruckCustomer->getData('paginate', array(
                'conditions' => $conditions,
                'order' => array(
                    'CustomerNoType.order_sort' => 'ASC', 
                    'Truck.nopol' => 'ASC', 
                ),
                'contain' => array(
                    'Truck',
                    'CustomerNoType',
                ),
            ));

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }
            $this->paginate = $options;
            $trucks = $this->paginate('TruckCustomer');

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
                $this->request->data['Ttuj']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
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
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    public function achievement_report( $data_action = false ) {
        if( in_array('view_achievement_report', $this->allowModule) ) {
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

            $options = $this->Customer->getData('paginate', array(
                'conditions' => array(
                    'Customer.status' => 1,
                ),
            ));

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }

            $this->paginate = $options;
            $ttujs = $this->paginate('Customer');
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
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    public function monitoring_truck( $data_action = false ) {
        if( in_array('view_monitoring_truck', $this->allowModule) ) {
            $this->loadModel('Customer');
            $this->loadModel('TruckCustomer');
            $this->loadModel('Truck');
            $this->loadModel('Ttuj');
            $this->loadModel('TtujTipeMotor');
            $this->loadModel('CalendarEvent');
            $this->loadModel('Laka');
            $this->loadModel('Setting');
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
                            $thisMonth = sprintf("%02s", $monthNumber['month']);
                        }

                        if( !empty($monthArr[1]) && !empty($thisMonth) ) {
                            $currentMonth = sprintf("%s-%s", $monthArr[1], $thisMonth);
                        }
                    }
                }
            }

            $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
            $thisMonth = !empty($thisMonth)?$thisMonth:date('m');
            $prevMonth = date('Y-m', mktime(0, 0, 0, date("m", strtotime($currentMonth))-1 , 1, date("Y", strtotime($currentMonth))));
            $nextMonth = date('Y-m', mktime(0, 0, 0, date("m", strtotime($currentMonth))+1 , 1, date("Y", strtotime($currentMonth))));
            $leftDay = date('N', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , 0, date("Y", strtotime($currentMonth))));
            $lastDay = date('t', strtotime($currentMonth));
            $customerId = array();
            $lakas = $this->Laka->getData('list', array(
                'conditions' => array(
                    'Laka.status'=> 1,
                    'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\') <=' => $currentMonth,
                    'OR' => array(
                        'DATE_FORMAT(Laka.completed_date, \'%Y-%m\') >=' => $currentMonth,
                        'Laka.completed_date' => NULL,
                    ),
                ),
                'order' => array(
                    'Laka.tgl_laka' => 'ASC', 
                ),
                'fields' => array(
                    'Laka.id', 'Laka.ttuj_id'
                ),
            ));
            $lakas = array_values($lakas);
            $lakas = array_unique($lakas);
            $conditions = array(
                'Ttuj.status'=> 1,
                'Ttuj.is_draft'=> 0,
                'OR' => array(
                    'DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m\')' => $currentMonth,
                    'DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m\')' => $currentMonth,
                    'DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m\')' => $currentMonth,
                    'DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m\')' => $currentMonth,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\')' => $currentMonth,
                    'Ttuj.id' => $lakas,
                ),
            );
            $conditionEvents = array(
                'CalendarEvent.status'=> 1,
                'DATE_FORMAT(CalendarEvent.from_date, \'%Y-%m\')' => $currentMonth,
            );
            $conditionTrucks = array(
                'Truck.status' => 1
            );
            $setting = $this->Setting->find('first');

            if( !empty($this->params['named']) ) {
                $refine = $this->params['named'];

                if( !empty($refine['monitoring_customer_id']) ) {
                    $refine['monitoring_customer_id'] = urldecode($refine['monitoring_customer_id']);
                    $customerId = explode(',', $refine['monitoring_customer_id']);
                    // $conditions['Ttuj.customer_id'] = $customerId;
                    $conditionTrucks['TruckCustomerWithOrder.customer_id'] = $customerId;
                }
            }

            $this->Truck->bindModel(array(
                'hasOne' => array(
                    'TruckCustomerWithOrder' => array(
                        'className' => 'TruckCustomerWithOrder',
                        'foreignKey' => 'truck_id',
                        'conditions' => array(
                            'TruckCustomerWithOrder.primary'=> 1,
                        ),
                        'order' => array(
                            'TruckCustomerWithOrder.primary' => 'DESC'
                        ),
                    ),
                    'CustomerNoType'=>array(
                        'foreignKey'=> false,
                        'type'=>'INNER',
                        'conditions'=>array(
                            'CustomerNoType.id = TruckCustomerWithOrder.customer_id',
                            'CustomerNoType.status'=> 1,
                        ),
                    ),
                )
            ));

            $this->paginate = $this->Truck->getData('paginate', array(
                'conditions' => $conditionTrucks,
                'contain' => array(
                    'TruckCustomerWithOrder',
                    'CustomerNoType',
                ),
                'order' => array(
                    'CustomerNoType.order_sort' => 'ASC',
                    'CustomerNoType.order' => 'ASC',
                    'Truck.nopol' => 'ASC',
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
                    'CalendarEvent.from_date' => 'ASC', 
                ),
            ));
            $dataTtuj = array();
            $dataEvent = array();
            $dataRit = array();

            if( !empty($ttujs) ) {
                foreach ($ttujs as $key => $value) {
                    $inArr = array();
                    $value = $this->Laka->getMergeTtuj($value['Ttuj']['id'], $value, array(
                        'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\')' => $currentMonth,
                    ));
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
                    $date = date('Y-m-d', strtotime($value['Ttuj']['tgljam_berangkat']));
                    $tglBerangkat = $this->MkCommon->customDate($value['Ttuj']['tgljam_berangkat'], 'Y-m-d H:i:s');
                    $tglTiba = $this->MkCommon->customDate($value['Ttuj']['tgljam_tiba'], 'Y-m-d H:i:s');
                    $tglBongkaran = $this->MkCommon->customDate($value['Ttuj']['tgljam_bongkaran'], 'Y-m-d H:i:s');
                    $tglBalik = $this->MkCommon->customDate($value['Ttuj']['tgljam_balik'], 'Y-m-d H:i:s');
                    $tglPool = $this->MkCommon->customDate($value['Ttuj']['tgljam_pool'], 'Y-m-d H:i:s');
                    $lakaDate = false;
                    $i = 0;
                    $differentTtuj = false;
                    $currMonth = date('Y-m', strtotime($date));

                    if( !empty($value['Laka']['id']) ) {
                        $lakaDate = date('Y-m-d', strtotime($value['Laka']['tgl_laka']));
                        $addClass = 'pool';
                        $urlTtuj = array(
                            'controller' => 'lakas',
                            'action' => 'edit',
                            $value['Laka']['id'],
                        );

                        if( !empty($value['Laka']['completed']) ) {
                            $end_date = date('Y-m-d', strtotime($value['Laka']['completed_date']));
                        } else if( date('Y-m-d') >= $lakaDate ) {
                            $end_date = date('Y-m-d', strtotime("-1 day"));
                        } else {
                            $end_date = $lakaDate;
                        }
                    } else if( !empty($value['Ttuj']['is_pool']) ) {
                        $titleTtuj = __('Sampai Pool');
                        $toDate = $value['Ttuj']['tgljam_pool'];
                        $end_date = date('Y-m-d', strtotime($toDate));
                        $addClass = 'pool';
                        $urlTtuj = array(
                            'controller' => 'revenues',
                            'action' => 'info_truk',
                            'pool',
                            $value['Ttuj']['id'],
                        );
                    } else if( !empty($value['Ttuj']['is_balik']) ) {
                        $titleTtuj = __('Balik');
                        $addClass = 'balik';
                        $urlTtuj = array(
                            'controller' => 'revenues',
                            'action' => 'info_truk',
                            'balik',
                            $value['Ttuj']['id'],
                        );
                    } else if( !empty($value['Ttuj']['is_bongkaran']) ) {
                        $titleTtuj = __('Bongkaran');
                        $urlTtuj = array(
                            'controller' => 'revenues',
                            'action' => 'info_truk',
                            'bongkaran',
                            $value['Ttuj']['id'],
                        );
                    } else if( !empty($value['Ttuj']['is_arrive']) ) {
                        $titleTtuj = __('Berangkat');
                        $urlTtuj = array(
                            'controller' => 'revenues',
                            'action' => 'info_truk',
                            'truk_tiba',
                            $value['Ttuj']['id'],
                        );
                    } else if( empty($value['Ttuj']['is_draft']) ) {
                        $urlTtuj = array(
                            'controller' => 'revenues',
                            'action' => 'ttuj_edit',
                            $value['Ttuj']['id'],
                        );
                    }

                    if( empty($value['Laka']['id']) && empty($value['Ttuj']['is_pool']) ) {
                        if( date('Y-m-d') >= $date ) {
                            $end_date = date('Y-m-d', strtotime("-1 day"));
                        } else {
                            $end_date = $date;
                        }
                    }

                    $dataTtujCalendar = array_merge($dataTmp, array(
                        'id' => $value['Ttuj']['id'],
                        'title' => __('Berangkat'),
                        'from_date' => $this->MkCommon->customDate($tglBerangkat, 'd/m/Y - H:i'),
                        'to_date' => !empty($tglPool)?$this->MkCommon->customDate($tglPool, 'd/m/Y - H:i'):'-',
                        'url' => $urlTtuj,
                    ));

                    if( !empty($value['Laka']['id']) ) {
                        $dataTtujCalendar = array_merge($dataTtujCalendar, array(
                            'is_laka' => true,
                            'laka_date' => $this->MkCommon->customDate($value['Laka']['tgl_laka'], 'd/m/Y'),
                            'laka_completed_date' => !empty($value['Laka']['completed_date'])?$this->MkCommon->customDate($value['Laka']['completed_date'], 'd/m/Y'):false,
                            'driver_name' => $value['Laka']['driver_name'],
                            'lokasi_laka' => $value['Laka']['lokasi_laka'],
                            'truck_condition' => $value['Laka']['truck_condition'],
                        ));
                    }
                    if( !empty($tglTiba) ) {
                        $dataTtujCalendar['tglTiba'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_tiba'], 'd/m/Y - H:i');
                    }
                    if( !empty($tglBongkaran) ) {
                        $dataTtujCalendar['tglBongkaran'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_bongkaran'], 'd/m/Y - H:i');
                    }
                    if( !empty($tglBalik) ) {
                        $dataTtujCalendar['tglBalik'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_balik'], 'd/m/Y - H:i');
                    }

                    if( !empty($lakaDate) && $this->MkCommon->customDate($lakaDate, 'Y-m') == $currMonth && $this->MkCommon->customDate($lakaDate, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($lakaDate, 'd'), $inArr) ) {
                        $dataTtujCalendar['title'] = __('LAKA');
                        $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_laka'])?$setting['Setting']['icon_laka']:'';
                        $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                        $dataTtujCalendar['color'] = '#dd545f';
                        $dataTtuj[$nopol][$this->MkCommon->customDate($lakaDate, 'm')][$this->MkCommon->customDate($lakaDate, 'd')][] = $dataTtujCalendar;
                        $differentTtuj = true;
                        $inArr[] = $this->MkCommon->customDate($lakaDate, 'd');
                    }
                    if( !empty($tglPool) && $this->MkCommon->customDate($tglPool, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglPool, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglPool, 'd'), $inArr) ) {
                        $dataTtujCalendar['title'] = __('Sampai Pool');
                        $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_pool'])?$setting['Setting']['icon_pool']:'';
                        $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                        $dataTtujCalendar['color'] = '#00a65a';
                        $dataTtuj[$nopol][$this->MkCommon->customDate($tglPool, 'm')][$this->MkCommon->customDate($tglPool, 'd')][] = $dataTtujCalendar;
                        $differentTtuj = true;
                        $inArr[] = $this->MkCommon->customDate($tglPool, 'd');
                        $dataRit[$nopol]['rit'][$this->MkCommon->customDate($tglPool, 'm')][$this->MkCommon->customDate($tglPool, 'd')][] = $tglPool;
                    }
                    if( !empty($tglBalik) && $this->MkCommon->customDate($tglBalik, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglBalik, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglBalik, 'd'), $inArr) ) {
                        $dataTtujCalendar['title'] = __('Balik');
                        $dataTtujCalendar['icon'] = '/img/on-the-way.gif';
                        $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                        $dataTtujCalendar['color'] = '#3d9970';
                        $dataTtuj[$nopol][$this->MkCommon->customDate($tglBalik, 'm')][$this->MkCommon->customDate($tglBalik, 'd')][] = $dataTtujCalendar;
                        $differentTtuj = true;
                        $inArr[] = $this->MkCommon->customDate($tglBalik, 'd');
                    }
                    if( !empty($tglBongkaran) && $this->MkCommon->customDate($tglBongkaran, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglBongkaran, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglBongkaran, 'd'), $inArr) ) {
                        $dataTtujCalendar['title'] = __('Bongkaran');
                        $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_bongkaran'])?$setting['Setting']['icon_bongkaran']:'';
                        $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                        $dataTtujCalendar['color'] = '#d3e3d4';
                        $dataTtuj[$nopol][$this->MkCommon->customDate($tglBongkaran, 'm')][$this->MkCommon->customDate($tglBongkaran, 'd')][] = $dataTtujCalendar;
                        $differentTtuj = true;
                        $inArr[] = $this->MkCommon->customDate($tglBongkaran, 'd');
                    }
                    if( !empty($tglTiba) && $this->MkCommon->customDate($tglTiba, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglTiba, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglTiba, 'd'), $inArr) ) {
                        $dataTtujCalendar['title'] = __('Tiba');
                        $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_tiba'])?$setting['Setting']['icon_tiba']:'';
                        $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                        $dataTtujCalendar['color'] = '#f39c12';
                        $dataTtuj[$nopol][$this->MkCommon->customDate($tglTiba, 'm')][$this->MkCommon->customDate($tglTiba, 'd')][] = $dataTtujCalendar;
                        $differentTtuj = true;
                        $inArr[] = $this->MkCommon->customDate($tglTiba, 'd');
                    }

                    while (strtotime($date) <= strtotime($end_date)) {
                        $currMonth = date('Y-m', strtotime($date));

                        // if( $currMonth == $currentMonth ) {
                            $currDay = date('d', strtotime($date));
                            $currMonthly = date('m', strtotime($date));
                            
                            // if( !empty($value['Laka']['id']) ) {
                            //     $dataTtuj[$nopol][$currDay][] = array(
                            //         'is_laka' => true,
                            //         'from_date' => $this->MkCommon->customDate($value['Laka']['tgl_laka'], 'd/m/Y - H:i'),
                            //         'to_date' => !empty($value['Laka']['completed_date'])?$this->MkCommon->customDate($value['Laka']['completed_date'], 'd/m/Y - H:i'):'-',
                            //         'driver_name' => $value['Laka']['driver_name'],
                            //         'lokasi_laka' => $value['Laka']['lokasi_laka'],
                            //         'truck_condition' => $value['Laka']['truck_condition'],
                            //         'icon' => ( empty($i) || ( $date == $end_date && !empty($value['Laka']['completed']) ) )?'/img/accident.png':false,
                            //         'iconPopup' => '/img/accident.png',
                            //         'color' => $color,
                            //         'url' => array(
                            //             'controller' => 'lakas',
                            //             'action' => 'edit',
                            //             $value['Laka']['id'],
                            //         ),
                            //     );
                            // }
                            if( !in_array($currDay, $inArr) ) {
                                $popIcon = false;

                                if( !empty($lakaDate) && $this->MkCommon->customDate($lakaDate, 'Y-m-d') <= $date ) {
                                    $dataTtujCalendar['color'] = '#dd545f';
                                    $icon = !empty($setting['Setting']['icon_laka'])?$setting['Setting']['icon_laka']:'';
                                } else if( !empty($tglPool) && $this->MkCommon->customDate($tglPool, 'Y-m-d') <= $date ) {
                                    $dataTtujCalendar['color'] = '#00a65a';
                                    $icon = !empty($setting['Setting']['icon_pool'])?$setting['Setting']['icon_pool']:'';
                                    $dataRit[$nopol]['rit'][$currMonthly][$currDay][] = $tglPool;
                                } else if( !empty($tglBalik) && $this->MkCommon->customDate($tglBalik, 'Y-m-d') <= $date ) {
                                    $dataTtujCalendar['color'] = '#3d9970';
                                    $icon = !empty($setting['Setting']['icon_balik'])?$setting['Setting']['icon_balik']:'';
                                } else if( !empty($tglBongkaran) && $this->MkCommon->customDate($tglBongkaran, 'Y-m-d') <= $date ) {
                                    $dataTtujCalendar['color'] = '#d3e3d4';
                                    $icon = !empty($setting['Setting']['icon_bongkaran'])?$setting['Setting']['icon_bongkaran']:'';
                                } else if( !empty($tglTiba) && $this->MkCommon->customDate($tglTiba, 'Y-m-d') <= $date ) {
                                    $dataTtujCalendar['color'] = '#f39c12';
                                    $icon = !empty($setting['Setting']['icon_tiba'])?$setting['Setting']['icon_tiba']:'';
                                } else {
                                    $dataTtujCalendar['color'] = '#4389fe';
                                    $icon = !empty($setting['Setting']['icon_berangkat'])?$setting['Setting']['icon_berangkat']:'';
                                }

                                if( $differentTtuj ) {
                                    if( empty($i) || ( $date == $end_date && !empty($toDate) ) ) {
                                        $dataTtujCalendar['icon'] = $icon;
                                        $popIcon = $dataTtujCalendar['icon'];
                                    } else {
                                        $popIcon = $icon;
                                        $dataTtujCalendar['icon'] = false;
                                    }
                                } else {
                                    if( empty($i) || ( $date == $end_date && !empty($toDate) ) ) {
                                        $popIcon = $icon;
                                        $dataTtujCalendar['icon'] = $icon;
                                    } else {
                                        $popIcon = $icon;
                                        $dataTtujCalendar['icon'] = false;
                                    }
                                }

                                $dataTtujCalendar['iconPopup'] = $popIcon;
                                $dataTtuj[$nopol][$currMonthly][$currDay][] = $dataTtujCalendar;
                            }

                            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                            $i++;
                        // } else {
                        //     break;
                        // }
                    }
                }
            }

            if( !empty($events) ) {
                foreach ($events as $key => $event) {
                    $date = date('Y-m-d', strtotime($event['CalendarEvent']['from_date']));
                    $end_date = date('Y-m-d', strtotime($event['CalendarEvent']['to_date']));
                    $i = 0;
                     
                    while (strtotime($date) <= strtotime($end_date)) {
                        if( date('Y-m', strtotime($date)) == $currentMonth ) {
                            $toDate = date('Y-m-d', strtotime($event['CalendarEvent']['to_date']));
                            $dataEvent[$event['CalendarEvent']['nopol']][date('m', strtotime($date))][date('d', strtotime($date))][] = array(
                                'id' => $event['CalendarEvent']['id'],
                                'from_date' => $this->MkCommon->customDate($event['CalendarEvent']['from_date'], 'd/m/Y - H:i'),
                                'to_date' => $this->MkCommon->customDate($event['CalendarEvent']['to_date'], 'd/m/Y - H:i'),
                                'title' => $event['CalendarEvent']['name'],
                                'note' => $event['CalendarEvent']['note'],
                                'color' => !empty($event['CalendarColor']['hex'])?$event['CalendarColor']['hex']:false,
                                'icon' => (!empty($event['CalendarIcon']['photo']) && ( empty($i) || $date == $toDate ))?$event['CalendarIcon']['photo']:false,
                                'iconPopup' => ( !empty($event['CalendarIcon']['photo']) )?$event['CalendarIcon']['photo']:false,
                            );

                            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                            $i++;
                        } else {
                            break;
                        }
                    }
                }
            }
            $customers = array();
            $truckCustomers = $this->TruckCustomer->getData('all', array(
                'conditions' => array(
                    'Truck.status' => 1,
                    'TruckCustomer.primary' => 1,
                ),
                'fields' => array(
                    'TruckCustomer.id', 'TruckCustomer.customer_id'
                ),
                'contain' => array(
                    'Truck',
                ),
            ));

            if( !empty($truckCustomers) ) {
                foreach ($truckCustomers as $key => $customer) {
                    $customer = $this->Customer->getMerge($customer, $customer['TruckCustomer']['customer_id']);

                    if( !empty($customer['Customer']) )
                    $customers[$customer['Customer']['id']] = $customer['Customer']['customer_name'];
                }
            }

            $this->set(compact(
                'data_action', 'lastDay', 'currentMonth',
                'trucks', 'prevMonth', 'nextMonth',
                'dataTtuj', 'dataEvent', 'customers',
                'customerId', 'dataRit', 'thisMonth'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function index(){
        if( in_array('view_revenues', $this->allowModule) ) {
            $this->loadModel('Revenue');
            $this->loadModel('Ttuj');
            $this->set('active_menu', 'revenues');
            $this->set('sub_module_title', __('Revenue'));

            $from_date = '';
            $to_date = '';
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
                if(!empty($refine['customer'])){
                    $customer = urldecode($refine['customer']);
                    $this->request->data['Revenue']['customer_id'] = $customer;
                    $conditions['Revenue.customer_id'] = $customer;
                }
                if(!empty($refine['no_ref'])){
                    $no_ref = urldecode($refine['no_ref']);
                    $this->request->data['RevenueDetail']['no_reference'] = $no_ref;

                    if( is_numeric($no_ref) ) {
                        $no_ref = intval($no_ref);
                    }

                    $conditions['Revenue.id LIKE'] = '%'.$no_ref.'%';
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
                        $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') >='] = $dateFrom;
                        $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Revenue']['date'] = $dateStr;
                }

                if(!empty($refine['nopol'])){
                    $nopol = urldecode($refine['nopol']);
                    $this->request->data['Ttuj']['nopol'] = $nopol;
                    $conditions['Ttuj.nopol LIKE '] = '%'.$nopol.'%';
                }

                if(!empty($refine['status'])){
                    $status = urldecode($refine['status']);

                    if( $status == 'paid' ) {
                        $this->request->data['Revenue']['transaction_status'] = $status;

                        $revenueList = $this->Revenue->getData('list', array(
                            'conditions' => $conditions,
                            'contain' => array(
                                'Ttuj',
                            ),
                            'fields' => array(
                                'Revenue.id', 'Revenue.id'
                            ),
                        ));
                        $paidList = $this->Revenue->InvoiceDetail->getInvoicedRevenueList($revenueList);
                        $conditions['Revenue.id'] = $paidList;
                    } else {
                        $this->request->data['Revenue']['transaction_status'] = $status;
                        $conditions['Revenue.transaction_status'] = $status;
                    }
                }
            }

            $this->paginate = $this->Revenue->getData('paginate', array(
                'conditions' => $conditions,
                'contain' => array(
                    'Ttuj',
                )
            ));
            $revenues = $this->paginate('Revenue');

            if(!empty($revenues)){
                foreach ($revenues as $key => $value) {
                    $value = $this->Revenue->InvoiceDetail->getInvoicedRevenue($value, $value['Revenue']['id']);
                    $value = $this->Ttuj->Customer->getMerge($value, $value['Ttuj']['customer_id']);
                    $revenues[$key] = $this->Ttuj->Customer->getMerge($value, $value['Ttuj']['customer_id']);
                }
            }
            $this->set('revenues', $revenues); 

            $this->loadModel('Customer');
            $customers = $this->Customer->getData('list', array(
                'conditions' => array(
                    'Customer.status' => 1
                ),
                'fields' => array(
                    'Customer.id', 'Customer.customer_name'
                ),
            ));
            $this->set('customers', $customers);
        } else {
            $this->redirect($this->referer());
        }
    }

    function revenue_add(){
        if( in_array('insert_revenues', $this->allowModule) ) {
            $this->loadModel('Revenue');
            $module_title = __('Tambah Revenue');
            $this->set('sub_module_title', trim($module_title));
            $this->doTTUJ();
        } else {
            $this->redirect($this->referer());
        }
    }

    function revenue_edit( $id ){
        if( in_array('update_revenues', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function add(){
        if( in_array('insert_revenues', $this->allowModule) ) {
            $this->loadModel('Revenue');
            $module_title = __('Tambah Revenue');
            $this->set('sub_module_title', trim($module_title));
            $this->doRevenue();
        } else {
            $this->redirect($this->referer());
        }
    }

    function edit( $id ){
        if( in_array('update_revenues', $this->allowModule) ) {
            $this->loadModel('Revenue');
            $revenue = $this->Revenue->getData('first', array(
                'conditions' => array(
                    'Revenue.id' => $id
                ),
                'contain' => array(
                    'Ttuj'
                )
            ));

            if(!empty($revenue)){
                $revenue = $this->Revenue->RevenueDetail->getMergeAll( $revenue, $revenue['Revenue']['id'] );
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function doRevenue($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TarifAngkutan');
        $this->loadModel('City');
        $this->loadModel('GroupMotor');
        $this->loadModel('TtujTipeMotorUse');

        $data_revenue_detail = array();
        $i = 0;
        $no_ref = '';

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['Revenue']['date_sj'] = !empty($data['Revenue']['date_sj']) ? date('Y-m-d', strtotime($data['Revenue']['date_sj'])) : '';
            $data['Revenue']['date_revenue'] = $this->MkCommon->getDate($data['Revenue']['date_revenue']);
            $data['Revenue']['ppn'] = !empty($data['Revenue']['ppn'])?$data['Revenue']['ppn']:0;
            $data['Revenue']['pph'] = !empty($data['Revenue']['pph'])?$data['Revenue']['pph']:0;
            $data['Revenue']['additional_charge'] = !empty($data['Revenue']['additional_charge'])?$data['Revenue']['additional_charge']:0;
            $tarif_angkutan_types = !empty($data['RevenueDetail']['tarif_angkutan_type'])?$data['RevenueDetail']['tarif_angkutan_type']:array();
            $dataRevenues = array();
            $flagSave = array();
            $dataTtuj = array();
            $checkQty = true;

            if( !empty($tarif_angkutan_types) ) {
                $tarif_angkutan_types = array_unique($tarif_angkutan_types);

                foreach ($tarif_angkutan_types as $key => $tarif_angkutan_type) {
                    $dataRevenue = $data;
                    $dataRevenue['Revenue']['type'] = $tarif_angkutan_type;
                    $dataRevenuDetail = array();

                    // if( !empty($i) ) {
                    //     $dataRevenue['Revenue']['no_doc'] .= '/'.chr(64+$i);
                    // }

                    if( !empty($dataRevenue['RevenueDetail']['tarif_angkutan_type']) ) {
                        $idx = 0;

                        foreach ($dataRevenue['RevenueDetail']['tarif_angkutan_type'] as $keyDetail => $revenueDetail) {
                            $tarifType = isset($data['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:false;

                            if( $tarifType == $tarif_angkutan_type ) {
                                $dataRevenuDetail['RevenueDetail']['city_id'][$idx] = isset($data['RevenueDetail']['city_id'][$keyDetail])?$data['RevenueDetail']['city_id'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['tarif_angkutan_id'][$idx] = isset($data['RevenueDetail']['tarif_angkutan_id'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_id'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['tarif_angkutan_type'][$idx] = isset($data['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['no_do'][$idx] = isset($data['RevenueDetail']['no_do'][$keyDetail])?$data['RevenueDetail']['no_do'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['no_sj'][$idx] = isset($data['RevenueDetail']['no_sj'][$keyDetail])?$data['RevenueDetail']['no_sj'][$keyDetail]:false;
                                // $dataRevenuDetail['RevenueDetail']['note'][$idx] = isset($data['RevenueDetail']['note'][$keyDetail])?$data['RevenueDetail']['note'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['group_motor_id'][$idx] = isset($data['RevenueDetail']['group_motor_id'][$keyDetail])?$data['RevenueDetail']['group_motor_id'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['qty_unit'][$idx] = isset($data['RevenueDetail']['qty_unit'][$keyDetail])?$data['RevenueDetail']['qty_unit'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['payment_type'][$idx] = isset($data['RevenueDetail']['payment_type'][$keyDetail])?$data['RevenueDetail']['payment_type'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['is_charge'][$idx] = isset($data['RevenueDetail']['is_charge'][$keyDetail])?$data['RevenueDetail']['is_charge'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['price_unit'][$idx] = isset($data['RevenueDetail']['price_unit'][$keyDetail])?$data['RevenueDetail']['price_unit'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['total_price_unit'][$idx] = isset($data['RevenueDetail']['total_price_unit'][$keyDetail])?$data['RevenueDetail']['total_price_unit'][$keyDetail]:false;
                                $idx++;
                            }
                        }
                    }

                    unset($dataRevenue['RevenueDetail']);
                    $dataRevenue['RevenueDetail'] = !empty($dataRevenuDetail['RevenueDetail'])?$dataRevenuDetail['RevenueDetail']:false;
                    $dataRevenues[$key] = $dataRevenue;
                    $i++;
                }
            }
            
            if( !empty($dataRevenues) ) {
                if($id && $data_local){
                    $this->Revenue->id = $id;
                    $msg = 'merubah';
                }else{
                    $this->loadModel('Revenue');
                    $this->Revenue->create();
                    $msg = 'membuat';
                }

                foreach ($dataRevenues as $key => $dataRevenue) {
                    /*validasi revenue detail*/
                    $validate_detail = true;
                    $validate_qty = true;
                    $total_revenue = 0;
                    $total_qty = 0;
                    $array_ttuj_tipe_motor = array();

                    if( !empty($dataRevenue['Ttuj']) ) {
                        $tarif = $this->TarifAngkutan->findTarif($dataRevenue['Ttuj']['from_city_id'], $dataRevenue['Ttuj']['to_city_id'], $dataRevenue['Revenue']['customer_id'], $dataRevenue['Ttuj']['truck_capacity']);

                        if( !empty($tarif['jenis_unit']) && $tarif['jenis_unit'] == 'per_truck' ) {
                            $tarifTruck = $tarif;

                            if( !empty($dataRevenue['Revenue']['additional_charge']) ) {
                                $tarifTruck['addCharge'] = $dataRevenue['Revenue']['additional_charge'];
                            }
                        }
                    }

                    if(!empty($dataRevenue['RevenueDetail'])){
                        foreach ($dataRevenue['RevenueDetail']['no_do'] as $keyDetail => $value) {
                            $tarif_angkutan_type = !empty($dataRevenue['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$dataRevenue['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:'angkut';

                            if( $tarif_angkutan_type == $dataRevenue['Revenue']['type'] ) {
                                $data_detail['RevenueDetail'] = array(
                                    'no_do' => $value,
                                    'no_sj' => $dataRevenue['RevenueDetail']['no_sj'][$keyDetail],
                                    // 'note' => $dataRevenue['RevenueDetail']['note'][$keyDetail],
                                    'qty_unit' => !empty($dataRevenue['RevenueDetail']['qty_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['qty_unit'][$keyDetail]:0,
                                    'price_unit' => !empty($dataRevenue['RevenueDetail']['price_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['price_unit'][$keyDetail]:0,
                                    'total_price_unit' => !empty($dataRevenue['RevenueDetail']['total_price_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['total_price_unit'][$keyDetail]:0,
                                    'city_id' => $dataRevenue['RevenueDetail']['city_id'][$keyDetail],
                                    'group_motor_id' => $dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail],
                                    'tarif_angkutan_id' => $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$keyDetail],
                                    'tarif_angkutan_type' => $tarif_angkutan_type,
                                    'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$keyDetail],
                                    'is_charge' => !empty($dataRevenue['RevenueDetail']['is_charge'][$keyDetail])?$dataRevenue['RevenueDetail']['is_charge'][$keyDetail]:0,
                                );

                                $this->Revenue->RevenueDetail->set($data_detail);
                                if( !$this->Revenue->RevenueDetail->validates() ){
                                    $validate_detail = false;
                                }
                                
                                if( $tarif_angkutan_type == 'angkut' ) {
                                    $total_qty += $dataRevenue['RevenueDetail']['qty_unit'][$keyDetail];

                                    if( empty($array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]]) ){
                                        $array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]] = array(
                                            'qty' => !empty($data_detail['RevenueDetail']['qty_unit'])?intval($data_detail['RevenueDetail']['qty_unit']):0,
                                            'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$keyDetail]
                                        );
                                    }else{
                                        $array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]]['qty'] += !empty($data_detail['RevenueDetail']['qty_unit'])?$data_detail['RevenueDetail']['qty_unit']:0;
                                        $array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]]['payment_type'] = $dataRevenue['RevenueDetail']['payment_type'][$keyDetail];
                                    }
                                }

                                if(!empty($dataRevenue['RevenueDetail']['price_unit'][$keyDetail]) && $dataRevenue['RevenueDetail']['qty_unit'][$keyDetail]){
                                    if($dataRevenue['RevenueDetail']['payment_type'][$keyDetail] == 'per_truck'){
                                        $total_revenue += $dataRevenue['RevenueDetail']['price_unit'][$keyDetail];
                                    }else{
                                        $total_revenue += $dataRevenue['RevenueDetail']['price_unit'][$keyDetail] * $dataRevenue['RevenueDetail']['qty_unit'][$keyDetail];
                                    }
                                }
                            }
                        }
                    }

                    if( !empty($dataRevenue['Revenue']['revenue_tarif_type']) && $dataRevenue['Revenue']['revenue_tarif_type'] == 'per_truck' ) {
                        $total_revenue = $dataRevenue['Revenue']['tarif_per_truck'];
                    }

                    $totalWithoutTax = $total_revenue;
                    if( !empty($dataRevenue['Revenue']['additional_charge']) && $dataRevenue['Revenue']['additional_charge'] > 0 ){
                        $total_revenue += $dataRevenue['Revenue']['additional_charge'];
                    }

                    if( !empty($dataRevenue['Revenue']['pph']) && $dataRevenue['Revenue']['pph'] > 0 ){
                        $pph = $total_revenue * ($dataRevenue['Revenue']['pph'] / 100);
                    }
                    if( !empty($dataRevenue['Revenue']['ppn']) && $dataRevenue['Revenue']['ppn'] > 0 ){
                        $ppn = $total_revenue * ($dataRevenue['Revenue']['ppn'] / 100);
                    }

                    if( !empty($dataRevenue['Revenue']['pph']) && $dataRevenue['Revenue']['pph'] > 0 ){
                        $total_revenue -= $pph;
                    }
                    if( !empty($dataRevenue['Revenue']['ppn']) && $dataRevenue['Revenue']['ppn'] > 0 ){
                        $total_revenue += $ppn;
                    }

                    $dataRevenue['Revenue']['total'] = $total_revenue;
                    $dataRevenue['Revenue']['total_without_tax'] = $totalWithoutTax;
                    $dataRevenues[$key] = $dataRevenue;
                    /*end validasi revenue detail*/

                    $this->Revenue->set($dataRevenues);
                    $validate_qty = true;
                    $qtyReview = $this->Revenue->checkQtyUsed( $dataRevenue['Revenue']['ttuj_id'], $id );
                    $qtyTtuj = !empty($qtyReview['qtyTtuj'])?$qtyReview['qtyTtuj']:0;
                    $qtyUse = !empty($qtyReview['qtyUsed'])?$qtyReview['qtyUsed']:0;
                    $qtyUse += $total_qty;

                    if( $qtyUse > $qtyTtuj ) {
                        $validate_qty = false;
                    }

                    if( $this->Revenue->validates($dataRevenue) && $validate_detail && $validate_qty ){
                        if( $dataRevenue['Revenue']['type'] == 'angkut' ) {
                            if( $qtyUse >= $qtyTtuj ) {
                                $dataTtuj['Ttuj']['is_revenue'] = 1;
                            } else {
                                $dataTtuj['Ttuj']['is_revenue'] = 0;
                            }
                        }
                    }else{
                        $checkQty = false;
                        $text = sprintf(__('Gagal %s Revenue'), $msg);
                        if(!$validate_detail){
                            $text .= ', mohon lengkapi field-field yang kosong';
                        }
                        if(!$validate_qty){
                            $text .= ', jumlah muatan melebihi jumlah maksimum TTUJ';
                        }
                        $this->MkCommon->setCustomFlash($text, 'error');
                        break;
                    }
                }

                if( $checkQty ) {
                    foreach ($dataRevenues as $key => $dataRevenue) {
                        if($id && $data_local){
                            $this->Revenue->id = $id;
                            $msg = 'merubah';
                        }else{
                            $this->loadModel('Revenue');
                            $this->Revenue->create();
                            $msg = 'membuat';
                        }

                        if($this->Revenue->save($dataRevenue)){
                            $revenue_id = $this->Revenue->id;

                            if( $dataRevenue['Revenue']['type'] == 'angkut' ) {
                                $no_ref = $revenue_id;
                            }

                            if($id && $data_local){
                                $this->Revenue->RevenueDetail->deleteAll(array(
                                    'revenue_id' => $revenue_id
                                ));

                                $this->TtujTipeMotorUse->deleteAll(array(
                                    'revenue_id' => $revenue_id
                                ));
                            }

                            foreach ($array_ttuj_tipe_motor as $group_motor_id => $value) {
                                $this->TtujTipeMotorUse->create();
                                $this->TtujTipeMotorUse->set(array(
                                    'revenue_id' => $revenue_id,
                                    'group_motor_id' => $group_motor_id,
                                    'qty' => $value['qty']
                                ));
                                $this->TtujTipeMotorUse->save();
                            }

                            $getLastReference = intval($this->Revenue->RevenueDetail->getLastReference())+1;

                            foreach ($dataRevenue['RevenueDetail']['no_do'] as $key => $value) {
                                $this->Revenue->RevenueDetail->create();
                                $data_detail['RevenueDetail'] = array(
                                    'no_do' => $value,
                                    'no_sj' => $dataRevenue['RevenueDetail']['no_sj'][$key],
                                    // 'note' => $dataRevenue['RevenueDetail']['note'][$key],
                                    'qty_unit' => !empty($dataRevenue['RevenueDetail']['qty_unit'][$key])?$dataRevenue['RevenueDetail']['qty_unit'][$key]:0,
                                    'price_unit' => !empty($dataRevenue['RevenueDetail']['price_unit'][$key])?$dataRevenue['RevenueDetail']['price_unit'][$key]:0,
                                    'total_price_unit' => !empty($dataRevenue['RevenueDetail']['total_price_unit'][$key])?$dataRevenue['RevenueDetail']['total_price_unit'][$key]:0,
                                    'revenue_id' => $revenue_id,
                                    'city_id' => $dataRevenue['RevenueDetail']['city_id'][$key],
                                    'group_motor_id' => $dataRevenue['RevenueDetail']['group_motor_id'][$key],
                                    'tarif_angkutan_id' => $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$key],
                                    'tarif_angkutan_type' => $dataRevenue['RevenueDetail']['tarif_angkutan_type'][$key],
                                    'no_reference' => str_pad ( $getLastReference++ , 10, "0", STR_PAD_LEFT),
                                    'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$key],
                                    'is_charge' => !empty($dataRevenue['RevenueDetail']['is_charge'][$key])?$dataRevenue['RevenueDetail']['is_charge'][$key]:0,
                                );

                                $this->Revenue->RevenueDetail->set($data_detail);
                                $this->Revenue->RevenueDetail->save();
                            }

                            if( $dataRevenue['Revenue']['type'] == 'angkut' ) {
                                if( !empty($dataTtuj) ) {
                                    $this->Ttuj->id = $dataRevenue['Revenue']['ttuj_id'];
                                    $this->Ttuj->save($dataTtuj);
                                }

                                if( !empty($data_local) && $data_local['Ttuj']['id'] <> $dataRevenue['Revenue']['ttuj_id'] ) {
                                    $this->Ttuj->set('is_revenue', 0);
                                    $this->Ttuj->id = $data_local['Ttuj']['id'];
                                    $this->Ttuj->save();
                                }
                            }
                            $flagSave[] = true;
                        }else{
                            $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Revenue'), $msg), 'error'); 
                            $this->Log->logActivity( sprintf(__('Gagal %s Revenue'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                        }
                    }

                    if( count($flagSave) == count($dataRevenues) ) {
                        if( empty($id) ) {
                            $msgAlert = sprintf(__('Sukses %s Revenue! No Ref: %s'), $msg, str_pad($no_ref, 5, '0', STR_PAD_LEFT));
                        } else {
                            $msgAlert = sprintf(__('Sukses %s Revenue!'), $msg);
                        }

                        $this->MkCommon->setCustomFlash($msgAlert, 'success');
                        $this->Log->logActivity( sprintf(__('Sukses %s Revenue'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                        $this->redirect(array(
                            'controller' => 'revenues',
                            'action' => 'index'
                        ));
                    }
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan Revenue'), 'error');
            }
        }else if($id && $data_local){
            $this->request->data = $data_local;

            if( !empty($this->request->data['Revenue']['date_revenue']) && $this->request->data['Revenue']['date_revenue'] != '0000-00-00' ) {
                $this->request->data['Revenue']['date_revenue'] = date('d/m/Y', strtotime($this->request->data['Revenue']['date_revenue']));
            } else {
                $this->request->data['Revenue']['date_revenue'] = '';
            }

            if( !empty($data_local['Revenue']['tarif_per_truck']) && !empty($data_local['Revenue']['revenue_tarif_type']) && $data_local['Revenue']['revenue_tarif_type'] == 'per_truck' ) {
                $tarifAmount = $data_local['Revenue']['tarif_per_truck'];
                $addCharge = $data_local['Revenue']['additional_charge'];

                $tarifTruck = array(
                    'jenis_unit' => 'per_truck',
                    'tarif' => $tarifAmount,
                    'addCharge' => $addCharge,
                    'tarif_angkutan_id' => false,
                    'tarif_angkutan_type' => false,
                );
            }

            if(!empty($this->request->data['RevenueDetail'])){
                if( !empty($this->request->data['RevenueDetail']) ) {
                    foreach ($this->request->data['RevenueDetail'] as $key => $value) {
                        $value = $this->GroupMotor->getMerge( $value, $value['RevenueDetail']['group_motor_id'] );
                        $data_revenue_detail[$key] = array(
                            'TtujTipeMotor' => array(
                                'qty' => !empty($value[0]['qty_unit'])?$value[0]['qty_unit']:0,
                            ),
                            'RevenueDetail' => array(
                                'no_do' => $value['RevenueDetail']['no_do'],
                                'no_sj' => $value['RevenueDetail']['no_sj'],
                                // 'note' => $value['RevenueDetail']['note'],
                                'to_city_name' => !empty($value['City']['name'])?$value['City']['name']:'',
                                'price_unit' => array(
                                    'jenis_unit' => $value['RevenueDetail']['payment_type'],
                                    'tarif' => ( $value['RevenueDetail']['payment_type'] == 'per_truck' && empty($value['RevenueDetail']['is_charge']) )?$data_local['Revenue']['tarif_per_truck']:$value['RevenueDetail']['price_unit'],
                                    'tarif_angkutan_id' => $value['RevenueDetail']['tarif_angkutan_id'],
                                    'tarif_angkutan_type' => $value['RevenueDetail']['tarif_angkutan_type'],
                                ),
                                'total_price_unit' => !empty($value[0]['total_price_unit'])?$value[0]['total_price_unit']:0,
                                'payment_type' => $value['RevenueDetail']['payment_type'],
                                'qty_unit' => !empty($value[0]['qty_unit'])?$value[0]['qty_unit']:0,
                                'group_motor_id' => $value['RevenueDetail']['group_motor_id'],
                                'city_id' => $value['RevenueDetail']['city_id'],
                                'is_charge' => $value['RevenueDetail']['is_charge'],
                            )
                        );
                    }
                }
            }
        }

        $ttuj_retail = false;
        $ttuj_data = array();
        $tarif_angkutan = false;
        if(!empty($this->request->data['Revenue']['ttuj_id'])){
            $ttuj_data = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['Revenue']['ttuj_id'],
                    'Ttuj.status' => 1,
                    'Ttuj.is_draft' => 0,
                )
            ), false);

            if(!empty($ttuj_data) && !empty($this->request->data['RevenueDetail']['no_do'])){
                foreach ($this->request->data['RevenueDetail']['no_do'] as $key => $value) {
                    $group_motor_id = $this->request->data['RevenueDetail']['group_motor_id'][$key];
                    $to_city_id = !empty($this->request->data['RevenueDetail']['city_id'][$key])?$this->request->data['RevenueDetail']['city_id'][$key]:false;
                    $groupMotor = $this->GroupMotor->getData('first', array(
                        'conditions' => array(
                            'GroupMotor.id' => $group_motor_id
                        )
                    ));
                    $city = $this->City->getData('first', array(
                        'conditions' => array(
                            'City.id' => $to_city_id,
                        )
                    ));
                    $group_motor_name = '';
                    $qty = 0;

                    if(!empty($groupMotor)){
                        $group_motor_name = $groupMotor['GroupMotor']['name'];
                    }

                    if($ttuj_data['Ttuj']['is_retail']){
                        $to_city_name = !empty($city['City']['name'])?$city['City']['name']:false;

                        $ttujTipeMotor = $this->Ttuj->TtujTipeMotor->getMergeTtujTipeMotor( $ttuj_data, $this->request->data['Revenue']['ttuj_id'], 'first', array(
                            'TtujTipeMotor.ttuj_id' => $this->request->data['Revenue']['ttuj_id'],
                            'TipeMotor.group_motor_id' => $group_motor_id,
                            'TtujTipeMotor.city_id' => $to_city_id,
                            'TtujTipeMotor.status'=> 1,
                        ));

                        if(!empty($ttujTipeMotor)){
                            $qty = $ttujTipeMotor[0]['qty'];
                        }

                        $tarif = $this->TarifAngkutan->findTarif($ttuj_data['Ttuj']['from_city_id'], $to_city_id, $ttuj_data['Ttuj']['customer_id'], $ttuj_data['Ttuj']['truck_capacity'], $this->request->data['RevenueDetail']['group_motor_id'][$key]);
                    }else{
                        $to_city_name = !empty($city['City']['name'])?$city['City']['name']:false;
                        $tarif = $this->TarifAngkutan->findTarif($ttuj_data['Ttuj']['from_city_id'], $to_city_id, $ttuj_data['Ttuj']['customer_id'], $ttuj_data['Ttuj']['truck_capacity'], $this->request->data['RevenueDetail']['group_motor_id'][$key]);
                    }

                    $data_revenue_detail[$key] = array(
                        'TtujTipeMotor' => array(
                            'qty' => $qty,
                        ),
                        'RevenueDetail' => array(
                            'no_do' => $this->request->data['RevenueDetail']['no_do'][$key],
                            'no_sj' => $this->request->data['RevenueDetail']['no_sj'][$key],
                            // 'note' => $this->request->data['RevenueDetail']['note'][$key],
                            'to_city_name' => $to_city_name,
                            'price_unit' => $tarif,
                            'total_price_unit' => $this->request->data['RevenueDetail']['total_price_unit'][$key],
                            'payment_type' => $tarif['jenis_unit'],
                            'qty_unit' => $this->request->data['RevenueDetail']['qty_unit'][$key],
                            'group_motor_id' => $this->request->data['RevenueDetail']['group_motor_id'][$key],
                            'city_id' => $to_city_id,
                            'is_charge' => $this->request->data['RevenueDetail']['is_charge'][$key],
                            'TipeMotor' => array(
                                'name' => $group_motor_name,
                            ),
                        )
                    );
                }
            }
        }
        $this->set('data_revenue_detail', $data_revenue_detail);

        $ttuj_id = !empty($data_local['Ttuj']['id'])?$data_local['Ttuj']['id']:false;
        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'OR' => array(
                    array(
                        'Ttuj.is_revenue' => 0,
                        'Ttuj.is_draft' => 0,
                        'Ttuj.status' => 1,
                    ),
                    array(
                        'Ttuj.id' => $ttuj_id,
                    ),
                ),
            ),
        ), false);
        $this->set('ttujs', $ttujs);

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name'
            ),
        ));
        $this->set('customers', $customers);

        $toCities = $this->City->toCities();
        $groupMotors = $this->GroupMotor->getData('list', array(
            'conditions' => array(
                'GroupMotor.status' => 1
            )
        ));

        $this->set(compact(
            'toCities', 'groupMotors', 'tarifTruck',
            'id', 'data_local'
        ));
        $this->set('active_menu', 'revenues');
        $this->render('revenue_form');
    }

    function revenue_toggle( $id ){
        if( in_array('delete_revenues', $this->allowModule) ) {
            $this->loadModel('Revenue');
            $this->loadModel('Ttuj');
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

                $this->Revenue->set('status', $value);
                $this->Revenue->id = $id;

                if($this->Revenue->save()){
                    $this->Ttuj->set('is_revenue', 0);
                    $this->Ttuj->id = $locale['Revenue']['ttuj_id'];
                    $this->Ttuj->save();

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
        } else {
            $this->redirect($this->referer());
        }
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
                    $date_from = $this->MkCommon->getDate($date_explode[0]);
                    $date_to = $this->MkCommon->getDate($date_explode[1]);
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
                $total_lku = 0;

                if(!empty($truk_ritase)){
                    $this->loadModel('Lku');
                    $this->loadModel('TtujTipeMotor');

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
        if( in_array('view_revenues', $this->allowModule) ) {
            $this->loadModel('Invoice');
            $this->set('active_menu', 'invoices');
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
                if(!empty($refine['status'])){
                    $status = urldecode($refine['status']);
                    $this->request->data['Invoice']['status'] = $status;

                    switch ($status) {
                        case 'paid':
                            $conditions['Invoice.complete_paid '] = 1;
                            break;

                        case 'halfpaid':
                            $conditions['Invoice.complete_paid '] = 0;
                            $conditions['Invoice.paid '] = 1;
                            break;

                        case 'void':
                            $conditions['Invoice.is_canceled '] = 1;
                            break;
                        
                        default:
                            $conditions['Invoice.complete_paid'] = 0;
                            $conditions['Invoice.paid'] = 0;
                            $conditions['Invoice.is_canceled'] = 0;
                            break;
                    }
                }
            }

            $this->paginate = $this->Invoice->getData('paginate', array(
                'conditions' => $conditions,
                'order' => array(
                    'Invoice.id' => 'DESC'
                )
            ), false);
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function invoice_add($action = false){
        if( in_array('insert_invoices', $this->allowModule) ) {
            $module_title = __('Tambah Invoice');
            $this->set('sub_module_title', trim($module_title));
            $this->doInvoice($action);
        } else {
            $this->redirect($this->referer());
        }
    }

    // function invoice_edit( $id ){
    //     if( in_array('update_invoices', $this->allowModule) ) {
    //         $this->loadModel('Invoice');
    //         $revenue = $this->Invoice->getData('first', array(
    //             'conditions' => array(
    //                 'Invoice.id' => $id
    //             )
    //         ));

     //        if(!empty($revenue)){
     //            $module_title = __('Rubah Invoice');
     //            $this->set('sub_module_title', trim($module_title));
     //            $this->doInvoice($id, $revenue);
     //        }else{
     //            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
     //            $this->redirect(array(
     //                'controller' => 'revenues',
     //                'action' => 'invoices'
     //            ));
     //        }
     //    } else {
     //        $this->redirect($this->referer());
     //    }
    // }

    function doInvoice($action, $id = false, $data_local = false){
        $this->loadModel('Revenue');
        $this->loadModel('Customer');
        $this->loadModel('Bank');
        $this->loadModel('User');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $customer_id = !empty($data['Invoice']['customer_id'])?$data['Invoice']['customer_id']:false;

            if($id && $data_local){
                $this->Invoice->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Invoice');
                $this->Invoice->create();
                $msg = 'membuat';
            }

            $data['Invoice']['period_from'] = $this->MkCommon->getDate($data['Invoice']['period_from']);
            $data['Invoice']['period_to'] = $this->MkCommon->getDate($data['Invoice']['period_to']);
            $data['Invoice']['invoice_date'] = $this->MkCommon->getDate($data['Invoice']['invoice_date']);

            $customer = $this->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $customer_id
                )
            ));

            if( !empty($customer) ) {
                $data['Invoice']['bank_id'] = $customer['Customer']['bank_id'];
                $data['Invoice']['billing_id'] = $customer['Customer']['billing_id'];
                $data['Invoice']['term_of_payment'] = $customer['Customer']['term_of_payment'];
            }

            $this->Invoice->set($data);

            if($this->Invoice->validates()){
                $revenues = $this->Revenue->getData('all', array(
                    'conditions' => array(
                        'Revenue.customer_id' => $customer_id,
                        'Revenue.transaction_status' => 'posting',
                        'Revenue.type' => !empty($data['Invoice']['tarif_type'])?$data['Invoice']['tarif_type']:false,
                        'Revenue.status' => 1,                      
                    ),
                    'order' => array(
                        'Revenue.date_revenue' => 'ASC'
                    ),
                ));

                if(!empty($revenues)){
                    $total = 0;
                    foreach ($revenues as $key => $value) {
                        $total += $value['Revenue']['total'];   
                    }
                    $data['Invoice']['total'] = $total;
                }
                
                $this->Invoice->set($data);

                if($action == 'tarif'){
                    if(!empty($revenues) && !empty($customer)){
                        $revenue_id = Set::extract('/Revenue/id', $revenues);
                        $revenue_detail = $this->Revenue->RevenueDetail->getData('all', array(
                            'conditions' => array(
                                'RevenueDetail.revenue_id' => $revenue_id,
                            ),
                            'order' => array(
                                'RevenueDetail.price_unit' => 'DESC'
                            )
                        ));
                        $result = array();

                        if(!empty($revenue_detail)){
                            foreach ($revenue_detail as $key => $value) {
                                $result[$value['RevenueDetail']['price_unit']][] = $value;
                            }
                        }

                        if(!empty($result)){
                            foreach ($result as $key => $value) {
                                $this->Invoice->create();
                                $invoice_number = $this->Invoice->getNoInvoice();
                                $data['Invoice']['no_invoice'] = $invoice_number;
                                $data['Invoice']['type_invoice'] = 'tarif';
                                $data['Invoice']['due_invoice'] = $customer['Customer']['term_of_payment'];
                                
                                $this->Invoice->set($data);
                                if($this->Invoice->save()){
                                    foreach ($value as $key => $value_detail) {
                                        $this->Revenue->RevenueDetail->id = $value_detail['RevenueDetail']['id'];
                                        $this->Revenue->RevenueDetail->set('invoice_id', $this->Invoice->id);
                                        $this->Revenue->RevenueDetail->save();
                                    }
                                }
                            }

                            $this->MkCommon->setCustomFlash(sprintf(__('Berhasil %s Invoice'), $msg), 'success'); 
                            $this->redirect(array(
                                'controller' => 'revenues',
                                'action' => 'invoices'
                            ));
                        }
                    }
                }else{
                    $data['Invoice']['due_invoice'] = $customer['Customer']['term_of_payment'];
                    $this->Invoice->set($data);

                    if($this->Invoice->save()){
                        $invoice_id = $this->Invoice->id;

                        if( !empty($customer['CustomerGroup']['CustomerGroupPattern']) ) {
                            $last_number = str_replace($customer['CustomerGroup']['CustomerGroupPattern']['pattern'], '', $data['Invoice']['no_invoice']);
                            $last_number = intval($last_number)+1;
                            $this->Customer->CustomerGroup->CustomerGroupPattern->set('last_number', $last_number);
                            $this->Customer->CustomerGroup->CustomerGroupPattern->id = $customer['CustomerGroup']['CustomerGroupPattern']['id'];
                            $this->Customer->CustomerGroup->CustomerGroupPattern->save();
                        }

                        // if( !empty($customer['CustomerPattern']) ) {
                        //     $last_number = str_replace($customer['CustomerPattern']['pattern'], '', $data['Invoice']['no_invoice']);
                        //     $last_number = intval($last_number)+1;
                        //     $this->Customer->CustomerPattern->set('last_number', $last_number);
                        //     $this->Customer->CustomerPattern->id = $customer['CustomerPattern']['id'];
                        //     $this->Customer->CustomerPattern->save();
                        // }

                        if($action == 'tarif'){
                            $revenue_id = $this->Revenue->getData('list', array(
                                'conditions' => array(
                                    'Revenue.customer_id' => $customer_id,
                                    'Revenue.transaction_status' => 'posting',
                                    'Revenue.type' => !empty($data['Invoice']['tarif_type'])?$data['Invoice']['tarif_type']:false,
                                    'Revenue.status' => 1,                      
                                ),
                                'order' => array(
                                    'Revenue.date_revenue' => 'ASC'
                                ),
                            ));
                        }else{
                            $revenue_id = $this->Revenue->getData('list', array(
                                'conditions' => array(
                                    'Revenue.customer_id' => $data['Invoice']['customer_id'],
                                    'Revenue.transaction_status' => 'posting',
                                    'Revenue.type' => !empty($data['Invoice']['tarif_type'])?$data['Invoice']['tarif_type']:false,
                                    'Revenue.status' => 1
                                )
                            ), false);

                            if(!empty($revenue_id)){
                                foreach ($revenue_id as $rev_id => $value) {
                                    $this->Invoice->InvoiceDetail->create();
                                    $this->Invoice->InvoiceDetail->set(array(
                                        'invoice_id' => $invoice_id,
                                        'revenue_id' => $rev_id
                                    ));
                                    $this->Invoice->InvoiceDetail->save();

                                    $this->Revenue->id = $rev_id;
                                    $this->Revenue->set('transaction_status', 'invoiced');
                                    $this->Revenue->save();
                                }
                            }
                        }

                        if(!empty($revenue_id)){
                            $this->Revenue->RevenueDetail->updateAll(array(
                                'RevenueDetail.invoice_id' => $invoice_id
                            ), array(
                                'RevenueDetail.revenue_id' => $revenue_id,
                            ));
                        }

                        $this->MkCommon->setCustomFlash(sprintf(__('Berhasil %s Invoice'), $msg), 'success'); 
                        $this->redirect(array(
                            'controller' => 'revenues',
                            'action' => 'invoices'
                        ));
                    }else{
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Invoice'), $msg), 'error'); 
                        $this->Log->logActivity( sprintf(__('Gagal %s Invoice'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                    }
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Invoice'), $msg), 'error'); 
                $this->Log->logActivity( sprintf(__('Gagal %s Invoice'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 

                $this->request->data['Invoice']['no_invoice'] = $this->MkCommon->getNoInvoice( $customer );
            }
        }else if(!empty($id) && !empty($data_local)){
             $this->request->data = $data_local;

             $data['Invoice']['invoice_date'] = $this->MkCommon->getDate($data['Invoice']['invoice_date'], true);
        }

        $conditionsRevenue = array(
            'Revenue.transaction_status' => 'posting',
            'Revenue.status' => 1,                      
        );
        $banks = $this->Bank->getData('list', array(
            'conditions' => array(
                'Bank.status' => 1,
            ),
        ));

        if($action == 'tarif'){
            $conditionsRevenue['revenue_tarif_type'] = 'per_unit';
        }

        $revenues = $this->Revenue->getData('all', array(
            'conditions' => $conditionsRevenue,
            'order' => array(
                'Revenue.date_revenue' => 'ASC'
            ),
            'group' => array(
                'Revenue.customer_id'
            ),
        ), false);
        $customers = array();

        if( !empty($revenues) ) {
            foreach ($revenues as $key => $revenue) {
                $revenueCustomer = $this->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.status' => 1,
                        'Customer.id' => $revenue['Revenue']['customer_id'],
                    ),
                ));

                if( !empty($revenueCustomer) ) {
                    $customers[$revenue['Revenue']['customer_id']] = $revenueCustomer['Customer']['customer_name'];
                }
            }
        }
        
        $this->set(compact(
            'customers', 'id', 'action',
            'banks'
        ));
        $this->set('active_menu', 'invoices');
        $this->render('invoice_form');
    }

    function invoice_print($id, $action_print = false){
        $this->loadModel('Invoice');
        $this->loadModel('Revenue');
        $this->loadModel('GroupMotor');
        $this->loadModel('City');
        $this->loadModel('Customer');
        $this->loadModel('Ttuj');
        $this->loadModel('User');

        if( !empty($this->params['named']) ){
            $data_print = $this->params['named']['print'];
        } else {
            $data_print = 'invoice';
        }

        $module_title = __('Print Invoice');
        $this->set('sub_module_title', trim($module_title));
        $this->set('active_menu', 'invoices');
        
        $invoice = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
                'Invoice.status' => array( 0,1 ),
            ),
            'contain' => array(
                'InvoiceDetail',
            )
        ));

        if(!empty($invoice)){
            $invoice = $this->Customer->getMerge($invoice, $invoice['Invoice']['customer_id']);
            $invoice = $this->User->getMerge($invoice, $invoice['Invoice']['billing_id']);
            $invoice = $this->Customer->Bank->getMerge($invoice, $invoice['Invoice']['bank_id']);

            if( $data_print == 'header' ) {
                $this->loadModel('Setting');
                $setting = $this->Setting->find('first');
                $invoice = $this->Revenue->RevenueDetail->getSumUnit($invoice, $invoice['Invoice']['id']);
            } else {
                $revenue_detail = $this->Revenue->RevenueDetail->getPreviewInvoice($invoice['Invoice']['id'], $invoice['Invoice']['tarif_type'], $action_print, $data_print);
            }

            $action = $invoice['Invoice']['type_invoice'];
            $this->set(compact(
                'invoice', 'revenue_detail', 'action',
                'setting', 'data_print'
            ));
        }

        if($action_print == 'pdf'){
            $this->layout = 'pdf';
        }else if($action_print == 'excel'){
            $this->layout = 'ajax';
        }
        
        $this->set('action_print', $action_print);
        switch ($data_print) {
            case 'header':
                $this->render('invoice_header_print');
                break;
        }
    }

    function invoice_reports( $data_action = false ){
        if( in_array('view_revenue_reports', $this->allowModule) ) {
            $this->loadModel('Invoice');
            $this->loadModel('Customer');

            $this->set('active_menu', 'revenue');
            $this->set('sub_module_title', __('Laporan Invoice Aging'));

            $default_conditions = array(
                 'Customer.status' => 1
            );
            $invoice_conditions = array();

            if( !empty($this->params['named']) ){
                $refine = $this->params['named'];

                if(!empty($refine['customer'])){
                    $keyword = urldecode($refine['customer']);
                    $default_conditions['Customer.id'] = $keyword;
                    $this->request->data['Invoice']['customer_id'] = $keyword;
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
                        $invoice_conditions['DATE_FORMAT(Invoice.period_from, \'%Y-%m-%d\') >='] = $dateFrom;
                        $invoice_conditions['DATE_FORMAT(Invoice.period_to, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Invoice']['date'] = $dateStr;
                }
            }

            $this->paginate = $this->Customer->getData('paginate', array(
                'conditions' => $default_conditions
            ));

            $customers = $this->paginate('Customer');

            $list_customer = array();
            foreach ($customers as $key => $value) {
                $default_conditions = array(
                    'Invoice.paid' => 0,
                    'Invoice.customer_id' => $value['Customer']['id'],
                );
                if(!empty($invoice_conditions)){
                    $default_conditions = array_merge($default_conditions, $invoice_conditions);
                }

                $customers[$key]['piutang'] = $this->Invoice->getData('all', array(
                    'conditions' => $default_conditions,
                    'fields' => array(
                        'SUM(Invoice.total) as total_pituang'
                    )
                ));

                $default_conditions = array(
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >=' => 1,
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) <=' => 15,
                    'Invoice.customer_id' => $value['Customer']['id'],
                );
                if(!empty($invoice_conditions)){
                    $default_conditions = array_merge($default_conditions, $invoice_conditions);
                }
                $customers[$key]['current_rev1to15'] = $this->Invoice->getData('all', array(
                    'conditions' => $default_conditions,
                    'fields' => array(
                        'SUM(Invoice.total) as current_rev1to15'
                    )
                ));
                $default_conditions = array(
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >=' => 16,
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) <=' => 30,
                    'Invoice.customer_id' => $value['Customer']['id'],
                );
                if(!empty($invoice_conditions)){
                    $default_conditions = array_merge($default_conditions, $invoice_conditions);
                }
                $customers[$key]['current_rev16to30'] = $this->Invoice->getData('all', array(
                    'conditions' => $default_conditions,
                    'fields' => array(
                        'SUM(Invoice.total) as current_rev16to30'
                    )
                ));

                $default_conditions = array(
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >' => 30,
                    'Invoice.customer_id' => $value['Customer']['id'],
                );
                if(!empty($invoice_conditions)){
                    $default_conditions = array_merge($default_conditions, $invoice_conditions);
                }
                $customers[$key]['current_rev30'] = $this->Invoice->getData('all', array(
                    'conditions' => $default_conditions,
                    'fields' => array(
                        'SUM(Invoice.total) as current_rev30'
                    )
                ));
            }
            $this->set('active_menu', 'invoice_reports');

            $list_customer = $this->Customer->find('list', array(
                'conditions' => array(
                    'Customer.status' => 1
                ),
                'order' => array(
                    'Customer.name' => 'ASC'
                )
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }

            $this->set(compact(
                'customers', 'list_customer', 'data_action'
            ));
        } else {
            $this->redirect($this->referer());
        }
    }

    public function ar_period_reports( $data_action = false ) {
        if( in_array('view_ar_period_reports', $this->allowModule) ) {
            $this->loadModel('Revenue');
            $this->loadModel('Invoice');
            $fromYear = date('Y');
            $toMonth = 12;

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if( !empty($refine['fromYear']) ){
                    $fromYear = urldecode($refine['fromYear']);
                    $this->request->data['Ttuj']['from']['year'] = $fromYear;
                }
            }

            $conditions = array(
                'Revenue.status'=> 1,
                'Revenue.transaction_status <>' => 'invoiced',
            );
            $defaultConditionsInvoice = array(
                'Invoice.status'=> 1,
                'Invoice.paid'=> 0,
            );
            $totalAr = array();

            for ($i=1; $i <= $toMonth; $i++) {
                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));

                $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\')'] = $month;
                $revenues = $this->Revenue->getData('first', array(
                    'conditions' => $conditions,
                    'fields' => array(
                        'SUM(Revenue.total) total'
                    ),
                ), false);
                $totalAr['AR'][$month] = !empty($revenues[0]['total'])?$revenues[0]['total']:0;

                $conditionsInvoice = $defaultConditionsInvoice;
                $conditionsInvoice['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\')'] = $month;
                $invoice = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsInvoice,
                    'fields' => array(
                        'SUM(Invoice.total) total'
                    ),
                ), false);
                $totalAr['Invoice'][$month] = !empty($invoice[0]['total'])?$invoice[0]['total']:0;

                if( $month <= date('Y-m') ) {
                    $conditionsInvoice = $defaultConditionsInvoice;
                    $conditionsInvoice['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <'] = $month;
                    $invoice = $this->Invoice->getData('first', array(
                        'conditions' => $conditionsInvoice,
                        'fields' => array(
                            'SUM(Invoice.total) total'
                        ),
                    ), false);
                    $totalAr['LastInvoice'][$month] = !empty($invoice[0]['total'])?$invoice[0]['total']:0;
                }
            }

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }

            $this->set('sub_module_title', sprintf(__('Laporan AR Per Period %s'), $fromYear));
            $this->set('active_menu', 'ar_period_reports');

            $this->set(compact(
                'toMonth', 'fromYear', 'totalCnt',
                'totalAr', 'data_action'
            ));
        }
    }

    function invoice_payments(){
        // if( in_array('view_invoice_payments', $this->allowModule) ) {
            $this->loadModel('Invoice');
            $this->loadModel('InvoicePayment');
            $this->loadModel('Customer');
            
            $this->set('active_menu', 'invoice_payments');
            $this->set('sub_module_title', __('Pembayaran Invoice'));

            $conditions = array();
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['from'])){
                    $from = urldecode(rawurldecode($refine['from']));
                    $this->request->data['InvoicePayment']['date_from'] = $from;
                    $conditions['DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m-%d\') >= '] = $this->MkCommon->getDate($from);
                }
                if(!empty($refine['to'])){
                    $to = urldecode(rawurldecode($refine['to']));
                    $this->request->data['InvoicePayment']['date_to'] = $to;
                    $conditions['DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m-%d\') <= '] = $this->MkCommon->getDate($to);
                }
                if(!empty($refine['nodoc'])){
                    $to = urldecode(rawurldecode(rawurldecode($refine['nodoc'])));
                    $this->request->data['InvoicePayment']['nodoc'] = $to;
                    $conditions['InvoicePayment.nodoc LIKE'] = '%'.$to.'%';
                }
            }

            $this->paginate = $this->InvoicePayment->getData('paginate', array(
                'conditions' => $conditions,
                'contain' => array(
                    'Coa'
                ),
                'order' => array(
                    'InvoicePayment.created' => 'DESC',
                    'InvoicePayment.id' => 'DESC',
                ),
            ), false);
            $invoices = $this->paginate('InvoicePayment');

            if(!empty($invoices)){
                foreach ($invoices as $key => $value) {
                    $invoices[$key] = $this->InvoicePayment->Customer->getMerge($value, $value['InvoicePayment']['customer_id']);
                }
            }
            
            $this->set('invoices', $invoices); 

            $customers = $this->Invoice->Customer->getData('list', array(
                'fields' => array(
                    'Customer.id', 'Customer.name'
                )
            ));
            $this->set('customers', $customers);
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function invoice_payment_add(){
        // if( in_array('insert_invoice_payments', $this->allowModule) ) {
            $this->loadModel('Invoice');
            $module_title = __('Tambah Pembayaran Invoice');
            $this->set('sub_module_title', trim($module_title));
            $this->doInvoicePayment();
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function doInvoicePayment($id = false, $data_local = false){
        $this->loadModel('Customer');
        $this->loadModel('Coa');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Invoice->InvoicePaymentDetail->InvoicePayment->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Invoice');
                $this->Invoice->InvoicePaymentDetail->InvoicePayment->create();
                $msg = 'membuat';
            }

            $data['InvoicePayment']['date_payment'] = !empty($data['InvoicePayment']['date_payment']) ? $this->MkCommon->getDate($data['InvoicePayment']['date_payment']) : '';
            $total = 0;
            $validate_price_pay = true;

            if(!empty($data['InvoicePaymentDetail']['price_pay']) && !empty($data['InvoicePaymentDetail']['invoice_id'])){
                foreach ($data['InvoicePaymentDetail']['price_pay'] as $key => $value) {
                    $price = $this->MkCommon->convertPriceToString($value);
                    $_invoice_id = $data['InvoicePaymentDetail']['invoice_id'][$key];

                    if(empty($price) || empty($data['InvoicePaymentDetail']['price_pay'][$key])){
                        $validate_price_pay = false;
                        break;
                    }else{
                        $invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
                            'conditions' => array(
                                'InvoicePaymentDetail.invoice_id' => $_invoice_id
                            ),
                            'fields' => array(
                                'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
                            )
                        ));

                        $invoice_has_paid = (!empty($invoice_has_paid[0]['invoice_has_paid'])) ? $invoice_has_paid[0]['invoice_has_paid'] : 0;
                        $total_paid = $invoice_has_paid + $price;

                        $invoice_data = $this->Invoice->getData('first', array(
                            'conditions' => array(
                                'Invoice.id' => $_invoice_id
                            )
                        ));
                        
                        if(!empty($invoice_data)){
                            if($total_paid > $invoice_data['Invoice']['total']){
                                $validate_price_pay = false;
                                break;
                            }else{
                                $data['InvoicePaymentDetail']['price_pay'][$key] = $price;
                                $total += $price;
                            }
                        }
                    }
                }
            }else{
                $validate_price_pay = false;
            }
            
            $temptotal = $total;
            $data['InvoicePayment']['total_payment'] = $total;

            if(!empty($data['InvoicePayment']['pph'])){
                $temptotal -= $total*($data['InvoicePayment']['pph']/100);
            }
            if(!empty($data['InvoicePayment']['ppn'])){
                $temptotal += $total*($data['InvoicePayment']['ppn']/100);
            }
            
            $total = $temptotal;
            $data['InvoicePayment']['grand_total_payment'] = $total;
            $this->Invoice->InvoicePaymentDetail->InvoicePayment->set($data);

            if($this->Invoice->InvoicePaymentDetail->InvoicePayment->validates() && $validate_price_pay){
                $this->Invoice->InvoicePaymentDetail->InvoicePayment->set($data);

                if($this->Invoice->InvoicePaymentDetail->InvoicePayment->save()){
                    $invoice_payment_id = $this->Invoice->InvoicePaymentDetail->InvoicePayment->id;

                    if($id && $data_local){
                        $this->Invoice->InvoicePaymentDetail->deleteAll(array(
                            'InvoicePaymentDetail.invoice_payment_id' => $invoice_payment_id
                        ));
                    }

                    if( !empty($data['InvoicePaymentDetail']['price_pay']) ) {
                        foreach ($data['InvoicePaymentDetail']['price_pay'] as $key => $value) {
                            $invoice_id = $data['InvoicePaymentDetail']['invoice_id'][$key];

                            $this->Invoice->InvoicePaymentDetail->create();
                            $this->Invoice->InvoicePaymentDetail->set(array(
                                'price_pay' => trim($value),
                                'invoice_id' => $invoice_id,
                                'invoice_payment_id' => $invoice_payment_id
                            ));
                            $this->Invoice->InvoicePaymentDetail->save();

                            $default_conditions_detail = array(
                                'InvoicePaymentDetail.invoice_id' => $invoice_id,
                                'InvoicePaymentDetail.status' => 1
                            );

                            $invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
                                'conditions' => $default_conditions_detail,
                                'fields' => array(
                                    '*',
                                    'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
                                ),
                                'contain' => array(
                                    'Invoice'
                                )
                            ));
                            $invoice_paid = !empty($invoice_has_paid[0]['invoice_has_paid'])?$invoice_has_paid[0]['invoice_has_paid']:0;
                            $invoice_total = !empty($invoice_has_paid['Invoice']['total'])?$invoice_has_paid['Invoice']['total']:0;
                            
                            if($invoice_paid >= $invoice_total){
                                $this->Invoice->id = $invoice_id;
                                $this->Invoice->set(array(
                                    'paid' => 1,
                                    'complete_paid' => 1
                                ));
                                $this->Invoice->save();
                            }else{
                                $this->Invoice->id = $invoice_id;
                                $this->Invoice->set(array(
                                    'paid' => 1,
                                    'complete_paid' => 0
                                ));
                                $this->Invoice->save();
                            }
                        }
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil %s Pembayaran Invoice'), $msg), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil %s Pembayaran Invoice'), $msg), $this->user_data, $this->RequestHandler, $this->params ); 
                    
                    $this->redirect(array(
                        'action' => 'invoice_payments'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran Invoice'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran Invoice'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $text = sprintf(__('Gagal %s Pembayaran Invoice'), $msg);

                if( !$validate_price_pay ){
                    $text .= ', harap isi semua field kosong dan isi pembayaran invoice tidak boleh lebih besar dari total invoice.';
                }
                $this->MkCommon->setCustomFlash($text, 'error'); 
            }
        }else if(!empty($id) && !empty($data_local)){
             $this->request->data = $data_local;
             $this->request->data['InvoicePayment']['date_payment'] = !empty($this->request->data['InvoicePayment']['date_payment']) ? $this->MkCommon->getDate($this->request->data['InvoicePayment']['date_payment'], true) : '';
        }

        if(!empty($this->request->data['InvoicePayment']['customer_id'])){
            $customer_id = $this->request->data['InvoicePayment']['customer_id'];
            $customer = $this->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $customer_id
                )
            ));
            $invoices = $this->Invoice->getdata('all', array(
                'conditions' => array(
                    'Invoice.customer_id' => $this->request->data['InvoicePayment']['customer_id'],
                    'Invoice.complete_paid' => 0
                )
            ));

            if( !empty($customer) ) {
                $this->request->data['InvoicePayment']['bank_id'] = $customer['Customer']['bank_id'];
            }

            if(!empty($invoices)){
                foreach ($invoices as $key => $value) {
                    $invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
                        'conditions' => array(
                            'InvoicePaymentDetail.invoice_id' => $value['Invoice']['id']
                        ),
                        'fields' => array(
                            'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
                        )
                    ));

                     $invoices[$key]['invoice_has_paid'] = $invoice_has_paid[0]['invoice_has_paid'];
                }
            }

            $this->set(compact('invoices'));
        }

        // $list_invoices = $this->Invoice->getData('list', array(
        //     'conditions' => array(
        //         'Invoice.paid' => 0
        //     ),
        //     'fields' => array(
        //         'Invoice.id', 'Invoice.no_invoice'
        //     ),
        // ));

        $list_customer = $this->Invoice->getData('list', array(
            'conditions' => array(
                'Invoice.complete_paid' => 0
            ),
            'contain' => array(
                'Customer'
            ),
            'group' => array(
                'Invoice.customer_id'
            ),
            'fields' => array(
                'Customer.id', 'Customer.name'
            )
        ));
        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                'Coa.status' => 1,
                'Coa.is_cash_bank' => 1
            ),
        ));

        $this->set(compact(
            'list_customer', 'id', 'action',
            'coas'
        ));
        $this->set('active_menu', 'invoice_payments');
        $this->layout = 'default';
        $this->render('invoice_payment_form');
    }

    function invoice_payment_delete($id){
        if(!empty($id)){
            $this->loadModel('Invoice');

            $invoice_payment = $this->Invoice->InvoicePaymentDetail->InvoicePayment->getData('first', array(
                'conditions' => array(
                    'InvoicePayment.status' => 1,
                    'InvoicePayment.id' => $id
                ),
                'contain' => array(
                    'InvoicePaymentDetail'
                )
            ));
            
            if(!empty($invoice_payment)){
                if(!empty($invoice_payment['InvoicePaymentDetail'])){
                    foreach ($invoice_payment['InvoicePaymentDetail'] as $key => $value) {
                        $invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
                            'conditions' => array(
                                'InvoicePaymentDetail.invoice_id' => $value['invoice_id'],
                                'InvoicePayment.status' => 1,
                            ),
                            'fields' => array(
                                '*',
                                'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
                            ),
                            'contain' => array(
                                'Invoice',
                                'InvoicePayment'
                            )
                        ));

                        if(!empty($invoice_has_paid)){
                            $total = $invoice_has_paid[0]['invoice_has_paid'] - $value['price_pay'];

                            if($total < $invoice_has_paid['Invoice']['total']){
                                $this->Invoice->id = $value['invoice_id'];
                                $this->Invoice->set(array(
                                    'complete_paid' => 0,
                                    'paid' => 0,
                                ));
                                $this->Invoice->save();
                            }
                        }
                    }

                    $this->Invoice->InvoicePaymentDetail->updateAll(array(
                        'InvoicePaymentDetail.status' => 0
                    ), array(
                        'InvoicePaymentDetail.invoice_payment_id' => $id
                    ));
                }

                $this->Invoice->InvoicePaymentDetail->InvoicePayment->id = $id;
                $this->Invoice->InvoicePaymentDetail->InvoicePayment->set(array(
                    'status' => 0,
                    'is_canceled' => 1,
                    'canceled_date' => date('d/m/Y')
                ));

                if($this->Invoice->InvoicePaymentDetail->InvoicePayment->save()){
                    $this->MkCommon->setCustomFlash(__('Berhasil menghapus invoice pembayaran'), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil menghapus invoice pembayara ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menghapus invoice pembayaran'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal menghapus invoice pembayara ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Invoice pembayaran tidak ditemukan'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Invoice pembayaran tidak ditemukan'), 'error');
        }
        $this->redirect($this->referer());
    }

    function action_post_revenue(){
        if(!empty($this->request->data['Revenue']['revenue_id'])){
            $this->loadModel('Revenue');
            $validasi = false;
            $arr_id = array();
            foreach ($this->request->data['Revenue']['revenue_id'] as $key => $value) {
                if(!empty($value)){
                    $validasi = true;
                    $arr_id[] = $value;
                }
            }

            if($validasi && in_array($this->request->data['Revenue']['posting_type'], array('posting', 'unposting'))){
                $check_save = $this->Revenue->updateAll(
                    array('transaction_status' => "'".$this->request->data['Revenue']['posting_type']."'"), 
                    array('Revenue.id' => $arr_id)
                );
                if($check_save){
                    $this->MkCommon->setCustomFlash(__('Berhasil merubah status revenue'), 'success');
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status revenue'), 'error');
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Revenue belum dipilih'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Revenue belum dipilih'), 'error');
        }

        $this->redirect($this->referer());
    }

    function detail_invoice_payment($id){
        if(!empty($id)){
            $this->loadModel('InvoicePayment');
            $invoice = $this->InvoicePayment->getData('first', array(
                'conditions' => array(
                    'InvoicePayment.id' => $id
                ),
                'contain' => array(
                    'InvoicePaymentDetail' => array(
                        'Invoice'
                    ),
                    'Coa',
                )
            ));

            if(!empty($invoice)){
                $invoice = $this->InvoicePayment->Customer->getMerge($invoice, $invoice['InvoicePayment']['customer_id']);
                $sub_module_title = 'Detail Pembayaran Invoice';
                $this->set('active_menu', 'invoice_payments');
                $this->set(compact('invoice', 'sub_module_title'));
            }else{
                $this->MkCommon->setCustomFlash(__('Pembayaran invoice tidak ditemukan'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Pembayaran invoice tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    public function list_kwitansi( $data_action = false ) {
        // if( in_array('view_list_kwitansi', $this->allowModule) ) {
            $this->loadModel('Invoice');
            $this->loadModel('Revenue');
            $invoice_conditions = array();
            $start = 1;
            $limit = 30;

            if( !empty($this->params['named']) ){
                $refine = $this->params['named'];

                if(!empty($refine['date'])){
                    $dateStr = urldecode($refine['date']);
                    $date = explode('-', $dateStr);

                    if( !empty($date) ) {
                        $date[0] = urldecode($date[0]);
                        $date[1] = urldecode($date[1]);
                        $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                        $dateFrom = $this->MkCommon->getDate($date[0]);
                        $dateTo = $this->MkCommon->getDate($date[1]);
                        $invoice_conditions['DATE_FORMAT(Invoice.period_from, \'%Y-%m-%d\') >='] = $dateFrom;
                        $invoice_conditions['DATE_FORMAT(Invoice.period_to, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Invoice']['date'] = $dateStr;
                }

                if(!empty($refine['customer'])){
                    $customer = urldecode($refine['customer']);
                    $this->request->data['Ttuj']['customer'] = $customer;
                    $invoice_conditions['CustomerNoType.id'] = $customer;
                }

                if(!empty($refine['no_invoice'])){
                    $no_invoice = urldecode($refine['no_invoice']);
                    $this->request->data['Invoice']['no_invoice'] = $no_invoice;
                    $invoice_conditions['Invoice.no_invoice LIKE'] = '%'.$no_invoice.'%';
                }

                if(!empty($refine['status'])){
                    $status = urldecode($refine['status']);
                    $this->request->data['Invoice']['status'] = $status;

                    switch ($status) {
                        case 'paid':
                            $invoice_conditions['Invoice.complete_paid '] = 1;
                            break;

                        case 'halfpaid':
                            $invoice_conditions['Invoice.complete_paid '] = 0;
                            $invoice_conditions['Invoice.paid '] = 1;
                            break;

                        case 'void':
                            $invoice_conditions['Invoice.is_canceled '] = 1;
                            break;
                        
                        default:
                            $invoice_conditions['Invoice.complete_paid '] = 0;
                            $invoice_conditions['Invoice.paid '] = 0;
                            $invoice_conditions['Invoice.is_canceled '] = 0;
                            break;
                    }
                }

                if(!empty($refine['page'])){
                    $start = (($refine['page']-1)*$limit)+1;
                }
            }

            $options = array(
                'conditions' => $invoice_conditions,
                'order' => array(
                    'Invoice.modified' => 'DESC',
                    'Invoice.id' => 'DESC',
                ),
                'contain' => array(
                    'CustomerNoType'
                ),
            );

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = $limit;
            }

            $this->paginate = $options;
            $invoices = $this->paginate('Invoice');
            $invoiceUnpaidOption = array(
                'Invoice.is_canceled' => 0,
                'Invoice.complete_paid' => 0,
                'Invoice.paid' => 0,
                'Invoice.status' => 1,
            );
            $invoicePaidOption = array(
                'Invoice.is_canceled' => 0,
                'Invoice.complete_paid' => 1,
                'Invoice.status' => 1,
            );
            $invoiceHalfPaidOption = array(
                'Invoice.is_canceled' => 0,
                'Invoice.complete_paid' => 0,
                'Invoice.paid' => 1,
                'Invoice.status' => 1,
            );
            $invoiceVoidOption = array(
                'Invoice.is_canceled' => 1,
            );
            $dataStatus['InvoiceUnpaid'] = $this->Invoice->getData('count', array(
                'conditions' => $invoiceUnpaidOption,
            ));
            $dataStatus['InvoicePaid'] = $this->Invoice->getData('count', array(
                'conditions' => $invoicePaidOption,
            ));
            $dataStatus['InvoiceHalfPaid'] = $this->Invoice->getData('count', array(
                'conditions' => $invoiceHalfPaidOption,
            ));
            $dataStatus['InvoiceVoid'] = $this->Invoice->getData('count', array(
                'conditions' => $invoiceVoidOption,
            ), false);

            if( !empty($invoices) ) {
                foreach ($invoices as $key => $invoice) {
                    $invoice = $this->Revenue->RevenueDetail->getSumUnit($invoice, $invoice['Invoice']['id']);
                    $invoice = $this->Invoice->getMergePayment($invoice, $invoice['Invoice']['id'] );
                    $invoices[$key] = $invoice;
                }
            }

            $customers = $this->Invoice->Customer->getData('list', array(
                'fields' => array(
                    'Customer.id', 'Customer.name'
                )
            ));
            $this->set('customers', $customers);
            $this->set('sub_module_title', __('List Kwitansi'));
            $this->set('active_menu', 'list_kwitansi');

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }

            $this->set(compact(
                'invoices', 'data_action', 'start',
                'dataStatus'
            ));
        // }
    }

    function invoice_delete($id){
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $this->loadModel('Invoice');
        $invoice = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.status' => 1,
                'Invoice.id' => $id
            ),
            'contain' => array(
                'InvoiceDetail'
            )
        ));
        
        // if( !empty($invoice) && empty($invoice['Invoice']['complete_paid']) && empty($invoice['Invoice']['paid']) ){
        if( !empty($invoice) ){
            if(!empty($this->request->data)){
                if(!empty($this->request->data['Invoice']['canceled_date'])){
                    $this->request->data['Invoice']['canceled_date'] = $this->MkCommon->getDate($this->request->data['Invoice']['canceled_date']);
                    $this->request->data['Invoice']['is_canceled'] = 1;
                    $this->request->data['Invoice']['paid'] = 0;
                    $this->request->data['Invoice']['complete_paid'] = 0;
                    $this->request->data['Invoice']['status'] = 0;

                    $this->Invoice->id = $id;
                    $this->Invoice->set($this->request->data);

                    if($this->Invoice->save()){
                        $this->Invoice->InvoiceDetail->updateAll(
                            array(
                                'InvoiceDetail.status' => 0
                            ),
                            array(
                                'InvoiceDetail.invoice_id' => $id,
                            )
                        );

                        if($invoice['Invoice']['type_invoice'] == 'region' && !empty($invoice['InvoiceDetail'])){
                            $revenue_id = Set::extract('/InvoiceDetail/revenue_id', $invoice);
                        }else{
                            $revenue_id = $this->Invoice->InvoiceDetail->Revenue->RevenueDetail->getData('list', array(
                                'conditions' => array(
                                    'RevenueDetail.invoice_id' => $id
                                ),
                                'group' => array(
                                    'RevenueDetail.revenue_id'
                                ),
                                'fields' => array(
                                    'RevenueDetail.revenue_id'
                                )
                            ));
                        }

                        if(!empty($revenue_id)){
                            $this->Invoice->InvoiceDetail->Revenue->updateAll(
                                array(
                                    'transaction_status' => "'posting'"
                                ),
                                array(
                                    'Revenue.id' => $revenue_id
                                )
                            );
                        }

                        $invoice_payment_id = $this->Invoice->InvoicePaymentDetail->getData('list', array(
                            'conditions' => array(
                                'InvoicePaymentDetail.invoice_id' => $id
                            ),
                            'group' => array(
                                'InvoicePaymentDetail.invoice_payment_id'
                            ),
                            'fields' => array(
                                'InvoicePaymentDetail.invoice_payment_id'
                            )
                        ));

                        if(!empty($invoice_payment_id)){
                            $this->Invoice->InvoicePaymentDetail->updateAll(
                                array(
                                    'InvoicePaymentDetail.status' => 0
                                ),
                                array(
                                    'InvoicePaymentDetail.invoice_payment_id' => $invoice_payment_id
                                )
                            );

                            $this->Invoice->InvoicePaymentDetail->InvoicePayment->updateAll(
                                array(
                                    'InvoicePayment.status' => 0,
                                    'InvoicePayment.is_canceled' => 1,
                                    'InvoicePayment.canceled_date' => "'".$this->request->data['Invoice']['canceled_date']."'"
                                ),
                                array(
                                    'InvoicePayment.id' => $invoice_payment_id
                                )
                            );
                        }

                        $msg = array(
                            'msg' => __('Berhasil menghapus invoice.'),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan invoice.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('invoice', $invoice);
        }else{
            // if( !empty($invoice['Invoice']['complete_paid']) || !empty($invoice['Invoice']['paid']) ) {
            //     $msg = array(
            //         'msg' => __('Invoice telah dibayar, tidak dapat dibatalkan'),
            //         'type' => 'error'
            //     );
            // } else {
                $msg = array(
                    'msg' => __('Invoice tidak ditemukan'),
                    'type' => 'error'
                );
            // }
        }

        $this->set(compact('msg', 'is_ajax'));
    }

    public function report_customers( $data_action = false ) {
        $this->loadModel('Customer');
        $this->loadModel('Invoice');
        $this->loadModel('InvoicePayment');
        $fromMonth = '01';
        $fromYear = date('Y');
        $toMonth = date('m');
        $conditions = array(
            'Customer.status' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['Customer.id '] = $customer;
            }

            if( !empty($refine['fromMonth']) ){
                $fromMonth = urldecode($refine['fromMonth']);
            }

            if( !empty($refine['fromYear']) ){
                $fromYear = urldecode($refine['fromYear']);
            }

            if( !empty($refine['toMonth']) ){
                $toMonth = urldecode($refine['toMonth']);
            }
        }
        $customers = $this->Customer->getData('all', array(
            'conditions' => $conditions,
        ));

        $fromDt = sprintf('%s-%s-01', $fromYear, $fromMonth);
        $fromDt = date('Y-m', strtotime($fromDt.' -1 day'));
        $toDt = sprintf('%s-%s', $fromYear, $toMonth);
        $totalCnt = $toMonth - $fromMonth;
        $avgYear = $fromYear - 1;

        if( !empty($customers) ) {
            foreach ($customers as $key => $customer) {
                $conditionsYear = array(
                    'DATE_FORMAT(Invoice.invoice_date, \'%Y\')' => $avgYear,
                    'Invoice.customer_id' => $customer['Customer']['id'],
                    'Invoice.status' => 1,
                );
                $invoiceYear = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsYear,
                    'group' => array(
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y\')'
                    ),
                    'fields'=> array(
                        'Invoice.customer_id', 
                        'SUM(Invoice.total) as total',
                    ),
                ), false);
                $customer['InvoiceYear'] = !empty($invoiceYear[0]['total'])?$invoiceYear[0]['total']/12:0;

                $invoices = $this->Invoice->getData('all', array(
                    'conditions' => array(
                        // 'Invoice.status' => 1,
                        'Invoice.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') >=' => $fromDt,
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <=' => $toDt,
                    ),
                    'fields' => array(
                        'SUM(Invoice.total) total',
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') invoice_date'
                    ),
                    'group' => array(
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\')'
                    ),
                ), false);

                $invoicePayments = $this->InvoicePayment->InvoicePaymentDetail->getData('all', array(
                    'conditions' => array(
                        'Invoice.status' => 1,
                        'InvoicePayment.status' => 1,
                        'InvoicePayment.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') >=' => $fromDt,
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') <=' => $toDt,
                    ),
                    'fields' => array(
                        'SUM(InvoicePaymentDetail.price_pay) total',
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') date_payment'
                    ),
                    'group' => array(
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\')'
                    ),
                    'contain' => array(
                        'Invoice',
                        'InvoicePayment',
                    ),
                ), false);
                $invoiceVoids = $this->Invoice->getData('all', array(
                    'conditions' => array(
                        'Invoice.is_canceled' => 1,
                        'Invoice.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') >=' => $fromDt,
                        'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') <=' => $toDt,
                    ),
                    'fields' => array(
                        'SUM(Invoice.total) total',
                        'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') canceled_date'
                    ),
                    'group' => array(
                        'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\')'
                    ),
                ), false);

                if( !empty($invoices) ) {
                    foreach ($invoices as $key_invoice => $invoices) {
                        if( !empty($invoices[0]['invoice_date']) ) {
                            $dt = $invoices[0]['invoice_date'];
                            $customer['Invoice'][$dt] = $invoices[0]['total'];
                        }
                    }
                }

                if( !empty($invoicePayments) ) {
                    foreach ($invoicePayments as $key_invoice => $invoicePayment) {
                        if( !empty($invoicePayment[0]['date_payment']) ) {
                            $dt = $invoicePayment[0]['date_payment'];
                            $customer['InvoicePayment'][$dt] = $invoicePayment[0]['total'];
                        }
                    }
                }

                if( !empty($invoiceVoids) ) {
                    foreach ($invoiceVoids as $key_invoice => $invoiceVoid) {
                        if( !empty($invoiceVoid[0]['canceled_date']) ) {
                            $dt = $invoiceVoid[0]['canceled_date'];
                            $customer['InvoiceVoid'][$dt] = $invoiceVoid[0]['total'];
                        }
                    }
                }

                $monthDt = date('Y-m', mktime(0, 0, 0, $fromMonth-1, 1, $fromYear));
                $invoicesBefore = $this->Invoice->getData('first', array(
                    'conditions' => array(
                        'Invoice.status' => 1,
                        'Invoice.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <=' => $monthDt,
                    ),
                    'fields' => array(
                        'SUM(Invoice.total) total',
                    ),
                ));

                $invoicePaymentsBefore = $this->InvoicePayment->getData('first', array(
                    'conditions' => array(
                        'InvoicePayment.status' => 1,
                        'InvoicePayment.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') <=' => $monthDt,
                    ),
                    'fields' => array(
                        'SUM(InvoicePayment.total_payment) total',
                    ),
                ));
                $invoiceVoidBefore = $this->Invoice->getData('first', array(
                    'conditions' => array(
                        'Invoice.is_canceled' => 1,
                        'Invoice.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') <=' => $monthDt,
                    ),
                    'fields' => array(
                        'SUM(Invoice.total) total',
                    ),
                ), false);
                $totalInvoice = !empty($invoicesBefore[0]['total'])?$invoicesBefore[0]['total']:0;
                $totalInvoicePayment = !empty($invoicePaymentsBefore[0]['total'])?$invoicePaymentsBefore[0]['total']:0;
                $totalInvoicePaymentVoid = !empty($invoiceVoidBefore[0]['total'])?$invoiceVoidBefore[0]['total']:0;
                $saldoInvoice = $totalInvoice - $totalInvoicePayment - $totalInvoicePaymentVoid;

                if( !empty($saldoInvoice) ) {
                    $customer['InvoiceBefore'][$monthDt] = $saldoInvoice;
                }

                $customers[$key] = $customer;
            }
        }

        $customerList = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name'
            )
        ));

        $this->set('sub_module_title', __('Laporan Piutang Per Customer'));
        $this->set('active_menu', 'report_customers');

        $this->set(compact(
            'customers', 'data_action', 'totalCnt',
            'fromYear', 'fromMonth', 'toMonth',
            'customerList', 'avgYear'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function surat_jalan( $ttuj_id = false ) {
        // if( in_array('view_ttuj', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->loadModel('SuratJalan');

            $ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $ttuj_id
                )
            ));

            if( !empty($ttuj) ) {
                $ttuj = $this->Ttuj->getSumUnit( $ttuj, $ttuj_id );
                $qtySJNow = !empty($ttuj['QtySJ'])?$ttuj['QtySJ']:0;
                $qtyTipeMotor = !empty($ttuj['Qty'])?$ttuj['Qty']:0;
                $flagAdd = false;
                $this->set('active_menu', 'surat_jalan');
                $this->set('sub_module_title', __('Surat Jalan'));

                if( $qtySJNow < $qtyTipeMotor ) {
                    $flagAdd = true;
                }

                $suratJalans = $this->SuratJalan->getData('all', array(
                    'conditions' => array(
                        'SuratJalan.status' => 1,
                        'SuratJalan.ttuj_id' => $ttuj_id,
                    ),
                ));

                $this->set('active_menu', 'ttuj');
                $this->set('suratJalans', $suratJalans);
                $this->set('ttuj_id', $ttuj_id);
                $this->set('ttuj', $ttuj);
                $this->set('flagAdd', $flagAdd);
            } else {
                $this->redirect($this->referer());
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function surat_jalan_add( $id = false ){
        // if( in_array('insert_surat_jalan', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $id
                )
            ));

            if( !empty($ttuj) ) {
                $ttuj = $this->Ttuj->getSumUnit( $ttuj, $id );
                $this->set('sub_module_title', __('Terima Surat Jalan'));
                $this->doSuratJalan($id, $ttuj);
            } else {
                $this->redirect($this->referer());
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    public function surat_jalan_edit($id = false) {
        // if( in_array('update_surat_jalan', $this->allowModule) ) {
            $this->loadModel('SuratJalan');
            $suratJalan = $this->SuratJalan->getData('first', array(
                'conditions' => array(
                    'SuratJalan.id' => $id,
                )
            ));

            if( !empty($suratJalan['Ttuj']) ) {
                $suratJalan = $this->SuratJalan->Ttuj->getSumUnit( $suratJalan, $suratJalan['SuratJalan']['ttuj_id'], $suratJalan['SuratJalan']['id'] );
                $this->set('sub_module_title', __('Terima Surat Jalan'));
                $this->doSuratJalan($suratJalan['Ttuj']['id'], $suratJalan);
            } else {
                $this->redirect($this->referer());
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function doSuratJalan( $ttuj_id = false, $ttuj = false ){
        $qtySJNow = !empty($ttuj['QtySJ'])?$ttuj['QtySJ']:0;
        $qtyTipeMotor = !empty($ttuj['Qty'])?$ttuj['Qty']:0;

        if( $qtySJNow < $qtyTipeMotor ) {
            if(!empty($this->request->data)){
                $this->loadModel('SuratJalan');
                $data = $this->request->data;
                $qtySJDiterima = !empty($data['SuratJalan']['qty'])?$data['SuratJalan']['qty']:0;
                $qtySJNow += $qtySJDiterima;
                $data['SuratJalan']['tgl_surat_jalan'] = $this->MkCommon->getDate($data['SuratJalan']['tgl_surat_jalan']);
                $data['SuratJalan']['ttuj_id'] = $ttuj_id;

                if( !empty($ttuj['SuratJalan']['id']) ) {
                    $this->SuratJalan->id = $ttuj['SuratJalan']['id'];
                } else {
                    $this->SuratJalan->create();
                }

                $this->SuratJalan->set($data);

                if($this->SuratJalan->validates($data)){
                    if($this->SuratJalan->save($data)){
                        if( $qtySJNow >= $qtyTipeMotor ) {
                            $this->SuratJalan->Ttuj->set('is_sj_completed', 1);
                        } else {
                            $this->SuratJalan->Ttuj->set('is_sj_completed', 0);
                        }
                        $this->SuratJalan->Ttuj->id = $ttuj_id;
                        $this->SuratJalan->Ttuj->save();

                        $this->MkCommon->setCustomFlash(__('Sukses menyimpan penerimaan SJ'), 'success');
                        $this->Log->logActivity( sprintf(__('Sukses menerima SJ #%s'), $this->SuratJalan->id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                        $this->redirect(array(
                            'controller' => 'revenues',
                            'action' => 'surat_jalan',
                            $ttuj_id,
                        ));
                    }else{
                        $this->MkCommon->setCustomFlash(__('Gagal menyimpan penerimaan SJ'), 'error'); 
                        $this->Log->logActivity( __('Gagal menyimpan penerimaan SJ'), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                    }
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menyimpan penerimaan SJ'), 'error');
                }
            } else if( !empty($ttuj['SuratJalan']) ) {
                $this->request->data = $ttuj;
                $this->request->data['SuratJalan']['tgl_surat_jalan'] = $this->MkCommon->getDate($this->request->data['SuratJalan']['tgl_surat_jalan'], true);
            }

            $this->set('active_menu', 'ttuj');
            $this->set('ttuj_id', $ttuj_id);
            $this->render('surat_jalan_add');
        } else {
            $this->MkCommon->setCustomFlash(__('SJ telah lengkap diterima'), 'error');
            $this->redirect($this->referer());
        }
    }

    function surat_jalan_delete( $id = false ){
        // if( in_array('delete_cities', $this->allowModule) ) {
            $this->loadModel('SuratJalan');
            $this->loadModel('Ttuj');
            $locale = $this->SuratJalan->getData('first', array(
                'conditions' => array(
                    'SuratJalan.id' => $id
                )
            ));

            if( !empty($locale) && !empty($locale['Ttuj']['id']) ){
                $this->SuratJalan->id = $id;
                $this->SuratJalan->set('status', 0);

                if($this->SuratJalan->save()){
                    $this->Ttuj->id = $locale['Ttuj']['id'];
                    $this->Ttuj->set('is_sj_completed', 0);
                    $this->Ttuj->save();

                    $this->MkCommon->setCustomFlash(__('Sukses membatalkan SJ.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses membatalkan SJ ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal membatalkan SJ.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal membatalkan SJ ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('SJ tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    public function surat_jalan_outstanding( $driver_id = false ) {
        // if( in_array('delete_cities', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->loadModel('Revenue');
            $this->loadModel('Driver');
            $driver = $this->Driver->getData('first', array(
                'conditions' => array(
                    'Driver.id' => $driver_id,
                )
            ), false);

            if( !empty($driver) ) {
                $ttujs = $this->Ttuj->getData('all', array(
                    'conditions' => array(
                        'Ttuj.driver_id' => $driver_id,
                        'Ttuj.is_sj_completed' => 0,
                        'Ttuj.status' => 1,
                    )
                ), false);

                if( !empty($ttujs) ) {
                    foreach ($ttujs as $key => $ttuj) {
                        $ttuj = $this->Revenue->getPaid( $ttuj, $ttuj['Ttuj']['id'] );
                        $ttujs[$key] = $ttuj;
                    }

                    $this->set('sub_module_title', __('Surat Jalan Belum Kembali'));
                    $this->set('active_menu', 'ttuj');
                    $this->set('ttujs', $ttujs);
                    $this->set('driver', $driver);
                } else {
                    $this->MkCommon->setCustomFlash(__('SJ tidak ditemukan.'), 'error');
                    $this->redirect($this->referer());
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Supir tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function report_invoice_payments(){
        // if( in_array('view_invoice_payments', $this->allowModule) ) {
            // $this->loadModel('Invoice');
            // $this->loadModel('InvoicePayment');
            // $this->loadModel('Customer');
            
            // $this->set('active_menu', 'invoice_payments');
            // $this->set('sub_module_title', __('Pembayaran Invoice'));

            // $conditions = array();
            // if(!empty($this->params['named'])){
            //     $refine = $this->params['named'];

            //     if(!empty($refine['from'])){
            //         $from = urldecode(rawurldecode($refine['from']));
            //         $this->request->data['InvoicePayment']['date_from'] = $from;
            //         $conditions['DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m-%d\') >= '] = $this->MkCommon->getDate($from);
            //     }
            //     if(!empty($refine['to'])){
            //         $to = urldecode(rawurldecode($refine['to']));
            //         $this->request->data['InvoicePayment']['date_to'] = $to;
            //         $conditions['DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m-%d\') <= '] = $this->MkCommon->getDate($to);
            //     }
            //     if(!empty($refine['nodoc'])){
            //         $to = urldecode(rawurldecode(rawurldecode($refine['nodoc'])));
            //         $this->request->data['InvoicePayment']['nodoc'] = $to;
            //         $conditions['InvoicePayment.nodoc LIKE'] = '%'.$to.'%';
            //     }
            // }

            // $this->paginate = $this->InvoicePayment->getData('paginate', array(
            //     'conditions' => $conditions,
            //     'contain' => array(
            //         'Bank'
            //     ),
            // ), false);
            // $invoices = $this->paginate('InvoicePayment');

            // if(!empty($invoices)){
            //     foreach ($invoices as $key => $value) {
            //         $invoices[$key] = $this->InvoicePayment->Customer->getMerge($value, $value['InvoicePayment']['customer_id']);
            //     }
            // }
            
            // $this->set('invoices', $invoices); 

            // $customers = $this->Invoice->Customer->getData('list', array(
            //     'fields' => array(
            //         'Customer.id', 'Customer.name'
            //     )
            // ));
            // $this->set('customers', $customers);
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    public function report_revenue_customers( $data_action = false ) {
        // if( in_array('view_achievement_report', $this->allowModule) ) {
            $this->loadModel('Customer');
            $this->loadModel('Revenue');
            $fromMonth = date('m');
            $fromYear = date('Y');
            $toMonth = 12;
            $toYear = date('Y');
            $conditions = array(
                'Revenue.status'=> 1,
            );
            $conditionsCustomer = array(
                'Customer.status'=> 1,
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['customer'])){
                    $customer = urldecode($refine['customer']);
                    $this->request->data['Ttuj']['customer'] = $customer;
                    $conditionsCustomer['Customer.id'] = $customer;
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

            $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') >='] = date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear));
            $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') <='] = date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear));


            $customerList = $this->Customer->getData('list', array(
                'fields' => array(
                    'Customer.id', 'Customer.customer_name'
                )
            ));

            $options = $this->Customer->getData('paginate', array(
                'conditions' => $conditionsCustomer,
            ));

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }

            $this->paginate = $options;
            $customers = $this->paginate('Customer');
            $avgYear = $fromYear - 1;

            if( !empty($customers) ) {
                foreach ($customers as $key => $customer) {
                    $conditions['Revenue.customer_id'] = $customer['Customer']['id'];
                    $revenues = $this->Revenue->getData('all', array(
                        'conditions' => $conditions,
                        'contain' => array(
                            'CustomerNoType',
                        ),
                        'order' => array(
                            'CustomerNoType.name' => 'ASC', 
                        ),
                        'group' => array(
                            'DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\')'
                        ),
                        'fields'=> array(
                            'Revenue.customer_id', 
                            'SUM(Revenue.total) as total',
                            'DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') as dt',
                        ),
                    ), false);

                    $conditionsYear = array(
                        'DATE_FORMAT(Revenue.date_revenue, \'%Y\')' => $avgYear,
                        'Revenue.customer_id' => $customer['Customer']['id'],
                        'Revenue.status' => 1,
                    );
                    $revenueYear = $this->Revenue->getData('first', array(
                        'conditions' => $conditionsYear,
                        'group' => array(
                            'DATE_FORMAT(Revenue.date_revenue, \'%Y\')'
                        ),
                        'fields'=> array(
                            'Revenue.customer_id', 
                            'SUM(Revenue.total) as total',
                        ),
                    ), false);
                    $customer['RevenueYear'] = !empty($revenueYear[0]['total'])?$revenueYear[0]['total']/12:0;

                    if( !empty($revenues) ) {
                        foreach ($revenues as $keyRevenue => $revenue) {
                            $customer['Customer'][$revenue[0]['dt']]['total_revenue'] = !empty($revenue[0]['total'])?$revenue[0]['total']:0;
                        }
                    }
                    $customers[$key] = $customer;
                }
            }

            $module_title = __('Laporan Pendapatan Per Customer Per Bulan');
            $period_text = sprintf('Periode %s %s - %s %s', date('F', mktime(0, 0, 0, $fromMonth, 10)), $fromYear, date('F', mktime(0, 0, 0, $toMonth, 10)), $toYear);
            $this->set('sub_module_title', $module_title);
            $this->set('period_text', $period_text);
            $this->set('active_menu', 'report_revenue_customers');
            $totalCnt = $toMonth - $fromMonth;
            $totalYear = $toYear - $fromYear;
            $this->request->data['Ttuj']['from']['month'] = $fromMonth;
            $this->request->data['Ttuj']['from']['year'] = $fromYear;
            $this->request->data['Ttuj']['to']['month'] = $toMonth;
            $this->request->data['Ttuj']['to']['year'] = $toYear;

            if( !empty($totalYear) && $totalYear > 0 ) {
                $totalYear = 12 * $totalYear;
                $totalCnt += $totalYear;
            }

            $this->set(compact(
                'data_action', 'totalCnt',
                'customerList', 'fromMonth', 'fromYear',
                'toYear', 'toMonth', 'customers',
                'avgYear'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    public function report_monitoring_sj_revenue( $data_action = false ) {
        // if( in_array('view_achievement_report', $this->allowModule) ) {
            $this->loadModel('Customer');
            $this->loadModel('Revenue');
            $this->loadModel('Ttuj');
            $dateFrom = date('Y-m-01');
            $dateTo = date('Y-m-t');
            $options = array(
                'conditions' => array(
                    'Ttuj.is_revenue' => 1,
                    'Ttuj.status' => 1,
                    'Ttuj.is_draft' => 0,
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >=' => $dateFrom,
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <=' => $dateTo,
                ),
                'contain' => false,
                'order'=> array(
                    'Ttuj.created' => 'DESC',
                    'Ttuj.id' => 'DESC',
                ),
                'group' => array(
                    'Ttuj.id'
                ),
            );
            $this->request->data['Ttuj']['date'] = sprintf('%s - %s', date('d/m/Y',strtotime($dateFrom)), date('d/m/Y',strtotime($dateTo)));

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['customer'])){
                    $customer = urldecode($refine['customer']);
                    $this->request->data['Ttuj']['customer'] = $customer;
                    $options['conditions']['Ttuj.customer_id '] = $customer;
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
                        $options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
                        $options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
                    }
                    $this->request->data['Ttuj']['date'] = $dateStr;
                }

                if(!empty($refine['status'])){
                    $status = urldecode($refine['status']);
                    $this->request->data['Ttuj']['status'] = $status;
                    $options['contain'][] = 'SuratJalan';

                    $this->Ttuj->bindModel(array(
                        'hasOne' => array(
                            'SuratJalan' => array(
                                'className' => 'SuratJalan',
                                'foreignKey' => 'ttuj_id',
                                'conditions' => array(
                                    'SuratJalan.status' => 1,
                                ),
                            )
                        )
                    ));

                    switch ($status) {
                        case 'pending':
                            $options['conditions']['Ttuj.is_sj_completed'] = 0;
                            $options['conditions']['SuratJalan.id'] = NULL;
                            break;

                        case 'hal_receipt':
                            $options['conditions']['Ttuj.is_sj_completed'] = 0;
                            $options['conditions']['SuratJalan.id <>'] = NULL;
                            break;

                        case 'receipt':
                            $options['conditions']['Ttuj.is_sj_completed'] = 1;
                            break;
                    }
                }
            }

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }

            $ttujs = $this->paginate('Ttuj');
            // $ttujs = $this->Ttuj->find('all', $options);

            if( !empty($ttujs) ) {
                foreach ($ttujs as $key => $ttuj) {
                    $ttuj = $this->Ttuj->getSumUnit($ttuj, $ttuj['Ttuj']['id'], false, 'tgl_surat_jalan');
                    $ttuj = $this->Revenue->getPaid($ttuj, $ttuj['Ttuj']['id'], 'unit');
                    $ttuj = $this->Revenue->getPaid($ttuj, $ttuj['Ttuj']['id'], 'invoiced');
                    $ttuj = $this->Revenue->RevenueDetail->getToCity($ttuj, $ttuj['Ttuj']['id']);
                    $ttujs[$key] = $ttuj;
                }
            }

            $customerList = $this->Customer->getData('list', array(
                'fields' => array(
                    'Customer.id', 'Customer.customer_name'
                )
            ));

            $this->set('sub_module_title', __('Laporan Monitoring Surat Jalan & Revenue'));
            $this->set('active_menu', 'report_monitoring_sj_revenue');
            $period_text = sprintf('Periode %s - %s', date('d M Y',strtotime($dateFrom)), date('d M Y',strtotime($dateTo)));
            $this->set('period_text', $period_text);

            $this->set(compact(
                'ttujs', 'data_action', 'customerList'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }



    function invoice_hso_print($id, $action_print = false){
        $this->loadModel('Invoice');
        $this->loadModel('Revenue');
        $this->loadModel('GroupMotor');
        $this->loadModel('City');
        $this->loadModel('Customer');
        $this->loadModel('Ttuj');
        $this->loadModel('User');

        $module_title = __('Print Invoice HSO');
        $this->set('sub_module_title', trim($module_title));
        $this->set('active_menu', 'invoices');

        if( !empty($this->params['named']) ){
            $data_print = $this->params['named']['print'];
        } else {
            $data_print = 'invoice';
        }
        
        $invoice = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
                'Invoice.status' => array( 0,1 ),
            ),
        ));

        if(!empty($invoice)){
            $invoice = $this->Customer->getMerge($invoice, $invoice['Invoice']['customer_id']);

            switch ($data_print) {
                case 'header':
                    $invoice = $this->Invoice->InvoiceDetail->getMerge($invoice, $invoice['Invoice']['id']);

                    if( !empty($invoice['InvoiceDetail']) ) {
                        $revenue_id = Set::extract('/InvoiceDetail/revenue_id', $invoice['InvoiceDetail']);
                        $invoice = $this->Revenue->getMerge($invoice, $revenue_id, 'all');

                        // if( !empty($invoice['Revenue']) ) {
                        //     foreach ($invoice['Revenue'] as $key => $revenue) {
                        //         $revenue = $this->Revenue->RevenueDetail->getSumUnit($revenue, $revenue['Revenue']['id'], 'revenue');
                        //         $revenue = $this->Revenue->RevenueDetail->getSumUnit($revenue, $revenue['Revenue']['id'], 'revenue_price');
                        //         $invoice['Revenue'][$key] = $revenue;
                        //     }
                        // }
                    }
                    break;

                default:
                    $revenue_detail = $this->Revenue->RevenueDetail->getPreviewInvoice($invoice['Invoice']['id'], $invoice['Invoice']['tarif_type'], $action_print, $data_print);
                    break;
            }

            $this->set(compact(
                'invoice', 'action_print', 'revenue_detail'
            ));

            if($action_print == 'pdf'){
                $this->layout = 'pdf';
            }else if($action_print == 'excel'){
                $this->layout = 'ajax';
            }

            switch ($data_print) {
                case 'invoice':
                    $this->render('invoice_hso_non_header_print');
                    break;
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
            $this->redirect($this->referer());
        }
    }
}