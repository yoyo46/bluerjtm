<?php
class Company extends AppModel {
	var $name = 'Company';
	var $validate = array(
        'company_type_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Customer harap dipilih'
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
        )
	);

	var $belongsTo = array(
        'CompanyType' => array(
            'className' => 'CompanyType',
            'foreignKey' => 'company_type_id',
        )
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Company.status' => 'DESC'
            ),
            'contain' => array(
                'CompanyType'
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
        if(empty($data['Company'])){
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