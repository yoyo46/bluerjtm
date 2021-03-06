<?php
class Truck extends AppModel {
    var $name = 'Truck';
    var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nopol truk harap diisi'
            ),
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'Nopol telah terdaftar',
            ),
        ),
        'truck_brand_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'merek truk harap diisi'
            ),
        ),
        'truck_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis truk harap diisi'
            ),
        ),
        'truck_facility_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Fasilitas truk harap dipilih'
            ),
        ),
        'no_rangka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor Rangka truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Nomor Rangka telah terdaftar',
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'perusahaan truk harap diisi'
            ),
        ),
        'no_machine' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor Mesin truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Nomor Mesin telah terdaftar',
            ),
        ),
        'driver_id' => array(
            'validateDriver' => array(
                'rule' => array('validateDriver'),
                'message' => 'Supir truk sudah terdaftar'
            ),
        ),
        'capacity' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kapasitas truk harap diisi'
            ),
        ),
        'tahun' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tahun truk harap diisi'
            ),
        ),
        'tahun_neraca' => array(
            'validateThnNeraca' => array(
                'rule' => array('validateThnNeraca'),
                'message' => 'Tahun Neraca truk harap diisi'
            ),
        ),
        // 'atas_nama' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Atas Nama truk harap diisi'
        //     ),
        // ),
        // 'bpkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'BPKB truk harap diisi'
        //     ),
        //     'isUnique' => array(
        //         'rule' => array('isUnique'),
        //         'message' => 'BPKB telah terdaftar',
        //     ),
        // ),
        // 'tgl_bpkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tanggal BPKB truk harap diisi'
        //     ),
        // ),
        // 'no_stnk' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Nomor STNK truk harap diisi'
        //     ),
        //     'isUnique' => array(
        //         'rule' => array('isUnique'),
        //         'message' => 'Nomor STNK telah terdaftar',
        //     ),
        // ),
        // 'tgl_stnk' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang STNK 1thn truk harap diisi'
        //     ),
        // ),
        // 'tgl_stnk_plat' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang STNK 5thn truk harap diisi'
        //     ),
        // ),
        // 'bbnkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya BBNKB truk harap diisi'
        //     ),
        // ),
        // 'pkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya PKB truk harap diisi'
        //     ),
        // ),
        // 'swdkllj' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya SWDKLLJ truk harap diisi'
        //     ),
        // ),
        // 'tgl_siup' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang SIUP truk harap diisi'
        //     ),
        // ),
        // 'siup' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya SIUP truk harap diisi'
        //     ),
        // ),
        // 'tgl_kir' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang KIR truk harap diisi'
        //     ),
        // ),
        // 'kir' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya KIR truk harap diisi'
        //     ),
        // ),
    );

    var $hasOne = array(
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'truck_id',
        ),
        'Laka' => array(
            'className' => 'Laka',
            'foreignKey' => 'truck_id',
            'conditions' => array(
                'Laka.status' => 1,
                'Laka.completed' => 0,
            ),
        ),
        'PurchaseOrderAsset' => array(
            'className' => 'PurchaseOrderAsset',
            'foreignKey' => 'truck_id',
        ),
        'LeasingDetail' => array(
            'className' => 'LeasingDetail',
            'foreignKey' => 'truck_id',
        ),
    );

    var $belongsTo = array(
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'driver_id',
            'conditions' => array(
                'Driver.status' => 1,
                'Driver.is_resign' => 0,
            ),
        ),
        'Stnk' => array(
            'className' => 'Stnk',
            'foreignKey' => 'truck_id',
        ),
        'TruckBrand' => array(
            'className' => 'TruckBrand',
            'foreignKey' => 'truck_brand_id',
        ),
        'TruckCategory' => array(
            'className' => 'TruckCategory',
            'foreignKey' => 'truck_category_id',
        ),
        'TruckFacility' => array(
            'className' => 'TruckFacility',
            'foreignKey' => 'truck_facility_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
        ),
    );

    var $hasMany = array(
        'Siup' => array(
            'className' => 'Siup',
            'foreignKey' => 'truck_id',
        ),
        'TruckAlocation' => array(
            'className' => 'TruckAlocation',
            'foreignKey' => 'truck_id',
        ),
        'Stnk' => array(
            'className' => 'Stnk',
            'foreignKey' => 'truck_id',
        ),
        'Kir' => array(
            'className' => 'Kir',
            'foreignKey' => 'truck_id',
        ),
        'TruckCustomer' => array(
            'className' => 'TruckCustomer',
            'foreignKey' => 'truck_id',
        ),
        'TruckPerlengkapan' => array(
            'className' => 'TruckPerlengkapan',
            'foreignKey' => 'truck_id',
        ),
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'truck_id',
        ),
        'CashBankDetail' => array(
            'className' => 'CashBankDetail',
            'foreignKey' => 'truck_id',
        ),
        'DocumentPaymentDetail' => array(
            'className' => 'DocumentPaymentDetail',
            'foreignKey' => 'truck_id',
        ),
        'Spk' => array(
            'className' => 'Spk',
            'foreignKey' => 'truck_id',
        ),
        'ViewTruckMaintenance' => array(
            'className' => 'ViewTruckMaintenance',
            'foreignKey' => 'truck_id',
        ),
        'Revenue' => array(
            'foreignKey' => 'truck_id',
        ),
        'InsuranceDetail' => array(
            'className' => 'InsuranceDetail',
            'foreignKey' => 'truck_id',
        ),
        'Asset' => array(
            'className' => 'Asset',
            'foreignKey' => 'asset_id',
        ),
    );

    function checkUniq() {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Truck.id', $id);
        $nopol = Common::hashEmptyField($this->data, 'Truck.nopol');

        $check = $this->getData('first', array(
            'conditions' => array(
                'Truck.id <>' => $id,
                'Truck.nopol' => $nopol,
            ),
        ), true, array(
            'branch' => false,
        ));

        if( !empty($check) ) {
            return false;
        } else {
            return true; 
        }
    }

    function uniqueUpdate($data, $id = false){
        $result = false;
        $id = !empty($this->data['Truck']['id'])?$this->data['Truck']['id']:$id;

        $find = $this->find('count', array(
            'conditions' => array(
                'Truck.id NOT' => $id,
                'Truck.nopol' => $data['nopol']
            )
        ));

        if(empty($find)){
            $result = true;
        }

        return $result;
    }



    function validateDriver(){
        if( !empty($this->data['Truck']['driver_id']) ) {
            $truck_id = !empty($this->data['Truck']['id'])?$this->data['Truck']['id']:false;
            $find = $this->find('count', array(
                'conditions' => array(
                    'Truck.driver_id' => $this->data['Truck']['driver_id'],
                    'Truck.id NOT' => $truck_id,
                    'Truck.status' => 1,
                )
            ));

            if( !empty($find) ) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function validateThnNeraca () {
        $is_asset = !empty($this->data['Truck']['is_asset'])?$this->data['Truck']['is_asset']:false;
        $tahun_neraca = !empty($this->data['Truck']['tahun_neraca'])?$this->data['Truck']['tahun_neraca']:false;

        if( !empty($is_asset) && empty($tahun_neraca) ) {
            return false;
        } else {
            return true;
        }
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $plant = isset($elements['plant'])?$elements['plant']:false;
        $branch_is_plant = Configure::read('__Site.config_branch_plant');

        $default_options = array(
            'conditions'=> array(),
            'order' => array(
                'Truck.nopol' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Truck.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Truck.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Truck.status'] = 1;
                break;
        }

        if( !empty($plant) && !empty($branch_is_plant) ) {
            $default_options['conditions']['Truck.branch_id'] = Configure::read('__Site.Branch.Plant.id');
        } else if( !empty($branch) ) {
            $default_options['conditions']['Truck.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
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
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
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

    function getTruck( $id, $options = array() ){
        $truck = $this->getData('first', array(
            'conditions' => array(
                'Truck.id' => $id
            )
        ), true, $options);

        if(!empty($truck)){
            $data = $truck['Truck'];

            $truck = $this->TruckCategory->getMerge($truck, $data['truck_category_id']);
            $truck = $this->TruckFacility->getMerge($truck, $data['truck_facility_id']);
            $truck = $this->TruckBrand->getMerge($truck, $data['truck_brand_id']);
            $truck = $this->Company->getMerge($truck, $data['company_id']);
            $truck = $this->Driver->getMerge($truck, $data['driver_id']);
        }
        return $truck;
    }

    function getInfoTruck( $truck_id, $branch_id = false, $fieldName = 'Truck.id' ) {
        $conditions = array(
            $fieldName => $truck_id,
        );

        if( !empty($branch_id) ) {
            $conditions['Truck.branch_id'] = $branch_id;
        }

        $result = $this->getData('first', array(
            'conditions' => $conditions,
        ), true, array(
            'branch' => false,
        ));

        if( !empty($result) ) {
            $result = $this->getMergeList($result, array(
                'contain' => array(
                    'Driver' => array(
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));
        }

        return $result;
    }

    function getMerge($data, $id, $fieldName = 'Truck.id'){
        if(empty($data['Truck'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    $fieldName => $id,
                    'Truck.status' => 1,
                )
            ), true, array(
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeAll($data, $modelName = false){
        if( !empty($data) ){
            foreach ($data as $key => $value) {
                $truck_id = !empty($value[$modelName]['truck_id'])?$value[$modelName]['truck_id']:false;

                $value = $this->getMerge($value, $truck_id);
                $data[$key] = $value;
            }
        }

        return $data;
    }

    function getListTruck ( $include_this_truck_id = false, $only_bind = false, $nopol = false, $branch_id = false, $conditions = array(), $ttujs = false ) {
        $ttuj_ongoing = $this->Ttuj->_callTtujOngoing();
        $branch_is_plant = Configure::read('__Site.config_branch_plant');

        if( !empty($include_this_truck_id) ) {
            // Ambil data truck berikut id ini
            $conditions = array_merge($conditions, array(
                'OR' => array(
                    array(
                        'Truck.id' => $include_this_truck_id,
                    ),
                    array(
                        'Truck.id NOT' => $ttuj_ongoing,
                    ),
                ),
                'AND' => array(
                    'OR' => array(
                        array(
                            'Truck.id' => $include_this_truck_id
                        ),
                    ),
                ),
            ));

            if( !empty($branch_is_plant) ) {
                $conditions['AND']['OR']['Truck.branch_id'] = Configure::read('__Site.Branch.Plant.id');
            } else {
                $conditions['AND']['OR']['Truck.branch_id'] = Configure::read('__Site.config_branch_id');
            }

            if( !empty($ttujs) ) {
                $conditions = $this->callUnset($conditions, array(
                    'Truck.branch_id',
                ));

                $conditions['AND']['OR'][]['Truck.id'] = $ttujs;
            }
        } else {
            $conditions['Truck.id NOT'] = $ttuj_ongoing;

            if( !empty($branch_is_plant) ) {
                $conditions['Truck.branch_id'] = Configure::read('__Site.Branch.Plant.id');
            } else {
                $conditions['Truck.branch_id'] = Configure::read('__Site.config_branch_id');
            }

            if( !empty($ttujs) ) {
                $branch_id = $conditions['Truck.branch_id'];
                $conditions = $this->callUnset($conditions, array(
                    'Truck.branch_id',
                ));

                $conditions[]['OR'] = array(
                    'Truck.branch_id' => $branch_id,
                    'Truck.id' => $ttujs,
                );
            }
        }

        if( empty($only_bind) ) {
            $trucks = $this->getData('list', array(
                'conditions' => array_merge($conditions, array(
                    'Truck.status' => 1,
                )),
                'fields' => array(
                    'Truck.id', 'Truck.nopol'
                ),
                // 'order' => array(
                //     'Truck.nopol'
                // ),
                'order' => false,
            ), false);

            if( !empty($nopol) && !empty($trucks[$include_this_truck_id]) ) {
                $trucks[$include_this_truck_id] = $nopol;
            }

            return $trucks;
        } else {
            return $conditions;
        }
    }

    function getByDriver($data, $driver_id){
        if(empty($data['Truck'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'Truck.driver_id' => $driver_id,
                )
            ), true, array(
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getByNopol( $data, $nopol = false ) {
        $result = $this->find('first', array(
            'conditions' => array(
                'Truck.nopol' => $nopol,
            ),
        ));

        if( !empty($result) ) {
            $data = array_merge($data, $result);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nopol = !empty($data['named']['nopol'])?urldecode($data['named']['nopol']):false;
        $type = !empty($data['named']['type'])?urldecode($data['named']['type']):1;
        $driver = !empty($data['named']['driver'])?urldecode($data['named']['driver']):false;
        $driver_no_id = !empty($data['named']['driver_no_id'])?urldecode($data['named']['driver_no_id']):false;
        $customerid = !empty($data['named']['customerid'])?urldecode($data['named']['customerid']):false;
        $year = !empty($data['named']['year'])?urldecode($data['named']['year']):false;
        $sort = !empty($data['named']['sort'])?urldecode($data['named']['sort']):false;
        $direction = !empty($data['named']['direction'])?urldecode($data['named']['direction']):false;
        $status_maintenance = !empty($data['named']['status_maintenance'])?urldecode($data['named']['status_maintenance']):false;

        if(!empty($customerid) || $sort == 'CustomerNoType.code'){
            $this->unBindModel(array(
                'hasMany' => array(
                    'TruckCustomer'
                )
            ));

            $this->bindModel(array(
                'hasOne' => array(
                    'TruckCustomer' => array(
                        'className' => 'TruckCustomer',
                        'foreignKey' => 'truck_id',
                        'conditions' => array(
                            'TruckCustomer.primary' => 1
                        )
                    ),
                    'CustomerNoType' => array(
                        'className' => 'CustomerNoType',
                        'foreignKey' => false,
                        'conditions' => array(
                            'TruckCustomer.customer_id = CustomerNoType.id',
                        )
                    )
                )
            ), false);
        }

        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['Truck.id'] = $nopol;
            } else {
                $default_options['conditions']['Truck.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($driver)){
            $default_options['conditions']['Driver.name LIKE '] = '%'.$driver.'%';
            $default_options['contain'][] = 'Driver';
        }
        if(!empty($driver_no_id)){
            $default_options['conditions']['Driver.no_id LIKE '] = '%'.$driver_no_id.'%';
            $default_options['contain'][] = 'Driver';
        }
        if(!empty($customerid)){
            $default_options['conditions']['TruckCustomer.customer_id'] = $customerid;
            $default_options['contain'][] = 'TruckCustomer';
        }
        if( !empty($status_maintenance) ) {
            $this->unBindModel(array(
                'hasMany' => array(
                    'ViewTruckMaintenance',
                )
            ));
            $this->bindModel(array(
                'hasOne' => array(
                    'ViewTruckMaintenance' => array(
                        'className' => 'ViewTruckMaintenance',
                        'foreignKey' => 'truck_id',
                    ),
                )
            ), false);
            
            $this->virtualFields['progress'] = 'IFNULL((ViewTruckMaintenance.total_lead_time / ViewTruckMaintenance.target)*100, 0)';

            $default_options['contain'][] = 'ViewTruckMaintenance';
            $having = false;

            switch ($status_maintenance) {
                case 'warning':
                    $having = ' HAVING 
                    Truck__progress > 60
                    AND Truck__progress < 80';
                    break;
                case 'danger':
                    $having = ' HAVING 
                    Truck__progress > 80';
                    break;
            }

            $default_options['group'][] = 'Truck.id '.$having;
        }

        if( !empty($sort) ) {
            switch ($sort) {
                case 'TruckBrand.name':
                    $default_options['contain'][] = 'TruckBrand';
                    break;
                case 'TruckCategory.name':
                    $default_options['contain'][] = 'TruckCategory';
                    break;
                case 'CustomerNoType.code':
                    $default_options['contain'][] = 'TruckCustomer';
                    $default_options['contain'][] = 'CustomerNoType';
                    break;
                case 'Revenue.total':
                    $this->unBindModel(array(
                        'hasMany' => array(
                            'Revenue',
                            'Ttuj',
                        )
                    ));

                    $this->bindModel(array(
                        'hasOne' => array(
                            'Ttuj' => array(
                                'className' => 'Ttuj',
                                'foreignKey' => 'truck_id',
                            ),
                            'Revenue' => array(
                                'className' => 'Revenue',
                                'foreignKey' => false,
                                'conditions' => array(
                                    'Revenue.ttuj_id = Ttuj.id',
                                    'Revenue.status' => 1,
                                )
                            )
                        )
                    ), false);

                    $this->Revenue->virtualFields['total'] = 'SUM(total_without_tax)';

                    $default_options['contain'][] = 'Ttuj';
                    $default_options['contain'][] = 'Revenue';
                    $default_options['group'][] = 'Truck.id';
                    break;
                case 'ProductHistory.grandtotal':
                    $this->unBindModel(array(
                        'hasMany' => array(
                            'Spk',
                        )
                    ), false);
                    $this->bindModel(array(
                        'hasOne' => array(
                            'Spk' => array(
                                'className' => 'Spk',
                                'foreignKey' => false,
                                'conditions' => array(
                                    'Spk.truck_id = Truck.id',
                                    'Spk.status' => 1,
                                )
                            ),
                            'ProductExpenditure' => array(
                                'className' => 'ProductExpenditure',
                                'foreignKey' => false,
                                'conditions' => array(
                                    'ProductExpenditure.document_id = Spk.id',
                                    'ProductExpenditure.document_type' => 'internal',
                                    'ProductExpenditure.transaction_status' => array(
                                        'approved',
                                        'paid',
                                        'half_paid',
                                        'posting'
                                    ),
                                    'ProductExpenditure.status' => 1,
                                )
                            ),
                            'ProductExpenditureDetail' => array(
                                'className' => 'ProductExpenditureDetail',
                                'foreignKey' => false,
                                'conditions' => array(
                                    'ProductExpenditureDetail.product_expenditure_id = ProductExpenditure.id',
                                    'ProductExpenditure.status' => 1,
                                )
                            ),
                            'ProductHistory' => array(
                                'className' => 'ProductHistory',
                                'foreignKey' => false,
                                'conditions' => array(
                                    'ProductExpenditureDetail.id = ProductHistory.transaction_id',
                                    'ProductHistory.transaction_type NOT' => array(
                                        'product_expenditure_void',
                                        'product_adjustment_min_void',
                                    ),
                                    'ProductHistory.transaction_type' => 'product_expenditure',
                                    'ProductHistory.status' => 1,
                                )
                            ),
                        )
                    ), false);
                    $this->ProductHistory->virtualFields['grandtotal'] = 'SUM(ProductHistory.qty*ProductHistory.price)';

                    $default_options['contain'][] = 'Spk';
                    $default_options['contain'][] = 'ProductExpenditure';
                    $default_options['contain'][] = 'ProductExpenditureDetail';
                    $default_options['contain'][] = 'ProductHistory';
                    $default_options['group'][] = 'Truck.id';

                    if( !empty($year) ) {
                        $default_options['conditions'][]['OR'] = array(
                            'DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y\')' => $year,
                            'ProductExpenditure.id' => NULL,
                        );
                    }
                    break;
            }
        }
        
        return $default_options;
    }

    function getBiayaLainLain ( $data, $id, $params = false ) {
        $dateFrom = !empty($params['named']['DateFrom'])?$params['named']['DateFrom']:false;
        $dateTo = !empty($params['named']['DateTo'])?$params['named']['DateTo']:false;
        $default_options = array(
            'conditions' => array(
                'CashBank.receiving_cash_type' => array( 'out', 'prepayment_out' ),
                'CashBankDetail.truck_id' => $id,
                'CashBank.status' => 1,
                'CashBank.is_rejected' => 0,
            ),
            'contain' => array(
                'CashBank',
            ),
        );
        $document_options = array(
            'conditions' => array(
                'DocumentPaymentDetail.truck_id' => $id,
                'DocumentPayment.status' => 1,
                'DocumentPayment.is_canceled' => 0,
            ),
            'contain' => array(
                'DocumentPayment',
            ),
        );

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') >='] = $dateFrom;
                $document_options['conditions']['DATE_FORMAT(DocumentPayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') <='] = $dateTo;
                $document_options['conditions']['DATE_FORMAT(DocumentPayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }

        $this->CashBankDetail->virtualFields['total_cashbank'] = 'SUM(total)';
        $value = $this->CashBankDetail->getData('first', $default_options);

        $this->DocumentPaymentDetail->virtualFields['total_amount'] = 'SUM(DocumentPaymentDetail.amount)';
        $document = $this->DocumentPaymentDetail->getData('first', $document_options);

        if( !empty($value) ) {
            $data = array_merge($data, $value);
        }
        if( !empty($document) ) {
            $data = array_merge($data, $document);
        }

        return $data;
    }

    function _callListTruck ( $id = false, $ttuj_id = false ) {
        $plantCityId = Configure::read('__Site.Branch.Plant.id');
        $conditions = array();
        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.truck_id', 'Ttuj.truck_id'
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

        if( !empty($plantCityId) ) {
            $conditions['Truck.branch_id'] = $plantCityId;
        }

        $conditions = $this->getListTruck( $id, true, false, $plantCityId, $conditions, $ttujs );

        return $this->getData('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
            'order' => array(
                'Truck.nopol'
            ),
        ), false, array(
            'branch' => false,
        ));
    }
}
?>