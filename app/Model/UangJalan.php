<?php
class UangJalan extends AppModel {
	var $name = 'UangJalan';
	var $validate = array(
        // 'customer_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Customer harap dipilih'
        //     ),
        // ),
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
        'UangExtraGroupMotor' => array(
            'className' => 'UangExtraGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'UangExtraGroupMotor.status' => 1,
            ),
        ),
        'CommissionGroupMotor' => array(
            'className' => 'CommissionGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'CommissionGroupMotor.status' => 1,
            ),
        ),
        'CommissionExtraGroupMotor' => array(
            'className' => 'CommissionExtraGroupMotor',
            'foreignKey' => 'uang_jalan_id',
            'conditions' => array(
                'CommissionExtraGroupMotor.status' => 1,
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

	function getData($find, $options = false){
        $default_options = array(
            'fields'=> array(),
            'conditions'=> array(
                'UangJalan.status' => 1,
            ),
            'order'=> array(
                'UangJalan.status' => 'DESC'
            ),
            'contain' => array(
                'FromCity',
                'ToCity',
                'UangJalanTipeMotor',
                // 'UangExtraGroupMotor',
                'CommissionGroupMotor',
                // 'CommissionExtraGroupMotor',
                'AsdpGroupMotor',
                'UangKawalGroupMotor',
                'UangKeamananGroupMotor',
            )
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
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

        if( !empty($uang_jalan_extra_per_unit) && ( empty($uang_jalan_extra) || empty($min_capacity) ) ) {
            return false;
        } else if( !empty($uang_jalan_extra) || !empty($min_capacity) ) {
            if( empty($uang_jalan_extra) ) {
                return false;
            } else if( empty($min_capacity) ) {
                return false;
            } else {
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

    // function getKotaAsal ($customer_id) {
    function getKotaAsal () {
        $fromCity = $this->getData('all', array(
            'conditions' => array(
                'UangJalan.status' => 1,
                // 'UangJalan.customer_id' => $customer_id,
            ),
            'group' => array(
                'UangJalan.from_city_id'
            ),
            'fields' => array(
                'UangJalan.from_city_id', 'FromCity.name'
            ),
            'contain' => array(
                'FromCity'
            ),
        ));
        $resultCity = array();

        if( !empty($fromCity) ) {
            foreach ($fromCity as $key => $city) {
                $resultCity[$city['UangJalan']['from_city_id']] = $city['FromCity']['name'];
            }
        }

        return $resultCity;
    }

    // function getKotaTujuan ($customer_id, $from_city_id) {
    function getKotaTujuan ( $from_city_id ) {
        $toCity = $this->getData('all', array(
            'conditions' => array(
                'UangJalan.status' => 1,
                // 'UangJalan.customer_id' => $customer_id,
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
        $resultCity = array();

        if( !empty($toCity) ) {
            foreach ($toCity as $key => $city) {
                $resultCity[$city['UangJalan']['to_city_id']] = $city['ToCity']['name'];
            }
        }

        return $resultCity;
    }

    // function getNopol ($customer_id, $from_city_id, $to_city_id) {
    function getNopol ( $from_city_id, $to_city_id, $capacity ) {
        $result = false;
        $this->Truck = ClassRegistry::init('Truck');
        $uangJalan = $this->getData('first', array(
            'conditions' => array(
                'UangJalan.status' => 1,
                'UangJalan.capacity' => $capacity,
                'UangJalan.from_city_id' => $from_city_id,
                'UangJalan.to_city_id' => $to_city_id,
            ),
        ));

        // if( !empty($uangJalan) ) {
        //     $result = $this->Truck->getData('list', array(
        //         'conditions' => array(
        //             'Truck.status' => 1,
        //             'Truck.capacity' => $uangJalan['UangJalan']['capacity'],
        //         ),
        //         'fields' => array(
        //             'Truck.id', 'Truck.nopol'
        //         ),
        //     ));
        // }

        // return array(
        //     'result' => $result,
        //     'uangJalan' => $uangJalan,
        // );
        return $uangJalan;
    }
}
?>