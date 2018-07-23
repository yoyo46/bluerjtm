<?php
App::uses('AppController', 'Controller');
class RevenuesController extends AppController {
    public $uses = array(
        'Ttuj'
    );

    public $components = array(
        'RjRevenue'
    );

    public $helper = array(
        'PhpExcel', 'Revenue', 'Ttuj',
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
            $result = $this->MkCommon->processFilter($data);

            if(!empty($id)){
                array_push($params, $id);
            }
            if(!empty($data_action)){
                array_push($params, $data_action);
            }

            $params = array_merge($params, $result);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    public function ttuj() {
        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', 'ttuj');
        $this->set('sub_module_title', __('TTUJ'));
        $this->set('label_tgl', __('Tgl Berangkat'));

        $options = array(
            'conditions' => array(),
            'order'=> array(
                'Ttuj.status' => 'DESC',
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        );
        $refine = $this->MkCommon->filterEmptyField( $this->params, 'named' );
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Ttuj->_callRefineParams($params, $options);
        $options['conditions'] = $this->RjRevenue->_callRefineStatusTTUJ($refine, $options['conditions']);

        $this->paginate = $this->Ttuj->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $ttujs = $this->paginate('Ttuj');

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $ttuj) {
                $id = $this->MkCommon->filterEmptyField( $ttuj, 'Ttuj', 'id' );
                $ttuj = $this->Ttuj->TtujPaymentDetail->TtujPayment->_callTtujPaid($ttuj, $id);
                $ttuj = $this->Ttuj->Revenue->getPaid( $ttuj, $id );

                $ttujs[$key] = $this->Ttuj->SuratJalanDetail->getMergeFirst( $ttuj, $id, 'SuratJalanDetail.ttuj_id', array(
                    'conditions' => array(
                        'SuratJalan.status' => 1,
                        'SuratJalan.is_canceled' => 0,
                    ),
                    'contain' => array(
                        'SuratJalan',
                    ),
                    'order' => array(
                        'SuratJalan.tgl_surat_jalan' => 'DESC',
                        'SuratJalan.id' => 'DESC',
                    ),
                ));
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'ttujs', 'customers'
        ));
    }

    function ttuj_add( $data_action = 'depo' ){
        $module_title = sprintf(__('Tambah TTUJ - %s'), strtoupper($data_action));
        $this->set('sub_module_title', trim($module_title));
        $this->doTTUJ( $data_action );
    }

    function ttuj_edit( $id ){
        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($ttuj)){
            $demo = Configure::read('__Site.Demo.Version');
            $is_draft = $this->MkCommon->filterEmptyField( $ttuj, 'Ttuj', 'is_draft' );
            $uang_jalan_id = $this->MkCommon->filterEmptyField( $ttuj, 'Ttuj', 'uang_jalan_id' );
            $ttuj = $this->Ttuj->UangJalan->getMerge($ttuj, $uang_jalan_id);

            if( empty($is_draft) ) {
                $allowEdit = $this->MkCommon->checkAllowFunction($this->params);

                if( empty($allowEdit) ) {
                    $this->redirect($this->referer());
                }
            }

            $ttuj = $this->Ttuj->getMergeContain( $ttuj, $id );
            $ttuj = $this->Ttuj->Revenue->getPaid( $ttuj, $id );
            $ttuj = $this->Ttuj->TtujPaymentDetail->TtujPayment->_callTtujPaid($ttuj, $id);
            $data_action = false;

            if( !empty($demo) ) {
                $data_action = 'demo';
            } else if( !empty($ttuj['Ttuj']['is_retail']) ) {
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

        $flagTarif = false;
        $revenue_tarif_type = $this->MkCommon->filterEmptyField($dataRevenue, 'Revenue', 'revenue_tarif_type');
        $to_city_id = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'to_city_id');

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
                $dataValidate['TtujTipeMotor']['qty'] = !empty($data['TtujTipeMotor']['qty'][$key])?trim($data['TtujTipeMotor']['qty'][$key]):false;
                $city_id = !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:false;

                if( in_array($data_action, array( 'retail', 'demo' )) ) {
                    $dataValidate['TtujTipeMotor']['city_id'] = $city_id;
                }

                $this->Ttuj->TtujTipeMotor->set($dataValidate);

                if( !empty($dataRevenue) ) {
                    if( !empty($tipe_motor_id) ) {
                        $groupMotor = $this->Ttuj->TtujTipeMotor->TipeMotor->getData('first', array(
                            'conditions' => array(
                                'TipeMotor.id' => $tipe_motor_id,
                                'TipeMotor.status' => 1,
                            ),
                        ));

                        if( !empty($groupMotor['GroupMotor']['id']) ) {
                            $group_motor_id = $groupMotor['GroupMotor']['id'];
                        }
                    }

                    if( in_array($data_action, array( 'retail', 'demo' )) ) {
                        $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $dataValidate['TtujTipeMotor']['city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity'], $group_motor_id);
                    } else {
                        $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity'], $group_motor_id);
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
                    } else {
                        $priceUnit = 0;
                        $jenis_unit = false;
                    }
                    
                    $qtyMuatan = !empty($dataValidate['TtujTipeMotor']['qty'])?trim($dataValidate['TtujTipeMotor']['qty']):0;

                    if( $revenue_tarif_type == 'per_truck' ) {
                        if( empty($flagTarif) && $to_city_id == $city_id ) {
                            $flagTarif = true;
                            $is_charge = 1;
                            $total_price_unit = $priceUnit;
                        } else {
                            $is_charge = 0;
                            $total_price_unit = 0;
                        }
                    } else {
                        $is_charge = 1;
                        $total_price_unit = $qtyMuatan * $priceUnit;
                    }

                    $totalTarif += $total_price_unit;

                    $dataRevenue['RevenueDetail'][] = array(
                        'RevenueDetail' => array(
                            'is_charge' => $is_charge,
                            'group_motor_id' => $group_motor_id,
                            'qty_unit' => $qtyMuatan,
                            'city_id' => !empty($data['TtujTipeMotor']['city_id'][$key])?$data['TtujTipeMotor']['city_id'][$key]:$data['Ttuj']['to_city_id'],
                            'price_unit' => $priceUnit,
                            'payment_type' => $jenis_unit,
                            'tarif_angkutan_id' => $tarif_angkutan_id,
                            'tarif_angkutan_type' => $tarif_angkutan_type,
                            'from_ttuj' => 1,
                            'total_price_unit' => $total_price_unit,
                        ),
                    );
                }

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

