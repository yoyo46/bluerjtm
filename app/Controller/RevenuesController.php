<?php
App::uses('AppController', 'Controller');
class RevenuesController extends AppController {
    public $uses = array();

    public $components = array(
        'RjRevenue'
    );

    public $helper = array(
        'PhpExcel', 'Revenue'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Revenue'));
        $this->set('module_title', __('Revenue'));
    }

    function search( $index = 'index', $id = false, $data_action = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjRevenue->processRefine($data);
            $params = $this->RjRevenue->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);

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
        $this->loadModel('Ttuj');
        $this->loadModel('SuratJalan');

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', 'ttuj');
        $this->set('sub_module_title', __('TTUJ'));
        $this->set('label_tgl', __('Tgl Berangkat'));

        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nottuj'])){
                $nottuj = urldecode($refine['nottuj']);
                $nottuj = $this->MkCommon->replaceSlash($nottuj);
                $this->request->data['Ttuj']['nottuj'] = $nottuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$nottuj.'%';
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Ttuj']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $nopol,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$nopol.'%',
                    );
                }

                $this->loadModel('City');
                $conditionsNopol = $this->City->getCityIdPlants( $conditionsNopol );

                $truckSearch = $this->Ttuj->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ));
                $conditions['Ttuj.truck_id'] = $truckSearch;
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
                    $conditions['DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') >='] = $dateFrom;
                    $conditions['DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') <='] = $dateTo;
                }
                $this->request->data['Ttuj']['date'] = $dateStr;
            }

            $conditions = $this->RjRevenue->_callRefineStatusTTUJ($refine, $conditions);
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Ttuj.status' => 'DESC',
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        ), true, array(
            'status' => 'all',
        ));
        $ttujs = $this->paginate('Ttuj');

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $ttuj) {
                $ttujs[$key] = $this->SuratJalan->getSJ( $ttuj, $ttuj['Ttuj']['id'] );
            }
        }

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
        $this->loadModel('Revenue');
        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id,
            ),
            'contain' => array(
                'UangJalan'
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($ttuj)){
            $is_draft = $this->MkCommon->filterEmptyField( $ttuj, 'Ttuj', 'is_draft' );

            if( empty($is_draft) ) {
                $allowEdit = $this->MkCommon->checkAllowFunction($this->params);

                if( empty($allowEdit) ) {
                    $this->redirect($this->referer());
                }
            }

            $ttuj = $this->Ttuj->getMergeContain( $ttuj, $id );
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
    }

    public function _callMergeTtujTipeMotor($dataTipeMotor, $data) {
        $tempTipeMotorId = array();

        if( !empty($dataTipeMotor) ) {
            foreach ($dataTipeMotor as $key => $tipe_motor_id) {
                $city_id = !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:0;
                $color_motor_id = !empty($data['TtujTipeMotor']['color_motor_id'][$key])?$data['TtujTipeMotor']['color_motor_id'][$key]:false;
                $qty = !empty($data['TtujTipeMotor']['qty'][$key])?$data['TtujTipeMotor']['qty'][$key]:false;

                if( isset($tempTipeMotorId[$city_id][$tipe_motor_id][$color_motor_id]) ) {
                    $idxData = $tempTipeMotorId[$city_id][$tipe_motor_id][$color_motor_id];

                    if( !empty($data['TtujTipeMotor']['city_id'][$key]) ) {
                        unset($data['TtujTipeMotor']['city_id'][$key]);
                    }
                    if( !empty($data['TtujTipeMotor']['tipe_motor_id'][$key]) ) {
                        unset($data['TtujTipeMotor']['tipe_motor_id'][$key]);
                    }
                    if( !empty($data['TtujTipeMotor']['color_motor_id'][$key]) ) {
                        unset($data['TtujTipeMotor']['color_motor_id'][$key]);
                    }
                    if( !empty($data['TtujTipeMotor']['qty'][$key]) ) {
                        if( !empty($data['TtujTipeMotor']['qty'][$idxData]) ) {
                            $data['TtujTipeMotor']['qty'][$idxData] += $data['TtujTipeMotor']['qty'][$key];
                        }

                        unset($data['TtujTipeMotor']['qty'][$key]);
                    }
                    if( !empty($dataTipeMotor[$key]) ) {
                        unset($dataTipeMotor[$key]);
                    }
                } else {
                    $tempTipeMotorId[$city_id][$tipe_motor_id][$color_motor_id] = $key;
                }
            }
        }

        return array(
            'Data' => $data,
            'DataTipeMotor' => $dataTipeMotor,
        );
    }

    function saveTtujTipeMotor ( $data_action, $dataTtujTipeMotor = false, $data = false, $dataRevenue = false, $ttuj_id = false, $revenue_id = false, $tarifDefault = false ) {
        $totalTarif = 0;
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($dataTtujTipeMotor) ) {
            if( !empty($ttuj_id) ) {
                $this->TtujTipeMotor->updateAll( array(
                    'TtujTipeMotor.status' => 0,
                ), array(
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                ));

                if( !empty($revenue_id) ) {
                    $this->RevenueDetail->updateAll(array(
                        'RevenueDetail.status' => 0
                    ), array(
                        'RevenueDetail.revenue_id' => $revenue_id,
                    ));
                }
            }

            foreach ($dataTtujTipeMotor as $key => $tipe_motor_id) {
                $group_motor_id = 0;
                $dataValidate['TtujTipeMotor']['tipe_motor_id'] = $tipe_motor_id;
                $dataValidate['TtujTipeMotor']['color_motor_id'] = !empty($data['TtujTipeMotor']['color_motor_id'][$key])?$data['TtujTipeMotor']['color_motor_id'][$key]:false;
                $dataValidate['TtujTipeMotor']['qty'] = !empty($data['TtujTipeMotor']['qty'][$key])?trim($data['TtujTipeMotor']['qty'][$key]):false;

                if( $data_action == 'retail' ) {
                    $dataValidate['TtujTipeMotor']['city_id'] = !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:false;
                }
                
                $this->TtujTipeMotor->set($dataValidate);

                if( !empty($dataRevenue) ) {
                    if( !empty($tipe_motor_id) ) {
                        $groupMotor = $this->TtujTipeMotor->TipeMotor->getData('first', array(
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
                    $tarif_angkutan_type = 'angkut';

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

                    $qtyMuatan = !empty($dataValidate['TtujTipeMotor']['qty'])?trim($dataValidate['TtujTipeMotor']['qty']):0;
                    $totalTarif += $priceUnit*$qtyMuatan;
                    $dataRevenue['RevenueDetail'] = array(
                        'revenue_id' => $revenue_id,
                        'group_motor_id' => $group_motor_id,
                        'qty_unit' => $qtyMuatan,
                        'city_id' => !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:$data['Ttuj']['to_city_id'],
                        'price_unit' => $priceUnit,
                        'payment_type' => $jenis_unit,
                        'tarif_angkutan_id' => $tarif_angkutan_id,
                        'tarif_angkutan_type' => $tarif_angkutan_type,
                        'from_ttuj' => 1,
                    );
                }

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujTipeMotor']['ttuj_id'] = $ttuj_id;
                    $this->TtujTipeMotor->create();
                    $this->TtujTipeMotor->save($dataValidate);

                    if( !empty($dataRevenue) ) {
                        $this->RevenueDetail->create();
                        $this->RevenueDetail->save($dataRevenue);
                    }
                } else {
                    if(!$this->TtujTipeMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }

            if( !empty($tarifDefault['tarif']) && $tarifDefault['tarif'] == 'per_unit' && !empty($revenue_id) && !empty($totalTarif) ) {
                $this->Revenue->set('total', $totalTarif);
                $this->Revenue->set('total_without_tax', $totalTarif);
                $this->Revenue->id = $revenue_id;
                $this->Revenue->save();
            }
        }

        return $result;
    }

    function saveTtujPerlengkapan ( $dataTtujPerlengkapan = false, $data = false, $ttuj_id = false ) {
        $this->loadModel('TtujPerlengkapan');
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($dataTtujPerlengkapan) ) {
            if( !empty($ttuj_id) ) {
                $this->TtujPerlengkapan->updateAll( array(
                    'TtujPerlengkapan.status' => 0,
                ), array(
                    'TtujPerlengkapan.ttuj_id' => $ttuj_id,
                ));
            }

            foreach ($dataTtujPerlengkapan as $key => $qty) {
                $dataValidate['TtujPerlengkapan']['qty'] = trim($qty);
                $dataValidate['TtujPerlengkapan']['perlengkapan_id'] = !empty($data['TtujPerlengkapan']['id'][$key])?$data['TtujPerlengkapan']['id'][$key]:false;
                $this->TtujPerlengkapan->set($dataValidate);

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujPerlengkapan']['ttuj_id'] = $ttuj_id;

                    $this->TtujPerlengkapan->create();
                    $this->TtujPerlengkapan->save($dataValidate);
                } else {
                    if(!$this->TtujPerlengkapan->validates($dataValidate)){
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
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');
        $this->loadModel('TarifAngkutan');
        $this->loadModel('Revenue');
        $this->loadModel('RevenueDetail');
        $this->loadModel('UangKuli');
        $this->loadModel('TtujTipeMotor');

        $paramController = $this->params['controller'];
        $paramAction = $this->params['action'];
        $is_draft = isset($data_local['Ttuj']['is_draft'])?$data_local['Ttuj']['is_draft']:true;

        $current_branch_id = Configure::read('__Site.config_branch_id');
        $_allowModule = Configure::read('__Site.config_allow_module');
        $group_id = Configure::read('__Site.config_group_id');

        $allowUpdate = false;
        $allowEditTtujBranch = false;

        if( !empty($_allowModule[$current_branch_id][$paramController]['action']) ) {
            $allowAction = $_allowModule[$current_branch_id][$paramController]['action'];

            if( in_array('ttuj_edit_branch', $allowAction) ) {
                $allowEditTtujBranch = true;
            }
        }
        if( $group_id == 1 ) {
            $allowEditTtujBranch = true;
        }

        $is_plant = Configure::read('__Site.config_branch_plant');
        $plantCityId = Configure::read('__Site.Branch.Plant.id');

        if( !empty($this->request->data) ) {
            $is_draft = true;
            $allowUpdate = true;
        }

        if( !empty($this->request->data) && $is_draft ){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Ttuj->id = $id;
                $msg = 'merubah';
                $no_ttuj = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'no_ttuj');
            }else{
                $this->Ttuj->create();
                $msg = 'menambah';
                $no_ttuj = $data['Ttuj']['no_ttuj'] = $this->Ttuj->generateNoId();
            }

            $customer_id = !empty($data['Ttuj']['customer_id'])?$data['Ttuj']['customer_id']:false;
            $from_city_id = !empty($data['Ttuj']['from_city_id'])?$data['Ttuj']['from_city_id']:false;
            $to_city_id = !empty($data['Ttuj']['to_city_id'])?$data['Ttuj']['to_city_id']:false;
            $truck_id = !empty($data['Ttuj']['truck_id'])?$data['Ttuj']['truck_id']:false;

            $uangJalan = $this->Ttuj->UangJalan->getData('first', array(
                'conditions' => array(
                    'UangJalan.from_city_id' => $from_city_id,
                    'UangJalan.to_city_id' => $to_city_id,
                ),
            ));
            $customer = $this->Ttuj->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $customer_id,
                ),
            ));
            $conditionsTruck = array(
                'Truck.id' => $truck_id,
            );

            if( !empty($plantCityId) ) {
                $conditionsTruck['Truck.branch_id'] = $plantCityId;
            }

            $truck = $this->Truck->getData('first', array(
                'conditions' => $conditionsTruck,
            ));

            if( !empty($truck) ) {
                $company_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'company_id');
                $truck = $this->Truck->Company->getMerge($truck, $company_id);
                $is_rjtm = $this->MkCommon->filterEmptyField($truck, 'Company', 'is_rjtm');
            }

            if( !empty($uangJalan) ) {
                $uangJalan = $this->City->getMerge($uangJalan, $from_city_id, 'FromCity');
                $uangJalan = $this->City->getMerge($uangJalan, $to_city_id, 'ToCity');
            }

            $data['Ttuj']['from_city_name'] = !empty($uangJalan['FromCity']['name'])?$uangJalan['FromCity']['name']:false;
            $data['Ttuj']['to_city_name'] = !empty($uangJalan['ToCity']['name'])?$uangJalan['ToCity']['name']:false;
            $data['Ttuj']['customer_name'] = !empty($customer['Customer']['customer_name_code'])?$customer['Customer']['customer_name_code']:'';
            $data['Ttuj']['uang_jalan_id'] = !empty($uangJalan['UangJalan']['id'])?$uangJalan['UangJalan']['id']:false;
            $data['Ttuj']['nopol'] = !empty($truck['Truck']['nopol'])?$truck['Truck']['nopol']:false;
            $data['Ttuj']['ttuj_date'] = $this->MkCommon->getDate($data['Ttuj']['ttuj_date']);
            $data['Ttuj']['driver_penganti_id'] = !empty($data['Ttuj']['driver_penganti_id'])?$data['Ttuj']['driver_penganti_id']:0;
            $data['Ttuj']['commission'] = !empty($uangJalan['UangJalan']['commission'])?$uangJalan['UangJalan']['commission']:0;
            $data['Ttuj']['commission_extra'] = $this->MkCommon->convertPriceToString($data['Ttuj']['commission_extra'], 0);
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

            if( empty($data['Ttuj']['branch_id']) || empty($allowEditTtujBranch) ) {
                $data['Ttuj']['branch_id'] = $current_branch_id;
            }

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

            // if( !empty($data['Ttuj']['completed_date']) ) {
            //     $data['Ttuj']['completed_date'] = $this->MkCommon->getDate($data['Ttuj']['completed_date']);
            // }

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

                        $resultMergeTipeMotor = $this->_callMergeTtujTipeMotor($dataTtujTipeMotor, $data);
                        $data = !empty($resultMergeTipeMotor['Data'])?$resultMergeTipeMotor['Data']:$data;
                        $dataTtujTipeMotor = !empty($resultMergeTipeMotor['DataTipeMotor'])?$resultMergeTipeMotor['DataTipeMotor']:$dataTtujTipeMotor;
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
                                $revenue_id = false;
                                $document_id = $this->Ttuj->id;
                                $document_no = !empty($data['Ttuj']['no_ttuj'])?$data['Ttuj']['no_ttuj']:false;

                                if( !empty($is_rjtm) && empty($is_draft) ) {
                                    $this->loadModel('Journal');

                                    $this->Journal->deleteJournal( $document_id, 'commission' );
                                    $this->Journal->deleteJournal( $document_id, 'uang_jalan' );
                                    $this->Journal->deleteJournal( $document_id, 'uang_kuli_muat' );
                                    $this->Journal->deleteJournal( $document_id, 'uang_kuli_bongkar' );
                                    $this->Journal->deleteJournal( $document_id, 'asdp' );
                                    $this->Journal->deleteJournal( $document_id, 'uang_kawal' );
                                    $this->Journal->deleteJournal( $document_id, 'uang_keamanan' );

                                    if ( $allowUpdate ) {
                                        if( !empty($data['Ttuj']['commission']) ) {
                                            $commissionJournal = $data['Ttuj']['commission'];

                                            if( !empty($data['Ttuj']['commission_extra']) ) {
                                                $commissionJournal += $data['Ttuj']['commission_extra'];
                                            }

                                            $this->Journal->setJournal( $document_id, $document_no, 'commission_coa_credit_id', 0, $commissionJournal, 'commission' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'commission_coa_debit_id', $commissionJournal, 0, 'commission' );
                                        }

                                        if( !empty($data['Ttuj']['uang_jalan_1']) ) {
                                            $uangJalanJournal = $data['Ttuj']['uang_jalan_1'];

                                            if( !empty($data['Ttuj']['uang_jalan_2']) ) {
                                                $uangJalanJournal += $data['Ttuj']['uang_jalan_2'];
                                            }

                                            if( !empty($data['Ttuj']['uang_jalan_extra']) ) {
                                                $uangJalanJournal += $data['Ttuj']['uang_jalan_extra'];
                                            }

                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_jalan_coa_credit_id', 0, $uangJalanJournal, 'uang_jalan' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_jalan_coa_debit_id', $uangJalanJournal, 0, 'uang_jalan' );
                                        }

                                        if( !empty($data['Ttuj']['uang_kuli_muat']) ) {
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_kuli_muat_coa_credit_id', 0, $data['Ttuj']['uang_kuli_muat'], 'uang_kuli_muat' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_kuli_muat_coa_debit_id', $data['Ttuj']['uang_kuli_muat'], 0, 'uang_kuli_muat' );
                                        }

                                        if( !empty($data['Ttuj']['uang_kuli_bongkar']) ) {
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_kuli_bongkar_coa_credit_id', 0, $data['Ttuj']['uang_kuli_bongkar'], 'uang_kuli_bongkar' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_kuli_bongkar_coa_debit_id', $data['Ttuj']['uang_kuli_bongkar'], 0, 'uang_kuli_bongkar' );
                                        }

                                        if( !empty($data['Ttuj']['asdp']) ) {
                                            $this->Journal->setJournal( $document_id, $document_no, 'asdp_coa_credit_id', 0, $data['Ttuj']['asdp'], 'asdp' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'asdp_coa_debit_id', $data['Ttuj']['asdp'], 0, 'asdp' );
                                        }

                                        if( !empty($data['Ttuj']['uang_kawal']) ) {
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_kawal_coa_credit_id', 0, $data['Ttuj']['uang_kawal'], 'uang_kawal' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_kawal_coa_debit_id', $data['Ttuj']['uang_kawal'], 0, 'uang_kawal' );
                                        }

                                        if( !empty($data['Ttuj']['uang_keamanan']) ) {
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_keamanan_coa_credit_id', 0, $data['Ttuj']['uang_keamanan'], 'uang_keamanan' );
                                            $this->Journal->setJournal( $document_id, $document_no, 'uang_keamanan_coa_debit_id', $data['Ttuj']['uang_keamanan'], 0, 'uang_keamanan' );
                                        }
                                    }
                                }

                                if( empty($data['Ttuj']['is_draft']) ) {
                                    $revenue = $this->Revenue->getData('first', array(
                                        'conditions' => array(
                                            'Revenue.ttuj_id' => $document_id,
                                        ),
                                    ));
                                    $transaction_status = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'transaction_status');
                                    $revenue_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'id');

                                    if( $transaction_status != 'posting' ) {
                                        $tarifDefault = $this->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity']);

                                        $dataRevenue['Revenue'] = array(
                                            'ttuj_id' => $document_id,
                                            'date_revenue' => $data['Ttuj']['ttuj_date'],
                                            'customer_id' => $data['Ttuj']['customer_id'],
                                            'revenue_tarif_type' => !empty($tarifDefault['jenis_unit'])?$tarifDefault['jenis_unit']:'per_unit',
                                            'branch_id' => $current_branch_id,
                                        );

                                        if( !empty($tarifDefault['jenis_unit']) && $tarifDefault['jenis_unit'] == 'per_truck' ) {
                                            $dataRevenue['Revenue']['total'] = $tarifDefault['tarif'];
                                            $dataRevenue['Revenue']['total_without_tax'] = $tarifDefault['tarif'];
                                            $dataRevenue['Revenue']['tarif_per_truck'] = $tarifDefault['tarif'];
                                        }

                                        if( !empty($revenue_id) ) {
                                            $this->Revenue->id = $revenue_id;
                                        } else {
                                            $this->Revenue->create();
                                        }

                                        $this->Revenue->save($dataRevenue);

                                        if( !empty($revenue_id) ) {
                                            $this->Log->logActivity( sprintf(__('Berhasil mengubah Revenue #%s dari TTUJ #%s'), $revenue_id, $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $revenue_id, 'revenue_ttuj_edit' );
                                        } else {
                                            $revenue_id = $this->Revenue->id;
                                            $this->Log->logActivity( sprintf(__('Berhasil menambah Revenue #%s dari TTUJ #%s'), $revenue_id, $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $revenue_id, 'revenue_ttuj_add' );
                                        }
                                    }
                                }

                                $this->saveTtujTipeMotor($data_action, $dataTtujTipeMotor, $data, $dataRevenue, $document_id, $revenue_id, $tarifDefault);

                                if( !empty($dataTtujPerlengkapan) ) {
                                    $this->saveTtujPerlengkapan($dataTtujPerlengkapan, $data, $document_id);
                                }

                                $this->params['old_data'] = $data_local;
                                $this->params['data'] = $data;

                                $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s TTUJ dengan no %s'), $msg, $no_ttuj), 'success');
                                $this->Log->logActivity( sprintf(__('Sukses %s TTUJ #%s'), $msg, $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $document_id );

                                $this->redirect(array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj'
                                ));
                            }else{
                                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error'); 
                                $this->Log->logActivity( sprintf(__('Gagal %s TTUJ #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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

            if( !empty($data['Ttuj']['from_city_id']) ) {
                $toCities = $this->Ttuj->UangJalan->getKotaTujuan($data['Ttuj']['from_city_id']);

                if( !empty($data['Ttuj']['to_city_id']) ) {
                    if( !empty($truck['Truck']['capacity']) ) {
                        $dataTruck = $this->Ttuj->UangJalan->getNopol($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $truck['Truck']['capacity']);
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
                                    $uangJalanTipeMotor['UangJalan'][$tipeMotor['UangJalanTipeMotor']['group_motor_id']] = $tipeMotor['UangJalanTipeMotor']['uang_jalan_1'];
                                }
                            }
                            if( !empty($uangJalan['CommissionGroupMotor']) ) {
                                foreach ($uangJalan['CommissionGroupMotor'] as $key => $tipeMotor) {
                                    $uangJalanTipeMotor['Commission'][$tipeMotor['CommissionGroupMotor']['group_motor_id']] = $tipeMotor['CommissionGroupMotor']['commission'];
                                }
                            }
                            if( !empty($uangJalan['AsdpGroupMotor']) ) {
                                foreach ($uangJalan['AsdpGroupMotor'] as $key => $tipeMotor) {
                                    $uangJalanTipeMotor['Asdp'][$tipeMotor['AsdpGroupMotor']['group_motor_id']] = $tipeMotor['AsdpGroupMotor']['asdp'];
                                }
                            }
                            if( !empty($uangJalan['UangKawalGroupMotor']) ) {
                                foreach ($uangJalan['UangKawalGroupMotor'] as $key => $tipeMotor) {
                                    $uangJalanTipeMotor['UangKawal'][$tipeMotor['UangKawalGroupMotor']['group_motor_id']] = $tipeMotor['UangKawalGroupMotor']['uang_kawal'];
                                }
                            }
                            if( !empty($uangJalan['UangKeamananGroupMotor']) ) {
                                foreach ($uangJalan['UangKeamananGroupMotor'] as $key => $tipeMotor) {
                                    $uangJalanTipeMotor['UangKeamanan'][$tipeMotor['UangKeamananGroupMotor']['group_motor_id']] = $tipeMotor['UangKeamananGroupMotor']['uang_keamanan'];
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
                                        $groupMotor = $this->TtujTipeMotor->TipeMotor->find('first', array(
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

                            // if( !empty($uangJalan['UangJalan']['uang_jalan_extra']) && !empty($uangJalan['UangJalan']['min_capacity']) ) {
                            if( !empty($uangJalan['UangJalan']['uang_jalan_extra']) ) {
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
                            $this->request->data['Ttuj']['uang_jalan_2'] = !empty($this->request->data['Ttuj']['uang_jalan_2'])?trim($this->request->data['Ttuj']['uang_jalan_2']):0;

                            $this->request->data['Ttuj']['uang_jalan_per_unit'] = !empty($uangJalan['UangJalan']['uang_jalan_per_unit'])?$uangJalan['UangJalan']['uang_jalan_per_unit']:0;
                            $this->request->data['Ttuj']['uang_kuli_muat_per_unit'] = !empty($uangJalan['UangJalan']['uang_kuli_muat_per_unit'])?$uangJalan['UangJalan']['uang_kuli_muat_per_unit']:0;
                            $this->request->data['Ttuj']['uang_kuli_bongkar_per_unit'] = !empty($uangJalan['UangJalan']['uang_kuli_bongkar_per_unit'])?$uangJalan['UangJalan']['uang_kuli_bongkar_per_unit']:0;
                            $this->request->data['Ttuj']['asdp_per_unit'] = !empty($uangJalan['UangJalan']['asdp_per_unit'])?$uangJalan['UangJalan']['asdp_per_unit']:0;
                            $this->request->data['Ttuj']['uang_kawal_per_unit'] = !empty($uangJalan['UangJalan']['uang_kawal_per_unit'])?$uangJalan['UangJalan']['uang_kawal_per_unit']:0;
                            $this->request->data['Ttuj']['uang_keamanan_per_unit'] = !empty($uangJalan['UangJalan']['uang_keamanan_per_unit'])?$uangJalan['UangJalan']['uang_keamanan_per_unit']:0;
                            $this->request->data['Ttuj']['uang_jalan_extra_per_unit'] = !empty($uangJalan['UangJalan']['uang_jalan_extra_per_unit'])?$uangJalan['UangJalan']['uang_jalan_extra_per_unit']:0;

                            if( !empty($data['Ttuj']['truck_id']) ) {
                                $truckInfo = $this->Truck->getInfoTruck($data['Ttuj']['truck_id'], $plantCityId);
                                $this->request->data['Ttuj']['driver_name'] = !empty($truckInfo['Driver']['name'])?$truckInfo['Driver']['name']:false;
                                $this->request->data['Ttuj']['truck_capacity'] = !empty($truckInfo['Truck']['capacity'])?$truckInfo['Truck']['capacity']:false;
                            }
                        }
                    }
                }
            }

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

                // if( !empty($data_local['Ttuj']['completed_date']) ) {
                //     $data_local['Ttuj']['completed_date'] = $this->MkCommon->getDate($data_local['Ttuj']['completed_date'], true);
                // }

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

            if( !empty($this->request->data['Ttuj']['from_city_id']) ) {
                $toCities = $this->Ttuj->UangJalan->getKotaTujuan($this->request->data['Ttuj']['from_city_id']);
            }
        }

        $customer_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'customer_id');
        $from_city_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'from_city_id');
        $customerConditions = array(
            'Customer.customer_type_id' => 2,
        );

        if( $data_action == 'retail' ) {
            $customerConditions['Customer.customer_type_id'] = 1;
            $tmpCities = $this->City->getData('list');
        }

        $customers = $this->Ttuj->Customer->getInclude($customerConditions, $customer_id);
        $fromCities = $this->Ttuj->UangJalan->getKotaAsal( $from_city_id );

        $ttuj_truck_id = !empty($data_local['Ttuj']['truck_id'])?$data_local['Ttuj']['truck_id']:false;
        $ttuj_truck_nopol = !empty($data_local['Ttuj']['nopol'])?$data_local['Ttuj']['nopol']:false;
        $trucks = $this->Truck->getListTruck($ttuj_truck_id, false, $ttuj_truck_nopol, $plantCityId);

        $driver_penganti_id = !empty($data_local['Ttuj']['driver_penganti_id'])?$data_local['Ttuj']['driver_penganti_id']:false;
        $driverPengantis = $this->Ttuj->Truck->Driver->getListDriverPenganti($driver_penganti_id);

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
        $tipeMotorTemps = $this->TtujTipeMotor->TipeMotor->getData('all');

        if( !empty($tipeMotorTemps) ) {
            foreach ($tipeMotorTemps as $key => $tipeMotorTemp) {
                $tipe_motor_id = $this->MkCommon->filterEmptyField($tipeMotorTemp, 'TipeMotor', 'id');
                $code_name = $this->MkCommon->filterEmptyField($tipeMotorTemp, 'TipeMotor', 'code_name');
                $group_motor_id = $this->MkCommon->filterEmptyField($tipeMotorTemp, 'GroupMotor', 'id');

                $tipeMotors[$tipe_motor_id] = $code_name;
                $groupTipeMotors[$tipe_motor_id] = $group_motor_id;
            }
        }

        $colors = $this->TtujTipeMotor->ColorMotor->getData('list', array(
            'fields' => array(
                'ColorMotor.id', 'ColorMotor.name',
            ),
        ));
        $branches = Configure::read('__Site.config_allow_branchs');
        $this->MkCommon->_layout_file('select');

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', 'ttuj');
        $this->set(compact(
            'trucks', 'customers', 'driverPengantis',
            'fromCities', 'toCities', 'uangJalan',
            'tipeMotors', 'perlengkapans',
            'truckInfo', 'data_local', 'data_action',
            'colors', 'tipeMotorTemps',
            'groupTipeMotors', 'uangKuli',
            'id', 'tmpCities', 'branches',
            'allowEditTtujBranch'
        ));
        $this->render('ttuj_form');
    }

    function ttuj_toggle( $id, $action_type = 'status' ){
        $this->loadModel('Ttuj');

        $conditions = array(
            'Ttuj.id' => $id,
        );
        $branchFlag = false;

        if( in_array($action_type, array( 'truk_tiba', 'bongkaran', 'balik' )) ) {
            $conditions = $this->Ttuj->_callConditionBranch( $conditions );
        } else if( $action_type == 'pool' ) {
            $conditions = $this->Ttuj->_callConditionTtujPool($conditions);
        } else {
            $branchFlag = true;
        }

        $locale = $this->Ttuj->getData('first', array(
            'conditions' => $conditions,
        ), true, array(
            'status' => 'all',
            'branch' => $branchFlag,
        ));

        if($locale){
            $value = true;
            if($locale['Ttuj']['status']){
                $value = false;
            }

            $this->Ttuj->id = $id;
            $deleteJournal = false;

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

                case 'pool':
                    $this->Ttuj->set('is_pool', 0);
                    break;
                
                default:
                    $this->Ttuj->set('status', 0);
                    $deleteJournal = true;
                    break;
            }

            if($this->Ttuj->save()){
                $this->loadModel('Journal');
                $document_no = !empty($locale['Ttuj']['no_ttuj'])?$locale['Ttuj']['no_ttuj']:false;

                if( $deleteJournal && empty($locale['Ttuj']['is_draft']) ) {
                    if( !empty($locale['Ttuj']['commission']) ) {
                        $commissionJournal = $locale['Ttuj']['commission'];

                        if( !empty($locale['Ttuj']['commission_extra']) ) {
                            $commissionJournal += $locale['Ttuj']['commission_extra'];
                        }

                        $this->Journal->setJournal( $id, $document_no, 'commission_coa_debit_id', 0, $commissionJournal, 'commission_void' );
                        $this->Journal->setJournal( $id, $document_no, 'commission_coa_credit_id', $commissionJournal, 0, 'commission_void' );
                    }

                    if( !empty($locale['Ttuj']['uang_jalan_1']) ) {
                        $uangJalanJournal = $locale['Ttuj']['uang_jalan_1'];

                        if( !empty($locale['Ttuj']['uang_jalan_2']) ) {
                            $uangJalanJournal += $locale['Ttuj']['uang_jalan_2'];
                        }

                        if( !empty($locale['Ttuj']['uang_jalan_extra']) ) {
                            $uangJalanJournal += $locale['Ttuj']['uang_jalan_extra'];
                        }

                        $this->Journal->setJournal( $id, $document_no, 'uang_jalan_coa_debit_id', 0, $uangJalanJournal, 'uang_jalan_void' );
                        $this->Journal->setJournal( $id, $document_no, 'uang_jalan_coa_credit_id', $uangJalanJournal, 0, 'uang_jalan_void' );
                    }

                    if( !empty($locale['Ttuj']['uang_kuli_muat']) ) {
                        $this->Journal->setJournal( $id, $document_no, 'uang_kuli_muat_coa_debit_id', 0, $locale['Ttuj']['uang_kuli_muat'], 'uang_kuli_muat_void' );
                        $this->Journal->setJournal( $id, $document_no, 'uang_kuli_muat_coa_credit_id', $locale['Ttuj']['uang_kuli_muat'], 0, 'uang_kuli_muat_void' );
                    }

                    if( !empty($locale['Ttuj']['uang_kuli_bongkar']) ) {
                        $this->Journal->setJournal( $id, $document_no, 'uang_kuli_bongkar_coa_debit_id', 0, $locale['Ttuj']['uang_kuli_bongkar'], 'uang_kuli_bongkar_void' );
                        $this->Journal->setJournal( $id, $document_no, 'uang_kuli_bongkar_coa_credit_id', $locale['Ttuj']['uang_kuli_bongkar'], 0, 'uang_kuli_bongkar_void' );
                    }

                    if( !empty($locale['Ttuj']['asdp']) ) {
                        $this->Journal->setJournal( $id, $document_no, 'asdp_coa_debit_id', 0, $locale['Ttuj']['asdp'], 'asdp_void' );
                        $this->Journal->setJournal( $id, $document_no, 'asdp_coa_credit_id', $locale['Ttuj']['asdp'], 0, 'asdp_void' );
                    }

                    if( !empty($locale['Ttuj']['uang_kawal']) ) {
                        $this->Journal->setJournal( $id, $document_no, 'uang_kawal_coa_debit_id', 0, $locale['Ttuj']['uang_kawal'], 'uang_kawal_void' );
                        $this->Journal->setJournal( $id, $document_no, 'uang_kawal_coa_credit_id', $locale['Ttuj']['uang_kawal'], 0, 'uang_kawal_void' );
                    }

                    if( !empty($locale['Ttuj']['uang_keamanan']) ) {
                        $this->Journal->setJournal( $id, $document_no, 'uang_keamanan_coa_debit_id', 0, $locale['Ttuj']['uang_keamanan'], 'uang_keamanan_void' );
                        $this->Journal->setJournal( $id, $document_no, 'uang_keamanan_coa_credit_id', $locale['Ttuj']['uang_keamanan'], 0, 'uang_keamanan_void' );
                    }
                }

                if( $action_type == 'status' ) {
                    $this->loadModel('Revenue');

                    $revenue = $this->Revenue->getData('first', array(
                        'conditions' => array(
                            'Revenue.ttuj_id' => $id,
                        ),
                    ));

                    $transaction_status = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'transaction_status');
                    $revenue_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'id');

                    if( $transaction_status != 'posting' ) {
                        $this->Revenue->id = $revenue_id;
                        $this->Revenue->set('status', 0);
                        $this->Revenue->save();
                        $this->Log->logActivity( sprintf(__('Berhasil membatalkan Revenue #%s dari TTUJ #%s'), $revenue_id, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $revenue_id, 'revenue_ttuj_toggle' );
                    }
                }

                $this->MkCommon->setCustomFlash(__('TTUJ berhasil dibatalkan.'), 'success');
                $this->Log->logActivity( sprintf(__('TTUJ ID #%s berhasil dibatalkan.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal membatalkan TTUJ.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal membatalkan TTUJ ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function truk_tiba() {
        $this->loadModel('Ttuj');

        $action_type = 'truk_tiba';
        $conditions = array(
            'Ttuj.is_arrive' => 1,
        );

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', $action_type);
        $this->set('sub_module_title', __('Truk Tiba'));
        $this->set('label_tgl', __('Tgl Tiba'));

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

            $conditions = $this->RjRevenue->_callRefineStatusTTUJ($refine, $conditions);
        }

        $conditions = $this->Ttuj->_callConditionBranch( $conditions );
        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Ttuj.tgljam_tiba' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        ), true, array(
            'branch' => false,
        ));
        $ttujs = $this->paginate('Ttuj');

        // if( !empty($ttujs) ) {
        //     foreach ($ttujs as $key => $ttuj) {
        //         $to_city_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'to_city_id');

        //         if( $this->Ttuj->validateTtujAfterLeave( $to_city_id, $this->GroupBranch->Branch ) ) {
        //             $ttujs[$key] = $ttuj;
        //         } else {
        //             unset($ttujs[$key]);
        //         }
        //     }
        // }

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function truk_tiba_add() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'truk_tiba');
        $this->doTTUJLanjutan();
    }

    public function ttuj_lanjutan_edit( $action_type = 'truk_tiba', $id = false ) {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'truk_tiba');

        $conditions = array(
            'Ttuj.id' => $id,
            'Ttuj.is_draft' => 0,
            'Ttuj.status' => 1,
        );

        switch ($action_type) {
            case 'pool':
                $conditions = $this->Ttuj->_callConditionTtujPool( $conditions );
                break;
            
            default:
                $conditions = $this->Ttuj->_callConditionBranch( $conditions );
                break;
        }

        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => $conditions,
        ), true, array(
            'branch' => false,
        ));

        if( !empty($ttuj) ) {
            $ttuj = $this->Ttuj->getMergeContain( $ttuj, $id );
            $to_city_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'to_city_id');

            // if( !$this->Ttuj->validateTtujAfterLeave( $to_city_id, $this->GroupBranch->Branch ) ) {
            //     $this->redirect(array(
            //         'action' => $action_type,
            //     ));
            // }

            $this->doTTUJLanjutan( $action_type, $id, $ttuj );
        } else {
            $this->MkCommon->setCustomFlash(__('Ttuj tidak ditemukan'), 'error');
            $this->redirect(array(
                'action' => $action_type,
            ));
        }
    }

    function doTTUJLanjutan( $action_type = 'truk_tiba', $id = false, $ttuj = false ){
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');
        $this->loadModel('ColorMotor');

        $data_action = false;

        if( !empty($this->params['named']['no_ttuj']) ) {
            $no_ttuj_id = $this->params['named']['no_ttuj'];
            $data_local = $this->Ttuj->_callDataTtujConditions($no_ttuj_id, $action_type);

            if( !empty($data_local) ) {
                $data_local = $this->Ttuj->getMergeContain( $data_local, $no_ttuj_id );

                if( !empty($data_local['Ttuj']['is_retail']) ) {
                    // $sub_module_title = __('Truk Tiba - RETAIL');
                    $data_action = 'retail';
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Ttuj tidak ditemukan'), 'error');
                $this->redirect(array(
                    'action' => $action_type,
                ));
            }
        }

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
                    $dataTiba['Ttuj']['tgljam_berangkat'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_berangkat');
                    $dataTiba['Ttuj']['tgljam_tiba'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_tiba');

                    if( !empty($data['Ttuj']['tgl_bongkaran']) ) {
                        $data['Ttuj']['tgl_bongkaran'] = $this->MkCommon->getDate($data['Ttuj']['tgl_bongkaran']);

                        if( !empty($data['Ttuj']['jam_bongkaran']) ) {
                            $data['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data['Ttuj']['jam_bongkaran']));
                            $data['Ttuj']['tgljam_bongkaran'] = $dataTiba['Ttuj']['tgljam_bongkaran'] = sprintf('%s %s', $data['Ttuj']['tgl_bongkaran'], $data['Ttuj']['jam_bongkaran']);
                        }
                    }
                    $referer = 'bongkaran';
                    break;

                case 'balik':
                    $dataTiba['Ttuj']['is_balik'] = 1;
                    $dataTiba['Ttuj']['tgljam_balik'] = '';
                    $dataTiba['Ttuj']['note_balik'] = !empty($data['Ttuj']['note_balik'])?$data['Ttuj']['note_balik']:'';
                    $dataTiba['Ttuj']['tgljam_berangkat'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_berangkat');
                    $dataTiba['Ttuj']['tgljam_tiba'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_tiba');
                    $dataTiba['Ttuj']['tgljam_bongkaran'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_bongkaran');

                    if( !empty($data['Ttuj']['tgl_balik']) ) {
                        $data['Ttuj']['tgl_balik'] = $this->MkCommon->getDate($data['Ttuj']['tgl_balik']);

                        if( !empty($data['Ttuj']['jam_balik']) ) {
                            $data['Ttuj']['jam_balik'] = date('H:i', strtotime($data['Ttuj']['jam_balik']));
                            $data['Ttuj']['tgljam_balik'] = $dataTiba['Ttuj']['tgljam_balik'] = sprintf('%s %s', $data['Ttuj']['tgl_balik'], $data['Ttuj']['jam_balik']);
                        }
                    }
                    $referer = 'balik';
                    break;

                case 'pool':
                    $dataTiba['Ttuj']['is_pool'] = 1;
                    $dataTiba['Ttuj']['tgljam_pool'] = '';
                    $dataTiba['Ttuj']['note_pool'] = !empty($data['Ttuj']['note_pool'])?$data['Ttuj']['note_pool']:'';
                    $dataTiba['Ttuj']['pool_branch_id'] = Configure::read('__Site.config_branch_id');
                    $dataTiba['Ttuj']['tgljam_berangkat'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_berangkat');
                    $dataTiba['Ttuj']['tgljam_tiba'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_tiba');
                    $dataTiba['Ttuj']['tgljam_bongkaran'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_bongkaran');
                    $dataTiba['Ttuj']['tgljam_balik'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_balik');

                    if( !empty($data['Ttuj']['tgl_pool']) ) {
                        $data['Ttuj']['tgl_pool'] = $this->MkCommon->getDate($data['Ttuj']['tgl_pool']);

                        if( !empty($data['Ttuj']['jam_pool']) ) {
                            $data['Ttuj']['jam_pool'] = date('H:i', strtotime($data['Ttuj']['jam_pool']));
                            $data['Ttuj']['tgljam_pool'] = $dataTiba['Ttuj']['tgljam_pool'] = sprintf('%s %s', $data['Ttuj']['tgl_pool'], $data['Ttuj']['jam_pool']);
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
                    $dataTiba['Ttuj']['tgljam_berangkat'] = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'tgljam_berangkat');

                    if( !empty($data['Ttuj']['tgl_tiba']) ) {
                        $data['Ttuj']['tgl_tiba'] = $this->MkCommon->getDate($data['Ttuj']['tgl_tiba']);

                        if( !empty($data['Ttuj']['jam_tiba']) ) {
                            $data['Ttuj']['jam_tiba'] = date('H:i', strtotime($data['Ttuj']['jam_tiba']));
                            $data['Ttuj']['tgljam_tiba'] = $dataTiba['Ttuj']['tgljam_tiba'] = sprintf('%s %s', $data['Ttuj']['tgl_tiba'], $data['Ttuj']['jam_tiba']);
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
                    $ttuj_id = $this->Ttuj->id;

                    $this->params['old_data'] = $ttuj;
                    $this->params['data'] = $dataTiba;

                    $this->MkCommon->setCustomFlash(__('Sukses merubah TTUJ'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah TTUJ ID #%s.'), $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $ttuj_id );
                    $this->redirect(array(
                        'controller' => 'revenues',
                        'action' => $referer
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah Ttuj'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah TTUJ #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }

            $this->request->data = $data;
        } else if( !empty($ttuj) ) {
            $this->request->data = $data_local = $ttuj;
        }

        if( !empty($data_local) ){
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

            if( !empty($this->request->data['Ttuj']) ) {
                $data_local['Ttuj'] = array_merge($data_local['Ttuj'], $this->request->data['Ttuj']);
            }

            $data_local = $this->MkCommon->generateDateTTUJ($data_local);
            $this->request->data = $data_local;

            if( !empty($id) ) {
                $ttuj_id = $id;
            }
        }

        if( !empty($this->params['named']['no_ttuj']) ) {
            $this->request->data['Ttuj']['no_ttuj'] = $this->params['named']['no_ttuj'];
        }

        $ttujs = $this->Ttuj->getTtujAfterLeave($id, $action_type);
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

        $this->set('sub_module_title', __('Tambah'));
        $this->set('module_title', __('TTUJ'));
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
        $this->set('module_title', __('TTUJ'));
        $conditions = array(
            'Ttuj.id' => $ttuj_id,
            'Ttuj.is_draft' => 0,
            'Ttuj.status' => 1,
        );

        switch ($action_type) {
            case 'bongkaran':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions = $this->Ttuj->_callConditionBranch( $conditions );
                
                $module_title = __('Info Bongkaran');
                $this->set('active_menu', 'bongkaran');
                break;

            case 'balik':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                $conditions = $this->Ttuj->_callConditionBranch( $conditions );

                $module_title = __('Info Truk Balik');
                $this->set('active_menu', 'balik');
                break;

            case 'pool':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions['Ttuj.is_bongkaran'] = 1;
                $conditions['Ttuj.is_balik'] = 1;
                $conditions['Ttuj.is_pool'] = 1;
                $conditions = $this->Ttuj->_callConditionTtujPool( $conditions );

                $module_title = __('Info Sampai Pool');
                $this->set('active_menu', 'pool');
                break;
            
            case 'truk_tiba':
                $conditions['Ttuj.is_arrive'] = 1;
                $conditions = $this->Ttuj->_callConditionBranch( $conditions );

                $module_title = __('Info Truk Tiba');
                $this->set('active_menu', 'truk_tiba');
                break;
            
            default:
                $conditions['Ttuj.is_draft'] = 0;

                $module_title = __('Info TTUJ');
                $this->set('active_menu', 'ttuj');
                break;
        }

        $data_action = false;
        $data_local = $this->Ttuj->getData('first', array(
            'conditions' => $conditions,
        ), true, array(
            'branch' => false,
        ));

        if( !empty($data_local) ){
            $data_local = $this->Ttuj->getMergeContain( $data_local, $ttuj_id );
            $data_local['Ttuj']['ttuj_date'] = date('d/m/Y', strtotime($data_local['Ttuj']['ttuj_date']));
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);
            $to_city_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'to_city_id');

            // if( !$this->Ttuj->validateTtujAfterLeave( $to_city_id, $this->GroupBranch->Branch ) ) {
            //     $this->redirect(array(
            //         'action' => $action_type,
            //     ));
            // } else {
                if( !empty($data_local['Ttuj']['is_retail']) ) {
                    $module_title = __('Info Truk Tiba - RETAIL');
                    $data_action = 'retail';
                }
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
            // }
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

        $action_type = 'bongkaran';
        $conditions = array(
            'Ttuj.is_arrive' => 1,
            'Ttuj.is_bongkaran' => 1,
        );
        $conditions = $this->Ttuj->_callConditionBranch( $conditions );

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', $action_type);
        $this->set('sub_module_title', __('Bongkaran'));
        $this->set('label_tgl', __('Tgl Bongkaran'));

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

            $conditions = $this->RjRevenue->_callRefineStatusTTUJ($refine, $conditions);
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Ttuj.tgljam_bongkaran' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        ), true, array(
            'branch' => false,
        ));
        $ttujs = $this->paginate('Ttuj');

        // if( !empty($ttujs) ) {
        //     foreach ($ttujs as $key => $ttuj) {
        //         $to_city_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'to_city_id');

        //         if( $this->Ttuj->validateTtujAfterLeave( $to_city_id, $this->GroupBranch->Branch ) ) {
        //             $ttujs[$key] = $ttuj;
        //         } else {
        //             unset($ttujs[$key]);
        //         }
        //     }
        // }

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function bongkaran_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('Bongkaran'));
        $this->set('active_menu', 'bongkaran');
        $this->doTTUJLanjutan( 'bongkaran' );
    }

    public function balik() {
        $this->loadModel('Ttuj');

        $action_type = 'balik';
        $conditions = array(
            'Ttuj.is_balik' => 1,
            'Ttuj.is_bongkaran' => 1,
        );
        $conditions = $this->Ttuj->_callConditionBranch( $conditions );

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', $action_type);
        $this->set('sub_module_title', __('Balik'));
        $this->set('label_tgl', __('Tgl Balik'));

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

            $conditions = $this->RjRevenue->_callRefineStatusTTUJ($refine, $conditions);
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Ttuj.tgljam_balik' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        ), true, array(
            'branch' => false,
        ));
        $ttujs = $this->paginate('Ttuj');

        // if( !empty($ttujs) ) {
        //     foreach ($ttujs as $key => $ttuj) {
        //         $to_city_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'to_city_id');

        //         if( $this->Ttuj->validateTtujAfterLeave( $to_city_id, $this->GroupBranch->Branch ) ) {
        //             $ttujs[$key] = $ttuj;
        //         } else {
        //             unset($ttujs[$key]);
        //         }
        //     }
        // }

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
        $this->loadModel('City');

        $action_type = 'pool';
        $conditions = array(
            'Ttuj.is_balik' => 1,
            'Ttuj.is_bongkaran' => 1,
            'Ttuj.is_balik' => 1,
            'Ttuj.is_pool' => 1,
        );
        $conditions = $this->Ttuj->_callConditionTtujPool($conditions);

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', $action_type);
        $this->set('sub_module_title', __('Sampai di Pool'));
        $this->set('label_tgl', __('Tgl Sampai Pool'));

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
            
            $conditions = $this->RjRevenue->_callRefineStatusTTUJ($refine, $conditions);
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Ttuj.tgljam_pool' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        ), true, array(
            'branch' => false,
        ));
        $ttujs = $this->paginate('Ttuj');

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $value) {
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $ttujs[$key] = $value;
            }
        }

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function pool_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('TTUJ Sampai Pool'));
        $this->set('active_menu', 'pool');
        $this->doTTUJLanjutan( 'pool' );
    }

    public function ritase_report( $data_type = 'depo' ) {
        $this->loadModel('Truck');
        $this->loadModel('TruckCustomer');
        $this->loadModel('Ttuj');
        $this->loadModel('Ttuj');
        $this->loadModel('CustomerNoType');

        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Truck.status'=> 1,
            'TruckCustomer.primary'=> 1,
            'TruckCustomer.branch_id' => $allow_branch_id,
        );
        $data_action = false;

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Ttuj']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditions ['Truck.id'] = $nopol;
                } else {
                    $conditions ['Truck.nopol LIKE'] = '%'.$nopol.'%';
                }
            }

            if(!empty($refine['driver_name'])){
                $driver_name = urldecode($refine['driver_name']);
                $this->request->data['Ttuj']['driver_name'] = $driver_name;
                $conditions['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$driver_name.'%';
            }

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditions['CustomerNoType.code LIKE '] = '%'.$customer.'%';
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

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'TruckCustomer', $conditions, 'conditions' );

            if(!empty($refine['data_action'])){
                $data_action = $refine['data_action'];
            }
            if(!empty($refine['company'])){
                $data = urldecode($refine['company']);
                $conditions['Truck.company_id'] = $data;
                $this->request->data['Truck']['company_id'] = $data;
            }
        }

        $conditionCustomers = array(
            'CustomerNoType.status' => 1,
        );

        if( $data_type == 'retail' ) {
            $conditionCustomers['CustomerNoType.customer_type_id'] = 1;
        } else {
            $conditionCustomers['CustomerNoType.customer_type_id'] = 2;
        }

        $customer_id = $this->CustomerNoType->find('list', array(
            'conditions' => $conditionCustomers,
            'fields' => array(
                'CustomerNoType.id', 'CustomerNoType.id'
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
        $defaultConditionsTtuj = array(
            // 'OR' => array(
            //     array(
                    'Ttuj.is_pool'=> 1,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='=> $dateFrom,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <=' => $dateTo,
            //     ),
            //     array(
            //         'Ttuj.completed'=> 1,
            //         'DATE_FORMAT(Ttuj.completed_date, \'%Y-%m-%d\') >='=> $dateFrom,
            //         'DATE_FORMAT(Ttuj.completed_date, \'%Y-%m-%d\') <=' => $dateTo,
            //     ),
            // ),
        );

        if( !empty($data_action) ) {
            $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
        } else {
            $options['limit'] = 20;
        }
        $this->paginate = $options;
        $trucks = $this->paginate('TruckCustomer');

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $truck) {
                $branch_id = $this->MkCommon->filterEmptyField($truck, 'TruckCustomer', 'branch_id');
                $conditionCustomers = array(
                    'TruckCustomer.truck_id'=> $truck['Truck']['id'],
                );

                $truck = $this->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
                $truck = $this->GroupBranch->Branch->getMerge($truck, $branch_id);

                $conditionsTtuj = $defaultConditionsTtuj;
                $conditionsTtuj['Ttuj.truck_id'] = $truck['Truck']['id'];

                $total = $this->Ttuj->getData('count', array(
                    'conditions' => $conditionsTtuj,
                ), true, array(
                    'branch' => false,
                ));
                $truck['Total'] = $total;

                $overTimeOptions = $conditionsTtuj;
                $overTimeOptions['Ttuj.arrive_over_time <>'] = 0;
                $overTime = $this->Ttuj->getData('count', array(
                    'conditions' => $overTimeOptions
                ), true, array(
                    'branch' => false,
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
                    ), true, array(
                        'branch' => false,
                    ));
                    $truck['City'] = $cities;
                }

                $trucks[$key] = $truck;
            }
        }

        $module_title = sprintf(__('Laporan Ritase - %s'), ucwords($data_type));

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Ttuj']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        $this->set('sub_module_title', $module_title);

        if( $data_type == 'retail' ) {
            $this->set('active_menu', 'ritase_report_retail');
        } else {
            $this->set('active_menu', 'ritase_report');
            $defaultConditionsTtuj['Ttuj.is_retail'] = 0;
            $cities = $this->Ttuj->getData('list', array(
                'conditions' => $defaultConditionsTtuj,
                'group' => array(
                    'Ttuj.to_city_id'
                ),
                'fields'=> array(
                    'Ttuj.to_city_id', 
                    'Ttuj.to_city_name', 
                ),
                'order' => array(
                    'Ttuj.to_city_name' => 'ASC',
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));
        }

        $companies = $this->Truck->Company->getData('list');

        $this->set(compact(
            'trucks', 'cities', 'data_action',
            'data_type', 'companies'
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
    }

    public function achievement_report( $data_action = false ) {
        $this->loadModel('CustomerTargetUnitDetail');
        $this->loadModel('Ttuj');
        $this->loadModel('TtujTipeMotor');
        $this->loadModel('Customer');
        $this->set('active_menu', 'achievement_report');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $fromMonth = 01;
        $fromYear = date('Y');
        $toMonth = date('m');
        $toYear = date('Y');
        $conditions = array(
            'TtujTipeMotor.status'=> 1,
            'Ttuj.status'=> 1,
            'Ttuj.is_draft'=> 0,
        );
        $options = array(
            'conditions' => array(
                'Customer.status' => 1,
                'Customer.branch_id' => $allow_branch_id,
            ),
            'order' => array(
                'Customer.order_sort' => 'ASC',
                'Customer.order' => 'ASC',
                'Customer.manual_group' => 'ASC',
                'Customer.customer_type_id' => 'DESC',
                'Customer.customer_group_id' => 'ASC',
            ),
            'contain' => array(
                'CustomerGroup'
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $options['conditions']['Customer.code LIKE '] = '%'.$customer.'%';
            }

            if( !empty($refine['fromMonth']) && !empty($refine['fromYear']) ){
                $fromMonth = urldecode($refine['fromMonth']);
                $fromYear = urldecode($refine['fromYear']);
            }

            if( !empty($refine['toMonth']) && !empty($refine['toYear']) ){
                $toMonth = urldecode($refine['toMonth']);
                $toYear = urldecode($refine['toYear']);
            }

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $options );
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

        $ttujs = $this->Customer->getData('all', $options);
        $cntPencapaian = array();
        $targetUnit = array();

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $ttuj) {
                $branch_id = $this->MkCommon->filterEmptyField($ttuj, 'Customer', 'branch_id');

                $ttuj = $this->GroupBranch->Branch->getMerge($ttuj, $branch_id);

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
                        $cntPencapaian[$ttujTipeMotor['Ttuj']['customer_id']][$ttujTipeMotor[0]['dt']] = $ttujTipeMotor[0]['cnt'];
                    }
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
    }

    public function monitoring_truck( $data_action = false ) {
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

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $default_conditions = array();
        $default_conditionsLaka = array();
        $default_conditionsTruck = array(
            'Truck.branch_id' => $allow_branch_id,
        );
        $default_conditionsEvent = array();

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

            if( !empty($refine['nopol']) ) {
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Ttuj']['nopol'] = $nopol;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Ttuj']['type'] = $typeTruck;

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
                ), true, array(
                    'branch' => false,
                ));
                $default_conditionsLaka['Laka.truck_id'] = $truckSearch;
                $default_conditions['Ttuj.truck_id'] = $truckSearch;
                $default_conditionsTruck['Truck.id'] = $truckSearch;
                $default_conditionsEvent['CalendarEvent.truck_id'] = $truckSearch;
            }
            if(!empty($refine['company'])){
                $data = urldecode($refine['company']);
                $default_conditionsTruck['Truck.company_id'] = $data;
                $this->request->data['Truck']['company_id'] = $data;
            }

            // Custom Otorisasi
            $default_conditionsTruck = $this->MkCommon->getConditionGroupBranch( $refine, 'Truck', $default_conditionsTruck, 'conditions' );
        }

        $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
        $thisMonth = !empty($thisMonth)?$thisMonth:date('m');
        $prevMonth = date('Y-m', mktime(0, 0, 0, date("m", strtotime($currentMonth))-1 , 1, date("Y", strtotime($currentMonth))));
        $nextMonth = date('Y-m', mktime(0, 0, 0, date("m", strtotime($currentMonth))+1 , 1, date("Y", strtotime($currentMonth))));
        $leftDay = date('N', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , 0, date("Y", strtotime($currentMonth))));
        $lastDay = date('t', strtotime($currentMonth));
        $customerId = array();
        $conditionsLaka = array(
            'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\') <=' => $currentMonth,
            'OR' => array(
                'DATE_FORMAT(Laka.completed_date, \'%Y-%m\') >=' => $currentMonth,
                'Laka.completed_date' => NULL,
            ),
        );
        $conditionsLaka = array_merge($conditionsLaka, $default_conditionsLaka);
        $lakas = $this->Laka->getData('list', array(
            'conditions' => $conditionsLaka,
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
        $conditions = array_merge($conditions, $default_conditions);
        $conditionEvents = array(
            'CalendarEvent.status'=> 1,
            'DATE_FORMAT(CalendarEvent.from_date, \'%Y-%m\')' => $currentMonth,
        );
        $conditionTrucks = $default_conditionsTruck;
        $conditionEvents = array_merge($conditionEvents, $default_conditionsEvent);
        $setting = $this->Setting->find('first');

        if( !empty($this->params['named']) ) {
            $refine = $this->params['named'];

            if( !empty($refine['monitoring_customer_id']) ) {
                $refine['monitoring_customer_id'] = urldecode($refine['monitoring_customer_id']);
                $customerId = explode(',', $refine['monitoring_customer_id']);
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
        ), false);

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
        ), true, array(
            'branch' => false,
        ));
        $trucks = $this->paginate('Truck');
        $truckList = Set::extract('/Truck/id', $trucks);
        $conditions['Ttuj.truck_id'] = $truckList;
        $dataLaka = array();

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $value) {
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'branch_id');
                $nopol = $this->MkCommon->filterEmptyField($value, 'Truck', 'nopol');

                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Laka->getMergeTruck($truck_id, $value, array(
                    'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\')' => $currentMonth,
                ));
                $laka_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'id');

                if( !empty($laka_id) ) {
                    $tgl_laka = $this->MkCommon->filterEmptyField($value, 'Laka', 'tgl_laka');
                    $laka_completed = $this->MkCommon->filterEmptyField($value, 'Laka', 'completed');
                    $laka_completed_date = $this->MkCommon->filterEmptyField($value, 'Laka', 'completed_date');
                    $driver_name = $this->MkCommon->filterEmptyField($value, 'Laka', 'driver_name', '-');
                    $lokasi_laka = $this->MkCommon->filterEmptyField($value, 'Laka', 'lokasi_laka');
                    $truck_condition = $this->MkCommon->filterEmptyField($value, 'Laka', 'truck_condition');
                    $change_driver_name = $this->MkCommon->filterEmptyField($value, 'Laka', 'change_driver_name', '-');
                    $change_driver_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'change_driver_id', '-');

                    if( $change_driver_name == '-' && !empty($change_driver_id) ) {
                        $value = $this->Truck->Driver->getMerge($value, $change_driver_id, 'DriverPenganti');
                        $change_driver_name = $this->MkCommon->filterEmptyField($value, 'DriverPenganti', 'driver_name', '-');
                    }

                    $lakaDate = $this->MkCommon->customDate($tgl_laka, 'Y-m-d');
                    $lakaCompletedDate = $this->MkCommon->customDate($laka_completed_date, 'Y-m-d', '-');
                    $addClass = 'pool';
                    $urlLaka = array(
                        'controller' => 'lakas',
                        'action' => 'edit',
                        $laka_id,
                    );

                    if( !empty($laka_completed) ) {
                        $end_date = $lakaCompletedDate;
                    } else if( date('Y-m-d') >= $lakaDate ) {
                        $end_date = date('Y-m-d', strtotime("-1 day"));
                    } else {
                        $end_date = $lakaDate;
                    }


                    $icon_laka = $this->MkCommon->filterEmptyField($setting, 'Setting', 'icon_laka');
                    $lakaDate = $this->MkCommon->customDate($tgl_laka, 'd/m/Y');
                    $lakaMonth = $this->MkCommon->customDate($tgl_laka, 'm');
                    $lakaDay = $this->MkCommon->customDate($tgl_laka, 'd');
                    $dataCalendar = array(
                        'is_laka' => true,
                        'laka_date' => $lakaDate,
                        'laka_completed_date' => $lakaCompletedDate,
                        'driver_name' => $driver_name,
                        'driver_pengganti_name' => $change_driver_name,
                        'lokasi_laka' => $lokasi_laka,
                        'truck_condition' => $truck_condition,
                        'title' => __('LAKA'),
                        'icon' => $icon_laka,
                        'iconPopup' => $icon_laka,
                        'color' => '#dd545f',
                        'NoPol' => $nopol,
                        'url' => array(
                            'controller' => 'lakas',
                            'action' => 'edit',
                            $laka_id,
                        ),
                    );

                    $dataLaka['Truck-'.$truck_id][$lakaMonth][$lakaDay][] = $dataCalendar;
                }

                $trucks[$key] = $value;
            }
        }

        $ttujs = $this->Ttuj->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Ttuj.customer_name' => 'ASC', 
            ),
        ), true, array(
            'branch' => false,
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
            $this->loadModel('Driver');

            foreach ($ttujs as $key => $value) {
                $inArr = array();
                $driver_penganti_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'driver_penganti_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $nopol = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'nopol');
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');

                $value = $this->Driver->getMerge($value, $driver_penganti_id, 'DriverPenganti');
                $value = $this->Laka->getMergeTtuj($truck_id, $value, array(
                    'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\')' => $currentMonth,
                ));
                $ttujTipeMotor = $this->TtujTipeMotor->find('first', array(
                    'conditions' => array(
                        'TtujTipeMotor.status' => 1,
                        'TtujTipeMotor.ttuj_id' => $ttuj_id,
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
                    'DriverChange' => !empty($value['DriverPenganti']['name'])?$value['DriverPenganti']['name']:false,
                    'Muatan' => $totalMuatan,
                    'NoPol' => $nopol,
                );
                $date = date('Y-m-d', strtotime($value['Ttuj']['tgljam_berangkat']));
                $tglBerangkat = $this->MkCommon->customDate($value['Ttuj']['tgljam_berangkat'], 'Y-m-d H:i:s');

                $is_arrive = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'is_arrive');
                $is_bongkaran = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'is_bongkaran');
                $is_balik = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'is_balik');
                $is_pool = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'is_pool');

                if( !empty($is_arrive) ) {
                    $tglTiba = $this->MkCommon->customDate($value['Ttuj']['tgljam_tiba'], 'Y-m-d H:i:s');
                } else {
                    $tglTiba = false;
                }
                if( !empty($is_bongkaran) ) {
                    $tglBongkaran = $this->MkCommon->customDate($value['Ttuj']['tgljam_bongkaran'], 'Y-m-d H:i:s');
                } else {
                    $tglBongkaran = false;
                }
                if( !empty($is_balik) ) {
                    $tglBalik = $this->MkCommon->customDate($value['Ttuj']['tgljam_balik'], 'Y-m-d H:i:s');
                } else {
                    $tglBalik = false;
                }
                if( !empty($is_pool) ) {
                    $tglPool = $this->MkCommon->customDate($value['Ttuj']['tgljam_pool'], 'Y-m-d H:i:s');
                } else {
                    $tglPool = false;
                }

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
                    if( date('Y-m-d') > $date ) {
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
                        'driver_pengganti_name' => !empty($value['DriverPenganti']['name'])?$value['DriverPenganti']['name']:false,
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
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($lakaDate, 'm')][$this->MkCommon->customDate($lakaDate, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($lakaDate, 'd');
                }
                if( !empty($tglPool) && $this->MkCommon->customDate($tglPool, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglPool, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglPool, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Sampai Pool');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_pool'])?$setting['Setting']['icon_pool']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtujCalendar['color'] = '#00a65a';
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglPool, 'm')][$this->MkCommon->customDate($tglPool, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglPool, 'd');
                    $dataRit['Truck-'.$truck_id]['rit'][$this->MkCommon->customDate($tglPool, 'm')][$this->MkCommon->customDate($tglPool, 'd')][] = $tglPool;
                }
                if( !empty($tglBalik) && $this->MkCommon->customDate($tglBalik, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglBalik, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglBalik, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Balik');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_balik'])?$setting['Setting']['icon_balik']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtujCalendar['color'] = '#3d9970';
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglBalik, 'm')][$this->MkCommon->customDate($tglBalik, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglBalik, 'd');
                }
                if( !empty($tglBongkaran) && $this->MkCommon->customDate($tglBongkaran, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglBongkaran, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglBongkaran, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Bongkaran');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_bongkaran'])?$setting['Setting']['icon_bongkaran']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtujCalendar['color'] = '#d3e3d4';
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglBongkaran, 'm')][$this->MkCommon->customDate($tglBongkaran, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglBongkaran, 'd');
                }
                if( !empty($tglTiba) && $this->MkCommon->customDate($tglTiba, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglTiba, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglTiba, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Tiba');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_tiba'])?$setting['Setting']['icon_tiba']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtujCalendar['color'] = '#f39c12';
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglTiba, 'm')][$this->MkCommon->customDate($tglTiba, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglTiba, 'd');
                }

                while (strtotime($date) <= strtotime($end_date)) {
                    $currMonth = date('Y-m', strtotime($date));
                    $currDay = date('d', strtotime($date));
                    $currMonthly = date('m', strtotime($date));
                    
                    if( !in_array($currDay, $inArr) ) {
                        $popIcon = false;

                        if( !empty($lakaDate) && $this->MkCommon->customDate($lakaDate, 'Y-m-d') <= $date ) {
                            $dataTtujCalendar['color'] = '#dd545f';
                            $icon = !empty($setting['Setting']['icon_laka'])?$setting['Setting']['icon_laka']:'';
                        } else if( !empty($tglPool) && $this->MkCommon->customDate($tglPool, 'Y-m-d') <= $date ) {
                            $dataTtujCalendar['color'] = '#00a65a';
                            $icon = !empty($setting['Setting']['icon_pool'])?$setting['Setting']['icon_pool']:'';
                            $dataRit['Truck-'.$truck_id]['rit'][$currMonthly][$currDay][] = $tglPool;
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
                        $dataTtuj['Truck-'.$truck_id][$currMonthly][$currDay][] = $dataTtujCalendar;
                    }

                    $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                    $i++;
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
                        $truck_id = $event['CalendarEvent']['truck_id'];

                        $dataEvent['Truck-'.$truck_id][date('m', strtotime($date))][date('d', strtotime($date))][] = array(
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
        $customers = $this->TruckCustomer->getData('list', array(
            'conditions' => array(
                'Truck.status' => 1,
                'TruckCustomer.primary' => 1,
                'TruckCustomer.branch_id' => $allow_branch_id,
            ),
            'fields' => array(
                'CustomerNoType.id', 'CustomerNoType.code'
            ),
            'contain' => array(
                'Truck',
                'CustomerNoType',
            ),
        ), array(
            'branch' => false,
            'plant' => false,
        ));
        $companies = $this->Truck->Company->getData('list');

        $this->set(compact(
            'data_action', 'lastDay', 'currentMonth',
            'trucks', 'prevMonth', 'nextMonth',
            'dataTtuj', 'dataEvent', 'customers',
            'customerId', 'dataRit', 'thisMonth',
            'dataLaka', 'companies'
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
    }

    function index(){
        $this->loadModel('Revenue');
        $this->loadModel('Ttuj');
        $this->loadModel('City');

        $this->set('active_menu', 'revenues');
        $this->set('sub_module_title', __('Revenue'));

        $from_date = '';
        $to_date = '';
        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nodoc'])){
                $nodoc = urldecode($refine['nodoc']);
                $nodoc = $this->MkCommon->replaceSlash($nodoc);
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

                $conditions['LPAD(Revenue.id, 5, 0) LIKE'] = '%'.$no_ref.'%';
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
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Ttuj']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $nopol,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$nopol.'%',
                    );
                }

                $conditionsNopol = $this->City->getCityIdPlants( $conditionsNopol );
                $truckSearch = $this->Ttuj->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ));
                $conditions['Ttuj.truck_id'] = $truckSearch;
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
            ),
            'order' => array(
                'Revenue.status' => 'DESC',
                'Revenue.created' => 'DESC',
                'Revenue.id' => 'DESC',
            ),
        ), true, array(
            'status' => 'all',
        ));
        $revenues = $this->paginate('Revenue');

        if(!empty($revenues)){
            $this->loadModel('Truck');

            foreach ($revenues as $key => $value) {
                $value = $this->Revenue->InvoiceDetail->getInvoicedRevenue($value, $value['Revenue']['id']);

                if( empty($value['Revenue']['ttuj_id']) ) {
                    $from_city_id = !empty($value['Revenue']['from_city_id'])?$value['Revenue']['from_city_id']:false;
                    $to_city_id = !empty($value['Revenue']['to_city_id'])?$value['Revenue']['to_city_id']:false;
                    $truck_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'truck_id');

                    $value = $this->City->getMerge($value, $from_city_id, 'FromCity');
                    $value = $this->City->getMerge($value, $to_city_id);
                    $value = $this->Ttuj->Customer->getMerge($value, $value['Revenue']['customer_id']);
                    $value = $this->Truck->getMerge($value, $truck_id);
                } else {
                    $value = $this->Ttuj->Customer->getMerge($value, $value['Ttuj']['customer_id']);
                }

                $revenues[$key] = $this->Ttuj->Customer->getMerge($value, $value['Ttuj']['customer_id']);
            }
        }
        $this->set('revenues', $revenues); 

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);
    }

    // function revenue_add(){
    //     $this->loadModel('Revenue');
    //     $module_title = __('Tambah Revenue');
    //     $this->set('sub_module_title', trim($module_title));
    //     $this->doTTUJ();
    // }

    // function revenue_edit( $id ){
    //     $this->loadModel('Ttuj');
    //     $ttuj = $this->Ttuj->getData('first', array(
    //         'conditions' => array(
    //             'Ttuj.id' => $id
    //         )
    //     ));

    //     if(!empty($ttuj)){
    //         $data_action = false;

    //         if( !empty($ttuj['Ttuj']['is_retail']) ) {
    //             $data_action = 'retail';
    //         }

    //         $module_title = sprintf(__('Rubah TTUJ %s'), ucwords($data_action));
    //         $this->set('sub_module_title', trim($module_title));
    //         $this->doTTUJ($data_action, $id, $ttuj);
    //     }else{
    //         $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan'), 'error');  
    //         $this->redirect(array(
    //             'controller' => 'revenues',
    //             'action' => 'ttuj'
    //         ));
    //     }
    // }

    function add( $action_type = false ){
        $this->loadModel('Revenue');
        $module_title = __('Tambah Revenue');
        $this->set('sub_module_title', trim($module_title));
        $this->doRevenue( false, false, $action_type );
    }

    function edit( $id, $action_type = false ){
        $this->loadModel('Revenue');
        $revenue = $this->Revenue->getData('first', array(
            'conditions' => array(
                'Revenue.id' => $id
            ),
            'contain' => array(
                'Ttuj'
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($revenue)){
            $revenue = $this->Revenue->RevenueDetail->getMergeAll( $revenue, $revenue['Revenue']['id'] );
            $this->MkCommon->getLogs($this->paramController, array( 'revenue_ttuj_add', 'revenue_ttuj_edit', 'revenue_ttuj_toggle', 'edit', 'add', 'revenue_toggle' ), $id);

            $module_title = __('Rubah Revenue');
            $this->set('sub_module_title', trim($module_title));
            $this->doRevenue($id, $revenue, $action_type);
        }else{
            $this->MkCommon->setCustomFlash(__('Revenue tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'index'
            ));
        }
    }

    function doRevenue($id = false, $data_local = false, $action_type = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TarifAngkutan');
        $this->loadModel('City');
        $this->loadModel('GroupMotor');
        $this->loadModel('TtujTipeMotor');
        $data_revenue_detail = array();

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['Revenue']['date_revenue'] = $this->MkCommon->getDate($data['Revenue']['date_revenue']);
            $data['Revenue']['branch_id'] = Configure::read('__Site.config_branch_id');
            $resultSave = $this->Revenue->saveRevenue($id, $data_local, $data, $this);
            $statusSave = !empty($resultSave['status'])?$resultSave['status']:false;
            $msgSave = !empty($resultSave['msg'])?$resultSave['msg']:false;
            $this->MkCommon->setCustomFlash($msgSave, $statusSave);

            if( $statusSave == 'success' ) {
                $this->redirect(array(
                    'controller' => 'revenues',
                    'action' => 'index'
                ));
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
                        $jenis_unit = !empty($this->request->data['Revenue']['revenue_tarif_type'])?$this->request->data['Revenue']['revenue_tarif_type']:'per_unit';
                        $total_price_unit = !empty($value[0]['total_price_unit'])?$value[0]['total_price_unit']:0;

                        if( $value['RevenueDetail']['payment_type'] == 'per_truck' ) {
                            if( empty($value['RevenueDetail']['is_charge']) && $jenis_unit == 'per_truck' ) {
                                $tarif = $data_local['Revenue']['tarif_per_truck'];
                            } else {
                                $tarif = 0;
                            }
                        } else {
                            $tarif = $value['RevenueDetail']['price_unit'];
                        }

                        $data_revenue_detail[$key] = array(
                            'TtujTipeMotor' => array(
                                'qty' => !empty($value[0]['qty_unit'])?$value[0]['qty_unit']:0,
                            ),
                            'RevenueDetail' => array(
                                'no_do' => $value['RevenueDetail']['no_do'],
                                'no_sj' => $value['RevenueDetail']['no_sj'],
                                'to_city_name' => !empty($value['City']['name'])?$value['City']['name']:'',
                                'price_unit' => array(
                                    'jenis_unit' => $value['RevenueDetail']['payment_type'],
                                    'tarif' => $tarif,
                                    'tarif_angkutan_id' => $value['RevenueDetail']['tarif_angkutan_id'],
                                    'tarif_angkutan_type' => $value['RevenueDetail']['tarif_angkutan_type'],
                                ),
                                'total_price_unit' => !empty($value[0]['total_price_unit'])?$value[0]['total_price_unit']:0,
                                'payment_type' => $value['RevenueDetail']['payment_type'],
                                'qty_unit' => !empty($value[0]['qty_unit'])?$value[0]['qty_unit']:0,
                                'group_motor_id' => $value['RevenueDetail']['group_motor_id'],
                                'city_id' => $value['RevenueDetail']['city_id'],
                                'is_charge' => $value['RevenueDetail']['is_charge'],
                                'from_ttuj' => !empty($value[0]['from_ttuj'])?true:false,
                            )
                        );
                    }
                }
            }
        }

        $ttuj_retail = false;
        $ttuj_data = array();
        $tarif_angkutan = false;

        if(!empty($this->request->data['Revenue'])){
            $ttuj_id = !empty($this->request->data['Revenue']['ttuj_id'])?$this->request->data['Revenue']['ttuj_id']:false;
            $ttuj_data = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $ttuj_id,
                    'Ttuj.is_draft' => 0,
                )
            ));

            if( !empty($this->request->data['RevenueDetail']['no_do']) ){
                foreach ($this->request->data['RevenueDetail']['no_do'] as $key => $value) {
                    $group_motor_id = $this->request->data['RevenueDetail']['group_motor_id'][$key];
                    $to_city_id = !empty($this->request->data['RevenueDetail']['city_id'][$key])?$this->request->data['RevenueDetail']['city_id'][$key]:false;
                    $groupMotor = $this->GroupMotor->getData('first', array(
                        'conditions' => array(
                            'GroupMotor.id' => $group_motor_id
                        )
                    ), array(
                        'status' => 'all',
                    ));
                    $city = $this->City->getData('first', array(
                        'conditions' => array(
                            'City.id' => $to_city_id,
                        )
                    ));
                    $group_motor_name = '';
                    $qty = 0;

                    if( !empty($ttuj_data['Ttuj']['from_city_id']) ) {
                        $form_city_id = $ttuj_data['Ttuj']['from_city_id'];
                    } else if( !empty($this->request->data['Revenue']['from_city_id']) ) {
                        $form_city_id = $this->request->data['Revenue']['from_city_id'];
                    } else {
                        $form_city_id = false;
                    }

                    if( !empty($ttuj_data['Ttuj']['customer_id']) ) {
                        $customer_id = $ttuj_data['Ttuj']['customer_id'];
                    } else if( !empty($this->request->data['Revenue']['customer_id']) ) {
                        $customer_id = $this->request->data['Revenue']['customer_id'];
                    } else {
                        $customer_id = false;
                    }

                    if( !empty($ttuj_data['Ttuj']['truck_capacity']) ) {
                        $truck_capacity = $ttuj_data['Ttuj']['truck_capacity'];
                    } else if( !empty($this->request->data['Revenue']['truck_capacity']) ) {
                        $truck_capacity = $this->request->data['Revenue']['truck_capacity'];
                    } else {
                        $truck_capacity = false;
                    }

                    if(!empty($groupMotor)){
                        $group_motor_name = $groupMotor['GroupMotor']['name'];
                    }

                    if( !empty($ttuj_data['Ttuj']['is_retail']) ){
                        $to_city_name = !empty($city['City']['name'])?$city['City']['name']:false;

                        $ttujTipeMotor = $this->TtujTipeMotor->getMergeTtujTipeMotor( $ttuj_data, $this->request->data['Revenue']['ttuj_id'], 'first', array(
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

                        $tarif = $this->TarifAngkutan->findTarif($form_city_id, $to_city_id, $customer_id, $truck_capacity, $this->request->data['RevenueDetail']['group_motor_id'][$key]);
                    }

                    $data_revenue_detail[$key] = array(
                        'TtujTipeMotor' => array(
                            'qty' => $qty,
                        ),
                        'RevenueDetail' => array(
                            'no_do' => $this->request->data['RevenueDetail']['no_do'][$key],
                            'no_sj' => $this->request->data['RevenueDetail']['no_sj'][$key],
                            'to_city_name' => $to_city_name,
                            'price_unit' => $tarif,
                            'total_price_unit' => $this->request->data['RevenueDetail']['total_price_unit'][$key],
                            'payment_type' => $tarif['jenis_unit'],
                            'qty_unit' => $this->request->data['RevenueDetail']['qty_unit'][$key],
                            'group_motor_id' => $this->request->data['RevenueDetail']['group_motor_id'][$key],
                            'city_id' => $to_city_id,
                            'is_charge' => $this->request->data['RevenueDetail']['is_charge'][$key],
                            'from_ttuj' => !empty($this->request->data['RevenueDetail']['from_ttuj'][$key])?true:false,
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
        ), true, array(
            'status' => 'all',
        ));
        $this->set('ttujs', $ttujs);

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);

        $toCities = $this->City->getListCities();
        $groupMotors = $this->GroupMotor->getData('list');
        $branches = $this->City->branchCities();

        $this->set(compact(
            'toCities', 'groupMotors', 'tarifTruck',
            'id', 'data_local', 'trucks', 'branches'
        ));
        $this->set('active_menu', 'revenues');

        if( $action_type == 'manual' ) {
            $this->loadModel('Truck');
            $this->Truck->bindModel(array(
                'hasOne' => array(
                    'Ttuj' => array(
                        'className' => 'Ttuj',
                        'foreignKey' => 'truck_id',
                        'conditions' => array(
                            'Ttuj.status' => 1,
                            'Ttuj.is_pool' => 0,
                            'Ttuj.id <>' => $id,
                            'Ttuj.is_laka' => 0,
                            'Ttuj.completed' => 0,
                        ),
                    )
                )
            ), false);

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

            $this->set(compact(
                'trucks'
            ));

            $this->render('revenue_manual_form');
        } else {
            $this->render('revenue_form');
        }
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

            $this->Revenue->set('status', $value);
            $this->Revenue->id = $id;

            if($this->Revenue->save()){
                $this->loadModel('Ttuj');
                $this->Ttuj->set('is_revenue', 0);
                $this->Ttuj->id = $locale['Revenue']['ttuj_id'];
                $this->Ttuj->save();

                $this->MkCommon->setCustomFlash(__('Revenue berhasil dibatalkan.'), 'success');
                $this->Log->logActivity( sprintf(__('Revenue ID #%s berhasil dibatalkan.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Revenue membatalkan TTUJ.'), 'error');
                $this->Log->logActivity( sprintf(__('Revenue membatalkan TTUJ ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
            ), false);
            $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

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
                    'Truck.branch_id' => $allow_branch_id,
                ),
                'contain' => array(
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
            
            if(!empty($truk)){
                $customer_id = !empty($truk['TruckCustomer'][0]['customer_id'])?$truk['TruckCustomer'][0]['customer_id']:false;
                $truck_brand_id = !empty($truk['Truck']['truck_brand_id'])?$truk['Truck']['truck_brand_id']:false;
                $truck_category_id = !empty($truk['Truck']['truck_category_id'])?$truk['Truck']['truck_category_id']:false;
                $truck_facility_id = !empty($truk['Truck']['truck_facility_id'])?$truk['Truck']['truck_facility_id']:false;
                $driver_id = !empty($truk['Truck']['driver_id'])?$truk['Truck']['driver_id']:false;

                $truk = $this->Customer->getMerge($truk, $customer_id);
                $truk = $this->Truck->TruckBrand->getMerge($truk, $truck_brand_id);
                $truk = $this->Truck->TruckCategory->getMerge($truk, $truck_category_id);
                $truk = $this->Truck->TruckFacility->getMerge($truk, $truck_facility_id);
                $truk = $this->Truck->Driver->getMerge($truk, $driver_id);
                $total_ritase = $this->Ttuj->getData('count', array(
                    'conditions' => array(
                        'Ttuj.truck_id' => $id,
                        'OR' => array(
                            'Ttuj.is_pool' => 1,
                            'Ttuj.completed' => 1,
                        ),
                    )
                ), true, array(
                    'branch' => false,
                ));
                $ttuj_id = $this->Ttuj->getData('list', array(
                    'conditions' => array(
                        'Ttuj.truck_id' => $id,
                        'Ttuj.is_draft' => 0,
                    ),
                    'fields' => array(
                        'Ttuj.id', 'Ttuj.id',
                    ),
                ), true, array(
                    'branch' => false,
                ));

                if(!empty($ttuj_id)){
                    $this->loadModel('TtujTipeMotor');

                    $total_unit = $this->TtujTipeMotor->getData('first', array(
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
                ), true, array(
                    'branch' => false,
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
                                'Lku.ttuj_id' => $value['Ttuj']['id']
                            ),
                            'fields' => array(
                                'SUM(Lku.total_klaim) as qty_lku'
                            )
                        ));

                        $from_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_berangkat');
                        $to_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_tiba');
                        $leadTimeArrive = $this->MkCommon->dateDiff($from_time, $to_time, 'day', true);

                        $from_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_balik');
                        $to_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_pool');
                        $leadTimeBack = $this->MkCommon->dateDiff($from_time, $to_time, 'day', true);

                        $truk_ritase[$key]['ArriveLeadTime'] = $leadTimeArrive;
                        $truk_ritase[$key]['BackLeadTime'] = $leadTimeBack;

                        $qty_lku = !empty($lkus[0]['qty_lku'])?$lkus[0]['qty_lku']:0;
                        $truk_ritase[$key]['qty_ritase'] = $qty_ritase[0]['qty_ritase'];
                        $truk_ritase[$key]['Lku']['qty'] = $qty_lku;

                        $total_lku += $qty_lku;
                    }
                }

                $sub_module_title = __('Detail Ritase Truk');
                $this->set('active_menu', 'ritase_report');
                $this->set(compact(
                    'id', 'truk', 'truk_ritase', 'sub_module_title', 
                    'total_ritase', 'total_unit', 'total_lku'
                ));
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
        $this->set('active_menu', 'invoices');
        $this->set('sub_module_title', __('Invoice'));

        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['no_invoice'])){
                $nodoc = urldecode($refine['no_invoice']);
                $nodoc = $this->MkCommon->replaceSlash($nodoc);
                $this->request->data['Invoice']['no_invoice'] = $nodoc;
                $conditions['Invoice.no_invoice LIKE '] = '%'.$nodoc.'%';
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
            ),
        ), true, array(
            'status' => 'all',
        ));
        $invoices = $this->paginate('Invoice');

        if(!empty($invoices)){
            foreach ($invoices as $key => $value) {
                $invoices[$key] = $this->Invoice->Customer->getMerge($value, $value['Invoice']['customer_id']);
            }
        }
        $this->set('invoices', $invoices); 

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);
    }

    function invoice_add($action = false){
        $module_title = __('Tambah Invoice');
        $this->set('sub_module_title', trim($module_title));
        $this->doInvoice($action);
    }

    function doInvoice($action, $id = false, $data_local = false){
        $this->loadModel('Revenue');
        $this->loadModel('Customer');
        $this->loadModel('Bank');

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
            $data['Invoice']['branch_id'] = Configure::read('__Site.config_branch_id');

            $customer = $this->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $customer_id
                )
            ));

            if( !empty($customer) ) {
                $data['Invoice']['billing_id'] = $customer['Customer']['billing_id'];
                $data['Invoice']['term_of_payment'] = $customer['Customer']['term_of_payment'];

                if( empty($data['Invoice']['bank_id']) ) {
                    $data['Invoice']['bank_id'] = !empty($customer['Customer']['bank_id'])?$customer['Customer']['bank_id']:false;
                }
            }

            $this->Invoice->set($data);

            if($this->Invoice->validates()){
                $this->loadModel('Journal');
                $this->loadModel('CustomerGroupPattern');

                $tarif_type = !empty($data['Invoice']['tarif_type'])?$data['Invoice']['tarif_type']:false;
                
                if($action == 'tarif'){
                    if(!empty($customer)){
                        $revenue_detail = $this->Revenue->RevenueDetail->getData('all', array(
                            'conditions' => array(
                                'Revenue.customer_id' => $customer_id,
                                'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                                'RevenueDetail.tarif_angkutan_type' => $tarif_type,
                                'RevenueDetail.invoice_id' => NULL,
                                'Revenue.status' => 1,
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
                            $invoice_number = $this->MkCommon->getNoInvoice( $customer );

                            foreach ($result as $key => $value) {
                                $this->Invoice->create();
                                $data['Invoice']['no_invoice'] = $invoice_number;
                                $data['Invoice']['type_invoice'] = 'tarif';
                                $data['Invoice']['due_invoice'] = $customer['Customer']['term_of_payment'];
                                $this->Invoice->set($data);

                                if($this->Invoice->save()){
                                    $invoice_id = $this->Invoice->id;
                                    $invoice_number = $this->CustomerGroupPattern->addPattern($customer, $data);
                                    $this->CustomerGroupPattern->addPattern($customer, $data);

                                    if( !empty($data['Invoice']['total']) ) {
                                        $this->Journal->setJournal( $invoice_id, $invoice_number, 'invoice_coa_credit_id', 0, $data['Invoice']['total'], 'invoice' );
                                        $this->Journal->setJournal( $invoice_id, $invoice_number, 'invoice_coa_debit_id', $data['Invoice']['total'], 0, 'invoice' );
                                    }

                                    $this->params['old_data'] = $data_local;
                                    $this->params['data'] = $data;

                                    $this->Revenue->getProsesInvoice( $customer_id, $invoice_id, $action, $tarif_type, $value );
                                    $this->Log->logActivity( sprintf(__('Berhasil %s Invoice #%s'), $msg, $invoice_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $invoice_id );
                                } else {
                                    $this->Log->logActivity( sprintf(__('Gagal %s Invoice #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                        $document_no = !empty($data['Invoice']['no_invoice'])?$data['Invoice']['no_invoice']:false;

                        if( !empty($data['Invoice']['total']) ) {
                            $this->Journal->setJournal( $invoice_id, $document_no, 'invoice_coa_credit_id', 0, $data['Invoice']['total'], 'invoice' );
                            $this->Journal->setJournal( $invoice_id, $document_no, 'invoice_coa_debit_id', $data['Invoice']['total'], 0, 'invoice' );
                        }

                        $this->CustomerGroupPattern->addPattern($customer, $data);

                        // if( !empty($customer['CustomerPattern']) ) {
                        //     $last_number = str_replace($customer['CustomerPattern']['pattern'], '', $data['Invoice']['no_invoice']);
                        //     $last_number = intval($last_number)+1;
                        //     $this->Customer->CustomerPattern->set('last_number', $last_number);
                        //     $this->Customer->CustomerPattern->id = $customer['CustomerPattern']['id'];
                        //     $this->Customer->CustomerPattern->save();
                        // }

                        $this->params['old_data'] = $data_local;
                        $this->params['data'] = $data;

                        $this->Revenue->getProsesInvoice( $customer_id, $invoice_id, $action, $tarif_type );
                        $this->MkCommon->setCustomFlash(sprintf(__('Berhasil %s Invoice'), $msg), 'success'); 
                        $this->Log->logActivity( sprintf(__('Berhasil %s Invoice #%s'), $msg, $invoice_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $invoice_id );
                        $this->redirect(array(
                            'controller' => 'revenues',
                            'action' => 'invoices'
                        ));
                    }else{
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Invoice'), $msg), 'error'); 
                        $this->Log->logActivity( sprintf(__('Gagal %s Invoice #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                    }
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Invoice'), $msg), 'error'); 

                $this->request->data['Invoice']['no_invoice'] = $this->MkCommon->getNoInvoice( $customer );
            }
        }else if(!empty($id) && !empty($data_local)){
             $this->request->data = $data_local;

             $data['Invoice']['invoice_date'] = $this->MkCommon->getDate($data['Invoice']['invoice_date'], true);
        }

        $conditionsRevenue = array(
            'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
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
        ));
        $customers = array();

        if( !empty($revenues) ) {
            foreach ($revenues as $key => $revenue) {
                $revenueCustomer = $this->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $revenue['Revenue']['customer_id'],
                    ),
                ));

                if( !empty($revenueCustomer) ) {
                    $customers[$revenue['Revenue']['customer_id']] = $revenueCustomer['Customer']['customer_name_code'];
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
            ),
            'contain' => array(
                'InvoiceDetail',
            )
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($invoice)){
            $this->loadModel('Customer');
            $this->loadModel('Bank');
            $this->loadModel('Revenue');

            $invoice = $this->Customer->getMerge($invoice, $invoice['Invoice']['customer_id']);
            $invoice = $this->User->getMerge($invoice, $invoice['Invoice']['billing_id']);
            $invoice = $this->Bank->getMerge($invoice, $invoice['Invoice']['bank_id']);
            $revenueDetailId = Set::extract('/InvoiceDetail/revenue_detail_id', $invoice);

            if( $data_print == 'header' ) {
                $this->loadModel('Setting');
                $setting = $this->Setting->find('first');
                $invoice = $this->Revenue->RevenueDetail->getSumUnit($invoice, $invoice['Invoice']['id']);
            } else {
                $revenue_detail = $this->Revenue->RevenueDetail->getPreviewInvoice($invoice['Invoice']['id'], $invoice['Invoice']['tarif_type'], $action_print, $data_print, $revenueDetailId);
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
        $this->loadModel('Invoice');
        $this->loadModel('Customer');

        $this->set('active_menu', 'revenue');
        $this->set('sub_module_title', __('Account Receivable Aging Report'));

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $default_conditions = array(
            'Customer.branch_id' => $allow_branch_id,
        );
        $invoice_conditions = array();
        $customer_id = '';
        $customer_collect_id = array();
        $due_30= false;
        $due_15= false;
        $due_above_30= false;
        $list_customer = array();

        if( !empty($this->params['named']) ){
            $refine = $this->params['named'];

            if(!empty($refine['customer'])){
                $keyword = urldecode($refine['customer']);
                $this->request->data['Invoice']['customer_id'] = $keyword;
                $customer_id = $keyword;

                $default_conditions['Customer.id'] = $customer_id;
            }

            if(!empty($refine['due_15'])){
                $inv_conditions = array(
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >=' => 1,
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) <=' => 15,
                    'Invoice.paid' => 0,
                );

                if(!empty($customer_id)){
                    $inv_conditions['Invoice.customer_id'] = $customer_id;
                }

                $customer_id_temp = $this->Invoice->getData('list', array(
                    'conditions' => $inv_conditions,
                    'fields' => array(
                        'Invoice.customer_id'
                    ),
                    'group' => array(
                        'Invoice.customer_id'  
                    )
                ));
                
                if(!empty($customer_id_temp)){
                    $customer_collect_id = array_merge($customer_collect_id, $customer_id_temp);
                }

                $this->request->data['Invoice']['due_15'] = 1;
                $due_15= true;
            }
            if(!empty($refine['due_30'])){
                $inv_conditions = array(
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >=' => 16,
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) <=' => 30,
                    'Invoice.paid' => 0,
                );

                if(!empty($customer_id)){
                    $inv_conditions['Invoice.customer_id'] = $customer_id;
                }

                $customer_id_temp = $this->Invoice->getData('list', array(
                    'conditions' => $inv_conditions,
                    'fields' => array(
                        'Invoice.customer_id'
                    ),
                    'group' => array(
                        'Invoice.customer_id'  
                    )
                ));
                
                if(!empty($customer_id_temp)){
                    $customer_collect_id = array_merge($customer_collect_id, $customer_id_temp);
                }

                $this->request->data['Invoice']['due_30'] = 1;
                $due_30= true;
            }
            if(!empty($refine['due_above_30'])){
                $inv_conditions = array(
                    'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >' => 30,
                    'Invoice.paid' => 0,
                );

                if(!empty($customer_id)){
                    $inv_conditions['Invoice.customer_id'] = $customer_id;
                }

                $customer_id_temp = $this->Invoice->getData('list', array(
                    'conditions' => $inv_conditions,
                    'fields' => array(
                        'Invoice.customer_id'
                    ),
                    'group' => array(
                        'Invoice.customer_id'  
                    )
                ));
                
                if(!empty($customer_id_temp)){
                    $customer_collect_id = array_merge($customer_collect_id, $customer_id_temp);
                }
                
                $this->request->data['Invoice']['due_above_30'] = 1;
                $due_above_30 = true;
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

            // Custom Otorisasi
            $default_conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $default_conditions, 'conditions' );
        }

        if(!empty($customer_collect_id)){
            $default_conditions['Customer.id'] = $customer_collect_id;
        }else if(empty($customer_collect_id) && ($due_30 || $due_15 || $due_above_30) ){
            $default_conditions['Customer.id'] = false;
        }

        if(empty($data_action)){
            $this->paginate = $this->Customer->getData('paginate', array(
                'conditions' => $default_conditions,
                'limit' => 20,
            ), array(
                'plant' => false,
                'branch' => false,
            ));

            $customers = $this->paginate('Customer');
        }else{
            $customers = $this->Customer->getData('all', array(
                'conditions' => $default_conditions,
            ), array(
                'plant' => false,
                'branch' => false,
            ));
        }

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
            ), true, array(
                'branch' => false,
            ));

            $default_conditions = array(
                'Invoice.paid' => 0,
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
            ), true, array(
                'branch' => false,
            ));
            $default_conditions = array(
                'Invoice.paid' => 0,
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
            ), true, array(
                'branch' => false,
            ));

            $default_conditions = array(
                'Invoice.paid' => 0,
                'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), Invoice.invoice_date) >' => 30,
                'Invoice.customer_id' => $value['Customer']['id'],
            );
            if(!empty($invoice_conditions)){
                $default_conditions = array_merge($default_conditions, $invoice_conditions);
            }
            $customers[$key]['current_rev30'] = $this->Invoice->getData('paginate', array(
                'conditions' => $default_conditions,
                'fields' => array(
                    'SUM(Invoice.total) as current_rev30'
                )
            ), true, array(
                'branch' => false,
            ));
        }
        $this->set('active_menu', 'invoice_reports');

        $list_customer = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => array(
                'Customer.branch_id' => $allow_branch_id,
            ),
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }

        $this->set(compact(
            'customers', 'list_customer', 'data_action'
        ));
    }

    public function ar_period_reports( $data_action = false ) {
        $this->loadModel('Revenue');
        $this->loadModel('Invoice');
        $fromYear = date('Y');
        $toMonth = 12;
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

        $conditions = array(
            'Revenue.transaction_status <>' => 'invoiced',
            'Revenue.branch_id' => $allow_branch_id,
        );
        $defaultConditionsInvoice = array(
            'Invoice.paid'=> 0,
            'Invoice.branch_id' => $allow_branch_id,
        );
        $totalAr = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if( !empty($refine['fromYear']) ){
                $fromYear = urldecode($refine['fromYear']);
                $this->request->data['Ttuj']['from']['year'] = $fromYear;
            }

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Revenue', $conditions, 'conditions' );
            $defaultConditionsInvoice = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $defaultConditionsInvoice, 'conditions' );
        }

        for ($i=1; $i <= $toMonth; $i++) {
            $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));

            $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\')'] = $month;
            $revenues = $this->Revenue->getData('first', array(
                'conditions' => $conditions,
                'fields' => array(
                    'SUM(Revenue.total) total'
                ),
            ), true, array(
                'branch' => false,
            ));
            $totalAr['AR'][$month] = !empty($revenues[0]['total'])?$revenues[0]['total']:0;

            $conditionsInvoice = $defaultConditionsInvoice;
            $conditionsInvoice['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\')'] = $month;
            $invoice = $this->Invoice->getData('first', array(
                'conditions' => $conditionsInvoice,
                'fields' => array(
                    'SUM(Invoice.total) total'
                ),
            ), true, array(
                'branch' => false,
            ));
            $totalAr['Invoice'][$month] = !empty($invoice[0]['total'])?$invoice[0]['total']:0;

            if( $month <= date('Y-12') ) {
                $conditionsInvoice = $defaultConditionsInvoice;
                $conditionsInvoice['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <'] = $month;
                $invoice = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsInvoice,
                    'fields' => array(
                        'SUM(Invoice.total) total'
                    ),
                ), true, array(
                    'branch' => false,
                ));
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

    function invoice_payments(){
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
        ), true, array(
            'status' => 'all',
        ));
        $invoices = $this->paginate('InvoicePayment');

        if(!empty($invoices)){
            foreach ($invoices as $key => $value) {
                $invoices[$key] = $this->InvoicePayment->Customer->getMerge($value, $value['InvoicePayment']['customer_id']);
            }
        }
        
        $this->set('invoices', $invoices); 

        $customers = $this->Invoice->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);
    }

    function invoice_payment_add(){
        $this->loadModel('Invoice');
        $module_title = __('Tambah Pembayaran Invoice');
        $this->set('sub_module_title', trim($module_title));
        $this->doInvoicePayment();
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
            $data['InvoicePayment']['branch_id'] = Configure::read('__Site.config_branch_id');
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
                                'InvoicePaymentDetail.invoice_id' => $_invoice_id,
                                'InvoicePayment.status' => 1,
                                'InvoicePayment.is_canceled' => 0,
                            ),
                            'fields' => array(
                                'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
                            ),
                            'contain' => array(
                                'InvoicePayment'
                            ),
                        ));

                        $invoice_has_paid = (!empty($invoice_has_paid[0]['invoice_has_paid'])) ? $invoice_has_paid[0]['invoice_has_paid'] : 0;
                        $total_paid = $invoice_has_paid + $price;

                        $invoice_data = $this->Invoice->getData('first', array(
                            'conditions' => array(
                                'Invoice.id' => $_invoice_id
                            ),
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

            // if(!empty($data['InvoicePayment']['pph'])){
            //     $temptotal -= $total*($data['InvoicePayment']['pph']/100);
            // }
            if(!empty($data['InvoicePayment']['ppn'])){
                $temptotal += $total*($data['InvoicePayment']['ppn']/100);
            }
            
            $total = $temptotal;
            $data['InvoicePayment']['grand_total_payment'] = $total;
            $this->Invoice->InvoicePaymentDetail->InvoicePayment->set($data);
            $validateInv = $this->Invoice->InvoicePaymentDetail->InvoicePayment->validates();

            if($validateInv && $validate_price_pay){
                $this->Invoice->InvoicePaymentDetail->InvoicePayment->set($data);

                if($this->Invoice->InvoicePaymentDetail->InvoicePayment->save()){
                    $this->loadModel('Journal');
                    $invoice_payment_id = $this->Invoice->InvoicePaymentDetail->InvoicePayment->id;
                    $document_no = !empty($data['InvoicePayment']['nodoc'])?$data['InvoicePayment']['nodoc']:false;
                    $this->Journal->deleteJournal( $invoice_payment_id, 'invoice_payment' );

                    if( !empty($data['InvoicePayment']['grand_total_payment']) ) {
                        $this->Journal->setJournal( $invoice_payment_id, $document_no, 'pembayaran_invoice_coa_credit_id', 0, $data['InvoicePayment']['grand_total_payment'], 'invoice_payment' );
                        $this->Journal->setJournal( $invoice_payment_id, $document_no, 'pembayaran_invoice_coa_debit_id', $data['InvoicePayment']['grand_total_payment'], 0, 'invoice_payment' );
                    }

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

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil %s Pembayaran Invoice'), $msg), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil %s Pembayaran Invoice #%s'), $msg, $invoice_payment_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $invoice_payment_id );
                    
                    $this->redirect(array(
                        'action' => 'invoice_payments'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran Invoice'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran Invoice #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            }else{
                $text = sprintf(__('Gagal %s Pembayaran Invoice'), $msg);

                if( !$validate_price_pay ){
                    $text .= ', pembayaran tidak boleh lebih besar dari total invoice';
                } else if( empty($validateInv) ) {
                    $text .= ', harap isi semua field';
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
                ),
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

        $customers = $this->Invoice->getData('list', array(
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
                'Invoice.id', 'Customer.id'
            )
        ));
        $list_customer = $this->Customer->getData('list', array(
            'conditions' => array(
                'Customer.id' => $customers,
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                'Coa.status' => 1,
                'Coa.is_cash_bank' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
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
                    $this->loadModel('Journal');

                    if( !empty($invoice_payment['InvoicePayment']['grand_total_payment']) ) {
                        $document_no = !empty($invoice_payment['InvoicePayment']['nodoc'])?$invoice_payment['InvoicePayment']['nodoc']:false;
                        $this->Journal->setJournal( $id, $document_no, 'pembayaran_invoice_coa_debit_id', 0, $invoice_payment['InvoicePayment']['grand_total_payment'], 'invoice_payment_void' );
                        $this->Journal->setJournal( $id, $document_no, 'pembayaran_invoice_coa_credit_id', $invoice_payment['InvoicePayment']['grand_total_payment'], 0, 'invoice_payment_void' );
                    }

                    $this->MkCommon->setCustomFlash(__('Berhasil menghapus invoice pembayaran'), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil menghapus invoice pembayaran ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menghapus invoice pembayaran'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal menghapus invoice pembayaran ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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

            $revenuDetails = $this->Revenue->RevenueDetail->getData('all', array(
                'conditions' => array(
                    'RevenueDetail.revenue_id' => $arr_id,
                ),
            ));

            if( !empty($revenuDetails) ) {
                foreach ($revenuDetails as $key => $detail) {
                    $revenue_detail_id = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'id');
                    $tarif_angkutan_type = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'tarif_angkutan_type');
                    $jenis_unit_angkutan = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'payment_type');
                    $is_charge = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'is_charge');
                    $revenue_id = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'revenue_id');
                    $price_unit = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'price_unit');
                    $qty = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'qty_unit');

                    $detail = $this->Revenue->getMerge($detail, false, $revenue_id, 'first');
                    $jenis_unit = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'revenue_tarif_type');

                    if( ( $jenis_unit_angkutan != 'per_truck' && ( $tarif_angkutan_type != 'angkut' && !empty($is_charge) ) ) || $jenis_unit == 'per_unit' ) {
                        $flagShowPrice = true;
                    } else {
                        $flagShowPrice = false;
                    }

                    $value_price = 0;

                    if( $flagShowPrice ) {
                        if( !empty($qty) && $jenis_unit_angkutan == 'per_unit' ){
                            $value_price = $price_unit * $qty;
                        }else if( $jenis_unit_angkutan == 'per_truck' ){
                            $value_price = $price_unit;
                        }

                        $this->Revenue->RevenueDetail->set('total_price_unit', $value_price);
                        $this->Revenue->RevenueDetail->id = $revenue_detail_id;
                        $this->Revenue->RevenueDetail->save();
                    }
                }

                $revenues = $this->Revenue->RevenueDetail->getData('all', array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $arr_id,
                    ),
                    'group' => array(
                        'RevenueDetail.revenue_id'
                    ),
                    'fields'=> array(
                        'RevenueDetail.revenue_id', 
                        'SUM(RevenueDetail.total_price_unit) as total',
                    ),
                ), true, array(
                    'branch' => false,
                ));

                if( !empty($revenues) ) {
                    foreach ($revenues as $key => $revenue) {
                        $revenue_id = $this->MkCommon->filterEmptyField($revenue, 'RevenueDetail', 'revenue_id');
                        $total = !empty($revenue[0]['total'])?$revenue[0]['total']:0;

                        $this->Revenue->set('total', $total);
                        $this->Revenue->id = $revenue_id;
                        $this->Revenue->save();
                    }
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
        $this->loadModel('Invoice');
        $this->loadModel('Revenue');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $start = 1;
        $limit = 30;

        $invoice_conditions = array(
            'Invoice.branch_id' => $allow_branch_id,
        );
        $invoiceUnpaidOption = array(
            'Invoice.is_canceled' => 0,
            'Invoice.complete_paid' => 0,
            'Invoice.paid' => 0,
            'Invoice.branch_id' => $allow_branch_id,
        );
        $invoicePaidOption = array(
            'Invoice.is_canceled' => 0,
            'Invoice.complete_paid' => 1,
            'Invoice.branch_id' => $allow_branch_id,
        );
        $invoiceHalfPaidOption = array(
            'Invoice.is_canceled' => 0,
            'Invoice.complete_paid' => 0,
            'Invoice.paid' => 1,
            'Invoice.branch_id' => $allow_branch_id,
        );
        $invoiceVoidOption = array(
            'Invoice.is_canceled' => 1,
            'Invoice.branch_id' => $allow_branch_id,
        );
        $customerConditions = array(
            'Customer.branch_id' => $allow_branch_id,
        );

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

            // Custom Otorisasi
            $invoice_conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoice_conditions, 'conditions' );
            $invoiceUnpaidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoiceUnpaidOption, 'conditions' );
            $invoicePaidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoicePaidOption, 'conditions' );
            $invoiceHalfPaidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoiceHalfPaidOption, 'conditions' );
            $invoiceVoidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoiceVoidOption, 'conditions' );
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

        $dataStatus['InvoiceUnpaid'] = $this->Invoice->getData('count', array(
            'conditions' => $invoiceUnpaidOption,
        ), true, array(
            'branch' => false,
        ));
        $dataStatus['InvoicePaid'] = $this->Invoice->getData('count', array(
            'conditions' => $invoicePaidOption,
        ), true, array(
            'branch' => false,
        ));
        $dataStatus['InvoiceHalfPaid'] = $this->Invoice->getData('count', array(
            'conditions' => $invoiceHalfPaidOption,
        ), true, array(
            'branch' => false,
        ));
        $dataStatus['InvoiceVoid'] = $this->Invoice->getData('count', array(
            'conditions' => $invoiceVoidOption,
        ), true, array(
            'status' => 'all',
        ));

        if( !empty($invoices) ) {
            foreach ($invoices as $key => $invoice) {
                $invoice = $this->Revenue->RevenueDetail->getSumUnit($invoice, $invoice['Invoice']['id']);
                $invoice = $this->Invoice->getMergePayment($invoice, $invoice['Invoice']['id'] );
                $invoices[$key] = $invoice;
            }
        }

        $customers = $this->Invoice->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => $customerConditions,
        ), true, array(
            'branch' => false,
            'plant' => false,
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
    }

    function invoice_delete($id){
        $this->loadModel('Invoice');
        $is_ajax = $this->RequestHandler->isAjax();
        $action_type = 'invoice';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $invoice = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id
            ),
            'contain' => array(
                'InvoiceDetail'
            )
        ));

        if( !empty($invoice) ){
            if(!empty($this->request->data)){
                if(!empty($this->request->data['Invoice']['canceled_date'])){
                    $this->loadModel('Revenue');

                    $this->request->data['Invoice']['canceled_date'] = $this->MkCommon->getDate($this->request->data['Invoice']['canceled_date']);
                    $this->request->data['Invoice']['is_canceled'] = 1;
                    $this->request->data['Invoice']['paid'] = 0;
                    $this->request->data['Invoice']['complete_paid'] = 0;
                    $this->request->data['Invoice']['status'] = 0;

                    $this->Invoice->id = $id;
                    $this->Invoice->set($this->request->data);

                    if($this->Invoice->save()){
                        $this->loadModel('Journal');

                        if( !empty($invoice['Invoice']['total']) ) {
                            $document_no = !empty($invoice['Invoice']['no_invoice'])?$invoice['Invoice']['no_invoice']:false;
                            $this->Journal->setJournal( $id, $document_no, 'invoice_coa_debit_id', 0, $invoice['Invoice']['total'], 'invoice_void' );
                            $this->Journal->setJournal( $id, $document_no, 'invoice_coa_credit_id', $invoice['Invoice']['total'], 0, 'invoice_void' );
                        }

                        $this->Invoice->InvoiceDetail->updateAll(
                            array(
                                'InvoiceDetail.status' => 0
                            ),
                            array(
                                'InvoiceDetail.invoice_id' => $id,
                            )
                        );

                        if($invoice['Invoice']['type_invoice'] == 'region' && !empty($invoice['InvoiceDetail'])){
                            $revenueId = Set::extract('/InvoiceDetail/revenue_id', $invoice);
                            $revenueDetailId = Set::extract('/InvoiceDetail/revenue_detail_id', $invoice);
                        }else{
                            $revenueId = $this->Revenue->RevenueDetail->getData('list', array(
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
                            $revenueDetailId = $this->Revenue->RevenueDetail->getData('list', array(
                                'conditions' => array(
                                    'RevenueDetail.invoice_id' => $id
                                ),
                                'fields' => array(
                                    'RevenueDetail.id', 'RevenueDetail.id',
                                )
                            ));
                        }

                        if(!empty($revenueDetailId)){
                            $this->Revenue->RevenueDetail->updateAll(
                                array(
                                    'invoice_id' => NULL,
                                ),
                                array(
                                    'RevenueDetail.id' => $revenueDetailId
                                )
                            );
                        }

                        $revenueId = array_unique($revenueId);

                        if( !empty($revenueId) ) {
                            foreach ($revenueId as $key => $revenue_id) {
                                $revenueDetails = $this->Revenue->RevenueDetail->getData('first', array(
                                    'conditions' => array(
                                        'RevenueDetail.revenue_id' => $revenue_id,
                                        'RevenueDetail.invoice_id' => $id,
                                    ),
                                ));

                                $this->Revenue->id = $revenue_id;

                                if(!empty($revenueDetails)){
                                    $this->Revenue->set('transaction_status', 'half_invoiced');
                                } else {
                                    $this->Revenue->set('transaction_status', 'posting');
                                }

                                $this->Revenue->save();
                            }
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
                        $this->Log->logActivity( sprintf(__('Berhasil menghapus invoice #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal menghapus invoice #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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

        $modelName = 'Invoice';
        $canceled_date = !empty($this->request->data['Invoice']['canceled_date']) ? $this->request->data['Invoice']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    public function report_customers( $data_action = false ) {
        $this->loadModel('Customer');
        $this->loadModel('Invoice');
        $this->loadModel('InvoicePayment');

        $fromMonth = '01';
        $fromYear = date('Y');
        $toMonth = date('m');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Customer.branch_id' => $allow_branch_id,
        );
        $optionConditions = $conditions;

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

            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $conditions, 'conditions' );
        }

        $customers = $this->Customer->getData('all', array(
            'conditions' => $conditions,
        ), true, array(
            'plant' => false,
            'branch' => false,
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
                ), true, array(
                    'branch' => false,
                ));
                $customer['InvoiceYear'] = !empty($invoiceYear[0]['total'])?$invoiceYear[0]['total']/12:0;

                $invoices = $this->Invoice->getData('all', array(
                    'conditions' => array(
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
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

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
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

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
                        'Invoice.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <=' => $monthDt,
                    ),
                    'fields' => array(
                        'SUM(Invoice.total) total',
                    ),
                ), true, array(
                    'branch' => false,
                ));

                $invoicePaymentsBefore = $this->InvoicePayment->getData('first', array(
                    'conditions' => array(
                        'InvoicePayment.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') <=' => $monthDt,
                    ),
                    'fields' => array(
                        'SUM(InvoicePayment.total_payment) total',
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
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
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));
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
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => $optionConditions,
        ), true, array(
            'plant' => false,
            'branch' => false,
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
        $this->loadModel('Ttuj');
        $this->loadModel('SuratJalan');

        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $ttuj_id
            )
        ));
        $is_draft = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'is_draft');

        if( !empty($ttuj) && empty($is_draft) ) {
            $ttuj = $this->Ttuj->getMergeContain( $ttuj, $ttuj_id );
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
    }

    function surat_jalan_add( $id = false ){
        $this->loadModel('Ttuj');
        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id
            )
        ), true, array(
            'status' => 'all',
        ));

        if( !empty($ttuj) ) {
            $ttuj = $this->Ttuj->getSumUnit( $ttuj, $id );
            $this->set('sub_module_title', __('Terima Surat Jalan'));
            $this->doSuratJalan($id, $ttuj);
        } else {
            $this->redirect($this->referer());
        }
    }

    public function surat_jalan_edit($id = false) {
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
                    if( $qtySJNow <= $qtyTipeMotor ) {
                        if($this->SuratJalan->save($data)){
                            $sj_id = $this->SuratJalan->id;

                            if( $qtySJNow >= $qtyTipeMotor ) {
                                $this->SuratJalan->Ttuj->set('is_sj_completed', 1);
                            } else {
                                $this->SuratJalan->Ttuj->set('is_sj_completed', 0);
                            }
                            $this->SuratJalan->Ttuj->id = $ttuj_id;
                            $this->SuratJalan->Ttuj->save();

                            $this->params['old_data'] = $ttuj;
                            $this->params['data'] = $data;

                            $this->MkCommon->setCustomFlash(__('Sukses menyimpan penerimaan SJ'), 'success');
                            $this->Log->logActivity( sprintf(__('Sukses menerima SJ #%s'), $sj_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $sj_id );
                            $this->redirect(array(
                                'controller' => 'revenues',
                                'action' => 'surat_jalan',
                                $ttuj_id,
                            ));
                        }else{
                            $this->MkCommon->setCustomFlash(__('Gagal menyimpan penerimaan SJ'), 'error'); 
                            $this->Log->logActivity( sprintf(__('Gagal menyimpan penerimaan SJ - TTUJ #%s'), $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_id ); 
                        }
                    } else {
                        $this->MkCommon->setCustomFlash(__('Qty diterima melebihi muatan truk'), 'error');
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
                $this->Log->logActivity( sprintf(__('Sukses membatalkan SJ ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal membatalkan SJ.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal membatalkan SJ ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('SJ tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function surat_jalan_outstanding( $driver_id = false, $pengganti = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('Revenue');
        $this->loadModel('Driver');
        $driver = $this->Driver->getData('first', array(
            'conditions' => array(
                'Driver.id' => $driver_id,
            )
        ), true, array(
            'status' => 'all',
            'branch' => false,
        ));

        if( !empty($driver) ) {
            $ttujs = $this->Ttuj->getData('all', array(
                'conditions' => array(
                    'OR' => array(
                        'Ttuj.driver_id' => $driver_id,
                        'Ttuj.driver_penganti_id' => $driver_id,
                    ),
                    'Ttuj.is_sj_completed' => 0,
                ),
                'order' => array(
                    'Ttuj.created' => 'DESC',
                    'Ttuj.id' => 'DESC',
                ),
            ), true, array(
                'branch' => false,
            ));

            if( !empty($ttujs) ) {
                $this->loadModel('TtujTipeMotor');

                foreach ($ttujs as $key => $ttuj) {
                    $ttuj['SjKembali'] = $this->Ttuj->SuratJalan->getSJKembali( $ttuj['Ttuj']['id'] );
                    $ttuj['TotalMuatan'] = $this->TtujTipeMotor->getTotalMuatan( $ttuj['Ttuj']['id'] );
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
    }

    public function report_revenue_customers( $data_action = false ) {
        $this->loadModel('Customer');
        $this->loadModel('Revenue');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $fromMonth = '01';
        $fromYear = date('Y');
        $toMonth = date('m');
        $toYear = date('Y');
        $conditions = array();
        $conditionsCustomer = array(
            'Customer.branch_id'=> $allow_branch_id,
        );
        $optionConditions = $conditionsCustomer;

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

            $conditionsCustomer = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $conditionsCustomer, 'conditions' );
        }

        $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') >='] = date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear));
        $conditions['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') <='] = date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear));


        $customerList = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => $optionConditions,
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));

        $options = $this->Customer->getData('paginate', array(
            'conditions' => $conditionsCustomer,
        ), true, array(
            'branch' => false,
            'plant' => false,
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
                $revenues = $this->Revenue->RevenueDetail->getData('all', array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Revenue',
                    ),
                    'group' => array(
                        'DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\')'
                    ),
                    'fields'=> array(
                        'Revenue.customer_id', 
                        'SUM(RevenueDetail.total_price_unit) as total',
                        'DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') as dt',
                    ),
                ), true, array(
                    'branch' => false,
                ));

                $conditionsYear = array(
                    'DATE_FORMAT(Revenue.date_revenue, \'%Y\')' => $avgYear,
                    'Revenue.customer_id' => $customer['Customer']['id'],
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
                ), true, array(
                    'branch' => false,
                ));
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
    }

    public function report_monitoring_sj_revenue( $data_action = false ) {
        $this->loadModel('Customer');
        $this->loadModel('Revenue');
        $this->loadModel('Ttuj');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $this->Ttuj->bindModel(array(
            'hasOne' => array(
                'Revenue' => array(
                    'className' => 'Revenue',
                    'foreignKey' => 'ttuj_id',
                    'conditions' => array(
                        'Revenue.status' => 1,
                    ),
                )
            )
        ), false);

        $options = array(
            'conditions' => array(
                'Revenue.id NOT' => NULL,
                'Ttuj.status' => 1,
                'Ttuj.is_draft' => 0,
                'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >=' => $dateFrom,
                'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <=' => $dateTo,
                'Ttuj.branch_id' => $allow_branch_id,
            ),
            'contain' => array(
                'Revenue',
            ),
            'order'=> array(
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
            'group' => array(
                'Ttuj.id'
            ),
        );
        $optionConditions = array(
            'Customer.branch_id' => $allow_branch_id,
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
                ), false);

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

                    case 'receipt_unpaid':
                        $options['conditions']['Ttuj.is_sj_completed'] = 1;
                        $revenueConditions = !empty($options['conditions'])?$options['conditions']:false;
                        $revenueConditions['Revenue.transaction_status <>'] = 'invoiced';
                        $revenues = $this->Revenue->getData('list', array(
                            'conditions' => $revenueConditions,
                            'contain' => array(
                                'Ttuj'
                            ),
                            'fields' => array(
                                'Revenue.id', 'Revenue.ttuj_id'
                            ),
                        ), true, array(
                            'status' => 'all',
                        ));

                        $options['conditions']['Ttuj.id'] = $revenues;
                        break;
                }
            }

            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
        }

        if( !empty($data_action) ) {
            $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
        } else {
            $options['limit'] = 20;
        }

        $this->paginate = $options;
        $ttujs = $this->paginate('Ttuj');

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
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => $optionConditions,
        ), true, array(
            'branch' => false,
            'plant' => false,
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
    }



    function invoice_hso_print($id, $action_print = false){
        $this->loadModel('Invoice');
        $this->loadModel('Revenue');
        $this->loadModel('GroupMotor');
        $this->loadModel('City');
        $this->loadModel('Customer');
        $this->loadModel('Ttuj');

        $module_title = __('Print Invoice HSO');
        $this->set('sub_module_title', trim($module_title));
        $this->set('active_menu', 'invoices');

        if( !empty($this->params['named']) ){
            $data_print = $this->params['named']['print'];
        } else {
            $data_print = 'invoice';
        }

        if( $action_print == 'excel' && !empty($this->params['named']) ) {
            $data_print = 'invoice';
        }

        $invoice = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($invoice)){
            $invoice = $this->Customer->getMerge($invoice, $invoice['Invoice']['customer_id']);

            switch ($data_print) {
                case 'header':
                    $invoice = $this->Invoice->InvoiceDetail->getMerge($invoice, $invoice['Invoice']['id']);

                    if( !empty($invoice['InvoiceDetail']) ) {
                        $revenue_id = Set::extract('/InvoiceDetail/revenue_id', $invoice['InvoiceDetail']);
                        $invoice = $this->Revenue->getMerge($invoice, $id, $revenue_id, 'all');

                        if( !empty($invoice['Revenue']) ) {
                            foreach ($invoice['Revenue'] as $key => $revenue) {
                                $revenue = $this->Revenue->RevenueDetail->getSumUnit($revenue, $id, 'revenue');
                                $revenue = $this->Revenue->RevenueDetail->getSumUnit($revenue, $id, 'revenue_price');
                                $invoice['Revenue'][$key] = $revenue;
                            }
                        }
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

            if( $data_print == 'invoice' || $action_print == 'excel' ) {
                $this->render('invoice_hso_non_header_print');
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
            $this->redirect($this->referer());
        }
    }

    public function report_revenue_monthly( $data_action = false ) {
        $this->loadModel('Customer');
        $this->loadModel('Invoice');
        $this->loadModel('InvoicePayment');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $fromMonthYear = date('Y-m');
        $toMonthYear = date('Y-m');
        $fromMonth = date('m');
        $toMonth = date('m');
        $fromYear = date('Y');
        $conditionsCustomer = array(
            'Customer.branch_id'=> $allow_branch_id,
        );
        $optionConditions = $conditionsCustomer;

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ttuj']['customer'] = $customer;
                $conditionsCustomer['Customer.id'] = $customer;
            }

            if( !empty($refine['fromMonth']) ){
                $fromMonth = urldecode($refine['fromMonth']);
            }

            if( !empty($refine['toMonth']) ){
                $toMonth = urldecode($refine['toMonth']);
            }

            if( !empty($refine['fromYear']) ){
                $fromYear = urldecode($refine['fromYear']);
            }

            if( !empty($fromYear) ) {
                if( !empty($fromMonth) ) {
                    $fromMonthYear = sprintf('%s-%s', $fromYear, $fromMonth);
                }
                if( !empty($toMonth) ) {
                    $toMonthYear = sprintf('%s-%s', $fromYear, $toMonth);
                }
            }
            $conditionsCustomer = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $conditionsCustomer, 'conditions' );
        }

        $lastMonth = date('Y-m', strtotime($fromMonthYear." -1 month"));
        $this->request->data['Ttuj']['from']['month'] = $fromMonth;
        $this->request->data['Ttuj']['to']['month'] = $toMonth;
        $this->request->data['Ttuj']['from']['year'] = $fromYear;

        $conditionsInvoice = array(
            'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') >=' => $fromMonthYear,
            'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <=' => $toMonthYear,
        );
        $conditionsInvoicePayment = array(
            'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') >=' => $fromMonthYear,
            'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') <=' => $toMonthYear,
            'InvoicePayment.is_canceled' => 0,
        );
        $customerList = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => $optionConditions,
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));

        $options = $this->Customer->getData('paginate', array(
            'conditions' => $conditionsCustomer,
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));
        $this->paginate = $options;
        $customers = $this->Customer->getData('all', $options, false);

        if( !empty($customers) ) {
            foreach ($customers as $key => $customer) {
                $conditionsInvoice['Invoice.customer_id'] = $customer['Customer']['id'];

                $conditionsInvLastMonth = array(
                    'Invoice.customer_id' => $customer['Customer']['id'],
                    'DATE_FORMAT(Invoice.invoice_date, \'%Y-%m\') <=' => $lastMonth,
                    'Invoice.paid' => 0,
                    'Invoice.complete_paid' => 0,
                );
                $customer['InvLastMonth'] = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsInvLastMonth,
                    'fields'=> array(
                        'Invoice.customer_id', 
                        'SUM(Invoice.total) as total',
                    ),
                    'group' => array(
                        'Invoice.customer_id'
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

                $conditionsInvVoidLastMonth = array(
                    'Invoice.customer_id' => $customer['Customer']['id'],
                    'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') <=' => $lastMonth,
                    'Invoice.is_canceled' => 1,
                );
                $customer['InvVoidLastMonth'] = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsInvVoidLastMonth,
                    'fields'=> array(
                        'Invoice.customer_id', 
                        'SUM(Invoice.total) as total',
                    ),
                    'group' => array(
                        'Invoice.customer_id'
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

                $conditionsInvPaidLastMonth = array(
                    'InvoicePayment.customer_id' => $customer['Customer']['id'],
                    'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') <=' => $lastMonth,
                    'InvoicePayment.is_canceled' => 0,
                );
                $customer['InvPaidLastMonth'] = $this->InvoicePayment->getData('first', array(
                    'conditions' => $conditionsInvPaidLastMonth,
                    'fields'=> array(
                        'InvoicePayment.customer_id', 
                        'SUM(InvoicePayment.grand_total_payment) as total',
                    ),
                    'group' => array(
                        'InvoicePayment.customer_id'
                    ),
                ), true, array(
                    'branch' => false,
                ));

                $conditionsInvoiceTotal = $conditionsInvoice;
                $customer['InvoiceTotal'] = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsInvoiceTotal,
                    'fields'=> array(
                        'Invoice.customer_id', 
                        'SUM(Invoice.total) as total',
                    ),
                    'group' => array(
                        'Invoice.customer_id'
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

                $conditionsInvoiceVoid = array(
                    'Invoice.customer_id' => $customer['Customer']['id'],
                    'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') >=' => $fromMonthYear,
                    'DATE_FORMAT(Invoice.canceled_date, \'%Y-%m\') <=' => $toMonthYear,
                    'Invoice.is_canceled' => 1,
                );
                $customer['InvoiceVoidTotal'] = $this->Invoice->getData('first', array(
                    'conditions' => $conditionsInvoiceVoid,
                    'fields'=> array(
                        'Invoice.customer_id', 
                        'SUM(Invoice.total) as total',
                    ),
                    'group' => array(
                        'Invoice.customer_id'
                    ),
                ), true, array(
                    'status' => 'all',
                ));

                $conditionsInvoicePayment['InvoicePayment.customer_id'] = $customer['Customer']['id'];
                $customer['InvoicePaymentTotal'] = $this->InvoicePayment->getData('first', array(
                    'conditions' => $conditionsInvoicePayment,
                    'fields'=> array(
                        'InvoicePayment.customer_id', 
                        'SUM(InvoicePayment.grand_total_payment) as total',
                    ),
                    'group' => array(
                        'InvoicePayment.customer_id'
                    ),
                ), true, array(
                    'branch' => false,
                ));

                $customers[$key] = $customer;
            }
        }

        $module_title = sprintf(__('Laporan Saldo Piutang Per Bulan %s'), $this->MkCommon->getCombineDate($fromMonthYear, $toMonthYear, 'short'));
        $this->set('sub_module_title', $module_title);
        $this->set('active_menu', 'report_revenue_monthly');

        $this->set(compact(
            'data_action', 'customerList', 
            'customers', 'lastMonth', 'fromMonthYear',
            'toMonthYear'
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
    }

    function ttuj_payments( $action_type = 'uang_jalan_commission' ){
        $this->loadModel('TtujPayment');
        $conditions = array(
            'TtujPayment.type' => $action_type,
        );

        switch ($action_type) {
            case 'biaya_ttuj':
                $this->set('active_menu', 'biaya_ttuj_payments');
                $this->set('sub_module_title', __('Pembayaran Biaya TTUJ'));
                break;
            
            default:
                $this->set('active_menu', 'uang_jalan_commission_payments');
                $this->set('sub_module_title', __('Pembayaran Uang Jalan/Komisi'));
                break;
        }

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nodoc'])){
                $to = urldecode(rawurldecode(rawurldecode($refine['nodoc'])));
                $this->request->data['InvoicePayment']['nodoc'] = $to;
                $conditions['TtujPayment.nodoc LIKE'] = '%'.$to.'%';
            }
            
            if(!empty($refine['no_ttuj'])){
                $no_ttuj = urldecode($refine['no_ttuj']);
                $this->request->data['Ttuj']['no_ttuj'] = $no_ttuj;
                $conditions['Ttuj.no_ttuj LIKE'] = '%'.$no_ttuj.'%';
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
                    $conditions['DATE_FORMAT(TtujPayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
                    $conditions['DATE_FORMAT(TtujPayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
                }
                $this->request->data['Ttuj']['date'] = $dateStr;
            }
            
            if(!empty($refine['receiver_name'])){
                $receiver_name = urldecode($refine['receiver_name']);
                $this->request->data['Ttuj']['receiver_name'] = $receiver_name;
                $conditions['TtujPayment.receiver_name LIKE'] = '%'.$receiver_name.'%';
            }
        }

        $this->paginate = $this->TtujPayment->getData('paginate', array(
            'conditions' => $conditions,
            'order' => array(
                'TtujPayment.created' => 'DESC',
                'TtujPayment.id' => 'DESC',
            ),
        ));
        $invoices = $this->paginate('TtujPayment');

        $this->set(compact(
            'invoices', 'action_type'
        )); 
    }

    function ttuj_payment_add( $action_type = 'uang_jalan_commission' ){
        switch ($action_type) {
            case 'biaya_ttuj':
                $module_title = __('Tambah Pembayaran TTUJ');
                break;
            
            default:
                $module_title = __('Tambah Pembayaran Uang Jalan/Komisi');
                break;
        }

        $this->set('sub_module_title', trim($module_title));
        $this->doTtujPayment( $action_type );
    }

    function detail_ttuj_payment($id = false, $action_type = 'uang_jalan_commission'){
        $this->loadModel('Customer');
        $this->loadModel('TtujPayment');
        $module_title = __('Kas/Bank');
        $invoice = $this->TtujPayment->getData('first', array(
            'conditions' => array(
                'TtujPayment.id' => $id
            ),
            'contain' => array(
                'TtujPaymentDetail',
            ),
        ));

        switch ($action_type) {
            case 'biaya_ttuj':
                $this->set('active_menu', 'biaya_ttuj_payments');
                $sub_module_title = $title_for_layout = 'Detail Pembayaran Biaya TTUJ';
                break;
            
            default:
                $this->set('active_menu', 'uang_jalan_commission_payments');
                $sub_module_title = $title_for_layout = 'Detail Pembayaran Uang Jalan';
                break;
        }

        if(!empty($invoice)){
            $this->request->data = $invoice;

            if( !empty($invoice['TtujPaymentDetail']) ) {
                $this->loadModel('Ttuj');

                foreach ($invoice['TtujPaymentDetail'] as $key => $ttujPaymentDetail) {
                    $ttuj_id = !empty($ttujPaymentDetail['ttuj_id'])?$ttujPaymentDetail['ttuj_id']:false;
                    $dataTtujType = !empty($ttujPaymentDetail['type'])?$ttujPaymentDetail['type']:false;
                    $amount = !empty($ttujPaymentDetail['amount'])?$ttujPaymentDetail['amount']:0;
                    $resultTtuj = $this->Ttuj->getTtujPayment($ttuj_id, $dataTtujType);
                    $this->request->data['Ttuj'][] = $resultTtuj;
                    $this->request->data['TtujPayment']['amount_payment'][] = $amount;
                    $this->request->data['TtujPayment']['ttuj_id'][] = $ttuj_id;
                    $this->request->data['TtujPayment']['data_type'][] = $dataTtujType;
                }
            }

            $this->loadModel('Coa');
            
            $coas = $this->Coa->getData('list', array(
                'fields' => array(
                    'Coa.id', 'Coa.coa_name'
                ),
            ), true, array(
                'status' => 'cash_bank_child',
            ));

            $this->set(compact(
                'invoice', 'sub_module_title', 'title_for_layout',
                'drivers', 'action_type', 'module_title', 'coas'
            ));

            $this->render('ttuj_payment_form');
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function doTtujPaymentDetail ( $dataAmount, $data, $ttuj_payment_id = false ) {
        $flagTtujPaymentDetail = true;
        $totalPayment = 0;
        $document_type = !empty($data['TtujPayment']['type'])?$data['TtujPayment']['type']:false;

        if( !empty($ttuj_payment_id) ) {
            $this->TtujPayment->TtujPaymentDetail->updateAll( array(
                'TtujPaymentDetail.status' => 0,
            ), array(
                'TtujPaymentDetail.ttuj_payment_id' => $ttuj_payment_id,
            ));
        }

        if( !empty($dataAmount) ) {
            foreach ($dataAmount as $key => $amount) {
                $ttuj_id = !empty($this->request->data['TtujPayment']['ttuj_id'][$key])?$this->request->data['TtujPayment']['ttuj_id'][$key]:false;
                $data_type = !empty($this->request->data['TtujPayment']['data_type'][$key])?$this->request->data['TtujPayment']['data_type'][$key]:false;
                $amount = !empty($amount)?$this->MkCommon->convertPriceToString($amount, 0):0;
                $dataTtuj = $this->TtujPayment->TtujPaymentDetail->Ttuj->getTtujPayment($ttuj_id, $data_type);
                $dataTtujPaymentDetail = array(
                    'TtujPaymentDetail' => array(
                        'ttuj_id' => $ttuj_id,
                        'type' => $data_type,
                        'amount' => $amount,
                    ),
                );
                $this->request->data['Ttuj'][$key] = $dataTtuj;
                $this->request->data['TtujPayment']['amount_payment'][$key] = $amount;
                $totalPayment += $amount;
                $total_dibayar = $this->TtujPayment->TtujPaymentDetail->getTotalPayment($ttuj_id, $data_type) + $amount;

                if( !empty($ttuj_payment_id) ) {
                    $dataTtujPaymentDetail['TtujPaymentDetail']['ttuj_payment_id'] = $ttuj_payment_id;
                    $total = !empty($dataTtuj['total'])?$dataTtuj['total']:0;

                    if( !empty($total_dibayar) ) {
                        $flagPaidTtuj = 'half';

                        if( $total <= $total_dibayar ) {
                            $flagPaidTtuj = 'full';
                        }
                    
                        $this->TtujPayment->TtujPaymentDetail->Ttuj->set('paid_'.$data_type, $flagPaidTtuj);
                        $this->TtujPayment->TtujPaymentDetail->Ttuj->id = $ttuj_id;
                        
                        if( !$this->TtujPayment->TtujPaymentDetail->Ttuj->save() ) {
                            $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran %s #%s'), $data_type, $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_id );
                        }
                    }
                }

                $this->TtujPayment->TtujPaymentDetail->create();
                $this->TtujPayment->TtujPaymentDetail->set($dataTtujPaymentDetail);

                if( !empty($ttuj_payment_id) ) {
                    if( !$this->TtujPayment->TtujPaymentDetail->save() ) {
                        $flagTtujPaymentDetail = false;
                    }
                } else {
                    if( !$this->TtujPayment->TtujPaymentDetail->validates() ) {
                        $flagTtujPaymentDetail = false;
                    }
                }
            }
        } else {
            $flagTtujPaymentDetail = false;
            $this->MkCommon->setCustomFlash(__('Mohon pilih biaya yang akan dibayar.'), 'error'); 
        }

        if( !empty($totalPayment) && !empty($ttuj_payment_id) ) {
            $this->TtujPayment->id = $ttuj_payment_id;
            $this->TtujPayment->set('total_payment', $totalPayment);

            if( !$this->TtujPayment->save() ) {
                $this->Log->logActivity( sprintf(__('Gagal mengubah total pembayaran ttuj #%s'), $ttuj_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_payment_id );
            } else {
                $this->loadModel('Journal');
                $document_no = !empty($data['TtujPayment']['nodoc'])?$data['TtujPayment']['nodoc']:false;

                switch ($document_type) {
                    case 'biaya_ttuj':
                        $this->Journal->deleteJournal( $ttuj_payment_id, 'biaya_ttuj_payment' );
                        $this->Journal->setJournal( $ttuj_payment_id, $document_no, 'biaya_ttuj_payment_coa_credit_id', 0, $totalPayment, 'biaya_ttuj_payment' );
                        $this->Journal->setJournal( $ttuj_payment_id, $document_no, 'biaya_ttuj_payment_coa_debit_id', $totalPayment, 0, 'biaya_ttuj_payment' );
                        break;
                    
                    default:
                        $this->Journal->deleteJournal( $ttuj_payment_id, 'uang_Jalan_commission_payment' );
                        $this->Journal->setJournal( $ttuj_payment_id, $document_no, 'uang_Jalan_commission_payment_coa_credit_id', 0, $totalPayment, 'uang_Jalan_commission_payment' );
                        $this->Journal->setJournal( $ttuj_payment_id, $document_no, 'uang_Jalan_commission_payment_coa_debit_id', $totalPayment, 0, 'uang_Jalan_commission_payment' );
                        break;
                }
            }
        }

        return $flagTtujPaymentDetail;
    }

    function doTtujPayment( $action_type, $id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TtujPayment');
        $ttuj_id = false;

        switch ($action_type) {
            case 'biaya_ttuj':
                $labelName = 'Biaya TTUJ';
                $this->set('active_menu', 'biaya_ttuj_payments');
                break;
            
            default:
                $labelName = 'Uang Jalan/Komisi';
                $this->set('active_menu', 'uang_jalan_commission_payments');
                break;
        }

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['TtujPayment']['date_payment'] = !empty($data['TtujPayment']['date_payment']) ? $this->MkCommon->getDate($data['TtujPayment']['date_payment']) : '';
            $data['TtujPayment']['type'] = $action_type;
            $data['TtujPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $dataAmount = !empty($data['TtujPayment']['amount_payment'])?$data['TtujPayment']['amount_payment']:false;
            $flagTtujPaymentDetail = $this->doTtujPaymentDetail($dataAmount, $data);

            $this->TtujPayment->create();
            $this->TtujPayment->set($data);

            if( $this->TtujPayment->validates() && !empty($flagTtujPaymentDetail) ){
                if($this->TtujPayment->save()){
                    $document_id = $this->TtujPayment->id;
                    $flagTtujPaymentDetail = $this->doTtujPaymentDetail($dataAmount, $data, $document_id);

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Pembayaran %s'), $labelName), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan Pembayaran %s #%s'), $labelName, $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $document_id );
                    
                    $this->redirect(array(
                        'action' => 'ttuj_payments',
                        $action_type,
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal melakukan Pembayaran %s'), $labelName), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal melakukan Pembayaran %s #%s'), $labelName, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $msgError = array();

                if( !empty($this->TtujPayment->TtujPaymentDetail->validationErrors) ) {
                    $errorPaymentDetails = $this->TtujPayment->TtujPaymentDetail->validationErrors;

                    foreach ($errorPaymentDetails as $key => $errorPaymentDetail) {
                        if( !empty($errorPaymentDetail) ) {
                            foreach ($errorPaymentDetail as $key => $err_msg) {
                                $msgError[] = $err_msg;
                            }
                        }
                    }
                }

                if( !empty($msgError) ) {
                    $this->MkCommon->setCustomFlash('<ul><li>'.implode('</li><li>', $msgError).'</li></ul>', 'error'); 
                } else if( $flagTtujPaymentDetail ) {
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran'), 'error'); 
                }
            }

            $this->request->data['TtujPayment']['date_payment'] = !empty($data['TtujPayment']['date_payment']) ? $data['TtujPayment']['date_payment'] : '';
        }else if(!empty($id) && !empty($data_local)){
             $this->request->data = $data_local;
        }

        $this->loadModel('Coa');

        $coas = $this->Coa->getData('list', array(
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ), true, array(
            'status' => 'cash_bank_child',
        ));

        $this->set(compact(
            'action_type', 'coas'
        ));
        $this->render('ttuj_payment_form');
    }

    function ttuj_payment_delete($id, $action_type){
        $this->loadModel('TtujPayment');
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $invoice = $this->TtujPayment->getData('first', array(
            'conditions' => array(
                'TtujPayment.id' => $id,
            ),
            'contain' => array(
                'TtujPaymentDetail',
            ),
        ));

        if( !empty($invoice) ){
            if(!empty($this->request->data)){
                $data = $this->request->data;

                switch ($action_type) {
                    case 'biaya_ttuj':
                        $labelName = 'Biaya TTUJ';
                        break;
                    
                    default:
                        $labelName = 'Uang Jalan/Komisi';
                        break;
                }

                if(!empty($data['TtujPayment']['canceled_date'])){
                    $ttuj_id = !empty($invoice['TtujPayment']['ttuj_id'])?$invoice['TtujPayment']['ttuj_id']:false;
                    $data['TtujPayment']['canceled_date'] = !empty($data['TtujPayment']['canceled_date'])?$this->MkCommon->getDate($data['TtujPayment']['canceled_date']):false;
                    $data['TtujPayment']['is_canceled'] = 1;

                    $this->TtujPayment->id = $id;
                    $this->TtujPayment->set($data);

                    if($this->TtujPayment->save()){
                        $this->loadModel('Journal');
                        $document_no = !empty($invoice['TtujPayment']['nodoc'])?$invoice['TtujPayment']['nodoc']:false;


                        if( !empty($invoice['TtujPaymentDetail']) ) {
                            foreach ($invoice['TtujPaymentDetail'] as $key => $ttujPaymentDetail) {
                                $ttuj_id = !empty($ttujPaymentDetail['ttuj_id'])?$ttujPaymentDetail['ttuj_id']:false;
                                $data_type = !empty($ttujPaymentDetail['type'])?$ttujPaymentDetail['type']:false;
                                $total_dibayar = $this->TtujPayment->TtujPaymentDetail->getTotalPayment($ttuj_id, $data_type);
                                $flagPaidTtuj = 'none';

                                if( !empty($total_dibayar) ) {
                                    $flagPaidTtuj = 'half';
                                }
                                    
                                $this->TtujPayment->TtujPaymentDetail->Ttuj->set('paid_'.$data_type, $flagPaidTtuj);
                                $this->TtujPayment->TtujPaymentDetail->Ttuj->id = $ttuj_id;
                                
                                if( !$this->TtujPayment->TtujPaymentDetail->Ttuj->save() ) {
                                    $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran %s #%s'), $data_type, $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_id );
                                }
                            }
                        }

                        if( !empty($invoice['TtujPayment']['total_payment']) ) {
                            switch ($action_type) {
                                case 'biaya_ttuj':
                                    $this->Journal->setJournal( $id, $document_no, 'biaya_ttuj_payment_coa_debit_id', 0, $invoice['TtujPayment']['total_payment'], 'biaya_ttuj_payment' );
                                    $this->Journal->setJournal( $id, $document_no, 'biaya_ttuj_payment_coa_credit_id', $invoice['TtujPayment']['total_payment'], 0, 'biaya_ttuj_payment' );
                                    break;
                                
                                default:
                                    $this->Journal->setJournal( $id, $document_no, 'uang_Jalan_commission_payment_coa_debit_id', 0, $invoice['TtujPayment']['total_payment'], 'uang_Jalan_commission_payment_void' );
                                    $this->Journal->setJournal( $id, $document_no, 'uang_Jalan_commission_payment_coa_credit_id', $invoice['TtujPayment']['total_payment'], 0, 'uang_Jalan_commission_payment_void' );
                                    break;
                            }
                        }

                        $msg = array(
                            'msg' => sprintf(__('Berhasil menghapus pembayaran %s.'), $labelName),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil menghapus pembayaran %s #%s'), $labelName, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal menghapus pembayaran %s #%s'), $labelName, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => sprintf(__('Harap masukkan tanggal pembatalan pembayaran %s.'), $labelName),
                        'type' => 'error'
                    );
                }
            }

            $this->set('invoice', $invoice);
        }else{
            $msg = array(
                'msg' => __('Data tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'TtujPayment';
        $canceled_date = !empty($this->request->data['TtujPayment']['canceled_date']) ? $this->request->data['TtujPayment']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    public function import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/revenues.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', 'Revenue');
            $this->set('active_menu', 'revenues');
            $this->set('sub_module_title', __('Import Revenue'));

            if(!empty($this->request->data)) { 
                $targetdir = $this->MkCommon->_import_excel( $this->request->data );

                if( !empty($targetdir) ) {
                    $xls_files = glob( $targetdir );

                    if(empty($xls_files)) {
                        $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                        $this->redirect(array(
                            'action'=>'import'
                        ));
                    } else {
                        $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
                        $uploaded_file = $uploadedXls['xls'];
                        $file = explode(".", $uploaded_file['name']);
                        $extension = array_pop($file);
                        
                        if($extension == 'xls') {
                            $dataimport = new Spreadsheet_Excel_Reader();
                            $dataimport->setUTFEncoder('iconv');
                            $dataimport->setOutputEncoding('UTF-8');
                            $dataimport->read($uploaded_file['tmp_name']);
                            
                            if(!empty($dataimport)) {
                                $this->loadModel('CustomerNoType');
                                $this->loadModel('Truck');
                                $this->loadModel('City');
                                $this->loadModel('TarifAngkutan');
                                $this->loadModel('GroupMotor');
                                $this->loadModel('Revenue');
                                $data = $dataimport;
                                $row_submitted = 0;
                                $successfull_row = 0;
                                $failed_row = 0;
                                $error_message = '';

                                for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                    $datavar = array();
                                    $flag = true;
                                    $i = 1;
                                    $tarifNotFound = false;

                                    while ($flag) {
                                        if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                            $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                            $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                            $$variable = $thedata;
                                            $datavar[] = $thedata;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }

                                    if(array_filter($datavar)) {
                                        $branch = $this->GroupBranch->Branch->getData('first', array(
                                            'conditions' => array(
                                                'Branch.code' => $kode_cabang,
                                            ),
                                        ));
                                        $customer = $this->CustomerNoType->find('first', array(
                                            'conditions' => array(
                                                'CustomerNoType.code' => $kode_customer,
                                                'CustomerNoType.status' => 1,
                                            ),
                                        ));
                                        $truck = $this->Truck->find('first', array(
                                            'conditions' => array(
                                                'Truck.nopol' => $nopol,
                                                'Truck.status' => 1,
                                            ),
                                        ), false);
                                        $formCity = $this->City->getData('first', array(
                                            'conditions' => array(
                                                'City.name' => $dari,
                                                'City.status' => 1,
                                            ),
                                        ));
                                        $toCity = $this->City->getData('first', array(
                                            'conditions' => array(
                                                'City.name' => $tujuan,
                                                'City.status' => 1,
                                            ),
                                        ));

                                        $branch_id = !empty($branch['Branch']['id'])?$branch['Branch']['id']:false;
                                        $customer_id = !empty($customer['CustomerNoType']['id'])?$customer['CustomerNoType']['id']:false;
                                        $truck_id = !empty($truck['Truck']['id'])?$truck['Truck']['id']:false;
                                        $truck_capacity = !empty($truck['Truck']['capacity'])?$truck['Truck']['capacity']:false;
                                        $from_city_id = !empty($formCity['City']['id'])?$formCity['City']['id']:false;
                                        $to_city_id = !empty($toCity['City']['id'])?$toCity['City']['id']:false;
                                        $tanggal_revenue = $this->MkCommon->getDate($tanggal_revenue);
                                        $tarif = $this->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, false, $customer_id, $truck_capacity, false );
                                        $tarif_per_truck = 0;
                                        $jenis_tarif = !empty($tarif['jenis_unit'])?$tarif['jenis_unit']:'per_unit';
                                        // $no_do = !empty($tarif['no_do'])?$tarif['no_do']:false;
                                        // $no_js = !empty($tarif['no_js'])?$tarif['no_js']:false;
                                        // $group_motor = !empty($tarif['group_motor'])?$tarif['group_motor']:false;
                                        // $jml_unit = !empty($tarif['jml_unit'])?$tarif['jml_unit']:false;
                                        // $is_charge = !empty($tarif['is_charge'])?$tarif['is_charge']:false;
                                        // $harga_unit = !empty($tarif['harga_unit'])?$tarif['harga_unit']:false;
                                        $ppn = !empty($ppn)?$this->MkCommon->convertPriceToString($ppn):0;
                                        $pph = !empty($pph)?$this->MkCommon->convertPriceToString($pph):0;
                                        $dataRevenue = array();

                                        if( !empty($tarif) ) {
                                            if( $jenis_tarif == 'per_truck' && !empty($tarif['tarif']) ) {
                                                $tarif_per_truck = $tarif['tarif'];
                                            }

                                            $i = 1;
                                            $idx = 0;
                                            $flag = true;
                                            $additional_charge = 0;

                                            while ($flag) {
                                                $varGroup = sprintf('tujuan_%s', $i);

                                                if( !empty($$varGroup) ) {
                                                    $tujuan_detail = $$varGroup;
                                                    $no_do_string = sprintf('no_do_%s', $i);
                                                    $no_do_detail = $$no_do_string;
                                                    $no_sj_string = sprintf('no_sj_%s', $i);
                                                    $no_sj_detail = $$no_sj_string;
                                                    $group_motor_string = sprintf('group_motor_%s', $i);
                                                    $group_motor_detail = $$group_motor_string;
                                                    $jml_unit_string = sprintf('jml_unit_%s', $i);
                                                    $jml_unit_detail = !empty($$jml_unit_string)?$this->MkCommon->convertPriceToString($$jml_unit_string):0;
                                                    $is_charge_string = sprintf('is_charge_%s', $i);
                                                    $is_charge_detail = !empty($$is_charge_string)?$$is_charge_string:0;
                                                    $harga_unit_string = sprintf('harga_unit_%s', $i);
                                                    $harga_unit_detail = !empty($$harga_unit_string)?$this->MkCommon->convertPriceToString($$harga_unit_string):0;
                                                    $toCityDetail = $this->City->getData('first', array(
                                                        'conditions' => array(
                                                            'City.name' => $tujuan_detail,
                                                            'City.status' => 1,
                                                        ),
                                                    ));
                                                    $groupMotor = $this->GroupMotor->getData('first', array(
                                                        'conditions' => array(
                                                            'GroupMotor.name' => $group_motor_detail,
                                                        ),
                                                    ));

                                                    $to_city_id_detail = !empty($toCityDetail['City']['id'])?$toCityDetail['City']['id']:false;
                                                    $group_motor_id = !empty($groupMotor['GroupMotor']['id'])?$groupMotor['GroupMotor']['id']:false;
                                                    $total_price_unit = $harga_unit_detail * $jml_unit_detail;

                                                    $tarif_detail = $this->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, $to_city_id_detail, $customer_id, $truck_capacity, $group_motor_id );

                                                    if( !empty($tarif_detail) ) {
                                                        $total_tarif_detail = !empty($tarif_detail['tarif'])?$tarif_detail['tarif']:0;
                                                        $tarif_angkutan_id = !empty($tarif_detail['tarif_angkutan_id'])?$tarif_detail['tarif_angkutan_id']:false;
                                                        $tarif_angkutan_type = !empty($tarif_detail['tarif_angkutan_type'])?$tarif_detail['tarif_angkutan_type']:false;
                                                        $jenis_tarif_detail = !empty($tarif_detail['jenis_unit'])?$tarif_detail['jenis_unit']:false;
                                                        $total_result = $this->MkCommon->getChargeTotal( $total_price_unit, $total_tarif_detail, $jenis_tarif_detail, $is_charge_detail );
                                                        $total_price_unit = !empty($total_result['total_tarif'])?$total_result['total_tarif']:0;
                                                        $additional_charge += !empty($total_result['additional_charge'])?$total_result['additional_charge']:0;

                                                        $dataRevenue['RevenueDetail']['city_id'][$idx] = $to_city_id_detail;
                                                        $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$idx] = $tarif_angkutan_id;
                                                        $dataRevenue['RevenueDetail']['tarif_angkutan_type'][$idx] = $tarif_angkutan_type;
                                                        $dataRevenue['RevenueDetail']['no_do'][$idx] = $no_do_detail;
                                                        $dataRevenue['RevenueDetail']['no_sj'][$idx] = $no_sj_detail;
                                                        $dataRevenue['RevenueDetail']['group_motor_id'][$idx] = $group_motor_id;
                                                        $dataRevenue['RevenueDetail']['qty_unit'][$idx] = $jml_unit_detail;
                                                        $dataRevenue['RevenueDetail']['payment_type'][$idx] = $jenis_tarif_detail;
                                                        $dataRevenue['RevenueDetail']['is_charge'][$idx] = $is_charge_detail;
                                                        $dataRevenue['RevenueDetail']['price_unit'][$idx] = $harga_unit_detail;
                                                        $dataRevenue['RevenueDetail']['total_price_unit'][$idx] = $total_price_unit;
                                                    } else {
                                                        $tarifNotFound = true;
                                                    }

                                                    $idx++;
                                                } else {
                                                    $flag = false;
                                                }
                                                $i++;
                                            }

                                            $dataRevenue['Revenue'] = array(
                                                'branch_id' => $branch_id,
                                                'no_doc' => $no_dokumen,
                                                'transaction_status' => 'unposting',
                                                'date_revenue' => $tanggal_revenue,
                                                'customer_id' => $customer_id,
                                                'truck_id' => $truck_id,
                                                'truck_capacity' => $truck_capacity,
                                                'from_city_id' => $from_city_id,
                                                'to_city_id' => $to_city_id,
                                                'tarif_per_truck' => $tarif_per_truck,
                                                'ppn' => $ppn,
                                                'pph' => $pph,
                                                'revenue_tarif_type' => $jenis_tarif,
                                                'additional_charge' => $additional_charge,
                                                'branch_id' => Configure::read('__Site.config_branch_id'),
                                            );

                                            if( !empty($dataRevenue['RevenueDetail']) && empty($tarifNotFound) ) {
                                                $resultSave = $this->Revenue->saveRevenue(false, false, $dataRevenue, $this);
                                                $statusSave = !empty($resultSave['status'])?$resultSave['status']:false;
                                                $msgSave = !empty($resultSave['msg'])?$resultSave['msg']:false;
                                                $this->MkCommon->setCustomFlash($msgSave, $statusSave);

                                                if( $statusSave == 'success' ) {
                                                    $successfull_row++;
                                                } else {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : %s'), $row_submitted+1, $msgSave) . '<br>';
                                                    $failed_row++;
                                                }
                                            } else {
                                                debug($tarifNotFound);die();
                                                if( !empty($tarifNotFound) ) {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Tarif tidak ditemukan, silahkan buat tarif angkutan terlebih dahulu'), $row_submitted+1) . '<br>';
                                                } else {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal menyimpan Revenue, mohon lengkapi field-field muatan'), $row_submitted+1) . '<br>';
                                                }
                                                $failed_row++;
                                            }
                                        } else {
                                            if( empty($from_city_id) || empty($to_city_id) || empty($customer_id) || empty($truck_capacity) ) {
                                                if( empty($from_city_id) ) {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Kota asal tidak benar'), $row_submitted+1) . '<br>';
                                                } else if( empty($to_city_id) ) {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Kota tujuan tidak benar'), $row_submitted+1) . '<br>';
                                                } else if( empty($customer_id) ) {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Kode Customer tidak benar'), $row_submitted+1) . '<br>';
                                                } else if( empty($truck_capacity) ) {
                                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Nopol Truk tidak benar'), $row_submitted+1) . '<br>';
                                                }
                                            } else {
                                                $error_message .= sprintf(__('Gagal pada baris ke %s : Tarif tidak ditemukan, silahkan buat tarif angkutan terlebih dahulu'), $row_submitted+1) . '<br>';
                                            }
                                            $failed_row++;
                                        }

                                        $row_submitted++;
                                    }
                                }
                            }
                        }
                    }

                    if(!empty($successfull_row)) {
                        $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted);
                        $this->MkCommon->setCustomFlash(__($message_import1), 'success');
                    }
                    
                    if(!empty($error_message)) {
                        $this->MkCommon->setCustomFlash(__($error_message), 'error');
                    }
                    $this->redirect(array('action'=>'import'));
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array(
                        'action'=>'import'
                    ));
                }
            }
        }
    }
}