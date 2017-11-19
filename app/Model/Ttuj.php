<?php
class Ttuj extends AppModel {
	var $name = 'Ttuj';
	var $validate = array(
        'no_ttuj' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No TTUJ harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No TTUJ telah terdaftar',
            ),
        ),
        'ttuj_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl TTUJ harap dipilih'
            ),
        ),
        'uang_jalan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer dan Tujuan harap diisi'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'from_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Dari kota harap dipilih'
            ),
        ),
        'to_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota tujuan harap dipilih'
            ),
        ),
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
            ),
        ),
        'driver_pengganti_id' => array(
            'getDriver' => array(
                'rule' => array('getDriver'),
                'message' => 'Supir pengganti harap dipilih'
            ),
        ),
        'tgljam_berangkat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam Berangkat harap dipilih'
            ),
        ),
        'tgljam_tiba' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam Tiba harap dipilih'
            ),
            'validateDateTtuj' => array(
                'rule' => array('validateDateTtuj', 'tgljam_berangkat', 'tgljam_tiba'),
                'message' => 'Tgl & Jam Tiba harus lebih besar daripada tgl & jam berangkat'
            ),
        ),
        'tgljam_bongkaran' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam Bongkar harap dipilih'
            ),
            'validateDateTtuj' => array(
                'rule' => array('validateDateTtuj', 'tgljam_tiba', 'tgljam_bongkaran'),
                'message' => 'Tgl & Jam bongkaran harus lebih besar daripada tgl & jam tiba'
            ),
        ),
        'tgljam_balik' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam balik harap dipilih'
            ),
            'validateDateTtuj' => array(
                'rule' => array('validateDateTtuj', 'tgljam_bongkaran', 'tgljam_balik'),
                'message' => 'Tgl & Jam Balik harus lebih besar daripada tgl & jam bongkaran'
            ),
        ),
        'tgljam_pool' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam sampai pool harap dipilih'
            ),
            'validateDateTtuj' => array(
                'rule' => array('validateDateTtuj', 'tgljam_balik', 'tgljam_pool'),
                'message' => 'Tgl & Jam sampai pool harus lebih besar daripada tgl & jam balik'
            ),
        ),
        'uang_jalan_1' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya Uang Jalan belum disetting'
            ),
        ),
        // 'date_sj' => array(
        //     'getSJ' => array(
        //         'rule' => array('getSJ'),
        //         'message' => 'Tgl SJ diterima harap dipilih'
        //     ),
        // ),
	);

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'ToCity' => array(
            'className' => 'City',
            'foreignKey' => 'to_city_id',
        ),
        'UangJalan' => array(
            'className' => 'UangJalan',
            'foreignKey' => 'uang_jalan_id',
        ),
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'driver_id',
        ),
        'DriverPengganti' => array(
            'className' => 'Driver',
            'foreignKey' => 'driver_pengganti_id',
        ),
    );

    var $hasMany = array(
        'TtujTipeMotor' => array(
            'className' => 'TtujTipeMotor',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'TtujTipeMotor.status' => 1,
            ),
        ),
        'TtujPerlengkapan' => array(
            'className' => 'TtujPerlengkapan',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'TtujPerlengkapan.status' => 1,
            ),
        ),
        'SuratJalanDetail' => array(
            'className' => 'SuratJalanDetail',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'SuratJalanDetail.status' => 1,
            ),
        ),
        'Revenue' => array(
            'className' => 'Revenue',
            'foreignKey' => 'ttuj_id',
        ),
        'TtujPaymentDetail' => array(
            'className' => 'TtujPaymentDetail',
            'foreignKey' => 'ttuj_id',
        ),
        'Lku' => array(
            'className' => 'Lku',
            'foreignKey' => 'ttuj_id',
        ),
        'Ksu' => array(
            'className' => 'Ksu',
            'foreignKey' => 'ttuj_id',
        ),
    );

    function validateDateTtuj ( $data, $target_date, $input_date ) {
        $allow = !empty($this->data['Ttuj']['allow_date_ttuj'])?$this->data['Ttuj']['allow_date_ttuj']:false;

        if( !empty($this->data['Ttuj'][$target_date]) && !empty($this->data['Ttuj'][$input_date]) ) {
            if( $this->data['Ttuj'][$input_date] >= $this->data['Ttuj'][$target_date] ) {
                return true;
            } else {
                return false;
            }
        } else {
            return $allow;
        }
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $plant = isset($elements['plant'])?$elements['plant']:false;

        $branch_is_plant = Configure::read('__Site.config_branch_plant');
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Ttuj.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Ttuj.status'] = 0;
                break;

            case 'commit':
                $default_options['conditions']['Ttuj.status'] = 1;
                $default_options['conditions']['Ttuj.is_draft'] = 0;
                break;
            
            default:
                $default_options['conditions']['Ttuj.status'] = 1;
                break;
        }

        if( !empty($plant) && !empty($branch_is_plant) ) {
            $default_options['conditions']['Ttuj.branch_id'] = Configure::read('__Site.Branch.Plant.id');
        } else if( !empty($branch) ) {
            $default_options['conditions']['Ttuj.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getDriver () {
        if( empty($this->data['Ttuj']['driver_name']) && empty($this->data['Ttuj']['driver_pengganti_id']) ) {
            return false;
        } else {
            return true;
        }
    }

    function getSumUnit($data, $ttuj_id, $surat_jalan_id = false, $data_action = false){
        if( empty($data['Qty']) ){
            $this->TtujTipeMotor = ClassRegistry::init('TtujTipeMotor');
            
            $this->TtujTipeMotor->virtualFields['qty'] = 'SUM(TtujTipeMotor.qty)';
            $data_merge = $this->TtujTipeMotor->find('first', array(
                'conditions' => array(
                    'TtujTipeMotor.status' => 1,
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                ),
                'group' => array(
                    'TtujTipeMotor.ttuj_id',
                ),
            ));

            if(!empty($data_merge)){
                $data['Qty'] = !empty($data_merge['TtujTipeMotor']['qty'])?$data_merge['TtujTipeMotor']['qty']:0;
            }

            $conditions = array(
                'SuratJalanDetail.status' => 1,
                'SuratJalan.status' => 1,
                'SuratJalan.is_canceled' => 0,
                'SuratJalanDetail.ttuj_id' => $ttuj_id,
                'SuratJalanDetail.surat_jalan_id <>' => $surat_jalan_id,
            );

            $this->SuratJalanDetail->virtualFields['qty'] = 'SUM(SuratJalanDetail.qty)';
            $data_merge = $this->SuratJalanDetail->getData('first', array(
                'conditions' => $conditions,
                'group' => array(
                    'SuratJalanDetail.ttuj_id',
                ),
                'contain' => array(
                    'SuratJalan',
                ),
            ));

            if(!empty($data_merge)){
                $data['QtySJ'] = !empty($data_merge['SuratJalanDetail']['qty'])?$data_merge['SuratJalanDetail']['qty']:0;
            }

            switch ($data_action) {
                case 'tgl_surat_jalan':
                    $data_merge = $this->SuratJalanDetail->find('first', array(
                        'conditions' => $conditions,
                        'order' => array(
                            'SuratJalan.tgl_surat_jalan' => 'DESC',
                            'SuratJalan.id' => 'DESC',
                        ),
                        'contain' => array(
                            'SuratJalan',
                        ),
                        'group' => array(
                            'SuratJalan.id',
                        ),
                    ));

                    if(!empty($data_merge['SuratJalan'])){
                        $data['SuratJalan']['tgl_surat_jalan'] = $data_merge['SuratJalan']['tgl_surat_jalan'];
                    }
                    break;
            }
        }

        return $data;
    }

    function getSJOutstanding ( $driver_id ) {
        $sjCount = $this->getData('count', array(
            'conditions' => array(
                'OR' => array(
                    'Ttuj.driver_id' => $driver_id,
                    'Ttuj.driver_pengganti_id' => $driver_id,
                ),
                'Ttuj.status_sj' => array( 'none', 'half' ),
            ),
            'contain' => false,
        ), true, array(
            'plant' => true,
        ));

        return $sjCount;
    }

    function getTruckStatus ( $data, $truck_id ) {
        $truckAway = $this->getData('paginate', array(
            'conditions' => array(
                'Ttuj.completed' => 0,
                'Ttuj.is_pool' => 0,
                'Ttuj.truck_id' => $truck_id,
            ),
        ));

        if( !empty($truckAway) ) {
            $data = array_merge($data, $truckAway);
        }

        return $data;
    }

    function getTtujPayment ( $ttuj_id, $data_action, $modelName = 'Ttuj' ) {
        $data_ttuj = $this->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $ttuj_id,
            ),
        ), true, array(
            'status' => 'all',
            'branch' => false,
        ));
        $total = 0;

        if( !empty($data_ttuj) ) {
            $customer_id = !empty($data_ttuj['Ttuj']['customer_id'])?$data_ttuj['Ttuj']['customer_id']:'';
            $data_ttuj = $this->Customer->getMerge($data_ttuj, $customer_id);
            $data_ttuj = $this->getMergeList($data_ttuj, array(
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

            switch ($data_action) {
                case 'commission':
                    $commission = !empty($data_ttuj['Ttuj']['commission'])?$data_ttuj['Ttuj']['commission']:0;
                    $total = $commission;
                    break;
                    
                case 'uang_kuli_muat':
                    $total = !empty($data_ttuj['Ttuj']['uang_kuli_muat'])?$data_ttuj['Ttuj']['uang_kuli_muat']:0;
                    break;
                    
                case 'uang_kuli_bongkar':
                    $total = !empty($data_ttuj['Ttuj']['uang_kuli_bongkar'])?$data_ttuj['Ttuj']['uang_kuli_bongkar']:0;
                    break;
                    
                case 'asdp':
                    $total = !empty($data_ttuj['Ttuj']['asdp'])?$data_ttuj['Ttuj']['asdp']:0;
                    break;
                    
                case 'uang_kawal':
                    $total = !empty($data_ttuj['Ttuj']['uang_kawal'])?$data_ttuj['Ttuj']['uang_kawal']:0;
                    break;
                    
                case 'uang_keamanan':
                    $total = !empty($data_ttuj['Ttuj']['uang_keamanan'])?$data_ttuj['Ttuj']['uang_keamanan']:0;
                    break;
                    
                case 'uang_jalan_2':
                    $total = !empty($data_ttuj['Ttuj']['uang_jalan_2'])?$data_ttuj['Ttuj']['uang_jalan_2']:0;
                    break;
                    
                case 'uang_jalan_extra':
                    $total = !empty($data_ttuj['Ttuj']['uang_jalan_extra'])?$data_ttuj['Ttuj']['uang_jalan_extra']:0;
                    break;
                    
                case 'commission_extra':
                    $total = !empty($data_ttuj['Ttuj']['commission_extra'])?$data_ttuj['Ttuj']['commission_extra']:0;
                    break;
                
                default:
                    $uang_jalan_1 = !empty($data_ttuj['Ttuj']['uang_jalan_1'])?$data_ttuj['Ttuj']['uang_jalan_1']:0;
                    $total = $uang_jalan_1;
                    break;
            }
        }

        if( empty($data_ttuj[$modelName]) && !empty($data_ttuj['Ttuj']) ) {
            $data_ttuj[$modelName] = $data_ttuj['Ttuj'];
            unset($data_ttuj['Ttuj']);
        }

        $data_ttuj['total'] = $total;
        return $data_ttuj;
    }

    // function setTtuj ( $ttuj_id, $data ) {
    //     $data['Ttuj'] = $data;
    //     $this->set($data);
    //     $this->id = $ttuj_id;

    //     return $this->save();
    // }

    function getMerge($data, $id, $fieldName = 'Ttuj.id'){
        if(empty($data['Ttuj'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    $fieldName => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeContain ( $data, $ttuj_id ) {
        $this->TtujTipeMotor = ClassRegistry::init('TtujTipeMotor');
        $this->TtujPerlengkapan = ClassRegistry::init('TtujPerlengkapan');

        $data = $this->TtujTipeMotor->getMergeTipeMotor( $data, $ttuj_id, 'all');
        $data = $this->TtujPerlengkapan->getMerge($data, $ttuj_id);
        $data = $this->getMergeList($data, array(
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
        $data['Ttuj']['driver_name'] = $this->filterEmptyField($data, 'Driver', 'driver_name');

        return $data;
    }

    function _callConditionBranch ( $conditions ) {
        $current_branch_id = Configure::read('__Site.config_branch_id');
        $branch_city_id = Configure::read('__Site.Branch.City.id');
        $branch_city_bongkar_id = Configure::read('__Site.Branch.City.Bongkar.id');
        $data_branch_city_id = Configure::read('__Site.Data.Branch.City.id');
        $branch_plant_id = Configure::read('__Site.Branch.Plant.id');
        $is_plant = Configure::read('__Site.config_branch_plant');

        $conditions['OR'] = array(
            array(
                'Ttuj.to_city_id' => $data_branch_city_id,
                'Ttuj.branch_id' => $branch_city_bongkar_id,
                'Ttuj.to_city_id' => $branch_city_id,
            ),
            array(
                'Ttuj.to_city_id <>' => $data_branch_city_id,
                'Ttuj.branch_id' => $current_branch_id,
            ),
        );

        if( $is_plant ) {
            $conditions['OR'][] = array(
                'Ttuj.to_city_id <>' => $data_branch_city_id,
                'Ttuj.branch_id' => $branch_plant_id,
                'Ttuj.branch_id' => $branch_city_bongkar_id,
            );
        }

        return $conditions;
    }

    function getTtujAfterLeave ( $id, $action_type ) {
        $current_branch_id = Configure::read('__Site.config_branch_id');
        $branch_city_id = Configure::read('__Site.Branch.City.id');
        $branch_city_bongkar_id = Configure::read('__Site.Branch.City.Bongkar.id');
        $data_branch_city_id = Configure::read('__Site.Data.Branch.City.id');
        $conditionsTtuj = array(
            'Ttuj.is_draft' => 0,
            'Ttuj.completed' => 0,
            'Ttuj.is_laka' => 0,
        );

        switch ($action_type) {
            case 'bongkaran':
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
                $conditionsTtuj = $this->_callConditionBranch($conditionsTtuj);
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
                $conditionsTtuj = $this->_callConditionBranch($conditionsTtuj);
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
                $conditionsTtuj = $this->_callConditionTtujPool($conditionsTtuj);
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
                $conditionsTtuj = $this->_callConditionBranch($conditionsTtuj);
                break;
        }

        return $this->getData('list', array(
            'conditions' => $conditionsTtuj,
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
        ), true, array(
            'branch' => false,
        ));
        // $ttujList = array();

        // if( !empty($ttujs) ) {
        //     $this->Branch = ClassRegistry::init('Branch');

        //     foreach ($ttujs as $key => $value) {
        //         $ttuj_id = !empty($value['Ttuj']['id'])?$value['Ttuj']['id']:false;
        //         $to_city_id = !empty($value['Ttuj']['to_city_id'])?$value['Ttuj']['to_city_id']:false;
        //         $no_ttuj = !empty($value['Ttuj']['no_ttuj'])?$value['Ttuj']['no_ttuj']:false;

        //         if( $this->validateTtujAfterLeave( $to_city_id, $this->Branch ) ) {
        //             $ttujList[$ttuj_id] = $no_ttuj;
        //         }
        //     }
        // }

        // return $ttujList;
    }

    function _callDataTtujConditions ( $id, $action_type ) {
        $conditionsDataLocal = array(
            'Ttuj.id' => $id,
            'Ttuj.is_draft' => 0,
            'Ttuj.completed' => 0,
            'Ttuj.status' => 1,
        );

        switch ($action_type) {
            case 'bongkaran':
                $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                $conditionsDataLocal['Ttuj.is_bongkaran <>'] = 1;
                $conditionsDataLocal = $this->_callConditionBranch($conditionsDataLocal);
                break;

            case 'balik':
                $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                $conditionsDataLocal['Ttuj.is_bongkaran'] = 1;
                $conditionsDataLocal['Ttuj.is_balik <>'] = 1;
                $conditionsDataLocal = $this->_callConditionBranch($conditionsDataLocal);
                break;

            case 'pool':
                $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                $conditionsDataLocal['Ttuj.is_bongkaran'] = 1;
                $conditionsDataLocal['Ttuj.is_balik'] = 1;
                $conditionsDataLocal['Ttuj.is_pool <>'] = 1;
                $conditionsDataLocal = $this->_callConditionTtujPool( $conditionsDataLocal );
                break;
            
            default:
                $conditionsDataLocal['Ttuj.is_arrive'] = 0;
                $conditionsDataLocal = $this->_callConditionBranch($conditionsDataLocal);
                break;
        }

        // $this->Branch = ClassRegistry::init('Branch');
        return $this->getData('first', array(
            'conditions' => $conditionsDataLocal,
        ), true, array(
            'branch' => false,
        ));
        // $to_city_id = !empty($ttuj['Ttuj']['to_city_id'])?$ttuj['Ttuj']['to_city_id']:false;

        // if( $this->validateTtujAfterLeave( $to_city_id, $this->Branch ) ) {
        //     return $ttuj;
        // } else {
        //     return array();
        // }
    }

    function _callConditionTtujPool ( $conditions ) {
        $current_branch_id = Configure::read('__Site.config_branch_id');
        $current_branch_plant = Configure::read('__Site.config_branch_plant');
        $branch_plant_id = Configure::read('__Site.Branch.Plant.id');

        if( !empty($current_branch_plant) ) {
            $conditions['Ttuj.branch_id'] = $branch_plant_id;
        } else {
            $conditions['Ttuj.branch_id'] = $current_branch_id;
        }

        return $conditions;
    }

    // function validateTtujAfterLeave ( $to_city_id, $objBranch ) {
    //     $current_branch_id = Configure::read('__Site.config_branch_id');
    //     $data_branch_city_id = Configure::read('__Site.Data.Branch.City.id');

    //     if( in_array($to_city_id, $data_branch_city_id) ) {
    //         $value = $objBranch->getBranch($to_city_id);
    //         $branch_id = !empty($value['Branch']['id'])?$value['Branch']['id']:false;

    //         $value = $objBranch->BranchCity->getMerge($value, $value, 'list');

    //         if( !empty($value['BranchCity']) && in_array($current_branch_id, $value['BranchCity']) ) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     } else {
    //         return true;
    //     }
    // }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'Ttuj.no_ttuj' => 'DESC'
            ),
            'fields' => array(
                'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'Ttuj.no_ttuj LIKE' => '%'.$format_id.'%',
            ),
        ), true, array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['Ttuj']['no_ttuj'])){
            $str_arr = explode('-', $last_data['Ttuj']['no_ttuj']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $dateFromTtuj = !empty($data['named']['DateFromTtuj'])?$data['named']['DateFromTtuj']:false;
        $dateToTtuj = !empty($data['named']['DateToTtuj'])?$data['named']['DateToTtuj']:false;
        $dateFromRange = !empty($data['named']['DateFromRange'])?$data['named']['DateFromRange']:false;
        $dateToRange = !empty($data['named']['DateToRange'])?$data['named']['DateToRange']:false;
        $dateRitaseFrom = !empty($data['named']['DateRitaseFrom'])?$data['named']['DateRitaseFrom']:false;
        $dateRitaseTo = !empty($data['named']['DateRitaseTo'])?$data['named']['DateRitaseTo']:false;

        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $company = !empty($data['named']['company'])?$data['named']['company']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $to_city = !empty($data['named']['to_city'])?$data['named']['to_city']:false;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $customerid = !empty($data['named']['customerid'])?$data['named']['customerid']:false;
        $driver = !empty($data['named']['driver'])?$data['named']['driver']:false;
        $fromcity = !empty($data['named']['fromcity'])?$data['named']['fromcity']:false;
        $tocity = !empty($data['named']['tocity'])?$data['named']['tocity']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;
        $leadtime = !empty($data['named']['leadtime'])?$data['named']['leadtime']:false;

        $uj1 = !empty($data['named']['uj1'])?$data['named']['uj1']:false;
        $uj2 = !empty($data['named']['uj2'])?$data['named']['uj2']:false;
        $uje = !empty($data['named']['uje'])?$data['named']['uje']:false;
        $com = !empty($data['named']['com'])?$data['named']['com']:false;
        $come = !empty($data['named']['come'])?$data['named']['come']:false;
        
        $sort = $this->filterEmptyField($data, 'named', 'sort');
        $direction = $this->filterEmptyField($data, 'named', 'direction');

        if( !empty($dateFromRange) || !empty($dateToRange) || $status == 'sj_receipt_unpaid' ) {
            $this->unBindModel(array(
                'hasMany' => array(
                    'SuratJalanDetail'
                )
            ));

            $this->bindModel(array(
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
            $default_options['contain'][] = 'SuratJalan';
            $default_options['contain'][] = 'SuratJalanDetail';
        }

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($dateFromTtuj) || !empty($dateToTtuj) ) {
            if( !empty($dateFromTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') >='] = $dateFromTtuj;
            }

            if( !empty($dateToTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') <='] = $dateToTtuj;
            }
        }
        if( !empty($dateFromRange) || !empty($dateToRange) ) {
            if( !empty($dateFromRange) ) {
                $default_options['conditions']['DATE_FORMAT(SuratJalan.tgl_surat_jalan, \'%Y-%m-%d\') >='] = $dateFromRange;
            }

            if( !empty($dateToRange) ) {
                $default_options['conditions']['DATE_FORMAT(SuratJalan.tgl_surat_jalan, \'%Y-%m-%d\') <='] = $dateToRange;
            }

            $default_options['contain'][] = 'SuratJalan';
            $default_options['conditions']['SuratJalan.id <>'] = NULL;
        }
        if( !empty($dateRitaseFrom) && !empty($dateRitaseTo) ) {
            $default_options['conditions']['OR'] = array(
                array(
                    'DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') >='=> $dateRitaseFrom,
                    'DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') <=' => $dateRitaseTo,
                ),
                array(
                    'DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m-%d\') >='=> $dateRitaseFrom,
                    'DATE_FORMAT(Ttuj.tgljam_tiba, \'%Y-%m-%d\') <=' => $dateRitaseTo,
                ),
                array(
                    'DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m-%d\') >='=> $dateRitaseFrom,
                    'DATE_FORMAT(Ttuj.tgljam_bongkaran, \'%Y-%m-%d\') <=' => $dateRitaseTo,
                ),
                array(
                    'DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m-%d\') >='=> $dateRitaseFrom,
                    'DATE_FORMAT(Ttuj.tgljam_balik, \'%Y-%m-%d\') <=' => $dateRitaseTo,
                ),
                array(
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') >='=> $dateRitaseFrom,
                    'DATE_FORMAT(Ttuj.tgljam_pool, \'%Y-%m-%d\') <=' => $dateRitaseTo,
                ),
                array(
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='=> $dateRitaseFrom,
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <=' => $dateRitaseTo,
                ),
            );
        }

        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['Ttuj.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['Ttuj.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($company)){
            $default_options['conditions']['Truck.company_id'] = $company;
            $default_options['contain'][] = 'Truck';
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Ttuj.no_ttuj LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($to_city)){
            $default_options['conditions']['Ttuj.to_city_name LIKE'] = '%'.$to_city.'%';
        }
        if(!empty($driver)){
            $default_options['conditions']['Ttuj.driver_name LIKE'] = '%'.$driver.'%';
        }
        if(!empty($customer)){
            $customers = $this->Customer->getData('list', array(
                'conditions' => array(
                    'Customer.customer_name_code LIKE' => '%'.$customer.'%',
                ),
                'fields' => array(
                    'Customer.id', 'Customer.id'
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));
            $default_options['conditions']['Ttuj.customer_id'] = $customers;
        }
        if(!empty($customerid)){
            $default_options['conditions']['Ttuj.customer_id'] = $customerid;
        }
        if(!empty($uj1)){
            $default_options['conditions'][0]['OR'][]['Ttuj.uang_jalan_1 <>'] = 0;
        }
        if(!empty($uj2)){
            $default_options['conditions'][0]['OR'][]['Ttuj.uang_jalan_2 <>'] = 0;
        }
        if(!empty($uje)){
            $default_options['conditions'][0]['OR'][]['Ttuj.uang_jalan_extra <>'] = 0;
        }
        if(!empty($com)){
            $default_options['conditions'][0]['OR'][]['Ttuj.commission <>'] = 0;
        }
        if(!empty($come)){
            $default_options['conditions'][0]['OR'][]['Ttuj.commission_extra <>'] = 0;
        }
        if(!empty($fromcity)){
            $default_options['conditions']['Ttuj.from_city_id'] = $fromcity;
        }
        if(!empty($tocity)){
            $default_options['conditions']['Ttuj.to_city_id'] = $tocity;
        }
        if(!empty($note)){
            $default_options['conditions']['Ttuj.note LIKE'] = '%'.$note.'%';
        }
        if(!empty($status)){
            switch ($status) {
                case 'paid':
                        $default_options['conditions'][1]['OR'] = array(
                            array(
                                'Ttuj.paid_uang_jalan' => 'full',
                            ),
                            array(
                                'Ttuj.paid_uang_jalan_2' => 'full',
                            ),
                            array(
                                'Ttuj.paid_uang_jalan_extra' => 'full',
                            ),
                            array(
                                'Ttuj.paid_commission' => 'full',
                            ),
                            array(
                                'Ttuj.paid_commission_extra' => 'full',
                            ),
                        );
                    break;
                case 'unpaid':
                        $default_options['conditions'][1]['OR'] = array(
                            array(
                                'Ttuj.paid_uang_jalan' => 'none',
                            ),
                            array(
                                'Ttuj.paid_uang_jalan_2' => 'none',
                            ),
                            array(
                                'Ttuj.paid_uang_jalan_extra' => 'none',
                            ),
                            array(
                                'Ttuj.paid_commission' => 'none',
                            ),
                            array(
                                'Ttuj.paid_commission_extra' => 'none',
                            ),
                        );
                    break;
                case 'sj_pending':
                    $default_options['conditions']['Ttuj.status_sj'] = array( 'none', 'half' );
                    break;
                case 'sj_receipt':
                    $default_options['conditions']['Ttuj.status_sj'] = 'full';
                    break;
                case 'sj_receipt_unpaid':
                    $this->Revenue->bindModel(array(
                        'hasOne' => array(
                            'SuratJalanDetail' => array(
                                'className' => 'SuratJalanDetail',
                                'foreignKey' => false,
                                'conditions' => array(
                                    'SuratJalanDetail.ttuj_id = Revenue.ttuj_id',
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

                    $default_options['conditions']['Ttuj.status_sj'] = array( 'full', 'half' );
                    $revenueConditions = !empty($default_options['conditions'])?$default_options['conditions']:false;
                    $revenueConditions['Revenue.transaction_status <>'] = 'invoiced';
                    $revenues = $this->Revenue->getData('list', array(
                        'conditions' => $revenueConditions,
                        'contain' => array(
                            'Ttuj',
                            'SuratJalan',
                            'SuratJalanDetail',
                        ),
                        'fields' => array(
                            'Revenue.id', 'Revenue.ttuj_id'
                        ),
                    ), true, array(
                        'status' => 'all',
                        'branch' => false,
                    ));

                    $default_options['conditions']['Ttuj.id'] = $revenues;
                    break;
                case 'sj_receipt_paid':
                    $default_options['conditions']['Ttuj.status_sj'] = array( 'none', 'half' );
                    $revenueConditions = !empty($default_options['conditions'])?$default_options['conditions']:false;
                    $revenueConditions['Revenue.transaction_status'] = 'invoiced';
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

                    $default_options['conditions']['Ttuj.id'] = $revenues;
                    break;
            }
        }

        if( !empty($status) ) {
            switch ($status) {
                case 'ng':
                    $this->unBindModel(array(
                        'hasMany' => array(
                            'Lku'
                        )
                    ));

                    $this->bindModel(array(
                        'hasOne' => array(
                            'Lku' => array(
                                'className' => 'Lku',
                                'foreignKey' => 'ttuj_id',
                                'conditions' => array(
                                    'Lku.status' => 1
                                ),
                            ),
                        )
                    ), false);

                    $default_options['contain'][] = 'Lku';
                    $default_options['conditions']['Lku.id NOT'] = NULL;
                    break;
                
                case 'laka':
                    $default_options['conditions']['Ttuj.is_laka'] = true;
                    break;
                
                case 'bt':
                    $default_options['conditions']['Ttuj.is_laka'] = false;
                    $default_options['conditions']['Ttuj.is_arrive'] = false;
                    $default_options['conditions']['Ttuj.is_bongkaran'] = false;
                    $default_options['conditions']['Ttuj.is_balik'] = false;
                    $default_options['conditions']['Ttuj.is_pool'] = false;
                    break;
                
                case 'ab':
                    $default_options['conditions']['Ttuj.is_laka'] = false;
                    $default_options['conditions']['Ttuj.is_arrive'] = true;
                    $default_options['conditions']['Ttuj.is_bongkaran'] = false;
                    $default_options['conditions']['Ttuj.is_balik'] = false;
                    $default_options['conditions']['Ttuj.is_pool'] = false;
                    break;
                
                case 'sb':
                    $default_options['conditions']['Ttuj.is_laka'] = false;
                    $default_options['conditions']['Ttuj.is_arrive'] = true;
                    $default_options['conditions']['Ttuj.is_bongkaran'] = true;
                    $default_options['conditions']['Ttuj.is_balik'] = false;
                    $default_options['conditions']['Ttuj.is_pool'] = false;
                    break;
                
                case 'bb':
                    $default_options['conditions']['Ttuj.is_laka'] = false;
                    $default_options['conditions']['Ttuj.is_arrive'] = true;
                    $default_options['conditions']['Ttuj.is_bongkaran'] = true;
                    $default_options['conditions']['Ttuj.is_balik'] = true;
                    $default_options['conditions']['Ttuj.is_pool'] = false;
                    break;
                
                case 'pool':
                    $default_options['conditions']['Ttuj.is_laka'] = false;
                    $default_options['conditions']['Ttuj.is_arrive'] = true;
                    $default_options['conditions']['Ttuj.is_bongkaran'] = true;
                    $default_options['conditions']['Ttuj.is_balik'] = true;
                    $default_options['conditions']['Ttuj.is_pool'] = true;
                    break;
            }
        }

        if( !empty($leadtime) ) {
            switch ($leadtime) {
                case 'overleadtime':
                    $default_options['conditions'][2]['OR'] = array(
                        'TIMESTAMPDIFF(HOUR, tgljam_berangkat, tgljam_tiba) > UangJalan.arrive_lead_time',
                        '(TIMESTAMPDIFF(HOUR, tgljam_berangkat, tgljam_tiba) + TIMESTAMPDIFF(HOUR, tgljam_balik, tgljam_pool)) > UangJalan.back_lead_time',
                    );
                    $default_options['contain'][] = 'UangJalan';
                    break;
                case 'goodleadtime':
                    $default_options['conditions'][2]['AND'] = array(
                        'TIMESTAMPDIFF(HOUR, tgljam_berangkat, tgljam_tiba) <= UangJalan.arrive_lead_time',
                        '(TIMESTAMPDIFF(HOUR, tgljam_berangkat, tgljam_tiba) + TIMESTAMPDIFF(HOUR, tgljam_balik, tgljam_pool)) <= UangJalan.back_lead_time',
                    );
                    $default_options['contain'][] = 'UangJalan';
                    break;
            }
        }

        if( $sort == 'Ttuj.driver_name' ) {
            $this->virtualFields['driver_name'] = 'IFNULL(DriverPengganti.name, Driver.name)';

            $default_options['contain'][] = 'DriverPengganti';
            $default_options['contain'][] = 'Driver';
        }

        return $default_options;
    }

    function getBiayaUangJalan ( $data, $id, $params = false ) {
        $dateFrom = !empty($params['named']['DateFrom'])?$params['named']['DateFrom']:false;
        $dateTo = !empty($params['named']['DateTo'])?$params['named']['DateTo']:false;
        $default_options = array(
            'conditions' => array(
                'Ttuj.truck_id' => $id,
            ),
        );

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }

        $this->virtualFields['biaya_uang_jalan'] = 'SUM(uang_jalan_1+uang_jalan_2+uang_kuli_muat+uang_kuli_bongkar+asdp+uang_kawal+uang_keamanan+uang_jalan_extra+commission+commission_extra)';
        $value = $this->getData('first', $default_options, true, array(
            'branch' => false,
        ));

        if( !empty($value) ) {
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function _callTtujOngoing ( $options = array() ) {
        $options = array_merge(array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.truck_id',
            ),
            'conditions' => array(
                'Ttuj.status' => 1,
                'Ttuj.is_pool' => 0,
                'Ttuj.is_laka' => 0,
                'Ttuj.completed' => 0,
            ),
        ), $options);
        return $this->getData('list', $options, true, array(
            'plant' => true,
        ));
    }
}
?>