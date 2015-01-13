<?php
class CustomerType extends AppModel {
	var $name = 'CustomerType';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Customer name harap diisi'
            ),
        ),
	);

	function getData($find, $options = false, $isActive = false){
        $default_options = array(
            'conditions'=> array(
                'CustomerType.status' => 1,
            ),
            'order'=> array(
                'CustomerType.name' => 'ASC'
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

        if( !empty($isActive) ) {
            $default_options['conditions']['CustomerType.status'] = 1;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMergeCustomerType ( $data = false ) {
        if( empty($data['Customer']['customer_type_id']) ) {
            $customerType = $this->getData('first', array(
                'conditions' => array(
                    'CustomerType.id' => $data['Customer']['customer_type_id'],
                    'PropertyType->.active' => 1,
                ),
            ));

            if( !empty($customerType) ) {
                $data = array_merge($data, $customerType);
            }
        }

        return $data;
    }
}
?>