            if( !empty($dataRevenue) && !empty($ttuj_id) ) {
                $dataRevenue['Revenue']['total_without_tax'] = $totalTarif;

                if( !empty($dataRevenue['Revenue']['pph']) ){
                    $pph = $totalTarif * ($dataRevenue['Revenue']['pph'] / 100);
                }
                if( !empty($dataRevenue['Revenue']['ppn']) ){
                    $ppn = $totalTarif * ($dataRevenue['Revenue']['ppn'] / 100);
                    $totalTarif += $ppn;
                }

                $dataRevenue['Revenue']['total'] = $totalTarif;

                $this->Ttuj->Revenue->RevenueDetail->validator()->remove('price_unit');
                $this->Ttuj->Revenue->RevenueDetail->validator()->remove('tarif_angkutan_type');
                $this->Ttuj->Revenue->RevenueDetail->validator()->remove('tarif_angkutan_id');
                $flag = $this->Ttuj->Revenue->saveAll($dataRevenue, array(
                    'validate' => 'only',
                ));


                if( !empty($flag) ) {
                    if( !empty($revenue_id) ) {
                        $this->Ttuj->Revenue->RevenueDetail->updateAll(array(
                            'RevenueDetail.status' => 0
                        ), array(
                            'RevenueDetail.revenue_id' => $revenue_id,
                        ));
                    }

                    $this->Ttuj->Revenue->saveAll($dataRevenue);
                    $revenue_id = $this->Ttuj->Revenue->id;
                    
                    if( !empty($ttuj_id) ) {
                        $this->Ttuj->updateAll( array(
                            'Ttuj.is_revenue' => 1,
                        ), array(
                            'Ttuj.id' => $ttuj_id,
                        ));
                    }

                    $this->Ttuj->Revenue->_callSetJournal($revenue_id, $dataRevenue);
                    $this->Log->logActivity( sprintf(__('Berhasil mengubah Revenue #%s dari TTUJ #%s'), $revenue_id, $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $revenue_id, 'revenue_ttuj_edit' );
                } else {
                    $this->Log->logActivity( sprintf(__('Gagal menambah Revenue dari TTUJ #%s'), $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $revenue_id, 'revenue_ttuj_add' );
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
                $dataValidate['TtujPerlengkapan']['qty'] = trim($qty);
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

        $paramController = $this->params['controller'];
        $paramAction = $this->params['action'];
        $is_draft = isset($data_local['Ttuj']['is_draft'])?$data_local['Ttuj']['is_draft']:true;
        $ttuj_truck_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'truck_id');

        $current_branch_id = Configure::read('__Site.config_branch_id');
        $_allowModule = Configure::read('__Site.config_allow_module');
        $group_id = Configure::read('__Site.config_group_id');

        $allowUpdate = false;
        $allowEditTtujBranch = false;
        $allowClosingTtuj = false;

        if( !empty($_allowModule[$current_branch_id][$paramController]['action']) ) {
            $allowAction = $_allowModule[$current_branch_id][$paramController]['action'];

            if( in_array('ttuj_edit_branch', $allowAction) ) {
                $allowEditTtujBranch = true;
            }
            if( in_array('closing_ttuj', $allowAction) ) {
                $allowClosingTtuj = true;
            }
        }
        if( $group_id == 1 ) {
            $allowEditTtujBranch = true;
            $allowClosingTtuj = true;
        }

        $is_plant = Configure::read('__Site.config_branch_plant');
        $plantCityId = Configure::read('__Site.Branch.Plant.id');

        if( !empty($this->request->data) ) {
            $is_draft = true;
            $allowUpdate = true;
        }

        if( !empty($this->request->data) && $is_draft ){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'Ttuj' => array(
                        'ttuj_date',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'Ttuj', 'ttuj_date');

            $ttuj_date = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'ttuj_date');
            $is_draft = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'is_draft');
            $redirectUrl = array(
                'controller' => 'revenues',
                'action' => 'ttuj_add',
                $data_action,
                'admin' => false,
            );

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

            $customer = $this->Ttuj->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $customer_id,
                ),
            ), true, array(
                'branch' => false,
            ));
            $conditionsTruck = array(
                'Truck.id' => $truck_id,
                'OR' => array(
                    'Truck.id' => $ttuj_truck_id,
                ),
            );

            if( !empty($plantCityId) ) {
                $conditionsTruck['OR']['Truck.branch_id'] = $plantCityId;
            } else {
                $conditionsTruck['OR']['Truck.branch_id'] = Configure::read('__Site.config_branch_id');
            }

            $truck = $this->Ttuj->Truck->getData('first', array(
                'conditions' => $conditionsTruck,
            ), true, array(
                'branch' => false,
            ));
            $capacity = $this->MkCommon->filterEmptyField($truck, 'Truck', 'capacity', 0);

            if( !empty($truck) ) {
                $company_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'company_id');
                $truck = $this->Ttuj->Truck->Company->getMerge($truck, $company_id);
            }

            $this->Ttuj->UangJalan->virtualFields['order_by_branch'] = 'CASE WHEN UangJalan.branch_id = '.$current_branch_id.' THEN 1 ELSE 0 END';
            $uangJalan = $this->Ttuj->UangJalan->getData('first', array(
                'conditions' => array(
                    'UangJalan.from_city_id' => $from_city_id,
                    'UangJalan.to_city_id' => $to_city_id,
                    'UangJalan.capacity' => $capacity,
                ),
                'order' => array(
                    'UangJalan.order_by_branch',
                ),
            ), true, array(
                'branch' => false,
            ));

            if( !empty($uangJalan) ) {
                $uangJalan = $this->City->getMerge($uangJalan, $from_city_id, 'FromCity');
                $uangJalan = $this->City->getMerge($uangJalan, $to_city_id, 'ToCity');
            }

            $is_rjtm = $this->MkCommon->filterEmptyField($truck, 'Company', 'is_rjtm');
            $nopol = $this->MkCommon->filterEmptyField($truck, 'Truck', 'nopol');

            $from_city_name = $this->MkCommon->filterEmptyField($uangJalan, 'FromCity', 'name');
            $to_city_name = $this->MkCommon->filterEmptyField($uangJalan, 'ToCity', 'name');
            $data = $this->Ttuj->getMergeList($data, array(
                'contain' => array(
                    'DriverPengganti' => array(
                        'uses' => 'Driver',
                        'primaryKey' => 'id',
                        'foreignKey' => 'driver_pengganti_id',
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                    'Driver' => array(
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));

            $driver_name = $this->MkCommon->_callGetDriver($data);
            $data['Ttuj']['driver_name'] = $driver_name;
            $data['Ttuj']['nopol'] = $nopol;
            $data['Ttuj']['from_city_name'] = $from_city_name;
            $data['Ttuj']['to_city_name'] = $to_city_name;

            $data['Ttuj']['customer_name'] = !empty($customer['Customer']['customer_name_code'])?$customer['Customer']['customer_name_code']:'';
            $data['Ttuj']['uang_jalan_id'] = !empty($uangJalan['UangJalan']['id'])?$uangJalan['UangJalan']['id']:false;
            $data['Ttuj']['commission'] = !empty($data['Ttuj']['commission'])?$this->MkCommon->convertPriceToString($data['Ttuj']['commission'], 0):0;
            $data['Ttuj']['commission_extra'] = $this->MkCommon->convertPriceToString($data['Ttuj']['commission_extra'], 0);
            $data['Ttuj']['commission_per_unit'] = !empty($data['Ttuj']['commission_per_unit'])?$data['Ttuj']['commission_per_unit']:0;
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
            $data['Ttuj']['is_rjtm'] = $is_rjtm;

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

            if( !empty($data['Ttuj']['completed_date']) ) {
                $data['Ttuj']['completed_date'] = $this->MkCommon->getDate($data['Ttuj']['completed_date']);
            }

            if( in_array($data_action, array( 'retail', 'demo' )) ) {
                $data['Ttuj']['is_retail'] = 1;
                $data['Ttuj']['allow_date_ttuj'] = true;

                $data = $this->RjRevenue->_callDataTtujLanjutan($data);

                $this->Ttuj->validator()->remove('tgljam_tiba', 'notempty');
                $this->Ttuj->validator()->remove('tgljam_bongkaran', 'notempty');
                $this->Ttuj->validator()->remove('tgljam_balik', 'notempty');
                $this->Ttuj->validator()->remove('tgljam_pool', 'notempty');
            }

            $this->Ttuj->set($data);
            $dataRevenue = array();

            if($this->Ttuj->validates($data)){
                if( !empty($data['TtujTipeMotor']['tipe_motor_id']) ) {
                    $dataTtujTipeMotor = $data['TtujTipeMotor']['tipe_motor_id'];

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
                            // if( empty($is_draft) && empty($data_local['Ttuj']['is_revenue']) ) {
                            //     $data['Ttuj']['is_revenue'] = 1;
                            // }

                            if($this->Ttuj->save($data)){
                                $tarifDefault = false;
                                $revenue_id = false;
                                $document_id = $this->Ttuj->id;

                                if( !empty($is_rjtm) && empty($is_draft) ) {
                                    $this->User->Journal->deleteJournal($document_id, array(
                                        'commission',
                                        'uang_jalan',
                                        'uang_kuli_muat',
                                        'uang_kuli_bongkar',
                                        'asdp',
                                        'uang_kawal',
                                        'uang_keamanan',
                                    ));

                                    if ( $allowUpdate ) {
                                        if( !empty($data['Ttuj']['commission']) ) {
                                            $commissionJournal = $data['Ttuj']['commission'];
                                            $titleJournalKomisi = sprintf(__('Komisi untuk supir %s'), $driver_name);

                                            if( !empty($data['Ttuj']['commission_extra']) ) {
                                                $commissionJournal += $data['Ttuj']['commission_extra'];
                                            }

                                            $this->User->Journal->setJournal($commissionJournal, array(
                                                'credit' => 'commission_coa_credit_id',
                                                'debit' => 'commission_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalKomisi,
                                                'document_no' => $no_ttuj,
                                                'type' => 'commission',
                                            ));
                                        }

                                        if( !empty($data['Ttuj']['uang_jalan_1']) ) {
                                            $uangJalanJournal = $data['Ttuj']['uang_jalan_1'];
                                            $titleJournalUj = sprintf(__('Biaya uang jalan %s tujuan %s'), $nopol, $to_city_name);

                                            if( !empty($data['Ttuj']['uang_jalan_2']) ) {
                                                $uangJalanJournal += $data['Ttuj']['uang_jalan_2'];
                                            }

                                            if( !empty($data['Ttuj']['uang_jalan_extra']) ) {
                                                $uangJalanJournal += $data['Ttuj']['uang_jalan_extra'];
                                            }

                                            $this->User->Journal->setJournal($uangJalanJournal, array(
                                                'credit' => 'uang_jalan_coa_credit_id',
                                                'debit' => 'uang_jalan_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalUj,
                                                'document_no' => $no_ttuj,
                                                'type' => 'uang_jalan',
                                            ));
                                        }

                                        if( !empty($data['Ttuj']['uang_kuli_muat']) ) {
                                            $biaya_uang_kuli_muat = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'uang_kuli_muat', 0);
                                            $titleJournalKuliMuat = sprintf(__('Biaya kuli muat %s tujuan %s'), $nopol, $to_city_name);

                                            $this->User->Journal->setJournal($biaya_uang_kuli_muat, array(
                                                'credit' => 'uang_kuli_muat_coa_credit_id',
                                                'debit' => 'uang_kuli_muat_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalKuliMuat,
                                                'document_no' => $no_ttuj,
                                                'type' => 'uang_kuli_muat',
                                            ));
                                        }

                                        if( !empty($data['Ttuj']['uang_kuli_bongkar']) ) {
                                            $biaya_uang_kuli_bongkar = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'uang_kuli_bongkar', 0);
                                            $titleJournalKuliBongkar = sprintf(__('Biaya kuli bongkar %s tujuan %s'), $nopol, $to_city_name);

                                            $this->User->Journal->setJournal($biaya_uang_kuli_bongkar, array(
                                                'credit' => 'uang_kuli_bongkar_coa_credit_id',
                                                'debit' => 'uang_kuli_bongkar_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalKuliBongkar,
                                                'document_no' => $no_ttuj,
                                                'type' => 'uang_kuli_bongkar',
                                            ));
                                        }

                                        if( !empty($data['Ttuj']['asdp']) ) {
                                            $asdp_uang_jalan = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'asdp', 0);
                                            $titleJournalAsdp = sprintf(__('Biaya penyebrangan %s tujuan %s'), $nopol, $to_city_name);

                                            $this->User->Journal->setJournal($asdp_uang_jalan, array(
                                                'credit' => 'asdp_coa_credit_id',
                                                'debit' => 'asdp_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalAsdp,
                                                'document_no' => $no_ttuj,
                                                'type' => 'asdp',
                                            ));
                                        }

                                        if( !empty($data['Ttuj']['uang_kawal']) ) {
                                            $biaya_uang_kawal = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'uang_kawal', 0);
                                            $titleJournalUangKawal = sprintf(__('Biaya uang kawal %s tujuan %s'), $nopol, $to_city_name);

                                            $this->User->Journal->setJournal($biaya_uang_kawal, array(
                                                'credit' => 'uang_kawal_coa_credit_id',
                                                'debit' => 'uang_kawal_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalUangKawal,
                                                'document_no' => $no_ttuj,
                                                'type' => 'uang_kawal',
                                            ));
                                        }

                                        if( !empty($data['Ttuj']['uang_keamanan']) ) {
                                            $biaya_uang_keamanan = $this->MkCommon->filterEmptyField($data, 'Ttuj', 'uang_keamanan', 0);
                                            $titleJournalUangKeamanan = sprintf(__('Biaya uang keamanan %s tujuan %s'), $nopol, $to_city_name);

                                            $this->User->Journal->setJournal($biaya_uang_keamanan, array(
                                                'credit' => 'uang_keamanan_coa_credit_id',
                                                'debit' => 'uang_keamanan_coa_debit_id',
                                            ), array(
                                                'date' => $ttuj_date,
                                                'document_id' => $document_id,
                                                'truck_id' => $truck_id,
                                                'nopol' => $nopol,
                                                'title' => $titleJournalUangKeamanan,
                                                'document_no' => $no_ttuj,
                                                'type' => 'uang_keamanan',
                                            ));
                                        }
                                    }
                                }

                                if( empty($is_draft) ) {
                                    $revenue = $this->Ttuj->Revenue->getData('first', array(
                                        'conditions' => array(
                                            'Revenue.ttuj_id' => $document_id,
                                        ),
                                    ));
                                    $transaction_status = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'transaction_status');

                                    if( $transaction_status != 'posting' ) {
                                        $revenue_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'id');
                                        $tarifDefault = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->findTarif($data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $data['Ttuj']['truck_capacity']);

                                        $dataRevenue['Revenue'] = array(
                                            'id' => $revenue_id,
                                            'ttuj_id' => $document_id,
                                            'truck_id' => $truck_id,
                                            'date_revenue' => $data['Ttuj']['ttuj_date'],
                                            'customer_id' => $data['Ttuj']['customer_id'],
                                            'revenue_tarif_type' => !empty($tarifDefault['jenis_unit'])?$tarifDefault['jenis_unit']:'per_unit',
                                            'from_city_id' => $from_city_id,
                                            'to_city_id' => $to_city_id,
                                            'branch_id' => $current_branch_id,
                                        );
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

                                $this->redirect($redirectUrl);
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
                        $uangKuli = $this->Ttuj->Customer->UangKuli->getUangKuli( $data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id'], $data['Ttuj']['customer_id'], $truck['Truck']['capacity'] );
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
                            $totalMuatanExtra = 0;
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
                                        $ttujTipeMotor = $this->MkCommon->filterEmptyField($data, 'TtujTipeMotor', 'tipe_motor_id');
                                        $tipe_motor_id = !empty($ttujTipeMotor[$key])?$ttujTipeMotor[$key]:false;
                                        $group_motor_id = 0;

                                        $groupMotor = $this->Ttuj->TtujTipeMotor->TipeMotor->find('first', array(
                                            'conditions' => array(
                                                'TipeMotor.id' => $tipe_motor_id,
                                                'TipeMotor.status' => 1,
                                            ),
                                        ));
                                        if( !empty($groupMotor) ) {
                                            $group_motor_id = $groupMotor['TipeMotor']['group_motor_id'];
                                        }

                                        $converterUJExtra = $this->Ttuj->TtujTipeMotor->TipeMotor->GroupMotor->getData('first', array(
                                            'conditions' => array(
                                                'GroupMotor.id' => $group_motor_id,
                                            ),
                                            'contain' => false,
                                        ), true, array(
                                            'converter' => true,
                                        ));
                                        $qtyConverterUJExtra = $this->MkCommon->filterEmptyField($converterUJExtra, 'GroupMotor', 'converter');

                                        $totalMuatan += $qty;

                                        if( !empty($qtyConverterUJExtra) ) {
                                            $totalMuatanExtra += $qty * $qtyConverterUJExtra;
                                        } else {
                                            $totalMuatanExtra = $totalMuatan;
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

                            if( !empty($uangJalan['UangJalan']['uang_jalan_extra']) ) {
                                $uangJalanMinCapacity = $this->MkCommon->filterEmptyField($uangJalan, 'UangJalan', 'min_capacity');
                                $uangJalanExtraPerUnit = $this->MkCommon->filterEmptyField($uangJalan, 'UangJalan', 'uang_jalan_extra_per_unit');

                                if( $totalMuatanExtra > $uangJalanMinCapacity ) {
                                    if( !empty($uangJalanExtraPerUnit) ) {
                                        $capacityCost = $totalMuatanExtra - $uangJalanMinCapacity;
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
                                $truckInfo = $this->Ttuj->Truck->getInfoTruck($data['Ttuj']['truck_id'], $plantCityId);
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
                    $uangKuli = $this->Ttuj->Customer->UangKuli->getUangKuli( $data_local['Ttuj']['from_city_id'], $data_local['Ttuj']['to_city_id'], $data_local['Ttuj']['customer_id'], $data_local['Ttuj']['truck_capacity'] );
                }

                $data_local = $this->RjRevenue->_callShowTglTtuj($data_local);

                if( !empty($data_local['Ttuj']['completed_date']) ) {
                    $data_local['Ttuj']['completed_date'] = $this->MkCommon->getDate($data_local['Ttuj']['completed_date'], true);
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

            if( !empty($this->request->data['Ttuj']['from_city_id']) ) {
                $toCities = $this->Ttuj->UangJalan->getKotaTujuan($this->request->data['Ttuj']['from_city_id']);
            }
        }

        $converterUjs = $this->Ttuj->TtujTipeMotor->TipeMotor->GroupMotor->getData('all', array(
            'contain' => false,
        ), true, array(
            'converter' => true,
        ));

        $customer_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'customer_id');
        $from_city_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'from_city_id');
        $customerConditions = array();

        if( in_array($data_action, array( 'retail', 'demo' )) ) {
            $tmpCities = $this->City->getData('list');

            if( $data_action == 'retail' ) {
                $customerConditions['Customer.customer_type_id'] = 1;
            }
        } else {
            $customerConditions['Customer.customer_type_id'] = 2;
        }

        $customers = $this->Ttuj->Customer->getInclude($customerConditions, $customer_id);
        $fromCities = $this->Ttuj->UangJalan->getKotaAsal( $from_city_id );

        $ttuj_truck_id = !empty($data_local['Ttuj']['truck_id'])?$data_local['Ttuj']['truck_id']:false;
        $ttuj_truck_nopol = !empty($data_local['Ttuj']['nopol'])?$data_local['Ttuj']['nopol']:false;
        $trucks = $this->Ttuj->Truck->getListTruck($ttuj_truck_id, false, $ttuj_truck_nopol, $plantCityId);

        $driver_pengganti_id = !empty($data_local['Ttuj']['driver_pengganti_id'])?$data_local['Ttuj']['driver_pengganti_id']:false;
        $driverPenggantis = $this->Ttuj->Truck->Driver->getListDriverPengganti($driver_pengganti_id);

        $perlengkapans = $this->Ttuj->Truck->TruckPerlengkapan->Perlengkapan->getData('list', array(
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
        $tipeMotorTemps = $this->Ttuj->TtujTipeMotor->TipeMotor->getData('all');

        if( !empty($tipeMotorTemps) ) {
            foreach ($tipeMotorTemps as $key => $tipeMotorTemp) {
                $tipe_motor_id = $this->MkCommon->filterEmptyField($tipeMotorTemp, 'TipeMotor', 'id');
                $code_name = $this->MkCommon->filterEmptyField($tipeMotorTemp, 'TipeMotor', 'code_name');
                $group_motor_id = $this->MkCommon->filterEmptyField($tipeMotorTemp, 'GroupMotor', 'id');

                $tipeMotors[$tipe_motor_id] = $code_name;
                $groupTipeMotors[$tipe_motor_id] = $group_motor_id;
            }
        }

        $colors = $this->Ttuj->TtujTipeMotor->ColorMotor->getData('list', array(
            'fields' => array(
                'ColorMotor.id', 'ColorMotor.name',
            ),
        ));
        $branches = Configure::read('__Site.config_allow_branchs');
        $this->MkCommon->_layout_file('select');

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->params['controller'], array( 'ttuj_add', 'ttuj_edit', 'ttuj_toggle', 'truk_tiba_add', 'ttuj_lanjutan_edit', 'bongkaran_add', 'balik_add', 'pool_add' ), $id);
        }

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', 'ttuj');
        $this->set(compact(
            'trucks', 'customers', 'driverPenggantis',
            'fromCities', 'toCities', 'uangJalan',
            'tipeMotors', 'perlengkapans',
            'truckInfo', 'data_local', 'data_action',
            'colors', 'tipeMotorTemps',
            'groupTipeMotors', 'uangKuli',
            'id', 'tmpCities', 'branches',
            'allowEditTtujBranch', 'converterUjs',
            'allowClosingTtuj'
        ));
        $this->render('ttuj_form');
    }

    function ttuj_toggle( $id, $action_type = 'status' ){
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
            $this->MkCommon->_callAllowClosing($locale, 'Ttuj', 'ttuj_date');
            $locale = $this->Ttuj->getMergeList($locale, array(
                'contain' => array(
                    'DriverPengganti' => array(
                        'uses' => 'Driver',
                        'primaryKey' => 'id',
                        'foreignKey' => 'driver_pengganti_id',
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                    'Driver' => array(
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));
            $locale = $this->Ttuj->Revenue->getPaid( $locale, $id );
            $allowSave = Common::_callTtujPaid($locale);

            if( !empty($allowSave) ) {
                $driver_name = $this->MkCommon->_callGetDriver($locale);
                $document_no = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'no_ttuj');
                $truck_id = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'truck_id');
                $to_city_name = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'to_city_name');
                $nopol = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'nopol');
                $ttuj_date = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'ttuj_date');

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
                    if( $deleteJournal && empty($locale['Ttuj']['is_draft']) ) {
                        if( !empty($locale['Ttuj']['commission']) ) {
                            $commissionJournal = $locale['Ttuj']['commission'];
                            $titleJournalKomisi = sprintf(__('<i>Pembatalan</i> komisi untuk supir %s'), $driver_name);

                            if( !empty($locale['Ttuj']['commission_extra']) ) {
                                $commissionJournal += $locale['Ttuj']['commission_extra'];
                            }

                            $this->User->Journal->setJournal($commissionJournal, array(
                                'credit' => 'commission_coa_debit_id',
                                'debit' => 'commission_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalKomisi,
                                'document_no' => $document_no,
                                'type' => 'commission_void',
                            ));
                        }

                        if( !empty($locale['Ttuj']['uang_jalan_1']) ) {
                            $uangJalanJournal = $locale['Ttuj']['uang_jalan_1'];
                            $titleJournalUj = sprintf(__('<i>Pembatalan</i> biaya uang jalan %s tujuan %s'), $nopol, $to_city_name);

                            if( !empty($locale['Ttuj']['uang_jalan_2']) ) {
                                $uangJalanJournal += $locale['Ttuj']['uang_jalan_2'];
                            }

                            if( !empty($locale['Ttuj']['uang_jalan_extra']) ) {
                                $uangJalanJournal += $locale['Ttuj']['uang_jalan_extra'];
                            }

                            $this->User->Journal->setJournal($uangJalanJournal, array(
                                'credit' => 'uang_jalan_coa_debit_id',
                                'debit' => 'uang_jalan_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalUj,
                                'document_no' => $document_no,
                                'type' => 'uang_jalan_void',
                            ));
                        }

                        if( !empty($locale['Ttuj']['uang_kuli_muat']) ) {
                            $titleJournalKuliMuat = sprintf(__('<i>Pembatalan</i> biaya kuli muat %s tujuan %s'), $nopol, $to_city_name);
                            $uangKuli = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'uang_kuli_muat');

                            $this->User->Journal->setJournal($uangKuli, array(
                                'credit' => 'uang_kuli_muat_coa_debit_id',
                                'debit' => 'uang_kuli_muat_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalKuliMuat,
                                'document_no' => $document_no,
                                'type' => 'uang_kuli_muat_void',
                            ));
                        }

                        if( !empty($locale['Ttuj']['uang_kuli_bongkar']) ) {
                            $titleJournalKuliBongkar = sprintf(__('<i>Pembatalan</i> biaya kuli bongkar %s tujuan %s'), $nopol, $to_city_name);
                            $uangKuliBongkar = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'uang_kuli_bongkar');

                            $this->User->Journal->setJournal($uangKuliBongkar, array(
                                'credit' => 'uang_kuli_bongkar_coa_debit_id',
                                'debit' => 'uang_kuli_bongkar_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalKuliBongkar,
                                'document_no' => $document_no,
                                'type' => 'uang_kuli_bongkar_void',
                            ));
                        }

                        if( !empty($locale['Ttuj']['asdp']) ) {
                            $titleJournalAsdp = sprintf(__('<i>Pembatalan</i> biaya penyebrangan %s tujuan %s'), $nopol, $to_city_name);
                            $asdp = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'asdp');

                            $this->User->Journal->setJournal($asdp, array(
                                'credit' => 'asdp_coa_debit_id',
                                'debit' => 'asdp_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalAsdp,
                                'document_no' => $document_no,
                                'type' => 'asdp_void',
                            ));
                        }

                        if( !empty($locale['Ttuj']['uang_kawal']) ) {
                            $titleJournalUangKawal = sprintf(__('<i>Pembatalan</i> biaya uang kawal %s tujuan %s'), $nopol, $to_city_name);
                            $uangKawal = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'uang_kawal');

                            $this->User->Journal->setJournal($uangKawal, array(
                                'credit' => 'uang_kawal_coa_debit_id',
                                'debit' => 'uang_kawal_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalUangKawal,
                                'document_no' => $document_no,
                                'type' => 'uang_kawal_void',
                            ));
                        }

                        if( !empty($locale['Ttuj']['uang_keamanan']) ) {
                            $titleJournalUangKeamanan = sprintf(__('<i>Pembatalan</i> biaya uang keamanan %s tujuan %s'), $nopol, $to_city_name);
                            $uangKeamanan = $this->MkCommon->filterEmptyField($locale, 'Ttuj', 'uang_keamanan');

                            $this->User->Journal->setJournal($uangKeamanan, array(
                                'credit' => 'uang_keamanan_coa_debit_id',
                                'debit' => 'uang_keamanan_coa_credit_id',
                            ), array(
                                'date' => $ttuj_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $titleJournalUangKeamanan,
                                'document_no' => $document_no,
                                'type' => 'uang_keamanan_void',
                            ));
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
        }else{
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function truk_tiba() {
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
            if(!empty($refine['nodoc'])){
                $nottuj = urldecode($refine['nodoc']);
                $this->request->data['Ttuj']['nodoc'] = $nottuj;
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

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set('ttujs', $ttujs);
        $this->set('customers', $customers);
        $this->render('ttuj');
    }

    public function truk_tiba_add() {
        $this->set('active_menu', 'truk_tiba');
        $this->doTTUJLanjutan();
    }

    public function ttuj_lanjutan_edit( $action_type = 'truk_tiba', $id = false ) {
        $this->set('active_menu', 'truk_tiba');

        $conditions = array(
            'Ttuj.id' => $id,
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
            $this->MkCommon->getLogs($this->params['controller'], array( 'ttuj_add', 'ttuj_edit', 'ttuj_toggle', 'truk_tiba_add', 'ttuj_lanjutan_edit', 'bongkaran_add', 'balik_add', 'pool_add' ), $id);

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
        $this->loadModel('ColorMotor');

        $data_action = false;

        if( !empty($this->params['named']['no_ttuj']) ) {
            $no_ttuj_id = $this->params['named']['no_ttuj'];
            $data_local = $this->Ttuj->_callDataTtujConditions($no_ttuj_id, $action_type);

            if( !empty($data_local) ) {
                $data_local = $this->Ttuj->getMergeContain( $data_local, $no_ttuj_id );

                if( !empty($data_local['Ttuj']['is_retail']) ) {
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
                    $dataTiba['Ttuj']['is_draft'] = 0;
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
        $perlengkapans = $this->Ttuj->Truck->TruckPerlengkapan->Perlengkapan->getData('list', array(
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
        $this->loadModel('TipeMotor');
        $this->loadModel('ColorMotor');

        $this->set('module_title', __('TTUJ'));
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

        $conditions = array(
            'Ttuj.id' => $ttuj_id,
            // 'Ttuj.status' => 1,
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
            
            case 'truk_tiba':
                $conditions['Ttuj.is_arrive'] = 1;

                $module_title = __('Info Truk Tiba');
                $this->set('active_menu', 'truk_tiba');
                break;
            
            default:
                $module_title = __('Info TTUJ');
                $this->set('active_menu', 'ttuj');
                break;
        }

        $data_action = false;
        $data_local = $this->Ttuj->getData('first', array(
            'conditions' => $conditions,
        ), true, array(
            'branch' => false,
            'status' => 'all',
        ));

        if( !empty($data_local) ){
            $data_local = $this->Ttuj->TtujPaymentDetail->TtujPayment->_callTtujPaid($data_local, $ttuj_id);
            $data_local = $this->Ttuj->getMergeContain( $data_local, $ttuj_id );
            $data_local['Ttuj']['ttuj_date'] = date('d/m/Y', strtotime($data_local['Ttuj']['ttuj_date']));
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);
            $to_city_id = $this->MkCommon->filterEmptyField($data_local, 'Ttuj', 'to_city_id');

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
            $perlengkapans = $this->Ttuj->Truck->TruckPerlengkapan->Perlengkapan->getData('list', array(
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
            $this->MkCommon->getLogs($this->params['controller'], array( 'ttuj_add', 'ttuj_edit', 'ttuj_toggle', 'truk_tiba_add', 'ttuj_lanjutan_edit', 'bongkaran_add', 'balik_add', 'pool_add' ), $ttuj_id);

            $this->set('info_truk', true);
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
            if(!empty($refine['nodoc'])){
                $nottuj = urldecode($refine['nodoc']);
                $this->request->data['Ttuj']['nodoc'] = $nottuj;
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

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->MkCommon->_layout_file('select');

        $this->set('customers', $customers);
        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function bongkaran_add() {
        $this->set('sub_module_title', __('Bongkaran'));
        $this->set('active_menu', 'bongkaran');
        $this->doTTUJLanjutan( 'bongkaran' );
    }

    public function balik() {
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
            if(!empty($refine['nodoc'])){
                $nottuj = urldecode($refine['nodoc']);
                $this->request->data['Ttuj']['nodoc'] = $nottuj;
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

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->MkCommon->_layout_file('select');

        $this->set('customers', $customers);
        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function balik_add() {
        $this->set('sub_module_title', __('Tambah TTUJ Balik'));
        $this->set('active_menu', 'balik');
        $this->doTTUJLanjutan( 'balik' );
    }

    public function pool() {
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
            if(!empty($refine['nodoc'])){
                $nottuj = urldecode($refine['nodoc']);
                $this->request->data['Ttuj']['nodoc'] = $nottuj;
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

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set('ttujs', $ttujs);
        $this->set('customers', $customers);
        $this->render('ttuj');
    }

    public function pool_add() {
        $this->set('sub_module_title', __('TTUJ Sampai Pool'));
        $this->set('active_menu', 'pool');
        $this->doTTUJLanjutan( 'pool' );
    }

    public function ritase_report( $data_type = 'depo' ) {
        $this->loadModel('TruckCustomer');
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
        $defaultConditionsTtuj = array(
            'OR' => array(
                array(
                    'Ttuj.is_pool'=> 1,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='=> $dateFrom,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <=' => $dateTo,
                ),
                array(
                    'Ttuj.completed'=> 1,
                    'DATE_FORMAT(Ttuj.completed_date, \'%Y-%m-%d\') >='=> $dateFrom,
                    'DATE_FORMAT(Ttuj.completed_date, \'%Y-%m-%d\') <=' => $dateTo,
                ),
            ),
        );
        $options = array(
            'conditions' => $conditions,
            'order' => array(
                'CustomerNoType.order_sort' => 'ASC', 
                'Truck.nopol' => 'ASC', 
            ),
            'contain' => array(
                'Truck',
                'CustomerNoType',
            ),
        );

        if( !empty($data_action) ) {
            $trucks = $this->TruckCustomer->getData('all', $options);
        } else {
            $options['limit'] = 20;
            $options = $this->TruckCustomer->getData('paginate', $options);
            $this->paginate = $options;
            $trucks = $this->paginate('TruckCustomer');
        }

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $truck) {
                $branch_id = $this->MkCommon->filterEmptyField($truck, 'TruckCustomer', 'branch_id');
                $conditionCustomers = array(
                    'TruckCustomer.truck_id'=> $truck['Truck']['id'],
                );

                $truck = $this->Ttuj->Truck->Driver->getMerge($truck, $truck['Truck']['driver_id']);
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
            // $defaultConditionsTtuj['Ttuj.is_retail'] = 0;
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

        $companies = $this->Ttuj->Truck->Company->getData('list');

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
                $ttujTipeMotor = $this->Ttuj->TtujTipeMotor->find('first', array(
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

    public function achievement_rit_report( $data_action = false ) {
        $this->loadModel('TtujTipeMotor');
        $this->set('active_menu', 'achievement_rit_report');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $fromMonth = 01;
        $fromYear = date('Y');
        $toMonth = date('m');
        $toYear = date('Y');
        $options = array(
            'conditions' => array(
                'Truck.branch_id' => $allow_branch_id,
            ),
        );


        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Ttuj->Truck->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if( !empty($refine['monthFrom']) && !empty($refine['yearFrom']) ){
                $fromMonth = urldecode($refine['monthFrom']);
                $fromYear = urldecode($refine['yearFrom']);
            }

            if( !empty($refine['monthTo']) && !empty($refine['yearTo']) ){
                $toMonth = urldecode($refine['monthTo']);
                $toYear = urldecode($refine['yearTo']);
            }

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Truck', $options );
        }

        $conditions = array(
            'Ttuj.status'=> 1,
            'Ttuj.is_draft'=> 0,
            'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\') >=' => date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear)),
            'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\') <=' => date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear)),
        );
        $defaultConditionsTtuj = array(
            'OR' => array(
                array(
                    'Ttuj.is_pool'=> 1,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') >='=> date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear)),
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') <=' => date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear)),
                ),
                array(
                    'Ttuj.completed'=> 1,
                    'DATE_FORMAT(Ttuj.completed_date, \'%Y-%m\') >='=> date('Y-m', mktime(0, 0, 0, $fromMonth, 1, $fromYear)),
                    'DATE_FORMAT(Ttuj.completed_date, \'%Y-%m\') <=' => date('Y-m', mktime(0, 0, 0, $toMonth, 1, $toYear)),
                ),
            ),
        );

        if( !empty($data_action) ) {
            $values = $this->Ttuj->Truck->getData('all', $options);
        } else {
            $this->loadModel('Truck');
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $this->Truck->getData('paginate', $options);
            $values = $this->paginate('Truck');
        }

        $cntPencapaian = array();
        $cntUnit = array();
        $targetUnit = array();

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'id');

                $value = $this->Ttuj->Truck->TruckCustomer->getMergeTruckCustomer($value, $truck_id, true);
                $customer_id = $this->MkCommon->filterEmptyField($value, 'TruckCustomer', 'customer_id');
                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);

                $conditionsTtuj = $defaultConditionsTtuj;
                $conditionsTtuj['Ttuj.truck_id'] = $truck_id;

                $this->Ttuj->virtualFields['dt'] = 'CASE WHEN Ttuj.completed = 1 THEN DATE_FORMAT(Ttuj.completed_date, \'%Y-%m\') ELSE DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m\') END';
                $this->Ttuj->virtualFields['cnt'] = 'COUNT(Ttuj.truck_id)';

                $totals = $this->Ttuj->getData('all', array(
                    'conditions' => $conditionsTtuj,
                    'group' => array(
                        'Ttuj.dt'
                    ),
                    // 'fields'=> array(
                    //     'Ttuj.id', 
                    //     'Ttuj.truck_id', 
                    //     'Ttuj.cnt',
                    //     'Ttuj.dt',
                    // ),
                    'order' => array(
                        'dt' => 'ASC',
                    ),
                ), true, array(
                    'branch' => false,
                ));
            // debug($totals);die();

                if( !empty($totals) ) {
                    foreach ($totals as $idx => $total) {
                        $dt = $this->MkCommon->filterEmptyField($total, 'Ttuj', 'dt');
                        $cnt = $this->MkCommon->filterEmptyField($total, 'Ttuj', 'cnt');

                        $cntPencapaian[$truck_id][$dt] = $cnt;
                    }
                }

                $conditions['Ttuj.truck_id'] = $truck_id;

                $this->Ttuj->TtujTipeMotor->virtualFields['dt'] = 'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\')';
                $this->Ttuj->TtujTipeMotor->virtualFields['cnt'] = 'SUM(TtujTipeMotor.qty)';

                $tipeMotors = $this->Ttuj->TtujTipeMotor->find('all', array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Ttuj',
                    ),
                    'group' => array(
                        'TtujTipeMotor.dt'
                    ),
                    'fields'=> array(
                        'Ttuj.id', 
                        'Ttuj.truck_id', 
                        'TtujTipeMotor.cnt',
                        'TtujTipeMotor.dt',
                    ),
                ), false);
                
                if( !empty($tipeMotors) ) {
                    foreach ($tipeMotors as $idx => $total) {
                        $dt = $this->MkCommon->filterEmptyField($total, 'TtujTipeMotor', 'dt');
                        $cnt = $this->MkCommon->filterEmptyField($total, 'TtujTipeMotor', 'cnt');

                        $cntUnit[$truck_id][$dt] = $cnt;
                    }
                }

                $values[$key] = $value;
            }
        }

        $customerTargetUnits = $this->Ttuj->Customer->CustomerTargetUnit->CustomerTargetUnitDetail->find('all', array(
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
        
        if( !empty($customerTargetUnits) ) {
            foreach ($customerTargetUnits as $key => $target) {
                $yearUnit = $this->MkCommon->filterEmptyField($target, 'CustomerTargetUnit', 'year');
                $monthUnit = $this->MkCommon->filterEmptyField($target, 'CustomerTargetUnit', 'month');
                $customer_id = $this->MkCommon->filterEmptyField($target, 'CustomerTargetUnit', 'customer_id');
                $unit = $this->MkCommon->filterEmptyField($target, 'CustomerTargetUnitDetail', 'unit');

                $dt = sprintf('%s-%s', $yearUnit, date('m', mktime(0, 0, 0, $monthUnit, 10)));
                $targetUnit[$customer_id][$dt] = $unit;
            }
        }

        $module_title = __('Laporan Pencapaian Per RIT');

        $this->request->data['Search']['from']['month'] = $fromMonth;
        $this->request->data['Search']['from']['year'] = $fromYear;
        $this->request->data['Search']['to']['month'] = $toMonth;
        $this->request->data['Search']['to']['year'] = $toYear;
        $module_title .= sprintf(' Periode %s %s - %s %s', date('F', mktime(0, 0, 0, $fromMonth, 10)), $fromYear, date('F', mktime(0, 0, 0, $toMonth, 10)), $toYear);
        $totalCnt = $toMonth - $fromMonth;
        $totalYear = $toYear - $fromYear;

        if( !empty($totalYear) && $totalYear > 0 ) {
            $totalYear = 12 * $totalYear;
            $totalCnt += $totalYear;
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('sub_module_title', $module_title);
        $this->set(compact(
            'values', 'data_action', 'totalCnt',
            'fromMonth', 'fromYear', 'cntPencapaian',
            'toYear', 'toMonth', 'customerTargetUnit',
            'targetUnit', 'cntUnit', 'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }

    public function monitoring_truck( $data_action = false ) {
        $this->loadModel('Customer');
        $this->loadModel('TruckCustomer');
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

        $this->Ttuj->Truck->bindModel(array(
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

        $this->paginate = $this->Ttuj->Truck->getData('paginate', array(
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
                    'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\') <=' => $currentMonth,
                    // 'Laka.completed' => 0,
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
                        $value = $this->Ttuj->Truck->Driver->getMerge($value, $change_driver_id, 'DriverPengganti');
                        $change_driver_name = $this->MkCommon->filterEmptyField($value, 'DriverPengganti', 'driver_name', '-');
                    }

                    $TglLaka = $this->MkCommon->customDate($tgl_laka, 'Y-m-d');
                    $lakaCompletedDate = $this->MkCommon->customDate($laka_completed_date, 'Y-m-d', '-');
                    $addClass = 'pool';
                    $urlLaka = array(
                        'controller' => 'lakas',
                        'action' => 'edit',
                        $laka_id,
                    );

                    if( !empty($laka_completed) ) {
                        $end_date = $lakaCompletedDate;
                    } else if( date('Y-m-d') >= $TglLaka ) {
                        $end_date = date('Y-m-d', strtotime("-1 day"));
                    } else {
                        $end_date = $TglLaka;
                    }


                    $icon_laka = $this->MkCommon->filterEmptyField($setting, 'Setting', 'icon_laka');
                    $lakaDate = $this->MkCommon->customDate($tgl_laka, 'd/m/Y');
                    $lakaDateOri = $this->MkCommon->customDate($tgl_laka, 'Y-m-d');
                    $lakaEndDate = $this->MkCommon->customDate($laka_completed_date, 'Y-m-d');
                    $lakaMonth = $this->MkCommon->customDate($tgl_laka, 'm');
                    $lakaDay = $this->MkCommon->customDate($tgl_laka, 'd');
                    $dataCalendar = array(
                        'is_laka' => true,
                        'laka_date' => $lakaDate,
                        'laka_date_ori' => $lakaDateOri,
                        'laka_completed_date' => $lakaCompletedDate,
                        'monitoring_date' => $lakaDateOri,
                        'driver_name' => $driver_name,
                        'driver_pengganti_name' => $change_driver_name,
                        'lokasi_laka' => $lokasi_laka,
                        'truck_condition' => $truck_condition,
                        'title' => __('LAKA'),
                        'iconPopup' => $icon_laka,
                        'color_laka' => '#dd545f',
                        'NoPol' => $nopol,
                        'url' => array(
                            'controller' => 'lakas',
                            'action' => 'edit',
                            $laka_id,
                        ),
                    );
                    $i = 0;

                    if( empty($lakaEndDate) ) {
                        $lakaEndDate = date('Y-m-d');
                    }

                    while (strtotime($TglLaka) <= strtotime($lakaEndDate)) {
                        $currMonth = date('Y-m', strtotime($TglLaka));
                        $currDay = date('d', strtotime($TglLaka));
                        $currMonthly = date('m', strtotime($TglLaka));

                        if( empty($i) ) {
                            $dataCalendar['icon'] = $icon_laka;
                        } else {
                            $dataCalendar['icon'] = false;
                        }
                        
                        $dataLaka['Truck-'.$truck_id][$currMonthly][$currDay][] = $dataCalendar;

                        $TglLaka = date ("Y-m-d", strtotime("+1 day", strtotime($TglLaka)));
                        $i++;
                    }
                }

                $trucks[$key] = $value;
            }
        }

        $ttujs = $this->Ttuj->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Ttuj.tgljam_berangkat' => 'ASC', 
                'Ttuj.tgljam_tiba' => 'ASC', 
                'Ttuj.tgljam_bongkaran' => 'ASC', 
                'Ttuj.tgljam_balik' => 'ASC', 
                'Ttuj.tgljam_pool' => 'ASC', 
                'Ttuj.id' => 'ASC', 
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
            foreach ($ttujs as $key => $value) {
                $inArr = array();
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $nopol = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'nopol');
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');

                $value = $this->Laka->getMergeTtuj($ttuj_id, $value, array(
                    'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m\')' => $currentMonth,
                ));
                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $ttujTipeMotor = $this->Ttuj->TtujTipeMotor->find('first', array(
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
                    'Driver' => $this->MkCommon->filterEmptyField($value, 'Driver', 'driver_name'),
                    'DriverChange' => $this->MkCommon->filterEmptyField($value, 'DriverPengganti', 'driver_name'),
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

                if( !empty($value['Ttuj']['is_pool']) ) {
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
                        'action' => 'info_truk',
                        'ttuj',
                        $value['Ttuj']['id'],
                    );
                }

                if( !empty($value['Laka']['id']) ) {
                    $lakaDate = date('Y-m-d', strtotime($value['Laka']['tgl_laka']));

                    if( !empty($value['Laka']['completed']) ) {
                        $end_date = date('Y-m-d', strtotime($value['Laka']['completed_date']));
                    } else if( date('Y-m-d') >= $lakaDate ) {
                        $end_date = date('Y-m-d', strtotime("-1 day"));
                    } else {
                        $end_date = $lakaDate;
                    }
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
                    'from_date_ori' => $this->MkCommon->customDate($tglBerangkat, 'Y-m-d'),
                    'to_date' => !empty($tglPool)?$this->MkCommon->customDate($tglPool, 'd/m/Y - H:i'):'-',
                    'to_date_ori' => !empty($tglPool)?$this->MkCommon->customDate($tglPool, 'Y-m-d'):false,
                    'url' => $urlTtuj,
                    'monitoring_date' => $this->MkCommon->customDate($tglBerangkat, 'Y-m-d'),
                ));

                if( !empty($tglTiba) ) {
                    $dataTtujCalendar['tglTiba'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_tiba'], 'd/m/Y - H:i');
                    $dataTtujCalendar['tglTibaOri'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_tiba'], 'Y-m-d');
                }
                if( !empty($tglBongkaran) ) {
                    $dataTtujCalendar['tglBongkaran'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_bongkaran'], 'd/m/Y - H:i');
                    $dataTtujCalendar['tglBongkaranOri'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_bongkaran'], 'Y-m-d');
                }
                if( !empty($tglBalik) ) {
                    $dataTtujCalendar['tglBalik'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_balik'], 'd/m/Y - H:i');
                    $dataTtujCalendar['tglBalikOri'] = $this->MkCommon->customDate($value['Ttuj']['tgljam_balik'], 'Y-m-d');
                }

                $dataTtujCalendar['color_pool'] = '#95b3d7';
                $dataTtujCalendar['color_balik'] = '#95b3d7';
                $dataTtujCalendar['color_bongkaran'] = '#fabf8f';
                $dataTtujCalendar['color_tiba'] = '#c4d79b';
                $dataTtujCalendar['color_berangkat'] = '#c4d79b';

                if( !empty($tglPool) && $this->MkCommon->customDate($tglPool, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglPool, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglPool, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Sampai Pool');
                    $dataTtujCalendar['monitoring_date'] = $this->MkCommon->customDate($tglPool, 'Y-m-d');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_pool'])?$setting['Setting']['icon_pool']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglPool, 'm')][$this->MkCommon->customDate($tglPool, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglPool, 'd');
                    $dataRit['Truck-'.$truck_id]['rit'][$this->MkCommon->customDate($tglPool, 'm')][$this->MkCommon->customDate($tglPool, 'd')][] = $tglPool;
                }
                if( !empty($tglBalik) && $this->MkCommon->customDate($tglBalik, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglBalik, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglBalik, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Balik');
                    $dataTtujCalendar['monitoring_date'] = $this->MkCommon->customDate($tglBalik, 'Y-m-d');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_balik'])?$setting['Setting']['icon_balik']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglBalik, 'm')][$this->MkCommon->customDate($tglBalik, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglBalik, 'd');
                }
                if( !empty($tglBongkaran) && $this->MkCommon->customDate($tglBongkaran, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglBongkaran, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglBongkaran, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Bongkaran');
                    $dataTtujCalendar['monitoring_date'] = $this->MkCommon->customDate($tglBongkaran, 'Y-m-d');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_bongkaran'])?$setting['Setting']['icon_bongkaran']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
                    $dataTtuj['Truck-'.$truck_id][$this->MkCommon->customDate($tglBongkaran, 'm')][$this->MkCommon->customDate($tglBongkaran, 'd')][] = $dataTtujCalendar;
                    $differentTtuj = true;
                    $inArr[] = $this->MkCommon->customDate($tglBongkaran, 'd');
                }
                if( !empty($tglTiba) && $this->MkCommon->customDate($tglTiba, 'Y-m') == $currMonth && $this->MkCommon->customDate($tglTiba, 'd') != $this->MkCommon->customDate($tglBerangkat, 'd') && !in_array($this->MkCommon->customDate($tglTiba, 'd'), $inArr) ) {
                    $dataTtujCalendar['title'] = __('Tiba');
                    $dataTtujCalendar['monitoring_date'] = $this->MkCommon->customDate($tglTiba, 'Y-m-d');
                    $dataTtujCalendar['icon'] = !empty($setting['Setting']['icon_tiba'])?$setting['Setting']['icon_tiba']:'';
                    $dataTtujCalendar['iconPopup'] = $dataTtujCalendar['icon'];
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
                        $dataTtujCalendar['color_pool'] = '#95b3d7';
                        $dataTtujCalendar['color_balik'] = '#95b3d7';
                        $dataTtujCalendar['color_bongkaran'] = '#fabf8f';
                        $dataTtujCalendar['color_tiba'] = '#c4d79b';
                        $dataTtujCalendar['color_berangkat'] = '#c4d79b';

                        if( !empty($tglPool) && $this->MkCommon->customDate($tglPool, 'Y-m-d') <= $date ) {
                            $icon = !empty($setting['Setting']['icon_pool'])?$setting['Setting']['icon_pool']:'';
                            $dataRit['Truck-'.$truck_id]['rit'][$currMonthly][$currDay][] = $tglPool;
                        } else if( !empty($tglBalik) && $this->MkCommon->customDate($tglBalik, 'Y-m-d') <= $date ) {
                            $icon = !empty($setting['Setting']['icon_balik'])?$setting['Setting']['icon_balik']:'';
                        } else if( !empty($tglBongkaran) && $this->MkCommon->customDate($tglBongkaran, 'Y-m-d') <= $date ) {
                            $icon = !empty($setting['Setting']['icon_bongkaran'])?$setting['Setting']['icon_bongkaran']:'';
                        } else if( !empty($tglTiba) && $this->MkCommon->customDate($tglTiba, 'Y-m-d') <= $date ) {
                            $icon = !empty($setting['Setting']['icon_tiba'])?$setting['Setting']['icon_tiba']:'';
                        } else {
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
        $companies = $this->Ttuj->Truck->Company->getData('list');

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
        $this->loadModel('City');

        $this->set('active_menu', 'revenues');
        $this->set('sub_module_title', __('Revenue'));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Revenue->_callRefineParams($params, array(
            'contain' => array(
                'Ttuj',
            ),
            'order' => array(
                'Revenue.status' => 'DESC',
                'Revenue.created' => 'DESC',
                'Revenue.id' => 'DESC',
            ),
        ));

        $this->paginate = $this->Revenue->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $revenues = $this->paginate('Revenue');

        if(!empty($revenues)){
            foreach ($revenues as $key => $value) {
                $value = $this->Revenue->InvoiceDetail->getInvoicedRevenue($value, $value['Revenue']['id']);
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'customer_id');

                if( empty($value['Revenue']['ttuj_id']) ) {
                    $from_city_id = !empty($value['Revenue']['from_city_id'])?$value['Revenue']['from_city_id']:false;
                    $to_city_id = !empty($value['Revenue']['to_city_id'])?$value['Revenue']['to_city_id']:false;
                    $truck_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'truck_id');

                    $value = $this->City->getMerge($value, $from_city_id, 'FromCity');
                    $value = $this->City->getMerge($value, $to_city_id, 'ToCity');
                    $value = $this->Ttuj->Truck->getMerge($value, $truck_id);
                }

                if( empty($customer_id) ) {
                    $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                }

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);

                $revenues[$key] = $value;
            }
        }
        
        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $cities = $this->City->getListCities();

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'cities', 'customers', 'revenues'
        ));
    }

    function add( $action_type = false ){
        $module_title = __('Tambah Revenue');
        $this->set('sub_module_title', trim($module_title));
        $this->doRevenue( false, false, $action_type );
    }

    function edit( $id, $action_type = false ){
        $revenue = $this->Ttuj->Revenue->getData('first', array(
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
            $revenue = $this->Ttuj->Revenue->RevenueDetail->getMergeAll( $revenue, $revenue['Revenue']['id'] );
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
        $this->loadModel('City');
        $data_revenue_detail = array();
        $allow_closing = true;
        $data = $this->request->data;

        if(!empty($data)){
            $data = $this->MkCommon->dataConverter($data, array(
                'price' => array(
                    'Revenue' => array(
                        'ppn_total',
                        'pph_total',
                    ),
                ),
                'date' => array(
                    'Revenue' => array(
                        'date_revenue',
                    ),
                ),
            ));
            $this->MkCommon->_callAllowClosing($data, 'Revenue', 'date_revenue');

            $dataTtuj = $this->MkCommon->filterEmptyField($data, 'Ttuj');
            $ttuj_id = $this->MkCommon->filterEmptyField($data, 'Revenue', 'ttuj_id');

            $data['Revenue']['branch_id'] = Configure::read('__Site.config_branch_id');

            if( $action_type == 'manual' ) {
                $data['Revenue']['is_manual'] = 1;

                if( empty($ttuj_id) ) {
                    $data['Revenue']['ttuj_id'] = 0;
                }
            }

            $resultSave = $this->Ttuj->Revenue->saveRevenue($id, $data_local, $data, $this);
            $statusSave = !empty($resultSave['status'])?$resultSave['status']:false;
            $msgSave = !empty($resultSave['msg'])?$resultSave['msg']:false;

            $this->MkCommon->setCustomFlash($msgSave, $statusSave);

            if( !empty($resultSave['data']) ) {
                $this->request->data = $resultSave['data'];
                $this->request->data['Ttuj'] = $dataTtuj;
            }

            if( $statusSave == 'success' ) {
                $this->redirect(array(
                    'controller' => 'revenues',
                    'action' => 'index'
                ));
            } else {
                $this->request->data['Revenue']['date_revenue'] = $this->MkCommon->getDate($data['Revenue']['date_revenue'], 'd/m/Y');
            }
        }else if($id && $data_local){
            $this->request->data = $data_local;
            $transaction_status = $this->MkCommon->filterEmptyField($data_local, 'Revenue', 'transaction_status');
            $allow_closing = $this->MkCommon->_callAllowClosing($data_local, 'Revenue', 'date_revenue', 'Y-m', false);

            if( !empty($this->request->data['Revenue']['date_revenue']) && $this->request->data['Revenue']['date_revenue'] != '0000-00-00' ) {
                $this->request->data['Revenue']['date_revenue'] = date('d/m/Y', strtotime($this->request->data['Revenue']['date_revenue']));
            } else {
                $this->request->data['Revenue']['date_revenue'] = '';
            }
        }

        $ttuj_id = Common::hashEmptyField($data_local, 'Ttuj.id');
        $ttuj_id = Common::hashEmptyField($data, 'Revenue.ttuj_id', $ttuj_id);
        
        $this->Ttuj->virtualFields['current_document_sort'] = 'CASE WHEN Ttuj.id = \''.$ttuj_id.'\' THEN 1 ELSE 0 END';
        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'order'=> array(
                'Ttuj.current_document_sort' => 'DESC',
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
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
            'limit' => 100,
        ), true, array(
            'plant' => true,
        ));
        $this->set('ttujs', $ttujs);

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);

        $toCities = $this->City->getListCities();
        $groupMotors = $this->Ttuj->Revenue->RevenueDetail->GroupMotor->getData('list');
        $cogs = $this->MkCommon->_callCogsOptGroup('Revenue');

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'toCities', 'groupMotors', 'tarifTruck',
            'id', 'data_local', 'trucks', 'tarif',
            'allow_closing'
        ));
        $this->set('active_menu', 'revenues');

        if( $action_type == 'manual' ) {
            $trucks = $this->Ttuj->Truck->_callListTruck($id, $ttuj_id);
            // debug($trucks);die();

            $this->set(compact(
                'trucks'
            ));

            $this->render('revenue_manual_form');
        } else {
            $this->render('revenue_form');
        }
    }

    function revenue_toggle( $id ){
        $locale = $this->Ttuj->Revenue->getData('first', array(
            'conditions' => array(
                'Revenue.id' => $id
            )
        ));

        if($locale){
            $this->MkCommon->_callAllowClosing($locale, 'Revenue', 'date_revenue');

            $date_revenue = $this->MkCommon->filterEmptyField($locale, 'Revenue', 'date_revenue');
            $no_doc = $this->MkCommon->filterEmptyField($locale, 'Revenue', 'no_doc');
            $customer_id = $this->MkCommon->filterEmptyField($locale, 'Revenue', 'customer_id');
            $total = $this->MkCommon->filterEmptyField($locale, 'Revenue', 'total', 0);
            $cogs_id = $this->MkCommon->filterEmptyField($locale, 'Revenue', 'cogs_id');

            $locale = $this->Ttuj->Customer->getMerge($locale, $customer_id);
            $customer_name = $this->MkCommon->filterEmptyField($locale, 'Customer', 'customer_name_code');

            $value = true;

            if($locale['Revenue']['status']){
                $value = false;
            }

            $this->Ttuj->Revenue->set('status', $value);
            $this->Ttuj->Revenue->id = $id;

            if($this->Ttuj->Revenue->save()){
                $this->Ttuj->set('is_revenue', 0);
                $this->Ttuj->id = $locale['Revenue']['ttuj_id'];
                $this->Ttuj->save();

                $titleJournal = sprintf(__('Pembatalan Revenue customer %s'), $customer_name);
                $this->User->Journal->setJournal($total, array(
                    'credit' => 'revenue_coa_debit_id',
                    'debit' => 'revenue_coa_credit_id',
                ), array(
                    'cogs_id' => $cogs_id,
                    'date' => $date_revenue,
                    'document_id' => $id,
                    'title' => $titleJournal,
                    'document_no' => $no_doc,
                    'type' => 'revenue_void',
                ));

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
            $this->Ttuj->Truck->bindModel(array(
                'hasOne' => array(
                    'TruckCustomer' => array(
                        'className' => 'TruckCustomer',
                        'foreignKey' => 'truck_id',
                    )
                )
            ), false);
            $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        
            $dateFrom = date('Y-m-d', strtotime('-1 Month'));
            $dateTo = date('Y-m-d');
            $params = $this->MkCommon->_callRefineParams($this->params);

            $truk = $this->Ttuj->Truck->getData('first', array(
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

                $truk = $this->Ttuj->Customer->getMerge($truk, $customer_id);
                $truk = $this->Ttuj->Truck->TruckBrand->getMerge($truk, $truck_brand_id);
                $truk = $this->Ttuj->Truck->TruckCategory->getMerge($truk, $truck_category_id);
                $truk = $this->Ttuj->Truck->TruckFacility->getMerge($truk, $truck_facility_id);
                $truk = $this->Ttuj->Truck->Driver->getMerge($truk, $driver_id);
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
                    'Ttuj.is_draft' => 0,
                );

                $this->Ttuj->virtualFields['arrive_leadtime_day'] = 'FLOOR(HOUR(TIMEDIFF(tgljam_berangkat, tgljam_tiba)) / 24)';
                $this->Ttuj->virtualFields['arrive_leadtime_hour'] = 'MOD(HOUR(TIMEDIFF(tgljam_berangkat, tgljam_tiba)), 24)';
                $this->Ttuj->virtualFields['arrive_leadtime_minute'] = 'MINUTE(TIMEDIFF(tgljam_berangkat, tgljam_tiba))';
                $this->Ttuj->virtualFields['arrive_leadtime_total'] = 'TIMESTAMPDIFF(HOUR, tgljam_berangkat, tgljam_tiba)';
                $this->Ttuj->virtualFields['back_leadtime_day'] = 'FLOOR(HOUR(TIMEDIFF(tgljam_balik, tgljam_pool)) / 24)';
                $this->Ttuj->virtualFields['back_leadtime_hour'] = 'MOD(HOUR(TIMEDIFF(tgljam_balik, tgljam_pool)), 24)';
                $this->Ttuj->virtualFields['back_leadtime_minute'] = 'MINUTE(TIMEDIFF(tgljam_balik, tgljam_pool))';
                $this->Ttuj->virtualFields['back_leadtime_total'] = 'TIMESTAMPDIFF(HOUR, tgljam_balik, tgljam_pool)';

                $options =  $this->Ttuj->_callRefineParams($params, array(
                    'conditions' => $default_conditions,
                    'order' => array(
                        'Ttuj.tgljam_berangkat' => 'ASC',
                        'Ttuj.tgljam_tiba' => 'ASC',
                        'Ttuj.tgljam_bongkaran' => 'ASC',
                        'Ttuj.tgljam_balik' => 'ASC',
                        'Ttuj.tgljam_pool' => 'ASC'
                    )
                ));
                $this->paginate = $this->Ttuj->getData('paginate', $options, true, array(
                    'branch' => false,
                ));
                $truk_ritase = $this->paginate('Ttuj');
                $total_lku = 0;
                $total_ksu = 0;

                if(!empty($truk_ritase)){
                    foreach ($truk_ritase as $key => $value) {
                        $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                        $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                        $value = $this->Ttuj->getMergeList($value, array(
                            'contain' => array(
                                'DriverPengganti' => array(
                                    'uses' => 'Driver',
                                    'primaryKey' => 'id',
                                    'foreignKey' => 'driver_pengganti_id',
                                    'elements' => array(
                                        'branch' => false,
                                    ),
                                ),
                                'Driver' => array(
                                    'elements' => array(
                                        'branch' => false,
                                    ),
                                ),
                            ),
                        ));

                        $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                        $qty_ritase = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $ttuj_id,
                                'TtujTipeMotor.status' => 1
                            ),
                            'fields' => array(
                                'sum(TtujTipeMotor.qty) as qty_ritase'
                            )
                        ));

                        $this->Ttuj->Lku->virtualFields['qty'] = 'SUM(Lku.total_klaim)';
                        $lkus = $this->Ttuj->Lku->getData('first', array(
                            'conditions' => array(
                                'Lku.ttuj_id' => $ttuj_id
                            ),
                        ), true, array(
                            'branch' => false,
                        ));

                        $this->Ttuj->Ksu->virtualFields['qty'] = 'SUM(Ksu.total_klaim)';
                        $ksus = $this->Ttuj->Ksu->getData('first', array(
                            'conditions' => array(
                                'Ksu.ttuj_id' => $ttuj_id
                            ),
                        ), true, array(
                            'branch' => false,
                        ));

                        $uang_jalan_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'uang_jalan_id');

                        $truk_ritase[$key] = $value;
                        $truk_ritase[$key] = $this->Ttuj->UangJalan->getMerge($truk_ritase[$key], $uang_jalan_id);

                        $truk_ritase[$key]['qty_ritase'] = $qty_ritase[0]['qty_ritase'];

                        $qty_lku = $this->MkCommon->filterEmptyField($lkus, 'Lku', 'qty', 0);
                        $qty_ksu = $this->MkCommon->filterEmptyField($ksus, 'Ksu', 'qty', 0);

                        $truk_ritase[$key]['Lku']['qty'] = $qty_lku;
                        $truk_ritase[$key]['Ksu']['qty'] = $qty_ksu;

                        $total_lku += $qty_lku;
                        $total_ksu += $qty_ksu;
                    }
                }
                
                $this->MkCommon->_layout_file(array(
                    'freeze',
                    'select',
                ));
                $customers = $this->Ttuj->Customer->getData('list', array(
                    'fields' => array(
                        'Customer.id', 'Customer.customer_name_code'
                    ),
                ));

                $sub_module_title = __('Detail Ritase Truk');
                $this->set('active_menu', 'ritase_report');
                $this->set(compact(
                    'id', 'truk', 'truk_ritase', 'sub_module_title', 
                    'total_ritase', 'total_unit', 'total_lku',
                    'total_ksu', 'customers'
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
                $value = $this->Invoice->Customer->getMerge($value, $value['Invoice']['customer_id']);
                $value = $this->Invoice->Company->getMerge($value, $value['Invoice']['company_id']);
                $invoices[$key] = $value;
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
        $this->loadModel('Bank');

        $elementRevenue = false;
        $customer_name_code = false;
        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'Invoice' => array(
                        'invoice_date',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'Invoice', 'invoice_date');

            $customer_id = $this->MkCommon->filterEmptyField($data, 'Invoice', 'customer_id');
            $invoice_date = $this->MkCommon->filterEmptyField($data, 'Invoice', 'invoice_date');
            $cogs_id = $this->MkCommon->filterEmptyField($data, 'Invoice', 'cogs_id');

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
            $data['Invoice']['branch_id'] = Configure::read('__Site.config_branch_id');

            $customer = $this->Ttuj->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $customer_id
                )
            ));

            if( !empty($customer) ) {
                $customer_name_code = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_name_code');
                $customer_group_id = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_group_id');
                $customer = $this->Ttuj->Customer->CustomerGroup->CustomerGroupPattern->getMerge($customer, $customer_group_id);

                $data['Invoice']['billing_id'] = $customer['Customer']['billing_id'];
                $data['Invoice']['term_of_payment'] = $customer['Customer']['term_of_payment'];

                if( empty($data['Invoice']['bank_id']) ) {
                    $data['Invoice']['bank_id'] = !empty($customer['Customer']['bank_id'])?$customer['Customer']['bank_id']:false;
                }
            }

            $this->Invoice->set($data);

            if($this->Invoice->validates()){
                $this->loadModel('CustomerGroupPattern');

                $tarif_type = !empty($data['Invoice']['tarif_type'])?$data['Invoice']['tarif_type']:false;
                
                if( in_array($action, array( 'tarif', 'tarif_name' )) ){
                    if(!empty($customer)){
                        $options = array(
                            'conditions' => array(
                                'Revenue.customer_id' => $customer_id,
                                'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                                'RevenueDetail.tarif_angkutan_type' => $tarif_type,
                                'RevenueDetail.invoice_id' => NULL,
                                'Revenue.status' => 1,
                                'RevenueDetail.status' => 1,
                            ),
                            'order' => array(
                                'Revenue.date_revenue' => 'ASC',
                                'Revenue.id' => 'ASC',
                                'RevenueDetail.id' => 'ASC',
                            )
                        );

                        if( $action == 'tarif_name' ) {
                            $options['contain'][] = 'TarifAngkutan';
                            $options['order'] = array_merge(array(
                                'TarifAngkutan.name_tarif' => 'ASC',
                            ), $options['order']);
                        } else {
                            $options['order'] = array_merge(array(
                                'RevenueDetail.price_unit' => 'ASC',
                            ), $options['order']);
                        }

                        $revenue_detail = $this->Ttuj->Revenue->RevenueDetail->getData('all', $options, $elementRevenue);

                        $result = array();
                        $flag = true;
                        $errorMsg = array();

                        if(!empty($revenue_detail)){
                            foreach ($revenue_detail as $key => $value) {
                                if( $action == 'tarif_name' ) {
                                    $grouping = $this->MkCommon->filterEmptyField($value, 'TarifAngkutan', 'name_tarif');
                                } else {
                                    $grouping = $this->MkCommon->filterEmptyField($value, 'RevenueDetail', 'price_unit');
                                }

                                $result[$grouping][] = $value;
                            }
                        }

                        if(!empty($result)){
                            $invoice_number = $this->MkCommon->getNoInvoice( $customer );

                            foreach ($result as $key => $value) {
                                $data['Invoice']['no_invoice'] = $invoice_number;
                                $data['Invoice']['type_invoice'] = $action;
                                $data['Invoice']['due_invoice'] = $customer['Customer']['term_of_payment'];
                                
                                $this->Invoice->create();
                                $this->Invoice->set($data);

                                if(!$this->Invoice->validates()){
                                    $errorValidations = $this->Invoice->validationErrors;
                                    $errorMsg = array_merge($errorMsg, $this->MkCommon->_callMsgValidationErrors($errorValidations));
                                }
                            }
                        }

                        if( !empty($errorMsg) ) {
                            $msg = array_unique($errorMsg);
                            $msg = implode('</li><li>', $msg);
                            $msg = '<ul><li>'.$msg.'</li></ul>';
                            $this->MkCommon->setCustomFlash($msg, 'error');
                        } else {
                            if(!empty($result)){
                                $invoice_number = $this->MkCommon->getNoInvoice( $customer );

                                foreach ($result as $key => $value) {
                                    $this->Invoice->create();
                                    $data['Invoice']['no_invoice'] = $invoice_number;
                                    $data['Invoice']['type_invoice'] = $action;
                                    $data['Invoice']['due_invoice'] = $customer['Customer']['term_of_payment'];
                                    $this->Invoice->set($data);

                                    if($this->Invoice->save()){
                                        $invoice_id = $this->Invoice->id;
                                        $invoice_number = $this->CustomerGroupPattern->addPattern($customer, $data);
                                        $this->CustomerGroupPattern->addPattern($customer, $data);

                                        $titleJournalInv = sprintf(__('Invoice customer: %s, No: %s'), $customer_name_code, $invoice_number);
                                        $journalData = array(
                                            'date' => $invoice_date,
                                            'document_id' => $invoice_id,
                                            'title' => $titleJournalInv,
                                            'document_no' => $invoice_number,
                                            'type' => 'invoice',
                                        );

                                        // if( !empty($data['Invoice']['total']) ) {
                                        //     $total = $this->MkCommon->filterEmptyField($data, 'Invoice', 'total');
                                        //     $total_pph = $this->MkCommon->filterEmptyField($data, 'Invoice', 'total_pph');

                                        //     $this->User->Journal->setJournal($total_pph, array(
                                        //         'credit' => 'pph_coa_credit_id',
                                        //         'debit' => 'pph_coa_debit_id',
                                        //     ), $journalData);
                                        // }

                                        $this->params['old_data'] = $data_local;
                                        $this->params['data'] = $data;

                                        $this->Ttuj->Revenue->getProsesInvoice( $customer_id, $invoice_id, $action, $tarif_type, $value, $journalData );
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
                    }
                }else{
                    $data['Invoice']['due_invoice'] = $customer['Customer']['term_of_payment'];
                    $this->Invoice->set($data);

                    if($this->Invoice->save()){
                        $invoice_id = $this->Invoice->id;
                        $document_no = !empty($data['Invoice']['no_invoice'])?$data['Invoice']['no_invoice']:false;

                        if( !empty($data['Invoice']['total']) ) {
                            $titleJournalInv = sprintf(__('Invoice customer: %s, No: %s'), $customer_name_code, $document_no);
                            $total = $this->MkCommon->filterEmptyField($data, 'Invoice', 'total');
                            // $total_pph = $this->MkCommon->filterEmptyField($data, 'Invoice', 'total_pph');

                            $this->User->Journal->setJournal($total, array(
                                'credit' => 'invoice_coa_credit_id',
                                'debit' => 'invoice_coa_debit_id',
                            ), array(
                                // 'cogs_id' => $cogs_id,
                                'date' => $invoice_date,
                                'document_id' => $invoice_id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'invoice',
                            ));
                            $this->User->Journal->setJournal($total, array(
                                'credit' => 'invoice_coa_2_credit_id',
                                'debit' => 'invoice_coa_2_debit_id',
                            ), array(
                                // 'cogs_id' => $cogs_id,
                                'date' => $invoice_date,
                                'document_id' => $invoice_id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'invoice',
                            ));
                            // $this->User->Journal->setJournal($total_pph, array(
                            //     'credit' => 'pph_coa_credit_id',
                            //     'debit' => 'pph_coa_debit_id',
                            // ), array(
                            //     'date' => $invoice_date,
                            //     'document_id' => $invoice_id,
                            //     'title' => $titleJournalInv,
                            //     'document_no' => $document_no,
                            //     'type' => 'invoice',
                            // ));
                        }

                        $this->CustomerGroupPattern->addPattern($customer, $data);

                        $this->params['old_data'] = $data_local;
                        $this->params['data'] = $data;

                        $this->Ttuj->Revenue->getProsesInvoice( $customer_id, $invoice_id, $action, $tarif_type );
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

        if( in_array($action, array( 'tarif', 'tarif_name' )) ){
            $conditionsRevenue['revenue_tarif_type'] = 'per_unit';
        }

        $revenues = $this->Ttuj->Revenue->getData('all', array(
            'conditions' => $conditionsRevenue,
            'order' => array(
                'Revenue.date_revenue' => 'ASC'
            ),
            'group' => array(
                'Revenue.customer_id'
            ),
        ), true, $elementRevenue);
        $customers = array();

        if( !empty($revenues) ) {
            foreach ($revenues as $key => $revenue) {
                $revenueCustomer = $this->Ttuj->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $revenue['Revenue']['customer_id'],
                    ),
                ));

                if( !empty($revenueCustomer) ) {
                    $customers[$revenue['Revenue']['customer_id']] = $revenueCustomer['Customer']['customer_name_code'];
                }
            }
        }

        $companies = $this->Ttuj->Truck->Company->getData('list', false, array(
            'status' => 'invoice',
        ));

        $this->set(compact(
            'customers', 'id', 'action',
            'banks', 'companies'
        ));
        $this->set('active_menu', 'invoices');
        $this->render('invoice_form');
    }

    function invoice_print($id, $action_print = false){
        $this->loadModel('Invoice');
        $data_print = $this->MkCommon->filterEmptyField($this->params, 'named', 'print', 'invoice');

        $this->set('active_menu', 'invoices');

        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = array(
            'status' => 'all',
        );

        if( !empty($head_office) ) {
            $elementRevenue['branch'] = false;
        }
        
        $invoice = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
            ),
            'contain' => array(
                'InvoiceDetail' => array(
                    'conditions' => array(
                        'InvoiceDetail.status' => 1,
                    ),
                ),
            )
        ), true, $elementRevenue);

        if(!empty($invoice)){
            $this->loadModel('Bank');

            $no_invoice = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'no_invoice');
            $company_id = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'company_id');

            $invoice = $this->Invoice->Customer->getMerge($invoice, $invoice['Invoice']['customer_id']);
            $invoice = $this->Invoice->Company->getMerge($invoice, $company_id);

            $invoice = $this->User->getMerge($invoice, $invoice['Invoice']['billing_id']);
            $invoice = $this->Bank->getMerge($invoice, $invoice['Invoice']['bank_id']);

            $employe_position_id = $this->MkCommon->filterEmptyField($invoice, 'Employe', 'employe_position_id');
            $invoice = $this->User->Employe->EmployePosition->getMerge($invoice, $employe_position_id);

            $revenueDetailId = Set::extract('/InvoiceDetail/revenue_detail_id', $invoice);

            if( $data_print == 'header' ) {
                $this->loadModel('Setting');
                $setting = $this->Setting->find('first');
                $billing_id = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'billing_id');

                $invoice = $this->Ttuj->Revenue->RevenueDetail->getSumUnit($invoice, $invoice['Invoice']['id']);
                $invoice = $this->User->getMerge($invoice, $billing_id);
            } else {
                $revenue_detail = $this->Ttuj->Revenue->RevenueDetail->getPreviewInvoice($invoice['Invoice']['id'], $invoice['Invoice']['tarif_type'], $action_print, $data_print, $revenueDetailId);
            }

            $action = $invoice['Invoice']['type_invoice'];
            $this->set(compact(
                'invoice', 'revenue_detail', 'action',
                'setting', 'data_print'
            ));

            if($action_print == 'pdf'){
                $this->layout = 'pdf';
            }else if($action_print == 'excel'){
                $this->layout = 'ajax';
            }
            
            $module_title = sprintf(__('Kwitansi No.%s'), $no_invoice);
            $this->set('sub_module_title', trim($module_title));
            $this->set('module_title', trim($module_title));
            $this->set('action_print', $action_print);

            switch ($data_print) {
                case 'header':
                    $this->render('invoice_header_print');
                    break;
                case 'mpm':
                    $this->render('invoice_mpm_print');
                    break;
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Kwitansi tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function invoice_reports( $data_action = false ){
        $this->loadModel('Invoice');
        $this->loadModel('Customer');

        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $sub_module_title = __('Account Receivable Aging Report');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $options = array(
            'conditions' => array(
                'Customer.branch_id' => $allow_branch_id,
            ),
        );
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

                $options['conditions']['Customer.id'] = $customer_id;
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

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $options );
        }

        if(!empty($customer_collect_id)){
            $options['conditions']['Customer.id'] = $customer_collect_id;
        }else if(empty($customer_collect_id) && ($due_30 || $due_15 || $due_above_30) ){
            $options['conditions']['Customer.id'] = false;
        }

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $invOptions =  $this->Invoice->_callRefineParams($params);
        $invoice_conditions = $this->MkCommon->filterEmptyField($invOptions, 'conditions');

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');

        if(empty($data_action)){
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $this->Customer->getData('paginate', $options, array(
                'plant' => false,
                'branch' => false,
            ));

            $customers = $this->paginate('Customer');
        }else{
            $customers = $this->Customer->getData('all', $options, array(
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
            $customers[$key]['current_rev30'] = $this->Invoice->getData('all', array(
                'conditions' => $default_conditions,
                'fields' => array(
                    'SUM(Invoice.total) as current_rev30'
                )
            ), true, array(
                'branch' => false,
            ));
        }

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

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $periode = $this->MkCommon->getCombineDate($dateFrom, $dateTo);
        }

        $this->set('active_menu', 'invoice_reports');
        $this->set(compact(
            'customers', 'list_customer', 'data_action',
            'dateFrom', 'dateTo', 'sub_module_title',
            'periode'
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
        $this->loadModel('InvoicePayment');
        
        $this->set('active_menu', 'invoice_payments');
        $this->set('sub_module_title', __('Pembayaran Invoice'));

        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->InvoicePayment->_callRefineParams($params, array(
            'contain' => array(
                'Coa'
            ),
            'order' => array(
                'InvoicePayment.status' => 'DESC',
                'InvoicePayment.created' => 'DESC',
                'InvoicePayment.id' => 'DESC',
            ),
        ));
        $this->paginate = $this->InvoicePayment->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $invoices = $this->paginate('InvoicePayment');

        if(!empty($invoices)){
            foreach ($invoices as $key => $value) {
                $invoices[$key] = $this->Ttuj->Customer->getMerge($value, $value['InvoicePayment']['customer_id']);
            }
        }
        
        $this->set('invoices', $invoices); 

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);
    }

    function invoice_payment_add(){
        $module_title = __('Tambah Pembayaran Invoice');
        $this->set('sub_module_title', trim($module_title));
        $this->doInvoicePayment();
    }

    function invoice_payment_edit($id = false){
        $this->loadModel('InvoicePayment');
        $invoice = $this->InvoicePayment->getData('first', array(
            'conditions' => array(
                'InvoicePayment.id' => $id,
                'InvoicePayment.is_canceled' => 0,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($invoice)){
            $coa_id = $this->MkCommon->filterEmptyField($invoice, 'InvoicePayment', 'coa_id');
            $transaction_status = $this->MkCommon->filterEmptyField($invoice, 'InvoicePayment', 'transaction_status');

            if( $transaction_status == 'posting' ) {
                $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
                $this->redirect($this->referer());
                die();
            }

            $invoice = $this->User->Journal->Coa->getMerge($invoice, $coa_id);
            $invoice = $this->InvoicePayment->InvoicePaymentDetail->getMergeAll($invoice, $id);
            $this->MkCommon->getLogs($this->params['controller'], array( 'invoice_payment_add', 'invoice_payment_edit', 'invoice_payment_delete' ), $id);

            $module_title = __('Edit Pembayaran Invoice');
            $this->set('sub_module_title', $module_title);
            $this->doInvoicePayment( $id, $invoice );
        }else{
            $this->MkCommon->setCustomFlash(__('Pembayaran invoice tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function doInvoicePayment($id = false, $data_local = false){        
        $this->loadModel('InvoicePayment');

        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;
        $data = $this->request->data;

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        if(!empty($data)){
            $data = $this->RjRevenue->_callBeforeSaveInvoicePayment($data, $data_local);
            $flag = $this->InvoicePayment->saveAll($data, array(
                'validate' => 'only',
            ));

            if( !empty($flag) ){
                if( !empty($id) ){
                    $this->InvoicePayment->InvoicePaymentDetail->deleteAll(array(
                        'InvoicePaymentDetail.invoice_payment_id' => $id
                    ));
                }

                $flag = $this->InvoicePayment->saveAll($data);

                if( !empty($flag) ) {
                    $this->RjRevenue->_callAfterSaveInvoicePayment($data, $data_local);
                } else {
                    $this->MkCommon->setCustomFlash(__('Gagal menyimpan Pembayaran Invoice'), 'error'); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan Pembayaran Invoice'), 'error'); 
            }
        }

        $this->RjRevenue->_callBeforeViewInvoicePayment($data, $data_local);

        $this->set(array(
            'active_menu' => 'invoice_payments',
        ));
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
            $invoice_payment = $this->Invoice->InvoicePaymentDetail->InvoicePayment->getMergeList($invoice_payment, array(
                'contain' => array(
                    'CashBank' => array(
                        'uses' => 'CashBank',
                        'primaryKey' => 'document_id',
                        'foreignKey' => 'id',
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));

            if(!empty($invoice_payment)){
                $this->MkCommon->_callAllowClosing($invoice_payment, 'InvoicePayment', 'date_payment');

                $customer_id = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'customer_id');
                $coa_id = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'coa_id');
                $date_payment = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'date_payment');
                $transaction_status = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'transaction_status');
                $cogs_id = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'cogs_id');
                
                $invoice_payment = $this->Invoice->InvoicePaymentDetail->InvoicePayment->Customer->getMerge($invoice_payment, $customer_id);
                $customer_name_code = $this->MkCommon->filterEmptyField($invoice_payment, 'Customer', 'customer_name_code');
                $total_payment = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'total_payment');
                
                $cash_bank_id = $this->MkCommon->filterEmptyField($invoice_payment, 'CashBank', 'id');
                // $pph = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'pph');

                // if( !empty($pph) ) {
                //     $pph_total = $this->MkCommon->_callPercentAmount($total_payment, $pph);
                // }

                if(!empty($invoice_payment['InvoicePaymentDetail'])){
                    if( $transaction_status == 'posting' ) {
                        foreach ($invoice_payment['InvoicePaymentDetail'] as $key => $value) {
                            $invoice_has_paid = $this->Invoice->InvoicePaymentDetail->getData('first', array(
                                'conditions' => array(
                                    'InvoicePaymentDetail.invoice_id' => $value['invoice_id'],
                                    'InvoicePayment.status' => 1,
                                    'InvoicePayment.is_canceled' => 0,
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
                    }

                    // $this->Invoice->InvoicePaymentDetail->updateAll(array(
                    //     'InvoicePaymentDetail.status' => 0
                    // ), array(
                    //     'InvoicePaymentDetail.invoice_payment_id' => $id
                    // ));
                }

                $this->Invoice->InvoicePaymentDetail->InvoicePayment->id = $id;
                $this->Invoice->InvoicePaymentDetail->InvoicePayment->set(array(
                    'status' => 0,
                    'is_canceled' => 1,
                    'canceled_date' => date('d/m/Y')
                ));

                if($this->Invoice->InvoicePaymentDetail->InvoicePayment->save()){
                    if( $transaction_status == 'posting' ) {
                        if( !empty($invoice_payment['InvoicePayment']['grand_total_payment']) ) {
                            $document_no = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'nodoc');
                            $grandTotal = $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'grand_total_payment');

                            $titleJournalInv = sprintf(__('pembayaran invoice oleh customer %s'), $customer_name_code);
                            $titleJournalInv = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($invoice_payment, 'InvoicePayment', 'description', $titleJournalInv));

                            $this->User->Journal->setJournal($grandTotal, array(
                                'credit' => $coa_id,
                                'debit' => 'pembayaran_invoice_coa_id',
                            ), array(
                                'cogs_id' => $cogs_id,
                                'date' => $date_payment,
                                'document_id' => $id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'invoice_payment_void',
                            ));

                            if( !empty($cash_bank_id) ) {
                                $tgl_cash_bank = $this->MkCommon->filterEmptyField($invoice_payment, 'CashBank', 'tgl_cash_bank');
                                $pph_total = $this->MkCommon->filterEmptyField($invoice_payment, 'CashBank', 'grand_total');
                                $description = $this->MkCommon->filterEmptyField($invoice_payment, 'CashBank', 'description');

                                $this->Invoice->InvoicePaymentDetail->InvoicePayment->CashBank->id = $cash_bank_id;
                                $this->Invoice->InvoicePaymentDetail->InvoicePayment->CashBank->set(array(
                                    'is_rejected' => 1,
                                ));
                                
                                if($this->Invoice->InvoicePaymentDetail->InvoicePayment->CashBank->save()){
                                    $noref = str_pad($cash_bank_id, 6, '0', STR_PAD_LEFT);
                                    $description = __('<i>Pembatalan</i> %s', $description);

                                    $this->User->Journal->setJournal($pph_total, array(
                                        'credit' => 'pph_coa_debit_id',
                                        'debit' => $coa_id,
                                    ), array(
                                        'cogs_id' => $cogs_id,
                                        'date' => $tgl_cash_bank,
                                        'document_id' => $cash_bank_id,
                                        'title' => $description,
                                        'document_no' => $noref,
                                        'type' => 'out_void',
                                    ));
                                }
                            }
                        }
                    }

                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil menghapus invoice pembayaran #%s'), $noref), 'success');
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

            $this->Revenue->RevenueDetail->virtualFields['total'] = 'SUM(RevenueDetail.total_price_unit)';
            $revenues = $this->Revenue->RevenueDetail->getData('all', array(
                'conditions' => array(
                    'RevenueDetail.revenue_id' => $arr_id,
                ),
                'group' => array(
                    'RevenueDetail.revenue_id'
                ),
            ), array(
                'branch' => false,
            ));

            if( !empty($revenues) ) {
                foreach ($revenues as $key => $revenue) {
                    $this->MkCommon->_callAllowClosing($revenue, 'Revenue', 'date_revenue');

                    $date_revenue = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'date_revenue');
                    $customer_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'customer_id');
                    $cogs_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'cogs_id');

                    $revenue_id = $this->MkCommon->filterEmptyField($revenue, 'RevenueDetail', 'revenue_id');
                    $revenue = $this->Ttuj->Customer->getMerge($revenue, $customer_id);
                    $total = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'total');
                    $customer_name = $this->MkCommon->filterEmptyField($revenue, 'Customer', 'customer_name_code');
                    $no_doc = str_pad($revenue_id, 5, '0', STR_PAD_LEFT);

                    $this->Revenue->set('total', $total);
                    $this->Revenue->id = $revenue_id;
                    $this->Revenue->save();

                    $titleJournal = sprintf(__('Revenue customer %s'), $customer_name);

                    $this->User->Journal->deleteJournal($revenue_id, array(
                        'revenue',
                    ));
                    $this->User->Journal->setJournal($total, array(
                        'credit' => 'revenue_coa_credit_id',
                        'debit' => 'revenue_coa_debit_id',
                    ), array(
                        'cogs_id' => $cogs_id,
                        'date' => $date_revenue,
                        'document_id' => $revenue_id,
                        'title' => $titleJournal,
                        'document_no' => $no_doc,
                        'type' => 'revenue',
                    ));
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
                // 'contain' => array(
                //     'InvoicePaymentDetail' => array(
                //         'Invoice'
                //     ),
                //     'Coa',
                // )
            ), true, array(
                'status' => 'all',
            ));
            $invoice = $this->InvoicePayment->getMergeList($invoice, array(
                'contain' => array(
                    'InvoicePaymentDetail' => array(
                        'Invoice',
                    ),
                    'Coa',
                ),
            ));

            if(!empty($invoice)){
                $customer_id = $this->MkCommon->filterEmptyField($invoice, 'InvoicePayment', 'customer_id');
                $invoice = $this->InvoicePayment->Customer->getMerge($invoice, $customer_id);
                $invoice = $this->InvoicePayment->getMergeList($invoice, array(
                    'contain' => array(
                        'Cogs',
                    ),
                ));

                $this->MkCommon->getLogs($this->params['controller'], array( 'invoice_payment_add', 'invoice_payment_edit', 'invoice_payment_delete' ), $id);
                $this->RjRevenue->_callBeforeViewInvoicePayment(array(), $invoice);
        
                $this->set(array(
                    'view' => true,
                    'sub_module_title' => __('Detail Pembayaran Invoice'),
                    'active_menu' => 'invoice_payments',
                ));
                $this->render('invoice_payment_form');
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

        $options = array(
            'conditions' => array(
                'Invoice.branch_id' => $allow_branch_id,
            ),
            'order' => array(
                'Invoice.modified' => 'DESC',
                'Invoice.id' => 'DESC',
            ),
            'contain' => array(
                'CustomerNoType'
            ),
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

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Invoice->_callRefineParams($params, $options);
        // debug($params);die();

        if( !empty($this->params['named']) ){
            $refine = $this->params['named'];

            if(!empty($refine['page'])){
                $start = (($refine['page']-1)*$limit)+1;
            }

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $options );
            $invoiceUnpaidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoiceUnpaidOption, 'conditions' );
            $invoicePaidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoicePaidOption, 'conditions' );
            $invoiceHalfPaidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoiceHalfPaidOption, 'conditions' );
            $invoiceVoidOption = $this->MkCommon->getConditionGroupBranch( $refine, 'Invoice', $invoiceVoidOption, 'conditions' );
        }

        if( !empty($data_action) ) {
            $invoices = $this->Invoice->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $invoices = $this->paginate('Invoice');
        }

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
                $company_id = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'company_id');

                $invoice = $this->Revenue->RevenueDetail->getSumUnit($invoice, $invoice['Invoice']['id']);
                $invoice = $this->Invoice->getMergePayment($invoice, $invoice['Invoice']['id'] );
                $invoice = $this->Invoice->Company->getMerge($invoice, $company_id );

                $invoices[$key] = $invoice;
            }
        }

        $customerGroups = $this->Invoice->Customer->CustomerGroup->getData('list', array(
            'fields' => array(
                'CustomerGroup.id', 'CustomerGroup.code'
            ),
        ));
        $customers = $this->Invoice->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => $customerConditions,
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));
        $companies = $this->Invoice->Company->getData('list', array(
            'fields' => array(
                'Company.id', 'Company.code'
            ),
        ));
        $this->set('customers', $customers);
        $this->set('sub_module_title', __('List Kwitansi'));
        $this->set('active_menu', 'list_kwitansi');

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));

            $this->set(compact(
                'layout_css', 'layout_js'
            ));
        }

        $this->set(compact(
            'invoices', 'data_action', 'start',
            'dataStatus', 'companies', 'customerGroups'
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
                'InvoiceDetail' => array(
                    'conditions' => array(
                        'InvoiceDetail.status' => 1,
                    ),
                ),
            )
        ));

        if( !empty($invoice) ){
            $this->MkCommon->_callAllowClosing($invoice, 'Invoice', 'invoice_date');

            $customer_id = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'customer_id');
            $invoice_date = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'invoice_date');
            $invoice = $this->Invoice->Customer->getMerge($invoice, $customer_id);
            $customer_name_code = $this->MkCommon->filterEmptyField($invoice, 'Customer', 'customer_name_code');

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
                        if( !empty($invoice['Invoice']['total']) ) {
                            $document_no = !empty($invoice['Invoice']['no_invoice'])?$invoice['Invoice']['no_invoice']:false;
                            $titleJournalInv = sprintf(__('<i>Pembatalan</i> invoice customer: %s, No: %s'), $customer_name_code, $document_no);
                            $total = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'total');
                            // $total_pph = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'total_pph');

                            $this->User->Journal->setJournal($total, array(
                                'credit' => 'invoice_coa_debit_id',
                                'debit' => 'invoice_coa_credit_id',
                            ), array(
                                'date' => $invoice_date,
                                'document_id' => $id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'invoice_void',
                            ));
                            $this->User->Journal->setJournal($total, array(
                                'credit' => 'invoice_coa_2_debit_id',
                                'debit' => 'invoice_coa_2_credit_id',
                            ), array(
                                'date' => $invoice_date,
                                'document_id' => $id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'invoice_void',
                            ));
                            // $this->User->Journal->setJournal($total_pph, array(
                            //     'credit' => 'pph_coa_debit_id',
                            //     'debit' => 'pph_coa_credit_id',
                            // ), array(
                            //     'date' => $invoice_date,
                            //     'document_id' => $id,
                            //     'title' => $titleJournalInv,
                            //     'document_no' => $document_no,
                            //     'type' => 'invoice_void',
                            // ));
                        }

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
                            ), array(
                                'branch' => false,
                            ));
                            $revenueDetailId = $this->Revenue->RevenueDetail->getData('list', array(
                                'conditions' => array(
                                    'RevenueDetail.invoice_id' => $id
                                ),
                                'fields' => array(
                                    'RevenueDetail.id', 'RevenueDetail.id',
                                )
                            ), array(
                                'branch' => false,
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
                                ), array(
                                    'branch' => false,
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
            $msg = array(
                'msg' => __('Invoice tidak ditemukan'),
                'type' => 'error'
            );
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
                        'InvoicePayment.is_canceled' => 0,
                        'InvoicePaymentDetail.status' => 1,
                        'InvoicePayment.transaction_status' => 'posting',
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
                        'InvoicePayment.is_canceled' => 0,
                        'InvoicePayment.transaction_status' => 'posting',
                        'InvoicePayment.customer_id' => $customer['Customer']['id'],
                        'DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m\') <=' => $monthDt,
                    ),
                    'fields' => array(
                        'SUM(InvoicePayment.total_payment) total',
                    ),
                ), true, array(
                    // 'status' => 'all',
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

    public function surat_jalan() {
        $this->loadModel('SuratJalan');

        $this->set('active_menu', 'surat_jalan');
        $this->set('sub_module_title', __('Surat Jalan'));
        
        // $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        // $dateTo = date('Y-m-d');

        $this->SuratJalan->unBindModel(array(
            'hasMany' => array(
                'SuratJalanDetail'
            )
        ));

        $this->SuratJalan->bindModel(array(
            'hasOne' => array(
                'SuratJalanDetail' => array(
                    'className' => 'SuratJalanDetail',
                    'foreignKey' => 'surat_jalan_id',
                ),
                'Ttuj' => array(
                    'className' => 'Ttuj',
                    'foreignKey' => false,
                    'conditions' => array(
                        'Ttuj.id = SuratJalanDetail.ttuj_id',
                    ),
                ),
            )
        ), false);

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            // 'dateFrom' => $dateFrom,
            // 'dateTo' => $dateTo,
        ));
        $options =  $this->SuratJalan->_callRefineParams($params, array(
            'contain' => array(
                'SuratJalanDetail',
                'Ttuj',
            ),
            'group' => array(
                'SuratJalan.id',
            ),
        ));

        $this->paginate = $this->SuratJalan->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('SuratJalan');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'SuratJalan', 'id');

                $value['SuratJalan']['cnt_ttuj'] = $this->SuratJalan->SuratJalanDetail->_callTotalTtujDiterima( $id );
                $value['SuratJalan']['qty_unit'] = $this->SuratJalan->SuratJalanDetail->_callQtyDetail( $id, 'SuratJalanDetail.surat_jalan_id' );

                $values[$key] = $value;
            }
        }

        // $customers = $this->Ttuj->Customer->getData('list', array(
        //     'fields' => array(
        //         'Customer.id', 'Customer.customer_name_code'
        //     ),
        // ));
        $this->MkCommon->_layout_file('select');

        $this->set(compact(
            'values', 'customers'
        ));
    }
    
    function surat_jalan_add(){
        $module_title = __('Penerimaan Surat Jalan');
        $this->set('sub_module_title', $module_title);

        $this->doSuratJalan();
    }

    function surat_jalan_edit( $id = false, $disabled_edit = false ){
        $module_title = __('Edit Surat Jalan');
        $this->set('sub_module_title', $module_title);

        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }
        if( !empty($disabled_edit) ) {
            $elementRevenue['status'] = 'all';
        }

        $value = $this->Ttuj->SuratJalanDetail->SuratJalan->getData('first', array(
            'conditions' => array(
                'SuratJalan.id' => $id
            ),
        ), $elementRevenue);
        $value = $this->Ttuj->SuratJalanDetail->getMerge($value, $id);

        $this->doSuratJalan( $id, $value, $disabled_edit );
    }

    function surat_jalan_detail( $id = false ){
        $this->surat_jalan_edit($id, true);
    }

    function doSjDetail ( $dataDetail, $data, $surat_jalan_id = false ) {
        $status = true;
        $totalQty = 0;
        $tgl_surat_jalan = $this->MkCommon->filterEmptyField($data, 'SuratJalan', 'tgl_surat_jalan');
        $data = $this->request->data;
        $msgError = array();

        if( !empty($surat_jalan_id) ) {
            $this->Ttuj->SuratJalanDetail->updateAll( array(
                'SuratJalanDetail.status' => 0,
            ), array(
                'SuratJalanDetail.surat_jalan_id' => $surat_jalan_id,
            ));
        }

        if( !empty($dataDetail) ) {
            foreach ($dataDetail as $key => $qty) {
                $qty = !empty($qty)?$qty:'';
                $ttuj_id = !empty($data['SuratJalanDetail']['ttuj_id'][$key])?$data['SuratJalanDetail']['ttuj_id'][$key]:false;
                $note = !empty($data['SuratJalanDetail']['note'][$key])?$data['SuratJalanDetail']['note'][$key]:false;
                $muatan = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );

                $dataSjDetail = array(
                    'SuratJalanDetail' => array(
                        'ttuj_id' => $ttuj_id,
                        'qty' => $qty,
                        'note' => $note,
                    ),
                );
                $dataSjDetail = $this->Ttuj->getMerge($dataSjDetail, $ttuj_id);
                $dataSjDetail['Ttuj']['qty'] = $muatan;

                $this->request->data['Ttuj'][$key] = $dataSjDetail;

                $totalQty += $qty;

                if( !empty($surat_jalan_id) ) {
                    $dataSjDetail['SuratJalanDetail']['surat_jalan_id'] = $surat_jalan_id;
                }

                $this->Ttuj->SuratJalanDetail->create();
                $this->Ttuj->SuratJalanDetail->set($dataSjDetail);

                if( !empty($surat_jalan_id) ) {
                    if( !$this->Ttuj->SuratJalanDetail->save() ) {
                        $status = false;
                    } else {
                        $qtyDiterima = $this->Ttuj->SuratJalanDetail->_callTotalQtyDiterima( $ttuj_id );

                        if( $qtyDiterima >= $muatan ) {
                            $this->Ttuj->set('status_sj', 'full');
                        } else {
                            $this->Ttuj->set('status_sj', 'half');
                        }

                        $this->Ttuj->id = $ttuj_id;
                        $this->Ttuj->save();
                    }
                } else {
                    if( !$this->Ttuj->SuratJalanDetail->validates() ) {
                        $errorValidations = $this->Ttuj->SuratJalanDetail->validationErrors;

                        if( !empty($errorValidations) ) {
                            foreach ($errorValidations as $key => $error) {
                                if( !empty($error) ) {
                                    foreach ($error as $key => $err_msg) {
                                        $msgError[] = $err_msg;
                                    }
                                }
                            }
                        }

                        $status = false;
                    }
                }
            }
        } else {
            $status = false;
            $this->MkCommon->setCustomFlash(__('Mohon pilih TTUJ.'), 'error'); 
        }

        if( !empty($msgError) ) {
            $msgError = array_unique($msgError);
        }

        return array(
            'status' => $status,
            'msgError' => $msgError,
        );
    }

    function doSuratJalan( $id = false, $value = false, $disabled_edit = false ){
        $this->set('active_menu', 'surat_jalan');

        if(!empty($this->request->data) && empty($disabled_edit)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'SuratJalan' => array(
                        'tgl_surat_jalan',
                    ),
                )
            ));
            $data['SuratJalan']['branch_id'] = Configure::read('__Site.config_branch_id');
            $sjDetails = $this->MkCommon->filterEmptyField($value, 'SuratJalanDetail');

            $dataDetail = $this->MkCommon->filterEmptyField($data, 'SuratJalanDetail', 'qty');
            $resutlDetail = $this->doSjDetail($dataDetail, $data);
            $flagDetail = $this->MkCommon->filterEmptyField($resutlDetail, 'status');
            $errorDetail = $this->MkCommon->filterEmptyField($resutlDetail, 'msgError');

            if( !empty($id) ) {
                $this->Ttuj->SuratJalanDetail->SuratJalan->id = $id;
            } else {
                $this->Ttuj->SuratJalanDetail->SuratJalan->create();
            }

            $this->Ttuj->SuratJalanDetail->SuratJalan->set($data);

            if( $this->Ttuj->SuratJalanDetail->SuratJalan->validates() && !empty($flagDetail) ){
                if($this->Ttuj->SuratJalanDetail->SuratJalan->save()){
                    $document_id = $this->Ttuj->SuratJalanDetail->SuratJalan->id;
                    $this->doSjDetail($dataDetail, $data, $document_id);
                    $this->Ttuj->SuratJalanDetail->SuratJalan->recoverTtuj($sjDetails);

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan penerimaan surat jalan #%s'), $noref), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan penerimaan surat jalan #%s'), $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $document_id );
                    
                    $this->redirect(array(
                        'action' => 'surat_jalan',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan penerimaan surat jalan'), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal melakukan penerimaan surat jalan #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                if( !empty($errorDetail) ) {
                    $this->MkCommon->setCustomFlash('<ul><li>'.implode('</li><li>', $errorDetail).'</li></ul>', 'error'); 
                } else {
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan penerimaan surat jalan'), 'error'); 
                }
            }
        } else if( !empty($value) ) {
            if( !empty($value['SuratJalanDetail']) ) {
                foreach ($value['SuratJalanDetail'] as $key => $val) {
                    $ttuj_id = $this->MkCommon->filterEmptyField($val, 'SuratJalanDetail', 'ttuj_id');
                    $qty = $this->MkCommon->filterEmptyField($val, 'SuratJalanDetail', 'qty');

                    $val = $this->Ttuj->getMerge($val, $ttuj_id);
                    $val = $this->Ttuj->getMergeList($val, array(
                        'contain' => array(
                            'DriverPengganti' => array(
                                'uses' => 'Driver',
                                'primaryKey' => 'id',
                                'foreignKey' => 'driver_pengganti_id',
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                            'Driver' => array(
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                        ),
                    ));

                    $muatan = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );
                    $val['Ttuj']['qty'] = $muatan;

                    $this->request->data['Ttuj'][$key] = $val;

                    $this->request->data['SuratJalanDetail']['qty'][$key] = $qty;
                    $this->request->data['SuratJalanDetail']['ttuj_id'][$key] = $ttuj_id;
                }
            }

            $this->request->data['SuratJalan'] = $this->MkCommon->filterEmptyField($value, 'SuratJalan');
            $this->request->data = $this->MkCommon->dataConverter($this->request->data, array(
                'date' => array(
                    'SuratJalan' => array(
                        'tgl_surat_jalan',
                    ),
                )
            ), true);
        } else {
            $this->request->data['SuratJalan']['tgl_surat_jalan'] = date('d/m/Y');
        }

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->params['controller'], array( 'surat_jalan_add', 'surat_jalan_edit', 'surat_jalan_delete' ), $id);
        }

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'id', 'disabled_edit'
        ));
        $this->render('surat_jalan_add');
    }

    function surat_jalan_delete($id = false){
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Ttuj->SuratJalanDetail->SuratJalan->getData('first', array(
            'conditions' => array(
                'SuratJalan.id' => $id,
            ),
        ));

        if( !empty($value) ){
            if(!empty($this->request->data)){
                $data = $this->request->data;
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'SuratJalan' => array(
                            'canceled_date',
                        ),
                    )
                ));

                $value = $this->Ttuj->SuratJalanDetail->getMerge($value, $id);
                $dataDetail = $this->MkCommon->filterEmptyField($value, 'SuratJalanDetail');

                if(!empty($data['SuratJalan']['canceled_date'])){
                    $data['SuratJalan']['canceled_date'] = $this->MkCommon->filterEmptyField($data, 'SuratJalan', 'canceled_date');
                    $data['SuratJalan']['is_canceled'] = 1;

                    $this->Ttuj->SuratJalanDetail->SuratJalan->id = $id;
                    $this->Ttuj->SuratJalanDetail->SuratJalan->set($data);

                    if($this->Ttuj->SuratJalanDetail->SuratJalan->save()){
                        $this->Ttuj->SuratJalanDetail->updateAll(array(
                            'SuratJalanDetail.status' => 0,
                        ), array(
                            'SuratJalanDetail.surat_jalan_id' => $id,
                        ));

                        $this->Ttuj->SuratJalanDetail->SuratJalan->recoverTtuj($dataDetail);

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil menghapus surat jalan #%s'), $noref),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil menghapus surat jalan #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal menghapus surat jalan #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan surat jalan.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('value', $value);
        }else{
            $msg = array(
                'msg' => __('Data tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'SuratJalan';
        $canceled_date = !empty($this->request->data['SuratJalan']['canceled_date']) ? $this->request->data['SuratJalan']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    public function surat_jalan_outstanding( $driver_id = false, $pengganti = false ) {
        $this->loadModel('Revenue');
        $this->loadModel('Driver');
        $driver = $this->Driver->getData('first', array(
            'conditions' => array(
                'Driver.id' => $driver_id,
            )
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if( !empty($driver) ) {
            $ttujs = $this->Ttuj->getData('all', array(
                'conditions' => array(
                    'OR' => array(
                        'Ttuj.driver_id' => $driver_id,
                        'Ttuj.driver_pengganti_id' => $driver_id,
                    ),
                    'Ttuj.status_sj' => 'none',
                ),
                'order' => array(
                    'Ttuj.created' => 'DESC',
                    'Ttuj.id' => 'DESC',
                ),
            ), true, array(
                'branch' => false,
            ));

            if( !empty($ttujs) ) {
                foreach ($ttujs as $key => $ttuj) {
                    $ttuj['SjKembali'] = $this->Ttuj->SuratJalanDetail->_callTotalQtyDiterima( $ttuj['Ttuj']['id'] );
                    $ttuj['TotalMuatan'] = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj['Ttuj']['id'] );
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

    function document_ttujs(  ){
        $this->loadModel('City');
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $title = __('Dokumen TTUJ');

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Ttuj->_callRefineParams($params, array(
            'conditions' => array(
                'Ttuj.is_draft' => 0,
                'Ttuj.status_sj' => array( 'none', 'half' ),
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        ));


        if(!empty($this->params['named'])){
            $refine = $this->params['named'];
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
        }

        $this->paginate = $this->Ttuj->getData('paginate', $options, true, array(
            'plant' => true,
        ));
        $values = $this->paginate('Ttuj');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');

                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value['Ttuj']['qty'] = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );
                $value['Ttuj']['qty_diterima'] = $this->Ttuj->SuratJalanDetail->_callTotalQtyDiterima( $ttuj_id );

                $values[$key] = $value;
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $cities = $this->City->getListCities();

        $data_action = 'browse-check-docs';
        $this->set(compact(
            'data_action', 'title', 'values',
            'customers', 'cities'
        ));
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

        $options =  array(
            'conditions' => $conditionsCustomer,
        );

        if( !empty($data_action) ) {
            $customers = $this->Customer->getData('all', $options, true, array(
                'branch' => false,
                'plant' => false,
            ));
        } else {
            $options['limit'] = 20;
            $options = $this->Customer->getData('paginate', $options, true, array(
                'branch' => false,
                'plant' => false,
            ));
            $this->paginate = $options;
            $customers = $this->paginate('Customer');
        }

        $avgYear = $fromYear - 1;

        if( !empty($customers) ) {
            foreach ($customers as $key => $customer) {
                $conditions['Revenue.customer_id'] = $customer['Customer']['id'];
                $revenues = $this->Revenue->RevenueDetail->getData('all', array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Revenue' => array(
                            'Ttuj',
                        ),
                    ),
                    'group' => array(
                        'DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\')'
                    ),
                    'fields'=> array(
                        'Revenue.customer_id', 
                        'SUM(RevenueDetail.total_price_unit) as total',
                        'DATE_FORMAT(Revenue.date_revenue, \'%Y-%m\') as dt',
                    ),
                ), array(
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
                    'status' => 'all',
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
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $this->Ttuj->unBindModel(array(
            'hasMany' => array(
                'Revenue',
            )
        ));
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

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $options =  $this->Ttuj->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Ttuj', $options );

        if( !empty($data_action) ){
            $values = $this->Ttuj->getData('all', $options, array(
                'status' => 'commit',
            ));
        } else {
            $this->paginate = $this->Ttuj->getData('paginate', array_merge($options, array(
                'limit' => Configure::read('__Site.config_pagination'),
            )), array(
                'status' => 'commit',
            ));
            $values = $this->paginate('Ttuj');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');

                $value = $this->Ttuj->getSumUnit($value, $id, false, 'tgl_surat_jalan');
                $value = $this->Ttuj->Revenue->getPaid($value, $id, 'unit');
                $value = $this->Ttuj->Revenue->getPaid($value, $id, 'invoiced');
                $value = $this->Ttuj->Revenue->RevenueDetail->getToCity($value, $id);

                $values[$key] = $value;
            }
        }

        $this->RjRevenue->_callBeforeViewReportMonitoringSj($params);
        $this->MkCommon->_callBeforeViewReport($data_action, array(
            'layout_file' => array(
                'select',
                'freeze',
            ),
        ));
        $this->set(compact(
            'values', 'data_action'
        ));
    }
    // public function report_monitoring_sj_revenue( $data_action = false ) {
    //     $this->loadModel('Customer');
    //     $this->loadModel('Revenue');

    //     $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
    //     $dateFrom = date('Y-m-01');
    //     $dateTo = date('Y-m-t');

    //     $this->Ttuj->bindModel(array(
    //         'hasOne' => array(
    //             'Revenue' => array(
    //                 'className' => 'Revenue',
    //                 'foreignKey' => 'ttuj_id',
    //                 'conditions' => array(
    //                     'Revenue.status' => 1,
    //                 ),
    //             )
    //         )
    //     ), false);

    //     $options = array(
    //         'conditions' => array(
    //             'Revenue.id NOT' => NULL,
    //             'Ttuj.status' => 1,
    //             'Ttuj.is_draft' => 0,
    //             'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >=' => $dateFrom,
    //             'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <=' => $dateTo,
    //             'Ttuj.branch_id' => $allow_branch_id,
    //         ),
    //         'contain' => array(
    //             'Revenue',
    //         ),
    //         'order'=> array(
    //             'Ttuj.created' => 'DESC',
    //             'Ttuj.id' => 'DESC',
    //         ),
    //         'group' => array(
    //             'Ttuj.id'
    //         ),
    //     );
    //     $optionConditions = array(
    //         'Customer.branch_id' => $allow_branch_id,
    //     );
    //     $this->request->data['Ttuj']['date'] = sprintf('%s - %s', date('d/m/Y',strtotime($dateFrom)), date('d/m/Y',strtotime($dateTo)));

    //     if(!empty($this->params['named'])){
    //         $refine = $this->params['named'];

    //         if(!empty($refine['customer'])){
    //             $customer = urldecode($refine['customer']);
    //             $this->request->data['Ttuj']['customer'] = $customer;
    //             $options['conditions']['Ttuj.customer_id '] = $customer;
    //         }

    //         if(!empty($refine['date'])){
    //             $dateStr = urldecode($refine['date']);
    //             $date = explode('-', $dateStr);

    //             if( !empty($date) ) {
    //                 $date[0] = urldecode($date[0]);
    //                 $date[1] = urldecode($date[1]);
    //                 $dateStr = sprintf('%s-%s', $date[0], $date[1]);
    //                 $dateFrom = $this->MkCommon->getDate($date[0]);
    //                 $dateTo = $this->MkCommon->getDate($date[1]);
    //                 $options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
    //                 $options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
    //             }
    //             $this->request->data['Ttuj']['date'] = $dateStr;
    //         }

    //         if(!empty($refine['status'])){
    //             $status = urldecode($refine['status']);
    //             $this->request->data['Ttuj']['status'] = $status;
    //             $options['contain'][] = 'SuratJalanDetail';
    //             $options['contain'][] = 'SuratJalan';

    //             $this->Ttuj->unBindModel(array(
    //                 'hasMany' => array(
    //                     'SuratJalanDetail'
    //                 )
    //             ));

    //             $this->Ttuj->bindModel(array(
    //                 'hasOne' => array(
    //                     'SuratJalanDetail' => array(
    //                         'className' => 'SuratJalanDetail',
    //                         'conditions' => array(
    //                             'SuratJalanDetail.status' => 1,
    //                         ),
    //                     ),
    //                     'SuratJalan' => array(
    //                         'className' => 'SuratJalan',
    //                         'foreignKey' => false,
    //                         'conditions' => array(
    //                             'SuratJalan.id = SuratJalanDetail.surat_jalan_id',
    //                             'SuratJalan.status' => 1,
    //                             'SuratJalan.is_canceled' => 0,
    //                         ),
    //                     ),
    //                 )
    //             ), false);

    //             switch ($status) {
    //                 case 'pending':
    //                     $options['conditions']['Ttuj.status_sj'] = 'none';
    //                     // $options['conditions']['SuratJalan.id'] = NULL;
    //                     break;

    //                 // case 'hal_receipt':
    //                 //     $options['conditions']['Ttuj.status_sj'] = 'half';
    //                 //     break;

    //                 case 'receipt':
    //                     $options['conditions']['Ttuj.status_sj'] = 'full';
    //                     break;

    //                 case 'receipt_unpaid':
    //                     $this->Ttuj->Revenue->bindModel(array(
    //                         'hasOne' => array(
    //                             'SuratJalanDetail' => array(
    //                                 'className' => 'SuratJalanDetail',
    //                                 'foreignKey' => false,
    //                                 'conditions' => array(
    //                                     'SuratJalanDetail.ttuj_id = Revenue.ttuj_id',
    //                                     'SuratJalanDetail.status' => 1,
    //                                 ),
    //                             ),
    //                             'SuratJalan' => array(
    //                                 'className' => 'SuratJalan',
    //                                 'foreignKey' => false,
    //                                 'conditions' => array(
    //                                     'SuratJalan.id = SuratJalanDetail.surat_jalan_id',
    //                                     'SuratJalan.status' => 1,
    //                                     'SuratJalan.is_canceled' => 0,
    //                                 ),
    //                             ),
    //                         )
    //                     ), false);

    //                     $options['conditions']['OR'] = array(
    //                         'Ttuj.status_sj' => 'full',
    //                         'SuratJalan.id <>' => NULL,
    //                     );
    //                     $revenueConditions = !empty($options['conditions'])?$options['conditions']:false;
    //                     $revenueConditions['Revenue.transaction_status <>'] = 'invoiced';
    //                     $revenues = $this->Revenue->getData('list', array(
    //                         'conditions' => $revenueConditions,
    //                         'contain' => array(
    //                             'Ttuj',
    //                             'SuratJalan',
    //                             'SuratJalanDetail',
    //                         ),
    //                         'fields' => array(
    //                             'Revenue.id', 'Revenue.ttuj_id'
    //                         ),
    //                     ), true, array(
    //                         'status' => 'all',
    //                         'branch' => false,
    //                     ));

    //                     $options['conditions']['Ttuj.id'] = $revenues;
    //                     break;

    //                 case 'sj_receipt_paid':
    //                     $options['conditions']['Ttuj.status_sj'] = array( 'none', 'half' );
    //                     $revenueConditions = !empty($options['conditions'])?$options['conditions']:false;
    //                     $revenueConditions['Revenue.transaction_status'] = 'invoiced';
    //                     $revenues = $this->Revenue->getData('list', array(
    //                         'conditions' => $revenueConditions,
    //                         'contain' => array(
    //                             'Ttuj'
    //                         ),
    //                         'fields' => array(
    //                             'Revenue.id', 'Revenue.ttuj_id'
    //                         ),
    //                     ), true, array(
    //                         'status' => 'all',
    //                     ));

    //                     $options['conditions']['Ttuj.id'] = $revenues;
    //                     break;
    //             }
    //         }

    //         $options['conditions'] = $this->MkCommon->_callSearchNopol($options['conditions'], $refine, 'Ttuj.truck_id');
    //         $options['conditions'] = $this->MkCommon->_callRefineGenerating($options['conditions'], $refine, array(
    //             array(
    //                 'modelName' => 'Ttuj',
    //                 'fieldName' => 'city',
    //                 'conditionName' => 'Ttuj.to_city_name',
    //                 'operator' => 'LIKE',
    //             ),
    //         ));

    //         $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
    //     }

    //     if( !empty($data_action) ){
    //         $ttujs = $this->Ttuj->getData('all', $options);
    //     } else {
    //         $options['limit'] = Configure::read('__Site.config_pagination');
    //         $this->paginate = $this->Ttuj->getData('paginate', $options);
    //         $ttujs = $this->paginate('Ttuj');
    //     }

    //     if( !empty($ttujs) ) {
    //         foreach ($ttujs as $key => $ttuj) {
    //             $ttuj = $this->Ttuj->getSumUnit($ttuj, $ttuj['Ttuj']['id'], false, 'tgl_surat_jalan');
    //             $ttuj = $this->Revenue->getPaid($ttuj, $ttuj['Ttuj']['id'], 'unit');
    //             $ttuj = $this->Revenue->getPaid($ttuj, $ttuj['Ttuj']['id'], 'invoiced');
    //             $ttuj = $this->Revenue->RevenueDetail->getToCity($ttuj, $ttuj['Ttuj']['id']);
    //             $ttujs[$key] = $ttuj;
    //         }
    //     }

    //     $customerList = $this->Customer->getData('list', array(
    //         'fields' => array(
    //             'Customer.id', 'Customer.customer_name_code'
    //         ),
    //         'conditions' => $optionConditions,
    //     ), true, array(
    //         'branch' => false,
    //         'plant' => false,
    //     ));

    //     $this->set('sub_module_title', __('Laporan Monitoring Surat Jalan & Revenue'));
    //     $this->set('active_menu', 'report_monitoring_sj_revenue');
    //     $period_text = sprintf('Periode %s - %s', date('d M Y',strtotime($dateFrom)), date('d M Y',strtotime($dateTo)));
    //     $this->set('period_text', $period_text);

    //     $this->set(compact(
    //         'ttujs', 'data_action', 'customerList'
    //     ));

    //     if($data_action == 'pdf'){
    //         $this->layout = 'pdf';
    //     }else if($data_action == 'excel'){
    //         $this->layout = 'ajax';
    //     } else {
    //         $this->MkCommon->_layout_file(array(
    //             'freeze',
    //             'select',
    //         ));
    //     }
    // }

    function invoice_hso_print($id, $action_print = false){
        $this->loadModel('Invoice');
        $this->loadModel('City');

        $module_title = __('Print Invoice HSO');
        $this->set('sub_module_title', trim($module_title));
        $this->set('active_menu', 'invoices');

        if( !empty($this->params['named']) ){
            $data_print = $this->params['named']['print'];
        } else {
            $data_print = 'invoice';
        }

        if( ( $action_print == 'excel' && $data_print == 'hso' ) && !empty($this->params['named']) ) {
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
            $resultDetails = array();
            $invoice = $this->Invoice->Customer->getMerge($invoice, $invoice['Invoice']['customer_id']);
            $tarif_type = $this->MkCommon->filterEmptyField($invoice, 'Invoice', 'tarif_type');

            if( in_array($data_print, array( 'header', 'hso-yogya' )) ) {
                $invoice = $this->Invoice->InvoiceDetail->getMerge($invoice, $invoice['Invoice']['id']);
                $invoice = $this->Invoice->Company->getMerge($invoice, $invoice['Invoice']['company_id']);

                if( !empty($invoice['InvoiceDetail']) ) {
                    switch ($data_print) {
                        case 'hso-yogya':
                            if( !empty($invoice['InvoiceDetail']) ) {
                                foreach ($invoice['InvoiceDetail'] as $key => $detail) {
                                    $revenue_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_id');
                                    $revenue_detail_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_detail_id');

                                    $detail = $this->Invoice->InvoiceDetail->RevenueDetail->getMerge($detail, $revenue_detail_id);
                                    $detail = $this->Invoice->InvoiceDetail->Revenue->getMerge($detail, false, $revenue_id);

                                    $price_unit = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'price_unit');

                                    $city_id = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'city_id');
                                    $ttuj_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'ttuj_id');
                                    $truck_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'truck_id');
                                   
                                    $detail = $this->City->getMerge($detail, $city_id);
                                    $detail = $this->Ttuj->getMerge($detail, $ttuj_id);
                                    $detail = $this->Ttuj->Truck->getMerge($detail, $truck_id);
                                    
                                    $resultDetails[$price_unit][] = $detail;
                                    $invoice['InvoiceDetail'][$key] = $detail;
                                }

                                ksort($resultDetails);
                            }
                            break;
                        
                        default:
                            $revenue_id = Set::extract('/InvoiceDetail/revenue_id', $invoice['InvoiceDetail']);
                            $revenue_detail_id = Set::extract('/InvoiceDetail/revenue_detail_id', $invoice['InvoiceDetail']);
                            $invoice = $this->Invoice->InvoiceDetail->Revenue->getMerge($invoice, $id, $revenue_id, 'all', $revenue_detail_id);

                            if( !empty($invoice['Revenue']) ) {
                                foreach ($invoice['Revenue'] as $key => $revenue) {
                                    $revenue_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'id');
                                    $truck_id = $this->MkCommon->filterEmptyField($revenue, 'Revenue', 'truck_id');

                                    $revenue = $this->Invoice->InvoiceDetail->Revenue->RevenueDetail->getSumUnit($revenue, $revenue_id, 'revenue', 'RevenueDetail.revenue_id');
                                    $revenue = $this->Invoice->InvoiceDetail->Revenue->RevenueDetail->getSumUnit($revenue, $id, 'revenue_price');

                                    $revenue = $this->Ttuj->Truck->getMerge($revenue, $truck_id);
                                    
                                    $invoice['Revenue'][$key] = $revenue;
                                }
                            }
                            break;
                    }
                }
            } else {
                $revenue_detail = $this->Invoice->InvoiceDetail->Revenue->RevenueDetail->getPreviewInvoice($id, $tarif_type, $action_print, $data_print);
            }

            $tarif_angkutans = Set::extract('/InvoiceDetail/RevenueDetail/tarif_angkutan_id', $invoice);

            if( !empty($tarif_angkutans) ) {
                $tarif_angkutans = array_values($tarif_angkutans);
                $tarif_angkutan_id = array_shift($tarif_angkutans);
                $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getMerge( array(), $tarif_angkutan_id );
                $tarif_name = $this->MkCommon->filterEmptyField($tarif, 'TarifAngkutan', 'name_tarif');
            }

            $this->set(compact(
                'invoice', 'action_print', 'revenue_detail',
                'tarif_name', 'id', 'resultDetails'
            ));

            if($action_print == 'pdf'){
                $this->layout = 'pdf';
            }else if($action_print == 'excel'){
                $this->layout = 'ajax';
            }

            if( $data_print == 'invoice' || ( $action_print == 'excel' && $data_print == 'hso' ) ) {
                $this->render('invoice_hso_non_header_print');
            } else if( $data_print == 'hso-yogya' ) {
                $this->render('invoice_hso_yogya_print');
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
            'InvoicePayment.transaction_status' => 'posting',
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
                    'InvoicePayment.transaction_status' => 'posting',
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
        $options = array(
            'conditions' => array(
                'TtujPayment.type' => $action_type,
            ),
            'order' => array(
                'TtujPayment.created' => 'DESC',
                'TtujPayment.id' => 'DESC',
            ),
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
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->TtujPayment->_callRefineParams($params, $options);

        $this->paginate = $this->TtujPayment->getData('paginate', $options);
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

    function edit_ttuj_payment($action_type = 'uang_jalan_commission', $id = false){
        $module_title = __('Kas/Bank');
        $invoice = $this->Ttuj->TtujPaymentDetail->TtujPayment->getData('first', array(
            'conditions' => array(
                'TtujPayment.id' => $id,
                'TtujPayment.is_canceled' => 0,
            ),
            'contain' => array(
                'TtujPaymentDetail',
            ),
        ));

        switch ($action_type) {
            case 'biaya_ttuj':
                $this->set('active_menu', 'biaya_ttuj_payments');
                $sub_module_title = $title_for_layout = __('Edit Pembayaran Biaya TTUJ');
                break;
            
            default:
                $this->set('active_menu', 'uang_jalan_commission_payments');
                $sub_module_title = $title_for_layout = __('Edit Pembayaran Uang Jalan');
                break;
        }

        if(!empty($invoice)){
            $transaction_status = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'transaction_status');

            if( $transaction_status == 'posting' ) {
                $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
                $this->redirect($this->referer());
                die();
            }

            if( !empty($this->request->data) ) {
                $this->doTtujPayment( $action_type, $id, $invoice );
            } else {
                $this->request->data = $invoice;

                if( !empty($invoice['TtujPaymentDetail']) ) {
                    foreach ($invoice['TtujPaymentDetail'] as $key => $ttujPaymentDetail) {
                        $ttuj_id = !empty($ttujPaymentDetail['ttuj_id'])?$ttujPaymentDetail['ttuj_id']:false;
                        $dataTtujType = !empty($ttujPaymentDetail['type'])?$ttujPaymentDetail['type']:false;
                        $amount = !empty($ttujPaymentDetail['amount'])?$ttujPaymentDetail['amount']:0;
                        $resultTtuj = $this->Ttuj->getTtujPayment($ttuj_id, $dataTtujType, 'UangJalanKomisiPayment');

                        $this->request->data['Ttuj'][] = $resultTtuj;
                        $this->request->data['TtujPayment']['amount_payment'][] = $amount;
                        $this->request->data['TtujPayment']['ttuj_id'][] = $ttuj_id;
                        $this->request->data['TtujPayment']['data_type'][] = $dataTtujType;
                    }
                }
            }

            $coas = $this->User->Coa->getData('list', array(
                'fields' => array(
                    'Coa.id', 'Coa.coa_name'
                ),
            ), array(
                'status' => 'cash_bank_child',
            ));

            switch ($action_type) {
                case 'biaya_ttuj':
                    $cogs = $this->MkCommon->_callCogsOptGroup('TtujPaymentCost', 'TtujPayment');
                    break;
                
                default:
                    $cogs = $this->MkCommon->_callCogsOptGroup('TtujPayment');
                    break;
            }
            
            $this->MkCommon->getLogs($this->params['controller'], array( 'ttuj_payment_add', 'edit_ttuj_payment', 'ttuj_payment_delete' ), $id);

            $this->MkCommon->_layout_file('select');
            $this->set(compact(
                'invoice', 'sub_module_title', 'title_for_layout',
                'action_type', 'module_title', 'coas'
            ));

            $this->render('ttuj_payment_form');
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function detail_ttuj_payment($id = false, $action_type = 'uang_jalan_commission'){
        $module_title = __('Kas/Bank');
        $invoice = $this->Ttuj->TtujPaymentDetail->TtujPayment->getData('first', array(
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
            $coa_id = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'coa_id');
            $invoice = $this->Ttuj->TtujPaymentDetail->TtujPayment->getMergeList($invoice, array(
                'contain' => array(
                    'Coa',
                    'Cogs',
                ),
            ));

            if( !empty($invoice['TtujPaymentDetail']) ) {

                foreach ($invoice['TtujPaymentDetail'] as $key => $ttujPaymentDetail) {
                    $ttuj_id = !empty($ttujPaymentDetail['ttuj_id'])?$ttujPaymentDetail['ttuj_id']:false;
                    $dataTtujType = !empty($ttujPaymentDetail['type'])?$ttujPaymentDetail['type']:false;
                    $amount = !empty($ttujPaymentDetail['amount'])?$ttujPaymentDetail['amount']:0;
                    $resultTtuj = $this->Ttuj->getTtujPayment($ttuj_id, $dataTtujType, 'UangJalanKomisiPayment');

                    $invoice['Ttuj'][] = $resultTtuj;
                    $invoice['TtujPayment']['amount_payment'][] = $amount;
                    $invoice['TtujPayment']['ttuj_id'][] = $ttuj_id;
                    $invoice['TtujPayment']['data_type'][] = $dataTtujType;
                }
            }

            $this->MkCommon->getLogs($this->params['controller'], array( 'ttuj_payment_add', 'edit_ttuj_payment', 'ttuj_payment_delete' ), $id);

            $this->set('document_info', true);
            $this->set(compact(
                'invoice', 'sub_module_title', 'title_for_layout',
                'action_type', 'module_title'
            ));
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function doTtujPaymentDetail ( $dataAmount, $data, $ttuj_payment_id = false ) {
        $flagTtujPaymentDetail = true;
        $totalPayment = 0;
        $document_type = !empty($data['TtujPayment']['type'])?$data['TtujPayment']['type']:false;
        $receiver_name = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'receiver_name');
        $receiver_type = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'receiver_type');
        $date_payment = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'date_payment');
        $transaction_status = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'transaction_status');

        if( !empty($ttuj_payment_id) ) {
            $this->Ttuj->TtujPaymentDetail->updateAll( array(
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

                $dataTtuj = $this->Ttuj->getTtujPayment($ttuj_id, $data_type, 'UangJalanKomisiPayment');
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
                $total_dibayar = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, $data_type, false, array(
                    'conditions' => array(
                        'TtujPayment.transaction_status' => 'posting',
                    ),
                )) + $amount;

                if( !empty($ttuj_payment_id) ) {
                    $dataTtujPaymentDetail['TtujPaymentDetail']['ttuj_payment_id'] = $ttuj_payment_id;
                    $total = !empty($dataTtuj['total'])?$dataTtuj['total']:0;

                    if( $transaction_status == 'posting' ) {
                        if( !empty($total_dibayar) ) {
                            $flagPaidTtuj = 'half';

                            if( $total <= $total_dibayar ) {
                                $flagPaidTtuj = 'full';
                            }
                        
                            $this->Ttuj->TtujPaymentDetail->Ttuj->set('paid_'.$data_type, $flagPaidTtuj);
                            $this->Ttuj->TtujPaymentDetail->Ttuj->id = $ttuj_id;
                            
                            if( !$this->Ttuj->TtujPaymentDetail->Ttuj->save() ) {
                                $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran %s #%s'), $data_type, $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_id );
                            }
                        }
                    }
                }

                $this->Ttuj->TtujPaymentDetail->create();
                $this->Ttuj->TtujPaymentDetail->set($dataTtujPaymentDetail);

                if( !empty($ttuj_payment_id) ) {
                    if( !$this->Ttuj->TtujPaymentDetail->save() ) {
                        $flagTtujPaymentDetail = false;
                    }
                } else {
                    if( !$this->Ttuj->TtujPaymentDetail->validates() ) {
                        $flagTtujPaymentDetail = false;
                    }
                }
            }
        } else {
            $flagTtujPaymentDetail = false;
            $this->MkCommon->setCustomFlash(__('Mohon pilih biaya yang akan dibayar.'), 'error'); 
        }

        if( !empty($totalPayment) && !empty($ttuj_payment_id) ) {
            $this->Ttuj->TtujPaymentDetail->TtujPayment->id = $ttuj_payment_id;
            $this->Ttuj->TtujPaymentDetail->TtujPayment->set('total_payment', $totalPayment);

            if( !$this->Ttuj->TtujPaymentDetail->TtujPayment->save() ) {
                $this->Log->logActivity( sprintf(__('Gagal mengubah total pembayaran ttuj #%s'), $ttuj_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_payment_id );
            } else {
                $document_no = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'nodoc');
                $cogs_id = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'cogs_id');
                $coa_id = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'coa_id');
                $paidType = $this->MkCommon->filterEmptyField($this->request->data, 'TtujPayment', 'data_type');
                $paidType = $this->RjRevenue->_callReceiverType($paidType);

                if( $transaction_status == 'posting' ) {
                    switch ($document_type) {
                        case 'biaya_ttuj':
                            $titleJournalInv = sprintf(__('Pembayaran biaya %s kepada %s %s'), $paidType, $receiver_type, $receiver_name);
                            $titleJournalInv = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'description', $titleJournalInv);

                            $this->User->Journal->deleteJournal($ttuj_payment_id, array(
                                'biaya_ttuj_payment',
                            ));
                            $this->User->Journal->setJournal($totalPayment, array(
                                'credit' => $coa_id,
                                'debit' => 'biaya_ttuj_payment_coa_id',
                            ), array(
                                'cogs_id' => $cogs_id,
                                'date' => $date_payment,
                                'document_id' => $ttuj_payment_id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'biaya_ttuj_payment',
                            ));
                            break;
                        
                        default:
                            $titleJournalInv = sprintf(__('Pembayaran biaya %s'), $paidType);
                            $titleJournalInv = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'description', $titleJournalInv);

                            $this->User->Journal->deleteJournal($ttuj_payment_id, array(
                                'uang_Jalan_commission_payment',
                            ));
                            $this->User->Journal->setJournal($totalPayment, array(
                                'credit' => $coa_id,
                                'debit' => 'uang_Jalan_commission_payment_coa_id',
                            ), array(
                                'cogs_id' => $cogs_id,
                                'date' => $date_payment,
                                'document_id' => $ttuj_payment_id,
                                'title' => $titleJournalInv,
                                'document_no' => $document_no,
                                'type' => 'uang_Jalan_commission_payment',
                            ));
                            break;
                    }
                }
            }
        }

        return $flagTtujPaymentDetail;
    }

    function doTtujPayment( $action_type, $id = false, $invoice = false){
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
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'TtujPayment' => array(
                        'date_payment',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'TtujPayment', 'date_payment');

            $data['TtujPayment']['type'] = $action_type;
            $data['TtujPayment']['branch_id'] = Configure::read('__Site.config_branch_id');

            $dataAmount = $this->MkCommon->filterEmptyField($data, 'TtujPayment', 'amount_payment');
            $flagTtujPaymentDetail = $this->doTtujPaymentDetail($dataAmount, $data);

            if( !empty($id) ) {
                $this->Ttuj->TtujPaymentDetail->TtujPayment->id = $id;
            } else {
                $this->Ttuj->TtujPaymentDetail->TtujPayment->create();
            }

            $this->Ttuj->TtujPaymentDetail->TtujPayment->set($data);

            if( $this->Ttuj->TtujPaymentDetail->TtujPayment->validates() && !empty($flagTtujPaymentDetail) ){
                if($this->Ttuj->TtujPaymentDetail->TtujPayment->save()){
                    $document_id = $this->Ttuj->TtujPaymentDetail->TtujPayment->id;
                    $flagTtujPaymentDetail = $this->doTtujPaymentDetail($dataAmount, $data, $document_id);

                    $this->params['old_data'] = $invoice;
                    $this->params['data'] = $data;

                    $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Pembayaran %s #%s'), $labelName, $noref), 'success'); 
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

                if( !empty($this->Ttuj->TtujPaymentDetail->validationErrors) ) {
                    $errorPaymentDetails = $this->Ttuj->TtujPaymentDetail->validationErrors;

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
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        switch ($action_type) {
            case 'biaya_ttuj':
                $cogs = $this->MkCommon->_callCogsOptGroup('TtujPaymentCost', 'TtujPayment');
                break;
            
            default:
                $cogs = $this->MkCommon->_callCogsOptGroup('TtujPayment');
                break;
        }

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'action_type', 'coas', 'invoice'
        ));
        $this->render('ttuj_payment_form');
    }

    function ttuj_payment_delete($id, $action_type){
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $invoice = $this->Ttuj->TtujPaymentDetail->TtujPayment->getData('first', array(
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
                $this->MkCommon->_callAllowClosing($invoice, 'TtujPayment', 'date_payment');

                $receiver_name = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'receiver_name');
                $receiver_type = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'receiver_type', __('Supir'));
                $date_payment = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'date_payment');
                $transaction_status = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'transaction_status');
                $cogs_id = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'cogs_id');

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

                    $this->Ttuj->TtujPaymentDetail->TtujPayment->id = $id;
                    $this->Ttuj->TtujPaymentDetail->TtujPayment->set($data);

                    if($this->Ttuj->TtujPaymentDetail->TtujPayment->save()){
                        $document_no = !empty($invoice['TtujPayment']['nodoc'])?$invoice['TtujPayment']['nodoc']:false;
                        $coa_id = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'coa_id');
                        $paidType = array();

                        if( !empty($invoice['TtujPaymentDetail']) ) {
                            foreach ($invoice['TtujPaymentDetail'] as $key => $ttujPaymentDetail) {
                                $ttuj_id = !empty($ttujPaymentDetail['ttuj_id'])?$ttujPaymentDetail['ttuj_id']:false;
                                $data_type = !empty($ttujPaymentDetail['type'])?$ttujPaymentDetail['type']:false;
                                $total_dibayar = $this->Ttuj->TtujPaymentDetail->getTotalPayment($ttuj_id, $data_type, false, array(
                                    'conditions' => array(
                                        'TtujPayment.transaction_status' => 'posting',
                                    ),
                                ));
                                $flagPaidTtuj = 'none';
                                $paidType[] = $data_type;

                                if( $transaction_status == 'posting' ) {
                                    if( !empty($total_dibayar) ) {
                                        $flagPaidTtuj = 'half';
                                    }
                                        
                                    $this->Ttuj->set('paid_'.$data_type, $flagPaidTtuj);
                                    $this->Ttuj->id = $ttuj_id;
                                    
                                    if( !$this->Ttuj->save() ) {
                                        $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran %s #%s'), $data_type, $ttuj_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $ttuj_id );
                                    }
                                }
                            }
                        }

                        if( $transaction_status == 'posting' ) {
                            if( !empty($invoice['TtujPayment']['total_payment']) ) {
                                $paidType = $this->RjRevenue->_callReceiverType($paidType);

                                switch ($action_type) {
                                    case 'biaya_ttuj':
                                        $titleJournalInv = sprintf(__('pembayaran biaya %s kepada %s %s'), $paidType, $receiver_type, $receiver_name);
                                        $titleJournalInv = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'description', $titleJournalInv));
                                        $totalPayment = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'total_payment');

                                        $this->User->Journal->setJournal($totalPayment, array(
                                            'credit' => 'biaya_ttuj_payment_coa_id',
                                            'debit' => $coa_id,
                                        ), array(
                                            'cogs_id' => $cogs_id,
                                            'date' => $date_payment,
                                            'document_id' => $id,
                                            'title' => $titleJournalInv,
                                            'document_no' => $document_no,
                                            'type' => 'biaya_ttuj_payment_void',
                                        ));
                                        break;
                                    
                                    default:
                                        $titleJournalInv = sprintf(__('pembayaran biaya %s'), $paidType);
                                        $titleJournalInv = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'description', $titleJournalInv));
                                        $totalPayment = $this->MkCommon->filterEmptyField($invoice, 'TtujPayment', 'total_payment');

                                        $this->User->Journal->setJournal($totalPayment, array(
                                            'credit' => 'uang_Jalan_commission_payment_coa_id',
                                            'debit' => $coa_id,
                                        ), array(
                                            'cogs_id' => $cogs_id,
                                            'date' => $date_payment,
                                            'document_id' => $id,
                                            'title' => $titleJournalInv,
                                            'document_no' => $document_no,
                                            'type' => 'uang_Jalan_commission_payment_void',
                                        ));
                                        break;
                                }
                            }
                        }

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil menghapus pembayaran %s #%s'), $labelName, $noref),
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
                                $this->loadModel('City');
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
                                        $truck = $this->Ttuj->Truck->find('first', array(
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
                                        $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, false, $customer_id, $truck_capacity, false );
                                        $jenis_tarif = !empty($tarif['jenis_unit'])?$tarif['jenis_unit']:'per_unit';
                                        $ppn = !empty($ppn)?$this->MkCommon->convertPriceToString($ppn):0;
                                        $pph = !empty($pph)?$this->MkCommon->convertPriceToString($pph):0;
                                        $dataRevenue = array();

                                        if( !empty($tarif) ) {
                                            $i = 1;
                                            $idx = 0;
                                            $flag = true;

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

                                                    $tarif_detail = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, $to_city_id_detail, $customer_id, $truck_capacity, $group_motor_id );

                                                    if( !empty($tarif_detail) ) {
                                                        $total_tarif_detail = !empty($tarif_detail['tarif'])?$tarif_detail['tarif']:0;
                                                        $tarif_angkutan_id = !empty($tarif_detail['tarif_angkutan_id'])?$tarif_detail['tarif_angkutan_id']:false;
                                                        $tarif_angkutan_type = !empty($tarif_detail['tarif_angkutan_type'])?$tarif_detail['tarif_angkutan_type']:false;
                                                        $jenis_tarif_detail = !empty($tarif_detail['jenis_unit'])?$tarif_detail['jenis_unit']:false;
                                                        $total_price_unit = $this->MkCommon->getChargeTotal( $total_price_unit, $total_tarif_detail, $jenis_tarif_detail, $is_charge_detail );

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
                                                'ppn' => $ppn,
                                                'pph' => $pph,
                                                'revenue_tarif_type' => $jenis_tarif,
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

    public function import_by_ttuj( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/revenues_by_ttuj.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', 'Revenue');
            $this->set('active_menu', 'revenues');
            $this->set('sub_module_title', __('Import Revenue By TTUJ'));

            if(!empty($this->request->data)) { 
                $targetdir = $this->MkCommon->_import_excel( $this->request->data );

                if( !empty($targetdir) ) {
                    $xls_files = glob( $targetdir );

                    if(empty($xls_files)) {
                        $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                        $this->redirect(array(
                            'action'=>'import_by_ttuj'
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
                                $this->loadModel('City');
                                $this->loadModel('GroupMotor');
                                $this->loadModel('Revenue');
                                $data = $dataimport;
                                $row_submitted = 0;
                                $successfull_row = 0;
                                $failed_row = 0;
                                $error_message = '';
                                $uniqcode = String::uuid();

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
                                        $no_ttuj = !empty($no_ttuj)?$no_ttuj:false;

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
                                        $truck = $this->Ttuj->Truck->find('first', array(
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
                                        $tgl_revenue = $this->MkCommon->getDate($tgl_revenue);
                                        $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, false, $customer_id, $truck_capacity, false );
                                        $jenis_tarif = !empty($tarif['jenis_unit'])?$tarif['jenis_unit']:'per_unit';
                                        $ppn = !empty($ppn)?$this->MkCommon->convertPriceToString($ppn):0;
                                        $pph = !empty($pph)?$this->MkCommon->convertPriceToString($pph):0;
                                        $dataRevenue = array();

                                        $conditionsTtuj = array(
                                            'Ttuj.truck_id' => $truck_id,
                                            'Ttuj.customer_id' => $customer_id,
                                            'Ttuj.branch_id' => $branch_id,
                                            'Ttuj.from_city_id' => $from_city_id,
                                            'Ttuj.to_city_id' => $to_city_id,
                                            // 'Ttuj.is_revenue' => 0,
                                            'Ttuj.is_draft' => 0,
                                            'Ttuj.status' => 1,
                                        );

                                        if( !empty($no_ttuj) ) {
                                            $conditionsTtuj['Ttuj.no_ttuj'] = $no_ttuj;
                                        } else {
                                            $conditionsTtuj['Ttuj.ttuj_date'] = $tgl_revenue;
                                        }

                                        $ttuj = $this->Ttuj->getData('first', array(
                                            'conditions' => $conditionsTtuj,
                                        ), true);
                                        $ttuj_id = Common::hashEmptyField($ttuj, 'Ttuj.id');

                                        if( !empty($tarif) ) {
                                            $i = 1;
                                            $idx = 0;
                                            $flag = true;

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

                                                    $tarif_detail = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, $to_city_id_detail, $customer_id, $truck_capacity, $group_motor_id );

                                                    if( !empty($tarif_detail) ) {
                                                        $total_tarif_detail = !empty($tarif_detail['tarif'])?$tarif_detail['tarif']:0;
                                                        $tarif_angkutan_id = !empty($tarif_detail['tarif_angkutan_id'])?$tarif_detail['tarif_angkutan_id']:false;
                                                        $tarif_angkutan_type = !empty($tarif_detail['tarif_angkutan_type'])?$tarif_detail['tarif_angkutan_type']:false;
                                                        $jenis_tarif_detail = !empty($tarif_detail['jenis_unit'])?$tarif_detail['jenis_unit']:false;
                                                        $total_price_unit = $this->MkCommon->getChargeTotal( $total_price_unit, $total_tarif_detail, $jenis_tarif_detail, $is_charge_detail );

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
                                                'date_revenue' => $tgl_revenue,
                                                'customer_id' => $customer_id,
                                                'truck_id' => $truck_id,
                                                'truck_capacity' => $truck_capacity,
                                                'from_city_id' => $from_city_id,
                                                'to_city_id' => $to_city_id,
                                                'ppn' => $ppn,
                                                'pph' => $pph,
                                                'revenue_tarif_type' => $jenis_tarif,
                                                'branch_id' => Configure::read('__Site.config_branch_id'),
                                                'import_code' => $uniqcode,
                                            );

                                            if( !empty($ttuj_id) ) {
                                                $dataRevenue['Revenue']['ttuj_id'] = $ttuj_id;
                                            }

                                            if( !empty($dataRevenue['RevenueDetail']) && empty($tarifNotFound) ) {
                                                $resultSave = $this->Revenue->saveRevenue(false, false, $dataRevenue, $this, true);
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
                    
                    if(!empty($error_message)) {
                        $this->MkCommon->setCustomFlash(__($error_message), 'error');
                    }

                    if(!empty($successfull_row)) {
                        $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted);
                        $this->MkCommon->setCustomFlash(__($message_import1), 'success');
                        $this->redirect(array(
                            'action'=>'import_view',
                            $uniqcode,
                        ));
                    } else {
                        $this->redirect(array('action'=>'import_by_ttuj'));
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array(
                        'action'=>'import_by_ttuj'
                    ));
                }
            }
        }
    }

    function import_view( $code = null ){
        $this->loadModel('Revenue');
        $this->loadModel('City');

        $this->set('active_menu', 'revenues');
        $this->set('sub_module_title', __('Preview Import'));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Revenue->_callRefineParams($params, array(
            'conditions' => array(
                'Revenue.import_code' => $code,
            ),
            'contain' => array(
                'Ttuj',
            ),
            'order' => array(
                'Revenue.id' => 'ASC',
            ),
        ));

        $this->paginate = $this->Revenue->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $revenues = $this->paginate('Revenue');

        if(!empty($revenues)){
            foreach ($revenues as $key => &$value) {
                $id = Common::hashEmptyField($value, 'Revenue.id');
                $customer_id = Common::hashEmptyField($value, 'Revenue.customer_id');

                if( empty($value['Revenue']['ttuj_id']) ) {
                    $from_city_id = !empty($value['Revenue']['from_city_id'])?$value['Revenue']['from_city_id']:false;
                    $to_city_id = !empty($value['Revenue']['to_city_id'])?$value['Revenue']['to_city_id']:false;
                    $truck_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'truck_id');

                    $value = $this->City->getMerge($value, $from_city_id, 'FromCity');
                    $value = $this->City->getMerge($value, $to_city_id, 'ToCity');
                    $value = $this->Ttuj->Truck->getMerge($value, $truck_id);
                } else {
                    $ttuj_id = $value['Revenue']['ttuj_id'];
                    $value['ttuj_unit'] = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );
                }

                if( empty($customer_id) ) {
                    $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                }

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->Revenue->RevenueDetail->getSumUnit($value, $id, 'revenue', 'RevenueDetail.revenue_id');
            }

            $this->set(compact(
                'revenues', 'code'
            ));
        } else {
            $this->MkCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
        }
    }

    function import_toggle( $id = null, $code = null ){
        $this->loadModel('Revenue');

        $conditions = array(
            'Revenue.id' => $id,
            'Revenue.import_code' => $code,
        );
        $value = $this->Revenue->getData('first', array(
            'conditions' => $conditions,
        ), true, array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($value)){
            $flag = $this->Revenue->deleteAll($conditions);

            if( !empty($flag) ) {
                $this->MkCommon->redirectReferer(__('Revenue berhasil dihapus'), 'success');
            } else {
                $this->MkCommon->redirectReferer(__('Gagal menghapus Revenue. silahkan coba kembali'), 'error');
            }
        } else {
            $this->MkCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
        }
    }

    function process_import_by_ttuj( $code = null ){
        $this->loadModel('Revenue');

        $values = $this->Revenue->getData('all', array(
            'conditions' => array(
                'Revenue.import_code' => $code,
            ),
            'order'=> array(
                'Revenue.id' => 'ASC',
            ),
        ), true, array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($values)){
            $error_message = '';
            $success_message = '';
            $error = false;

            foreach ($values as $key => $value) {
                $value = $this->Revenue->getMergeList($value, array(
                    'contain' => array(
                        'RevenueDetail',
                    ),
                ));

                $noref = Common::hashEmptyField($value, 'Revenue.id');
                $dataRevenue['Revenue'] = Common::hashEmptyField($value, 'Revenue');
                $details = Common::hashEmptyField($value, 'RevenueDetail');

                if( !empty($details) ) {
                    foreach ($details as $idx => $detail) {
                        $dataRevenue['RevenueDetail']['city_id'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.city_id');
                        $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.tarif_angkutan_id');
                        $dataRevenue['RevenueDetail']['tarif_angkutan_type'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.tarif_angkutan_type');
                        $dataRevenue['RevenueDetail']['no_do'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.no_do');
                        $dataRevenue['RevenueDetail']['no_sj'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.no_sj');
                        $dataRevenue['RevenueDetail']['group_motor_id'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.group_motor_id');
                        $dataRevenue['RevenueDetail']['qty_unit'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.qty_unit');
                        $dataRevenue['RevenueDetail']['payment_type'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.payment_type');
                        $dataRevenue['RevenueDetail']['is_charge'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.is_charge');
                        $dataRevenue['RevenueDetail']['price_unit'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.price_unit');
                        $dataRevenue['RevenueDetail']['total_price_unit'][$idx] = Common::hashEmptyField($detail, 'RevenueDetail.total_price_unit');
                    }
                }

                $dataRevenue['Revenue']['import_code'] = 0;
                $resultSave = $this->Revenue->saveRevenue(false, false, $dataRevenue, $this, true);
                $statusSave = !empty($resultSave['status'])?$resultSave['status']:false;
                $msgSave = !empty($resultSave['msg'])?$resultSave['msg']:false;
                
                if( $statusSave == 'error' ) {
                    $error_message .= __('Gagal menyimpan noref %s : %s<br>', $noref, $msgSave);
                    $error = true;
                } else {
                    $success_message .= __('Berhasil menyimpan noref %s<br>');
                }
            }

            if( !empty($error) ) {
                $this->MkCommon->setCustomFlash($error_message, 'error');
                $this->MkCommon->setCustomFlash($success_message, 'success');

                $this->redirect(array(
                    'action' => 'import_by_ttuj',
                ));
            } else {
                $this->MkCommon->redirectReferer(__('Revenue berhasil disimpan'), 'success', array(
                    'action' => 'index',
                ));   
            }
        } else {
            $this->MkCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
        }
    }

    public function invoice_report_detail( $id = false, $data_action = false ) {
        $this->loadModel('Invoice');
        $customer = $this->Ttuj->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $id,
            ),
        ), array(
            'plant' => false,
            'branch' => false,
        ));

        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        
        if( !empty($customer) ) {
            $name = $this->MkCommon->filterEmptyField($customer, 'Customer', 'code');
            $options = array(
                'conditions' => array(
                    'Invoice.paid' => 0,
                    'Invoice.customer_id' => $id,
                ),
                'order' => array(
                    'Invoice.due_invoice' => 'ASC',
                    'Invoice.id' => 'ASC',
                ),
                'limit' => Configure::read('__Site.config_pagination'),
            );

            $params = $this->MkCommon->_callRefineParams($this->params, array(
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ));
            $options =  $this->Invoice->_callRefineParams($params, $options);

            $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
            $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');

            $this->Invoice->virtualFields['expired_date'] = 'DATE_ADD(Invoice.invoice_date, INTERVAL Invoice.due_invoice DAY)';
            $this->paginate = $this->Invoice->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $values = $this->paginate('Invoice');
            $sub_module_title = sprintf(__('Account Receivable Aging - %s'), $name);

            if( !empty($dateFrom) && !empty($dateTo) ) {
                $periode = $this->MkCommon->getCombineDate($dateFrom, $dateTo);
            }

            $this->set('active_menu', 'invoice_reports');
            $this->set(compact(
                'sub_module_title', 'values',
                'data_action', 'id', 'periode'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->MkCommon->redirectReferer(__('Customer tidak ditemukan'), 'error');
        }
    }

    public function report_ttuj_payment( $data_action = false ) {
        $this->loadModel('TtujPaymentDetail');
        $this->loadModel('City');
        $module_title = __('Laporan Pembayaran Biaya Uang Jalan');
        $values = array();
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

        $this->set('sub_module_title', $module_title);
        $options =  $this->TtujPaymentDetail->TtujPayment->getData('paginate', array(
            'conditions' => array(
                'TtujPayment.is_canceled' => 0,
                'TtujPaymentDetail.status' => 1,
                'TtujPayment.branch_id' => $allow_branch_id,
                'TtujPayment.transaction_status' => 'posting',
            ),
            'contain' => array(
                'TtujPayment',
                'Ttuj',
            ),
            'order' => array(
                'TtujPayment.nodoc' => 'ASC',
                'TtujPayment.id' => 'ASC',
            ),
        ), true, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->TtujPaymentDetail->TtujPayment->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'TtujPayment', $options );
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $values = $this->TtujPaymentDetail->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('TtujPaymentDetail');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'TtujPayment', 'id');
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'TtujPaymentDetail', 'ttuj_id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'TtujPayment', 'branch_id');
                $type = $this->MkCommon->filterEmptyField($value, 'TtujPaymentDetail', 'type');

                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->TtujPaymentDetail->TtujPayment->_callTtujPaid($value, $ttuj_id, $type, array(
                    'conditions' => array(
                        'TtujPayment.id' => $id,
                    ),
                ));
                
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $values[$key] = $value;
            }
        }

        $cities = $this->City->getListCities();

        $this->set('active_menu', 'report_ttuj_payment');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'cities'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }

    public function report_ttuj_outstanding( $data_action = false ) {
        $this->loadModel('TtujOutstanding');
        $this->loadModel('City');

        $module_title = __('Laporan Saldo Biaya Uang Jalan');
        $values = array();
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $this->set('sub_module_title', $module_title);
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->TtujOutstanding->_callRefineParams($params, array(
            'conditions' => array(
                'TtujOutstanding.branch_id' => $allow_branch_id,
            ),
        ));

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'TtujOutstanding', $options );
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
        }

        $this->paginate = $options;
        $values = $this->paginate('TtujOutstanding');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'TtujOutstanding', 'id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'TtujOutstanding', 'branch_id');
                $customer_id = $this->MkCommon->filterEmptyField($value, 'TtujOutstanding', 'customer_id');
                $data_type = $this->MkCommon->filterEmptyField($value, 'TtujOutstanding', 'data_type');
                $driver_id = $this->MkCommon->filterEmptyField($value, 'TtujOutstanding', 'driver_id');
                $driver_pengganti_id = $this->MkCommon->filterEmptyField($value, 'TtujOutstanding', 'driver_pengganti_id');

                $value = $this->Ttuj->TtujPaymentDetail->TtujPayment->_callTtujPaid($value, $id, $data_type);

                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->Ttuj->Driver->getMerge($value, $driver_id);
                $value = $this->Ttuj->Driver->getMerge($value, $driver_pengganti_id, 'DriverPengganti');

                $values[$key] = $value;
            }
        }

        $cities = $this->City->getListCities();

        $this->set('active_menu', 'report_ttuj_outstanding');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'cities'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }

    public function report_revenue_period( $data_action = false ) {
        $this->loadModel('City');
        $module_title = __('Laporan Detail Revenue per Priode');
        $values = array();
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $this->set('sub_module_title', $module_title);
        
        $options =  $this->Ttuj->Revenue->getData('paginate', array(
            'conditions' => array(
                'RevenueDetail.status' => 1,
            ),
            'contain' => array(
                'Revenue',
            ),
            'group' => array(
                'RevenueDetail.revenue_id',
            ),
        ), true, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->Ttuj->Revenue->RevenueDetail->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Revenue', $options );
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $values = $this->Ttuj->Revenue->RevenueDetail->find('all', $options);
        } else {
            $this->loadModel('RevenueDetail');
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('RevenueDetail');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'branch_id');
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'ttuj_id');
                $value = $this->Ttuj->getMerge($value, $ttuj_id);
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                
                $value['Ttuj']['total_qty'] = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );

                $invoice_id = $this->Ttuj->Revenue->RevenueDetail->getData('list', array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $id,
                        'RevenueDetail.status' => 1,
                    ),
                    'fields' => array(
                        'RevenueDetail.invoice_id', 'RevenueDetail.invoice_id',
                    ),
                    'group' => array(
                        'RevenueDetail.revenue_id',
                        'RevenueDetail.invoice_id',
                    ),
                ), array(
                    'branch' => false,
                ));

                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'customer_id', $customer_id);

                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'truck_id', $truck_id);
                $value = $this->Ttuj->Truck->getMerge($value, $truck_id);

                $from_city_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'from_city_id');
                $to_city_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'to_city_id');
                $from_city_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'from_city_id', $from_city_id);
                $to_city_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'to_city_id', $to_city_id);

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->Ttuj->Revenue->RevenueDetail->getSumUnit($value, $id, 'revenue', 'RevenueDetail.revenue_id');
                $value = $this->Ttuj->Revenue->RevenueDetail->Invoice->getMerge($value, $invoice_id, 'all');
                
                $value = $this->City->getMerge($value, $from_city_id, 'FromCity');
                $value = $this->City->getMerge($value, $to_city_id, 'ToCity');

                $values[$key] = $value;
            }
        }

        $cities = $this->City->getListCities();
        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('active_menu', 'report_revenue_period');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'cities', 'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file('select');
        }
    }

    public function report_revenue( $data_action = false ) {
        $this->loadModel('City');
        $module_title = __('Laporan Detail Revenue');
        $values = array();
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $this->set('sub_module_title', $module_title);
        $options =  $this->Ttuj->Revenue->getData('paginate', array(
            'conditions' => array(
                'RevenueDetail.status' => 1,
            ),
            'contain' => array(
                'Revenue',
            ),
        ), true, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->Ttuj->Revenue->RevenueDetail->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Revenue', $options );
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $values = $this->Ttuj->Revenue->RevenueDetail->find('all', $options);
        } else {
            $this->loadModel('RevenueDetail');
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('RevenueDetail');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'id');
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'ttuj_id');
                $invoice_id = $this->MkCommon->filterEmptyField($value, 'RevenueDetail', 'invoice_id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'branch_id');
                
                $value = $this->Ttuj->getMerge($value, $ttuj_id);
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);

                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'customer_id', $customer_id);

                $from_city_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'from_city_id');
                $from_city_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'from_city_id', $from_city_id);
                
                $to_city_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'to_city_id');
                $city_id = $this->MkCommon->filterEmptyField($value, 'RevenueDetail', 'city_id', $to_city_id);

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->Ttuj->Revenue->RevenueDetail->Invoice->getMerge($value, $invoice_id);
                $value = $this->Ttuj->Revenue->RevenueDetail->City->getMerge($value, $city_id, 'ToCity');
                $value = $this->City->getMerge($value, $from_city_id, 'FromCity');

                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Revenue', 'truck_id', $truck_id);
                $value = $this->Ttuj->Truck->getMerge($value, $truck_id);

                $values[$key] = $value;
            }
        }

        $cities = $this->City->getListCities();
        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('active_menu', 'report_revenue');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'cities', 'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file('select');
        }
    }

    public function report_expense_per_truck( $data_action = false ) {
        $this->loadModel('Truck');

        $module_title = __('Laporan Expense Revenue per Truk');
        $values = array();
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $this->set('sub_module_title', $module_title);
        $options =  $this->Truck->getData('paginate', false, true, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->Truck->_callRefineParams($params, $options);

        if( !empty($data_action) ){
            $values = $this->Truck->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('Truck');
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Truck', 'id');
                $truck_category_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'truck_category_id');
                $truck_brand_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'truck_brand_id');

                $value = $this->Truck->TruckCategory->getMerge($value, $truck_category_id);
                $value = $this->Truck->TruckBrand->getMerge($value, $truck_brand_id);
                $value = $this->Truck->TruckCustomer->getFirst($value, $id);
                $value = $this->Ttuj->Revenue->getTotal($value, $id, $params);
                $value = $this->Ttuj->getBiayaUangJalan($value, $id, $params);
                $value = $this->Truck->getBiayaLainLain($value, $id, $params);

                $values[$key] = $value;
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('active_menu', 'report_expense_per_truck');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file('select');
        }
    }

    function generateRevenue () {
        $revenues = $this->Ttuj->Revenue->RevenueDetail->find('all', array(
            'conditions' => array(
                'Revenue.total' => 0,
                'Revenue.pph' => 0,
                'Revenue.ppn' => 0,
                'Revenue.revenue_tarif_type' => 'per_unit',
                'RevenueDetail.status' => 1,
                'RevenueDetail.total_price_unit <>' => 0,
            ),
            'contain' => array(
                'Revenue',
            ),
            'group' => array(
                'RevenueDetail.revenue_id',
            ),
            // 'limit' => 5,
        ));

        if( !empty($revenues) ) {
            foreach ($revenues as $key => $value) {
                $id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'id' );

                $this->Ttuj->Revenue->RevenueDetail->virtualFields['total'] = 'SUM(RevenueDetail.total_price_unit)';
                $detail = $this->Ttuj->Revenue->RevenueDetail->find('first', array(
                    'conditions' => array(
                        'RevenueDetail.status' => 1,
                        'RevenueDetail.revenue_id' => $id,
                    ),
                    'group' => array(
                        'RevenueDetail.revenue_id',
                    ),
                ));
                $total = $this->MkCommon->filterEmptyField( $detail, 'RevenueDetail', 'total' );

                $this->Ttuj->Revenue->id = $id;
                $this->Ttuj->Revenue->set('total', $total);
                $this->Ttuj->Revenue->set('total_without_tax', $total);
                $this->Ttuj->Revenue->save();
            }
        }

        die();
    }

    public function generate_tarif_angkut () {
        $revenues = $this->Ttuj->Revenue->InvoiceDetail->find('list', array(
            'conditions' => array(
                'InvoiceDetail.status' => 1,
                'Invoice.status' => 1,
            ),
            'fields' => array(
                'InvoiceDetail.id', 'InvoiceDetail.revenue_id',
            ),
            'contain' => array(
                'Invoice',
            ),
            'group' => array(
                'InvoiceDetail.revenue_id',
            ),
        ));
        $values = $this->Ttuj->Revenue->RevenueDetail->find('all', array(
            'conditions' => array(
                'Revenue.id NOT' => $revenues,
                'RevenueDetail.status' => 1,
                'Revenue.status' => 1,
                'revenue_tarif_type <>' => 'per_truck',
            ),
            'contain' => array(
                'Revenue',
            ),
            'offset' => 0,
            'limit' => 500,
        ));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $revenue_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'id' );
                $revenue_detail_id = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'id' );
                $customer_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'customer_id' );
                $ttuj_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'ttuj_id' );
                $branch_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'branch_id' );
                $revenue_tarif_type = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'revenue_tarif_type' );

                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Ttuj->getMerge($value, $ttuj_id);
                $rev_cnt = $this->Ttuj->Revenue->RevenueDetail->find('count', array(
                    'conditions' => array(
                        'RevenueDetail.status' => 1,
                        'RevenueDetail.revenue_id' => $revenue_id,
                    ),
                ));
                $branch = $this->MkCommon->filterEmptyField( $value, 'Branch', 'name' );

                $from_city_id = $this->MkCommon->filterEmptyField( $value, 'Ttuj', 'from_city_id' );
                $to_city_id = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'city_id' );
                $price_unit = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'price_unit' );
                $qty_unit = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'qty_unit' );
                $total_price_unit = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'total_price_unit' );

                $group_motor_id = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'group_motor_id' );
                $truck_capacity = $this->MkCommon->filterEmptyField( $value, 'Ttuj', 'truck_capacity' );
                
                $value = $this->Ttuj->Revenue->RevenueDetail->GroupMotor->getMerge( $value, $group_motor_id );
                $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->findTarif($from_city_id, $to_city_id, $customer_id, $truck_capacity, $group_motor_id);
                $group_motor = $this->MkCommon->filterEmptyField( $value, 'GroupMotor', 'name' );

                if( !empty($tarif) ) {
                    $tarif_angkutan = $this->MkCommon->filterEmptyField( $tarif, 'tarif' );
                    $jenis_unit = $this->MkCommon->filterEmptyField( $tarif, 'jenis_unit' );

                    if( $jenis_unit == 'per_truck' ) {
                        $price_unit = $total_price_unit;
                    }

                    if( $tarif_angkutan != $price_unit ) {
                        $journals = $this->User->Journal->find('all', array(
                            'conditions' => array(
                                'Journal.status' => 1,
                                'Journal.type' => 'revenue',
                                'Journal.document_id' => $revenue_id,
                            ),
                        ));

                        $this->Ttuj->Revenue->RevenueDetail->id = $revenue_detail_id;

                        if( $jenis_unit == 'per_truck' ) {
                            $total_price_unit = $tarif_angkutan;
                        } else {
                            $total_price_unit = $tarif_angkutan * $qty_unit;
                            $this->Ttuj->Revenue->RevenueDetail->set('price_unit', $tarif_angkutan);
                        }

                        $this->Ttuj->Revenue->RevenueDetail->set('payment_type', $jenis_unit);
                        $this->Ttuj->Revenue->RevenueDetail->set('total_price_unit', $total_price_unit);
                        $this->Ttuj->Revenue->RevenueDetail->save();

                        $this->Ttuj->Revenue->RevenueDetail->virtualFields['total'] = 'SUM(RevenueDetail.total_price_unit)';
                        $rev_calc = $this->Ttuj->Revenue->RevenueDetail->find('first', array(
                            'conditions' => array(
                                'RevenueDetail.status' => 1,
                                'RevenueDetail.revenue_id' => $revenue_id,
                            ),
                        ));
                        $total_revenue = $this->MkCommon->filterEmptyField( $rev_calc, 'RevenueDetail', 'total' );

                        $this->Ttuj->Revenue->id = $revenue_id;
                        $this->Ttuj->Revenue->set('total', $total_revenue);
                        $this->Ttuj->Revenue->set('total_without_tax', $total_revenue);
                        $this->Ttuj->Revenue->save();

                        if( !empty($journals) ) {
                            foreach ($journals as $key => $journal) {
                                $journal_id = $this->MkCommon->filterEmptyField( $journal, 'Journal', 'id' );
                                $debit = $this->MkCommon->filterEmptyField( $journal, 'Journal', 'debit' );
                                $credit = $this->MkCommon->filterEmptyField( $journal, 'Journal', 'credit' );

                                $this->User->Journal->id = $journal_id;

                                if( !empty($credit) ) {
                                    $this->User->Journal->set('credit', $total_revenue);
                                } else if( !empty($debit) ) {
                                    $this->User->Journal->set('debit', $total_revenue);
                                }

                                $this->User->Journal->save();
                            }
                        }

                        echo sprintf('Tarif: %s <br>', $tarif_angkutan);
                        echo sprintf('Branch: %s <br>', $branch);
                        echo sprintf('Rev ID: %s <br>', $revenue_id);
                        echo sprintf('Rev Detail ID: %s <br>', $revenue_detail_id);
                        echo sprintf('Group Motor: %s <br>', $group_motor);
                        echo sprintf('Detail cnt: %s <br>', $rev_cnt);
                        // echo sprintf('Total: %s <br>', $total_revenue);
                        echo sprintf('Tipe Tarif: %s <br> <br>', $revenue_tarif_type);
                        // debug($rev_calc);die();
                    }
                }
            }
        }
        die();
    }

    function invoice_yamaha_rit($id = false, $action_print = false){
        $this->loadModel('Invoice');

        $module_title = __('Print Yamaha Per RIT');
        $this->set('sub_module_title', trim($module_title));
        $this->set('active_menu', 'invoices');

        $data_print = $this->MkCommon->filterEmptyField($this->params, 'named', 'print', 'default');

        $value = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $customer_id = $this->MkCommon->filterEmptyField($value, 'Invoice', 'customer_id');
            $billing_id = $this->MkCommon->filterEmptyField($value, 'Invoice', 'billing_id');
            $tarif_type = $this->MkCommon->filterEmptyField($value, 'Invoice', 'tarif_type');

            $value = $this->Invoice->Customer->getMerge($value, $customer_id);
            $value = $this->Invoice->InvoiceDetail->getMerge($value, $id);
            $value = $this->User->getMerge($value, $billing_id);

            $employe_position_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'employe_position_id');
            $value = $this->User->Employe->EmployePosition->getMerge($value, $employe_position_id);

            $invDetails = $this->MkCommon->filterEmptyField($value, 'InvoiceDetail');

            if( !empty($invDetails) ) {
                foreach ($invDetails as $idx => $detail) {
                    $revenue_detail_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_detail_id');
                    $revenue_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_id');
                    
                    $detail = $this->Invoice->InvoiceDetail->RevenueDetail->getMerge($detail, $revenue_detail_id);
                    $detail = $this->Invoice->InvoiceDetail->Revenue->getMerge($detail, false, $revenue_id);

                    $ttuj_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'ttuj_id');
                    $truck_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'truck_id');

                    $detail = $this->Ttuj->getMerge($detail, $ttuj_id);
                    $detail = $this->Ttuj->Truck->getMerge($detail, $truck_id);

                    $invDetails[$idx] = $detail;
                }
            }

            $this->loadModel('Setting');
            $setting = $this->Setting->find('first');

            $this->set(compact(
                'value', 'action_print', 'invDetails',
                'setting'
            ));

            if($action_print == 'pdf'){
                $this->layout = 'pdf';
            }else if($action_print == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
            $this->redirect($this->referer());
        }
    }

    function invoice_yamaha_unit($id = false, $action_print = false){
        $this->loadModel('Invoice');
        $this->loadModel('City');

        $module_title = __('Print Yamaha Per Unit');
        $this->set('sub_module_title', trim($module_title));
        $this->set('active_menu', 'invoices');

        $data_print = $this->MkCommon->filterEmptyField($this->params, 'named', 'print', 'default');

        $value = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $customer_id = $this->MkCommon->filterEmptyField($value, 'Invoice', 'customer_id');
            $billing_id = $this->MkCommon->filterEmptyField($value, 'Invoice', 'billing_id');
            $tarif_type = $this->MkCommon->filterEmptyField($value, 'Invoice', 'tarif_type');

            $value = $this->Invoice->Customer->getMerge($value, $customer_id);
            $value = $this->Invoice->InvoiceDetail->getMerge($value, $id);
            $value = $this->User->getMerge($value, $billing_id);
            
            $employe_position_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'employe_position_id');
            $value = $this->User->Employe->EmployePosition->getMerge($value, $employe_position_id);

            $invDetails = $this->MkCommon->filterEmptyField($value, 'InvoiceDetail');

            if( !empty($invDetails) ) {
                foreach ($invDetails as $idx => $detail) {
                    $revenue_detail_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_detail_id');
                    $revenue_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_id');
                    
                    $detail = $this->Invoice->InvoiceDetail->RevenueDetail->getMerge($detail, $revenue_detail_id);
                    $detail = $this->Invoice->InvoiceDetail->Revenue->getMerge($detail, false, $revenue_id);

                    $city_id = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'city_id');
                    $group_motor_id = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'group_motor_id');
                    $ttuj_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'ttuj_id');
                    $truck_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'truck_id');

                    $detail = $this->City->getMerge($detail, $city_id);
                    $detail = $this->Ttuj->Truck->getMerge($detail, $truck_id);
                    $detail = $this->Ttuj->getMerge($detail, $ttuj_id);
                    $detail = $this->Ttuj->Revenue->RevenueDetail->GroupMotor->getMerge( $detail, $group_motor_id );

                    $invDetails[$idx] = $detail;
                }
            }

            $this->loadModel('Setting');
            $setting = $this->Setting->find('first');

            $this->set(compact(
                'value', 'action_print', 'invDetails',
                'setting'
            ));

            if($action_print == 'pdf'){
                $this->layout = 'pdf';
            }else if($action_print == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
            $this->redirect($this->referer());
        }
    }

    function invoice_nozomi_unit($id = false, $action_print = false){
        $this->loadModel('Invoice');
        $this->loadModel('City');

        $data_print = $this->MkCommon->filterEmptyField($this->params, 'named', 'print', 'default');

        $value = $this->Invoice->getData('first', array(
            'conditions' => array(
                'Invoice.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $customer_id = $this->MkCommon->filterEmptyField($value, 'Invoice', 'customer_id');
            $billing_id = $this->MkCommon->filterEmptyField($value, 'Invoice', 'billing_id');
            $tarif_type = $this->MkCommon->filterEmptyField($value, 'Invoice', 'tarif_type');

            $value = $this->Invoice->Customer->getMerge($value, $customer_id);
            $value = $this->Invoice->InvoiceDetail->getMerge($value, $id);
            $value = $this->User->getMerge($value, $billing_id);

            $invDetails = $this->MkCommon->filterEmptyField($value, 'InvoiceDetail');

            if( !empty($invDetails) ) {
                foreach ($invDetails as $idx => $detail) {
                    $revenue_detail_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_detail_id');
                    $revenue_id = $this->MkCommon->filterEmptyField($detail, 'InvoiceDetail', 'revenue_id');
                    
                    $detail = $this->Invoice->InvoiceDetail->RevenueDetail->getMerge($detail, $revenue_detail_id);
                    $detail = $this->Invoice->InvoiceDetail->Revenue->getMerge($detail, false, $revenue_id);
                    $city_id = $this->MkCommon->filterEmptyField($detail, 'RevenueDetail', 'city_id');

                    $ttuj_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'ttuj_id');
                    $detail = $this->Ttuj->getMerge($detail, $ttuj_id);
                    $detail = $this->City->getMerge($detail, $city_id);

                    $truck_id = $this->MkCommon->filterEmptyField($detail, 'Revenue', 'truck_id');
                    $detail = $this->Ttuj->Truck->getMerge($detail, $truck_id);

                    $truck_category_id = $this->MkCommon->filterEmptyField($detail, 'Truck', 'truck_category_id');
                    $detail = $this->Ttuj->Truck->TruckCategory->getMerge($detail, $truck_category_id);

                    $invDetails[$idx] = $detail;
                }
            }

            $this->loadModel('Setting');
            $setting = $this->Setting->find('first');

            $module_title = __('Print Others');
            $this->set('sub_module_title', trim($module_title));
            $this->set('active_menu', 'invoices');

            $this->set(compact(
                'value', 'action_print', 'invDetails',
                'setting'
            ));

            if($action_print == 'pdf'){
                $this->layout = 'pdf';
            }else if($action_print == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Invoice tidak ditemukan'), 'error');  
            $this->redirect($this->referer());
        }
    }

    // public function import_by_ttuj( $download = false ) {
    //     if(!empty($download)){
    //         $link_url = FULL_BASE_URL . '/files/revenues_by_ttuj.xls';
    //         $this->redirect($link_url);
    //         exit;
    //     } else {
    //         App::import('Vendor', 'excelreader'.DS.'excel_reader2');

    //         $this->set('module_title', 'Revenue');
    //         $this->set('active_menu', 'revenues');
    //         $this->set('sub_module_title', __('Import Revenue'));

    //         if(!empty($this->request->data)) { 
    //             $targetdir = $this->MkCommon->_import_excel( $this->request->data );

    //             if( !empty($targetdir) ) {
    //                 $xls_files = glob( $targetdir );

    //                 if(empty($xls_files)) {
    //                     $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
    //                     $this->redirect(array(
    //                         'action'=>'import'
    //                     ));
    //                 } else {
    //                     $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
    //                     $uploaded_file = $uploadedXls['xls'];
    //                     $file = explode(".", $uploaded_file['name']);
    //                     $extension = array_pop($file);
                        
    //                     if($extension == 'xls') {
    //                         $dataimport = new Spreadsheet_Excel_Reader();
    //                         $dataimport->setUTFEncoder('iconv');
    //                         $dataimport->setOutputEncoding('UTF-8');
    //                         $dataimport->read($uploaded_file['tmp_name']);
                            
    //                         if(!empty($dataimport)) {
    //                             $this->loadModel('CustomerNoType');
    //                             $this->loadModel('City');
    //                             $this->loadModel('GroupMotor');
    //                             $this->loadModel('Revenue');
    //                             $data = $dataimport;
    //                             $row_submitted = 0;
    //                             $successfull_row = 0;
    //                             $failed_row = 0;
    //                             $error_message = '';

    //                             for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
    //                                 $datavar = array();
    //                                 $flag = true;
    //                                 $i = 1;
    //                                 $tarifNotFound = false;

    //                                 while ($flag) {
    //                                     if( !empty($data->sheets[0]["cells"][1][$i]) ) {
    //                                         $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
    //                                         $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
    //                                         $$variable = $thedata;
    //                                         $datavar[] = $thedata;
    //                                     } else {
    //                                         $flag = false;
    //                                     }
    //                                     $i++;
    //                                 }

    //                                 if(array_filter($datavar)) {
    //                                     $branch = $this->GroupBranch->Branch->getData('first', array(
    //                                         'conditions' => array(
    //                                             'Branch.code' => $kode_cabang,
    //                                         ),
    //                                     ));
    //                                     $customer = $this->CustomerNoType->find('first', array(
    //                                         'conditions' => array(
    //                                             'CustomerNoType.code' => $kode_customer,
    //                                             'CustomerNoType.status' => 1,
    //                                         ),
    //                                     ));
    //                                     $truck = $this->Ttuj->Truck->find('first', array(
    //                                         'conditions' => array(
    //                                             'Truck.nopol' => $nopol,
    //                                             'Truck.status' => 1,
    //                                         ),
    //                                     ), false);
    //                                     $formCity = $this->City->getData('first', array(
    //                                         'conditions' => array(
    //                                             'City.name' => $dari,
    //                                             'City.status' => 1,
    //                                         ),
    //                                     ));
    //                                     $toCity = $this->City->getData('first', array(
    //                                         'conditions' => array(
    //                                             'City.name' => $tujuan,
    //                                             'City.status' => 1,
    //                                         ),
    //                                     ));

    //                                     $branch_id = !empty($branch['Branch']['id'])?$branch['Branch']['id']:false;
    //                                     $customer_id = !empty($customer['CustomerNoType']['id'])?$customer['CustomerNoType']['id']:false;
    //                                     $truck_id = !empty($truck['Truck']['id'])?$truck['Truck']['id']:false;
    //                                     $truck_capacity = !empty($truck['Truck']['capacity'])?$truck['Truck']['capacity']:false;
    //                                     $from_city_id = !empty($formCity['City']['id'])?$formCity['City']['id']:false;
    //                                     $to_city_id = !empty($toCity['City']['id'])?$toCity['City']['id']:false;
    //                                     $tanggal_revenue = $this->MkCommon->getDate($tanggal_revenue);
    //                                     $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, false, $customer_id, $truck_capacity, false );
    //                                     $jenis_tarif = !empty($tarif['jenis_unit'])?$tarif['jenis_unit']:'per_unit';
    //                                     $ppn = !empty($ppn)?$this->MkCommon->convertPriceToString($ppn):0;
    //                                     $pph = !empty($pph)?$this->MkCommon->convertPriceToString($pph):0;
    //                                     $dataRevenue = array();

    //                                     if( !empty($tarif) ) {
    //                                         $i = 1;
    //                                         $idx = 0;
    //                                         $flag = true;

    //                                         while ($flag) {
    //                                             $varGroup = sprintf('tujuan_%s', $i);

    //                                             if( !empty($$varGroup) ) {
    //                                                 $tujuan_detail = $$varGroup;
    //                                                 $no_do_string = sprintf('no_do_%s', $i);
    //                                                 $no_do_detail = $$no_do_string;
    //                                                 $no_sj_string = sprintf('no_sj_%s', $i);
    //                                                 $no_sj_detail = $$no_sj_string;
    //                                                 $group_motor_string = sprintf('group_motor_%s', $i);
    //                                                 $group_motor_detail = $$group_motor_string;
    //                                                 $jml_unit_string = sprintf('jml_unit_%s', $i);
    //                                                 $jml_unit_detail = !empty($$jml_unit_string)?$this->MkCommon->convertPriceToString($$jml_unit_string):0;
    //                                                 $is_charge_string = sprintf('is_charge_%s', $i);
    //                                                 $is_charge_detail = !empty($$is_charge_string)?$$is_charge_string:0;
    //                                                 $harga_unit_string = sprintf('harga_unit_%s', $i);
    //                                                 $harga_unit_detail = !empty($$harga_unit_string)?$this->MkCommon->convertPriceToString($$harga_unit_string):0;
    //                                                 $toCityDetail = $this->City->getData('first', array(
    //                                                     'conditions' => array(
    //                                                         'City.name' => $tujuan_detail,
    //                                                         'City.status' => 1,
    //                                                     ),
    //                                                 ));
    //                                                 $groupMotor = $this->GroupMotor->getData('first', array(
    //                                                     'conditions' => array(
    //                                                         'GroupMotor.name' => $group_motor_detail,
    //                                                     ),
    //                                                 ));

    //                                                 $to_city_id_detail = !empty($toCityDetail['City']['id'])?$toCityDetail['City']['id']:false;
    //                                                 $group_motor_id = !empty($groupMotor['GroupMotor']['id'])?$groupMotor['GroupMotor']['id']:false;
    //                                                 $total_price_unit = $harga_unit_detail * $jml_unit_detail;

    //                                                 $tarif_detail = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, $to_city_id_detail, $customer_id, $truck_capacity, $group_motor_id );

    //                                                 if( !empty($tarif_detail) ) {
    //                                                     $total_tarif_detail = !empty($tarif_detail['tarif'])?$tarif_detail['tarif']:0;
    //                                                     $tarif_angkutan_id = !empty($tarif_detail['tarif_angkutan_id'])?$tarif_detail['tarif_angkutan_id']:false;
    //                                                     $tarif_angkutan_type = !empty($tarif_detail['tarif_angkutan_type'])?$tarif_detail['tarif_angkutan_type']:false;
    //                                                     $jenis_tarif_detail = !empty($tarif_detail['jenis_unit'])?$tarif_detail['jenis_unit']:false;
    //                                                     $total_price_unit = $this->MkCommon->getChargeTotal( $total_price_unit, $total_tarif_detail, $jenis_tarif_detail, $is_charge_detail );

    //                                                     $dataRevenue['RevenueDetail']['city_id'][$idx] = $to_city_id_detail;
    //                                                     $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$idx] = $tarif_angkutan_id;
    //                                                     $dataRevenue['RevenueDetail']['tarif_angkutan_type'][$idx] = $tarif_angkutan_type;
    //                                                     $dataRevenue['RevenueDetail']['no_do'][$idx] = $no_do_detail;
    //                                                     $dataRevenue['RevenueDetail']['no_sj'][$idx] = $no_sj_detail;
    //                                                     $dataRevenue['RevenueDetail']['group_motor_id'][$idx] = $group_motor_id;
    //                                                     $dataRevenue['RevenueDetail']['qty_unit'][$idx] = $jml_unit_detail;
    //                                                     $dataRevenue['RevenueDetail']['payment_type'][$idx] = $jenis_tarif_detail;
    //                                                     $dataRevenue['RevenueDetail']['is_charge'][$idx] = $is_charge_detail;
    //                                                     $dataRevenue['RevenueDetail']['price_unit'][$idx] = $harga_unit_detail;
    //                                                     $dataRevenue['RevenueDetail']['total_price_unit'][$idx] = $total_price_unit;
    //                                                 } else {
    //                                                     $tarifNotFound = true;
    //                                                 }

    //                                                 $idx++;
    //                                             } else {
    //                                                 $flag = false;
    //                                             }
    //                                             $i++;
    //                                         }

    //                                         $dataRevenue['Revenue'] = array(
    //                                             'branch_id' => $branch_id,
    //                                             'no_doc' => $no_dokumen,
    //                                             'transaction_status' => 'unposting',
    //                                             'date_revenue' => $tanggal_revenue,
    //                                             'customer_id' => $customer_id,
    //                                             'truck_id' => $truck_id,
    //                                             'truck_capacity' => $truck_capacity,
    //                                             'from_city_id' => $from_city_id,
    //                                             'to_city_id' => $to_city_id,
    //                                             'ppn' => $ppn,
    //                                             'pph' => $pph,
    //                                             'revenue_tarif_type' => $jenis_tarif,
    //                                             'branch_id' => Configure::read('__Site.config_branch_id'),
    //                                         );

    //                                         if( !empty($dataRevenue['RevenueDetail']) && empty($tarifNotFound) ) {
    //                                             $resultSave = $this->Revenue->saveRevenue(false, false, $dataRevenue, $this);
    //                                             $statusSave = !empty($resultSave['status'])?$resultSave['status']:false;
    //                                             $msgSave = !empty($resultSave['msg'])?$resultSave['msg']:false;
    //                                             $this->MkCommon->setCustomFlash($msgSave, $statusSave);

    //                                             if( $statusSave == 'success' ) {
    //                                                 $successfull_row++;
    //                                             } else {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : %s'), $row_submitted+1, $msgSave) . '<br>';
    //                                                 $failed_row++;
    //                                             }
    //                                         } else {
    //                                             if( !empty($tarifNotFound) ) {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : Tarif tidak ditemukan, silahkan buat tarif angkutan terlebih dahulu'), $row_submitted+1) . '<br>';
    //                                             } else {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal menyimpan Revenue, mohon lengkapi field-field muatan'), $row_submitted+1) . '<br>';
    //                                             }
    //                                             $failed_row++;
    //                                         }
    //                                     } else {
    //                                         if( empty($from_city_id) || empty($to_city_id) || empty($customer_id) || empty($truck_capacity) ) {
    //                                             if( empty($from_city_id) ) {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : Kota asal tidak benar'), $row_submitted+1) . '<br>';
    //                                             } else if( empty($to_city_id) ) {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : Kota tujuan tidak benar'), $row_submitted+1) . '<br>';
    //                                             } else if( empty($customer_id) ) {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : Kode Customer tidak benar'), $row_submitted+1) . '<br>';
    //                                             } else if( empty($truck_capacity) ) {
    //                                                 $error_message .= sprintf(__('Gagal pada baris ke %s : Nopol Truk tidak benar'), $row_submitted+1) . '<br>';
    //                                             }
    //                                         } else {
    //                                             $error_message .= sprintf(__('Gagal pada baris ke %s : Tarif tidak ditemukan, silahkan buat tarif angkutan terlebih dahulu'), $row_submitted+1) . '<br>';
    //                                         }
    //                                         $failed_row++;
    //                                     }

    //                                     $row_submitted++;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }

    //                 if(!empty($successfull_row)) {
    //                     $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted);
    //                     $this->MkCommon->setCustomFlash(__($message_import1), 'success');
    //                 }
                    
    //                 if(!empty($error_message)) {
    //                     $this->MkCommon->setCustomFlash(__($error_message), 'error');
    //                 }
    //                 $this->redirect(array('action'=>'import'));
    //             } else {
    //                 $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
    //                 $this->redirect(array(
    //                     'action'=>'import'
    //                 ));
    //             }
    //         }
    //     }
    // }

    public function import_revision( $download = false ) {
        App::import('Vendor', 'excelreader'.DS.'excel_reader2');

        $this->set('module_title', __('TTUJ'));
        $this->set('active_menu', 'ttujs');
        $this->set('sub_module_title', __('Import TTUJ'));

        if(!empty($this->request->data)) { 
            $targetdir = $this->MkCommon->_import_excel( $this->request->data );

            if( !empty($targetdir) ) {
                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array(
                        'action'=>'import_revision'
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
                            $data = $dataimport;
                            $row_submitted = 0;
                            $successfull_row = 0;
                            $failed_row = 0;
                            $error_message = '';

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;
                                $notFound = false;

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
                                    if( !empty($keterangan) ) {
                                        $value = $this->Ttuj->find('first', array(
                                            'conditions' => array(
                                                'Ttuj.no_ttuj' => $no_ttuj,
                                            ),
                                        ));

                                        if( !empty($value) ) {
                                            $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');

                                            if( $this->Ttuj->updateAll(array(
                                                'Ttuj.note'=> "'".$keterangan."'",
                                            ), array(
                                                'Ttuj.id'=> $id,
                                            )) ) {
                                                $successfull_row++;
                                            } else {
                                                $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal mengubah keterangan'), $row_submitted+1) . '<br>';
                                                $failed_row++;
                                            }
                                        } else {
                                            $error_message .= sprintf(__('Gagal pada baris ke %s : data tidak ditemukan'), $row_submitted+1) . '<br>';
                                            $failed_row++;
                                        }
                                    } else {
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Tidak ada revisi keterangan'), $row_submitted+1) . '<br>';
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
                $this->redirect(array('action'=>'import_revision'));
            } else {
                $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                $this->redirect(array(
                    'action'=>'import_revision'
                ));
            }
        }
    }

    function generate_revenue_tarif_ankut () {
        $this->loadModel('City');
        $this->Ttuj->Revenue->RevenueDetail->virtualFields['min_id'] = 'MIN(RevenueDetail.id)';
        $revenues = $this->Ttuj->Revenue->RevenueDetail->find('all', array(
            'conditions' => array(
                'Revenue.revenue_tarif_type' => 'per_truck',
                'RevenueDetail.payment_type' => 'per_truck',
                'RevenueDetail.is_charge' => 0,
                'RevenueDetail.status' => 1,
                'Revenue.status' => 1,
                // 'Revenue.id >' => 560,
            ),
            'contain' => array(
                'Revenue',
            ),
            'group' => array(
                'Revenue.id',
            ),
            'order' => array(
                'Revenue.id' => 'ASC',
            ),
            'limit' => 5,
        ));
        // debug($revenues);die();

        if( !empty($revenues) ) {
            foreach ($revenues as $key => $value) {
                $id = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'id' );
                $city_id = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'city_id' );
                $group_motor_id = $this->MkCommon->filterEmptyField( $value, 'RevenueDetail', 'group_motor_id' );

                $revenue_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'id' );
                $truck_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'truck_id' );
                $branch_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'branch_id' );
                $ttuj_id = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'ttuj_id' );
                $tarif_per_truck = $this->MkCommon->filterEmptyField( $value, 'Revenue', 'tarif_per_truck' );
                
                $value = $this->Ttuj->getMerge($value, $ttuj_id);
                $value = $this->City->getMerge($value, $city_id);

                $from_city_id = $this->MkCommon->filterEmptyField( $value, 'Ttuj', 'from_city_id' );
                $to_city_id = $this->MkCommon->filterEmptyField( $value, 'Ttuj', 'to_city_id' );
                $customer_id = $this->MkCommon->filterEmptyField( $value, 'Ttuj', 'customer_id' );
                
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $branch = $this->MkCommon->filterEmptyField( $value, 'Branch', 'name' );

                if( !empty($truck_id) ) {
                    $value = $this->Ttuj->Truck->getMerge($value, $truck_id);
                    $truck_capacity = $this->MkCommon->filterEmptyField($value, 'Truck', 'capacity');
                } else {
                    $truck_capacity = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_capacity');
                }
                
                $tarif = $this->Ttuj->Revenue->RevenueDetail->TarifAngkutan->getTarifAngkut( $from_city_id, $to_city_id, $city_id, $customer_id, $truck_capacity, $group_motor_id );
                $tarif_angkutan_id = $this->MkCommon->filterEmptyField( $tarif, 'tarif_angkutan_id' );

                $this->Ttuj->Revenue->RevenueDetail->validator()->remove('price_unit');
                $this->Ttuj->Revenue->RevenueDetail->validator()->remove('total_price_unit');
                $this->Ttuj->Revenue->RevenueDetail->validator()->remove('tarif_angkutan_id');

                $this->Ttuj->Revenue->RevenueDetail->id = $id;
                $this->Ttuj->Revenue->RevenueDetail->set('is_charge', 1);
                $this->Ttuj->Revenue->RevenueDetail->set('price_unit', $tarif_per_truck);
                $this->Ttuj->Revenue->RevenueDetail->set('total_price_unit', $tarif_per_truck);
                $this->Ttuj->Revenue->RevenueDetail->set('tarif_angkutan_id', $tarif_angkutan_id);
                $this->Ttuj->Revenue->RevenueDetail->save();

                echo sprintf('Tarif Per Truk: %s <br>', $tarif_per_truck);
                echo sprintf('Branch: %s <br>', $branch);
                echo sprintf('Rev ID: %s <br>', $revenue_id);
                echo sprintf('Rev Detail ID: %s  <br> <br>', $id);
            }
        }

        die();
    }

    public function report_surat_jalan( $data_action = false ) {
        $module_title = __('Laporan Surat Jalan');
        $values = array();

        $this->set('sub_module_title', $module_title);

        $this->Ttuj->unBindModel(array(
            'hasMany' => array(
                'SuratJalanDetail'
            )
        ));

        $this->Ttuj->bindModel(array(
            'hasOne' => array(
                'SuratJalanDetail' => array(
                    'className' => 'SuratJalanDetail',
                    'conditions' => array(
                        'SuratJalanDetail.status' => 1,
                    ),
                ),
                'SuratJalan' => array(
                    'className' => 'SuratJalan',
                    'foreignKey' => false,
                    'conditions' => array(
                        'SuratJalan.id = SuratJalanDetail.surat_jalan_id',
                        'SuratJalan.status' => 1,
                        'SuratJalan.is_canceled' => 0,
                    ),
                ),
            )
        ), false);

        $options =  $this->Ttuj->getData('paginate', array(
            'contain' => array(
                'SuratJalan',
                'SuratJalanDetail',
            ),
        ), true, array(
            'status' => 'commit',
            'branch' => false,
            'plant' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->Ttuj->SuratJalanDetail->SuratJalan->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Ttuj', $options );

        if( !empty($options['contain']) ) {
            foreach (array_keys($options['contain'], 'Ttuj', true) as $key) {
                unset($options['contain'][$key]);
            }
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $values = $this->Ttuj->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('Ttuj');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'branch_id');
                
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $muatan = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );
                $value['Ttuj']['qty'] = $muatan;

                $values[$key] = $value;
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('active_menu', 'report_surat_jalan');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'freeze',
                'select',
            ));
        }
    }
}