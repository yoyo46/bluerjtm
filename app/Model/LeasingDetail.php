<?php
class LeasingDetail extends AppModel {
	var $name = 'LeasingDetail';
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
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap di pilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'LeasingDetail.status' => 'DESC'
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