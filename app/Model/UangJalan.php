<?php
class UangJalan extends AppModel {
	var $name = 'UangJalan';
	var $validate = array(
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'group_classification_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup klasifikasi harap dipilih'
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
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Kapasitas harus berupa angka',
            ),
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
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang jalan kedua harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang jalan kedua harus berupa angka',
            ),
        ),
        'commission' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Komisi harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Komisi harus berupa angka',
            ),
        ),
        'uang_kuli_muat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang kuli muat harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang kuli muat harus berupa angka',
            ),
        ),
        'uang_kuli_bongkar' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang kuli bongkar harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang kuli bongkar harus berupa angka',
            ),
        ),
        'asdp' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang penyebrangan (ASDP) harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang penyebrangan (ASDP) harus berupa angka',
            ),
        ),
        'uang_kawal' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang kawal harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang kawal harus berupa angka',
            ),
        ),
        'uang_keamanan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang keamanan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang keamanan harus berupa angka',
            ),
        ),
        'uang_jalan_extra' => array(
            'checkUangJalanExtra' => array(
                'rule' => array('checkUangJalanExtra'),
                'message' => 'Uang jalan extra harap diisi dan berupa angka'
            ),
        ),
        'min_capacity' => array(
            'checkMinCapacity' => array(
                'rule' => array('checkMinCapacity'),
                'message' => 'Kapasitas minimum harap diisi dan berupa angka'
            ),
        ),
	);

    var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'GroupClassification' => array(
            'className' => 'GroupClassification',
            'foreignKey' => 'group_classification_id',
        ),
        'FromCity' => array(
            'className' => 'City',
            'foreignKey' => 'from_city_id',
        ),
        'ToCity' => array(
            'className' => 'City',
            'foreignKey' => 'to_city_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'UangJalan.status' => 'DESC'
            ),
            'contain' => array(
                'Customer',
                'GroupClassification',
                'FromCity',
                'ToCity'
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
        if( !empty($this->data['UangJalan']['is_unit']) ) {
            if( !empty($data['uang_jalan_extra']) && is_numeric($data['uang_jalan_extra']) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true; 
        }
    }

    function checkMinCapacity($data) {
        if( !empty($this->data['UangJalan']['is_unit']) ) {
            if( !empty($data['min_capacity']) && is_numeric($data['min_capacity']) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true; 
        }
    }
}
?>