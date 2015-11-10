<?php
class UangJalan extends AppModel {
	var $name = 'UangJalan';
	var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'title' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama Uang Jalan harap diisi'
            ),
        ),
        'from_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota asal harap dipilih'
            ),
        ),
        'to_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota tujuan harap dipilih'
            ),
        ),
        'distance' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jarak tempuh harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Jarak tempuh harus berupa angka',
            ),
        ),
        'capacity' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kapasitas harap diisi'
            ),
            // 'numeric' => array(
            //     'rule' => array('numeric'),
            //     'message' => 'Kapasitas harus berupa angka',
            // ),
        ),
        'arrive_lead_time' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Lead time sampai tujuan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Lead time sampai tujuan harus berupa angka',
            ),
        ),
        'back_lead_time' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Lead time pulang ke pool harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Lead time pulang ke pool harus berupa angka',
            ),
        ),
        'uang_jalan_1' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang jalan pertama harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang jalan pertama harus berupa angka',
            ),
        ),
        'uang_jalan_2' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Uang jalan kedua harap diisi'
            // ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang jalan kedua harus berupa angka',
            ),
        ),
        'uang_jalan_extra' => array(
            'checkUangJalanExtra' => array(
                'rule' => array('checkUangJalanExtra'),
                'message' => 'Mohon lengkapi data Uang Jalan Extra'
            ),
        ),
        'commission' => array(
            'checkCommission' => array(
                'rule' => array('checkCommission'),
                'message' => 'Komisi harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Komisi harus berupa angka',
            ),
        ),
        'commission_extra' => array(
            'commissionExtra' => array(
                'rule' => array('commissionExtra'),
                'message' => 'Mohon lengkapi data Komisi Extra'
            ),
        ),
        'uang_kuli_muat' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Uang kuli muat harap diisi'
            // ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang kuli muat harus berupa angka',
            ),
        ),
        'uang_kuli_bongkar' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Uang kuli bongkar harap diisi'
            // ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang kuli bongkar harus berupa angka',
            ),
        ),
        'asdp' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Uang penyebrangan (ASDP) harap diisi'
            // ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang penyebrangan (ASDP) harus berupa angka',
            ),
        ),
        'uang_kawal' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Uang kawal harap diisi'
            // ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang kawal harus berupa angka',
            ),
        ),
        'uang_keamanan' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Uang keamanan harap diisi'
            // ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang keamanan harus berupa angka',
            ),
        ),
	);

    var $belongsTo = array(
        'FromCity' => array(
            'className' => 'City',
            'foreignKey' => 'from_city_id',
        ),
        'ToCity' => array(
            'className' => 'City',
            'foreignKey' => 'to_city_id',
        ),
    );

    var $hasMany = array(
        'UangJalanTipeMotor' => array(
            'className' => 'UangJalanTipeMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'UangJalanTipeMotor.status' => 1,
            ),
        ),
        'CommissionGroupMotor' => array(
            'className' => 'CommissionGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'CommissionGroupMotor.status' => 1,
            ),
        ),
        'AsdpGroupMotor' => array(
            'className' => 'AsdpGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'AsdpGroupMotor.status' => 1,
            ),
        ),
        'UangKawalGroupMotor' => array(
            'className' => 'UangKawalGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'UangKawalGroupMotor.status' => 1,
            ),
        ),
        'UangKeamananGroupMotor' => array(
            'className' => 'UangKeamananGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'UangKeamananGroupMotor.status' => 1,
            ),
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'UangJalan.title' => 'DESC'
            ),
            'contain' => array(),
            // 'contain' => array(
            //     'FromCity',
            //     'ToCity',
            //     'UangJalanTipeMotor',
            //     'CommissionGroupMotor',
            //     'AsdpGroupMotor',
            //     'UangKawalGroupMotor',
            //     'UangKeamananGroupMotor',
            // ),
            'fields'=> array(),
            'group'=> array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['UangJalan.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['UangJalan.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['UangJalan.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['UangJalan.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options)){
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function checkUangJalanExtra($data) {
        $uang_jalan_extra = !empty($this->data['UangJalan']['uang_jalan_extra'])?trim(str_replace(',', '', $this->data['UangJalan']['uang_jalan_extra'])):false;
        $min_capacity = !empty($this->data['UangJalan']['min_capacity'])?$this->data['UangJalan']['min_capacity']:false;
        $uang_jalan_extra_per_unit = !empty($this->data['UangJalan']['uang_jalan_extra_per_unit'])?$this->data['UangJalan']['uang_jalan_extra_per_unit']:false;

        // if( !empty($uang_jalan_extra_per_unit) && ( empty($uang_jalan_extra) || empty($min_capacity) ) ) {
        if( !empty($uang_jalan_extra_per_unit) && ( empty($uang_jalan_extra) ) ) {
            return false;
        // } else if( !empty($uang_jalan_extra) || !empty($min_capacity) ) {
        } else if( !empty($uang_jalan_extra) ) {
            if( empty($uang_jalan_extra) ) {
                return false;
            } 
            // else if( empty($min_capacity) ) {
            //     return false;
            // } 
            else {
                return true; 
            }
        } else {
            return true; 
        }
    }

    function checkMinCapacity($data) {
        $uang_jalan_extra = !empty($this->data['UangJalan']['uang_jalan_extra'])?trim($this->data['UangJalan']['uang_jalan_extra']):false;

        if( !empty($uang_jalan_extra) ) {
            $min_capacity = !empty($data['min_capacity'])?trim($data['min_capacity']):false;
            $min_capacity = !empty($min_capacity)?str_replace(',', '', $min_capacity):false;

            if( !empty($min_capacity) && is_numeric($min_capacity) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true; 
        }
    }

    function commissionExtra () {
        $commission_extra = !empty($this->data['UangJalan']['commission_extra'])?trim(str_replace(',', '', $this->data['UangJalan']['commission_extra'])):false;
        $commission_min_qty = !empty($this->data['UangJalan']['commission_min_qty'])?$this->data['UangJalan']['commission_min_qty']:false;

        if( !empty($commission_extra) || !empty($commission_min_qty) || !empty($commission_extra_op) ) {
            if( empty($commission_extra) ) {
                return false;
            } else if( empty($commission_min_qty) ) {
                return false;
            } else {
                return true; 
            }
        } else {
            return true; 
        }
    }

    function checkCommission () {
        $commission = !empty($this->data['UangJalan']['commission'])?trim(str_replace(',', '', $this->data['UangJalan']['commission'])):false;
        $uang_jalan_per_unit = !empty($this->data['UangJalan']['uang_jalan_per_unit'])?$this->data['UangJalan']['uang_jalan_per_unit']:false;

        if( empty($uang_jalan_per_unit) ) {
            if( empty($commission) ) {
                return false;
            } else {
                return true; 
            }
        } else {
            return true; 
        }
    }

    function getKotaAsal ( $include_this_city = false ) {
        return $this->getData('list', array(
            'group' => array(
                'UangJalan.from_city_id'
            ),
            'fields' => array(
                'UangJalan.from_city_id', 'FromCity.name'
            ),
            'contain' => array(
                'FromCity'
            ),
            'conditions' => array(
                'OR' => array(
                    'UangJalan.branch_id' => Configure::read('__Site.config_branch_id'),
                    'UangJalan.from_city_id' => $include_this_city,
                ),
            )
        ), true, array(
            'branch' => false,
        ));
    }

    function getKotaTujuan ( $from_city_id ) {
        return $this->getData('list', array(
            'conditions' => array(
                'UangJalan.from_city_id' => $from_city_id,
            ),
            'group' => array(
                'UangJalan.to_city_id'
            ),
            'fields' => array(
                'UangJalan.to_city_id', 'ToCity.name'
            ),
            'contain' => array(
                'ToCity'
            ),
        ));
    }

    function getNopol ( $from_city_id, $to_city_id, $capacity ) {
        $uangJalan = $this->getData('first', array(
            'conditions' => array(
                'UangJalan.capacity' => $capacity,
                'UangJalan.from_city_id' => $from_city_id,
                'UangJalan.to_city_id' => $to_city_id,
            ),
        ));

        if( !empty($uangJalan) ) {
            $id = !empty($uangJalan['UangJalan']['id'])?$uangJalan['UangJalan']['id']:false;
            $uangJalan = $this->gerMergeBiaya( $uangJalan, $id );
        }
        
        return $uangJalan;
    }

    function gerMergeBiaya ( $data, $id, $with_count = false ) {
        $data = $this->UangJalanTipeMotor->getMerge( $data, $id, $with_count );
        $data = $this->CommissionGroupMotor->getMerge( $data, $id, $with_count );
        $data = $this->AsdpGroupMotor->getMerge( $data, $id, $with_count );
        $data = $this->UangKawalGroupMotor->getMerge( $data, $id, $with_count );
        $data = $this->UangKeamananGroupMotor->getMerge( $data, $id, $with_count );

        return $data;
    }

    function getMerge( $data, $id ){
        if(empty($data['UangJalan'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'UangJalan.id' => $id
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>