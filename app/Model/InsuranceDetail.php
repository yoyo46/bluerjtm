<?php
class InsuranceDetail extends AppModel {
	var $name = 'InsuranceDetail';
	var $validate = array(
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga harap diisi dengan angka',
            ),
        ),
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nopol truk harap diisi'
            ),
            'checkNopol' => array(
                'rule' => array('checkNopol'),
                'message' => 'Nopol telah terdaftar. Mohon masukan nopol lain.',
            ),
        ),
        'condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kondisi pertanggungan harap diisi'
            ),
        ),
        'rate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Rate harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Insurance' => array(
            'className' => 'Insurance',
            'foreignKey' => 'insurance_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

    function checkNopol() {
        $truck_id = $this->filterEmptyField($this->data, 'InsuranceDetail', 'truck_id');
        $nopol = $this->filterEmptyField($this->data, 'InsuranceDetail', 'nopol');
        $insurance_id = $this->filterEmptyField($this->Insurance->data, 'Insurance', 'id');

        $options = $this->Insurance->getData('paginate', array(
            'conditions' => array(
                'InsuranceDetail.nopol' => $nopol,
                'InsuranceDetail.insurance_id <>' => $insurance_id,
            ),
            'contain' => array(
                'Insurance',
            ),
        ), array(
            'status' => 'publish',
        ));
        $value = $this->getData('first', $options);

        if( !empty($value) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'InsuranceDetail.status' => 1,
            ),
            'order'=> array(
                'InsuranceDetail.status' => 'DESC'
            ),
            'contain' => array(),
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
}
?>