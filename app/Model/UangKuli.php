<?php
class UangKuli extends AppModel {
    var $name = 'UangKuli';
    var $validate = array(
        'title' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama Uang Jalan harap diisi'
            ),
        ),
        'city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota harap dipilih'
            ),
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'Uang Kuli sudah terdaftar'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'capacity' => array(
            'checkCapacity' => array(
                'rule' => array('checkCapacity'),
                'message' => 'Kapasitas Muat harap diisi'
            ),
        ),
        'uang_kuli_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Uang Kuli Muat harap diisi'
            ),
        ),
        'uang_kuli_muat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang kuli muat harap diisi'
            ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang kuli muat harus berupa angka',
            ),
        ),
    );

    var $belongsTo = array(
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
    );

    var $hasMany = array(
        'UangKuliGroupMotor' => array(
            'className' => 'UangKuliGroupMotor',
            'foreignKey' => 'uang_kuli_id',
            'conditions' => array(
                'UangKuliGroupMotor.status' => 1,
            ),
        ),
    );

    function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'fields'=> array(),
            'conditions'=> array(
                'UangKuli.status' => 1,
            ),
            'order'=> array(
                'UangKuli.status' => 'DESC'
            ),
            'contain' => array(
                'City',
                'UangKuliGroupMotor',
            )
        );

        if(!empty($options) && $is_merge){
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
        } else if(!empty($options)) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function checkCapacity($data) {
        $uang_kuli_type = !empty($this->data['UangKuli']['uang_kuli_type'])?$this->data['UangKuli']['uang_kuli_type']:false;

        if( $uang_kuli_type == 'per_truck' ) {
            if( empty($this->data['UangKuli']['capacity']) ) {
                return false;
            } else {
                return true; 
            }
        } else {
            return true; 
        }
    }

    function checkUniq($data) {
        $city_id = !empty($this->data['UangKuli']['city_id'])?trim($this->data['UangKuli']['city_id']):false;
        $category = !empty($this->data['UangKuli']['category'])?trim($this->data['UangKuli']['category']):false;
        $customer_id = !empty($this->data['UangKuli']['customer_id'])?trim($this->data['UangKuli']['customer_id']):false;
        $checkCity = $this->getData('first', array(
            'conditions' => array(
                'UangKuli.customer_id' => $customer_id,
                'UangKuli.city_id' => $city_id,
                'UangKuli.category' => $category,
                'UangKuli.id <>' => $this->id,
                'UangKuli.status' => 1,
            ),
        ), false);

        if( !empty($checkCity) ) {
            return false;
        } else {
            return true; 
        }
    }

    function getUangKuli ( $from_city_id, $to_city_id, $customer_id ) {
        $capacity = !empty($capacity)?$capacity:0;
        $uangKuliMuat = $this->getData('first', array(
            'conditions' => array(
                'UangKuli.status' => 1,
                'UangKuli.city_id' => $from_city_id,
                'UangKuli.category' => 'muat',
            ),
        ));
        $uangKuliBongkar = $this->getData('first', array(
            'conditions' => array(
                'UangKuli.status' => 1,
                'UangKuli.customer_id' => $customer_id,
                'UangKuli.city_id' => $to_city_id,
                'UangKuli.category' => 'bongkar',
            ),
        ));

        return array(
            'UangKuliMuat' => $uangKuliMuat,
            'UangKuliBongkar' => $uangKuliBongkar,
        );
    }
}
?>