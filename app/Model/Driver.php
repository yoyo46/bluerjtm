<?php
class Driver extends AppModel {
	var $name = 'Driver';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama supir harap diisi'
            ),
        ),
        'identity_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nomor identitas harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'nomor identitas harus berupa angka'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'alamat harap diisi'
            ),
        ),
        'phone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'telepon harap diisi'
            ),
        ),
        'uang_makan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang makan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang makan harus berupa angka'
            ),
        ),
	);

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'driver_id',
		)
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'status' => 'DESC'
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

    function getMerge($data, $id){
        if(empty($data['Driver'])){
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