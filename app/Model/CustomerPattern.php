<?php
class CustomerPattern extends AppModel {
	var $name = 'CustomerPattern';
	var $validate = array(
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer tidak ditemukan'
            ),
        ),
        'pattern' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pattern harap diisi'
            ),
        ),
        'last_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No awal dokumen harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'No awal dokumen harus berupa angka',
            ),
        ),
        'min_digit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Min Digit harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Min Digit harus berupa angka',
            ),
        ),
	);

	var $belongsTo = array(
        'CustomerNoType' => array(
            'className' => 'CustomerNoType',
            'foreignKey' => 'customer_id',
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions' => array(),
            'contain' => array(
                'CustomerNoType',
            ),
            'fields' => array(),
            'group' => array(),
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
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