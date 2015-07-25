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
        'driver_penganti_id' => array(
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
        'UangJalan' => array(
            'className' => 'UangJalan',
            'foreignKey' => 'uang_jalan_id',
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'ToCity' => array(
            'className' => 'City',
            'foreignKey' => 'to_city_id',
        ),
    );

    var $hasMany = array(
        'TtujPerlengkapan' => array(
            'className' => 'TtujPerlengkapan',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'TtujPerlengkapan.status' => 1,
            ),
        ),
        'SuratJalan' => array(
            'className' => 'SuratJalan',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'SuratJalan.status' => 1,
            ),
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $status = 'active' ){
        $default_options = array(
            'conditions'=> array(
                'Ttuj.group_branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
            'contain' => array(),
            // 'contain' => array(
            //     'TtujPerlengkapan',
            //     'UangJalan' => array(
            //         'UangJalanTipeMotor',
            //         'CommissionGroupMotor',
            //         'AsdpGroupMotor',
            //         'UangKawalGroupMotor',
            //         'UangKeamananGroupMotor',
            //     ),
            // ),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Ttuj.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Ttuj.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Ttuj.status'] = 1;
                break;
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
        if( empty($this->data['Ttuj']['driver_name']) && empty($this->data['Ttuj']['driver_penganti_id']) ) {
            return false;
        } else {
            return true;
        }
    }

    function getSumUnit($data, $ttuj_id, $surat_jalan_id = false, $data_action = false){
        if( empty($data['Qty']) ){
            $this->TtujTipeMotor = ClassRegistry::init('TtujTipeMotor');
            
            $data_merge = $this->TtujTipeMotor->find('first', array(
                'conditions' => array(
                    'TtujTipeMotor.status' => 1,
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                ),
                'group' => array(
                    'TtujTipeMotor.ttuj_id',
                ),
                'fields' => array(
                    'SUM(TtujTipeMotor.qty) AS qty',
                ),
            ));

            if(!empty($data_merge[0])){
                $data['Qty'] = $data_merge[0]['qty'];
            }

            $conditions = array(
                'SuratJalan.status' => 1,
                'SuratJalan.ttuj_id' => $ttuj_id,
                'SuratJalan.id <>' => $surat_jalan_id,
            );
            $data_merge = $this->SuratJalan->find('first', array(
                'conditions' => $conditions,
                'group' => array(
                    'SuratJalan.ttuj_id',
                ),
                'fields' => array(
                    'SUM(SuratJalan.qty) AS qty',
                ),
            ));

            if(!empty($data_merge[0])){
                $data['QtySJ'] = $data_merge[0]['qty'];
            }

            switch ($data_action) {
                case 'tgl_surat_jalan':
                    $data_merge = $this->SuratJalan->find('first', array(
                        'conditions' => $conditions,
                        'fields' => array(
                            'SuratJalan.tgl_surat_jalan',
                        ),
                        'order' => array(
                            'SuratJalan.tgl_surat_jalan' => 'DESC',
                            'SuratJalan.id' => 'DESC',
                        ),
                    ));

                    if(!empty($data_merge)){
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
                    'Ttuj.driver_penganti_id' => $driver_id,
                ),
                'Ttuj.is_sj_completed' => 0,
            ),
            'contain' => false,
        ));

        return $sjCount;
    }

    function getTruckStatus ( $data, $truck_id ) {
        $truckAway = $this->getData('paginate', array(
            'conditions' => array(
                'Ttuj.is_pool' => 0,
                'Ttuj.truck_id' => $truck_id,
            ),
        ));

        if( !empty($truckAway) ) {
            $data = array_merge($data, $truckAway);
        }

        return $data;
    }

    function getTtujPayment ( $ttuj_id, $data_action ) {
        $data_ttuj = $this->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $ttuj_id,
            ),
        ), true, 'all');
        $total = 0;

        if( !empty($data_ttuj) ) {
            $this->Customer = ClassRegistry::init('Customer');
            $this->Driver = ClassRegistry::init('Driver');
            $customer_id = !empty($data_ttuj['Ttuj']['customer_id'])?$data_ttuj['Ttuj']['customer_id']:'';
            $driver_id = !empty($data_ttuj['Ttuj']['driver_id'])?$data_ttuj['Ttuj']['driver_id']:'';
            $data_ttuj = $this->Customer->getMerge($data_ttuj, $customer_id);
            $data_ttuj = $this->Driver->getMerge($data_ttuj, $driver_id);

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

        $data_ttuj['total'] = $total;
        return $data_ttuj;
    }

    // function setTtuj ( $ttuj_id, $data ) {
    //     $data['Ttuj'] = $data;
    //     $this->set($data);
    //     $this->id = $ttuj_id;

    //     return $this->save();
    // }

    function getMerge($data, $id){
        if(empty($data['Ttuj'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Ttuj.id' => $id
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
        $this->Driver = ClassRegistry::init('Driver');

        $driver_penganti_id = !empty($data['Ttuj']['driver_penganti_id'])?$data['Ttuj']['driver_penganti_id']:false;
        $data = $this->Driver->getMerge($data, $driver_penganti_id, 'DriverPenganti');
        $data = $this->TtujTipeMotor->getMergeTtujTipeMotor( $data, $ttuj_id, 'all');
        $data = $this->TtujPerlengkapan->getMerge($data, $ttuj_id);

        return $data;
    }
}
?>