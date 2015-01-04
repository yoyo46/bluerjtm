<?php
class Customer extends AppModel {
	var $name = 'Customer';
	var $validate = array(
        'customer_type_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Customer harap dipilih'
            ),
        ),
        'customer_group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup Customer harap dipilih'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer name harap diisi'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Address harap diisi'
            ),
        ),
        'phone_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Phone harap diisi'
            ),
        ),
        'target_rit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Taget Rit / bln harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'CustomerType' => array(
            'className' => 'CustomerType',
            'foreignKey' => 'customer_type_id',
        ),
        'CustomerGroup' => array(
            'className' => 'CustomerGroup',
            'foreignKey' => 'customer_group_id',
        )
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Customer.status' => 1,
            ),
            'order'=> array(
                'Customer.name' => 'ASC'
            ),
            'contain' => array(
                'CustomerType',
                'CustomerGroup',
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

    function getMerge($data, $id){
        if(empty($data['Customer'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>