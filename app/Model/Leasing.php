<?php
class Leasing extends AppModel {
	var $name = 'Leasing';
	var $validate = array(
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
            ),
        ),
        'installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cicilan perbulan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Cicilan perbulan harap diisi dengan angka',
            ),
        ),
        'paid_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'fine' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Denda harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Denda harap diisi dengan angka',
            ),
        ),
	);

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Leasing.status' => 'DESC'
            ),
            'contain' => array(
                'Truck'
            ),
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