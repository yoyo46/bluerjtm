<?php
class CustomerTargetUnit extends AppModel {
	var $name = 'CustomerTargetUnit';
	var $validate = array(
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
            'customer_id' => array(
                'rule' => array('isUnique', array('customer_id', 'month', 'year'), false),
                'message' => 'Target sudah terdaftar.'
            )
        ),
        'month' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bulan harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Bulan harap dipilih'
            ),
        ),
        'year' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tahun harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Tahun harap dipilih'
            ),
        ),
        'unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Target Unit harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Target Unit dipilih'
            ),
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'CustomerTargetUnit.status' => 1,
            ),
            'order'=> array(
                'CustomerTargetUnit.customer_id' => 'ASC'
            ),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
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
}
?>