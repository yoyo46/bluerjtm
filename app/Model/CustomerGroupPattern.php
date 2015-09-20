<?php
class CustomerGroupPattern extends AppModel {
	var $name = 'CustomerGroupPattern';
	var $validate = array(
        'customer_group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group Customer tidak ditemukan'
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
        'CustomerGroup' => array(
            'className' => 'CustomerGroup',
            'foreignKey' => 'customer_group_id',
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions' => array(),
            'contain' => array(
                'CustomerGroup',
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

    function addPattern ( $customer, $data ) {
        $last_number = false;

        if( !empty($customer['CustomerGroupPattern']) ) {
            $last_number = str_replace($customer['CustomerGroupPattern']['pattern'], '', $data['Invoice']['no_invoice']);
            $last_number = intval($last_number)+1;
            $this->set('last_number', $last_number);
            $this->id = $customer['CustomerGroupPattern']['id'];
            $this->save();

            $last_number = sprintf('%s%s', str_pad($last_number, $customer['CustomerGroupPattern']['min_digit'], '0', STR_PAD_LEFT), $customer['CustomerGroupPattern']['pattern']);
        }

        return $last_number;
    }

    function getMerge( $data, $id ){
        if(empty($data['CustomerGroupPattern'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'CustomerGroupPattern.customer_group_id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>