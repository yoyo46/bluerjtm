<?php
class TruckMutationCustomer extends AppModel {
	var $name = 'TruckMutationCustomer';
	var $validate = array(
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alokasi harap dipilih'
            ),
        ),
    );

	var $belongsTo = array(
		'TruckMutation' => array(
			'className' => 'TruckMutation',
			'foreignKey' => 'truck_mutation_id',
		),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
	);

    function getData( $find, $options = false, $is_merge = true ){
		$default_options = array(
			'conditions' => array(
				'TruckMutationCustomer.status' => 1,
			),
            'order' => array(
                'TruckMutationCustomer.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
		);

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['groups'])){
                $default_options['groups'] = $options['groups'];
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

	function getMerge ( $data = false, $truck_id = false ) {
		if( empty($data['TruckMutationCustomer']) ) {
			$value = $this->getData('all', array(
				'conditions' => array(
					'TruckMutationCustomer.truck_mutation_id'=> $truck_id,
				),
			));

			if( !empty($value) ) {
				$data['TruckMutationCustomer'] = $value;
			}
		}

		return $data;
	}

    function doSave( $datas, $value = false, $id = false, $truck_mutation_id, $is_validate = false ) {
        $result = false;

        if ( !empty($datas) ) {
            if( !empty($truck_mutation_id) ) {
                $this->deleteAll(array(
                    'truck_mutation_id' => $truck_mutation_id
                ));
            }
            
            foreach ($datas as $key => $customer_id) {
                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                $dataCustomer = $this->Customer->getMerge(array(), $customer_id);
                $data['TruckMutationCustomer'] = array(
                    'customer_id' => $customer_id,
                	'customer_name' => !empty($dataCustomer['Customer']['customer_name_code'])?$dataCustomer['Customer']['customer_name_code']:false,
            	);

                if( !empty($truck_mutation_id) ) {
                    $data['TruckMutationCustomer']['truck_mutation_id'] = $truck_mutation_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( $flagSave ) {
                        $result = array(
                            'msg' => __('Berhasil menyimpan alokasi customer'),
                            'status' => 'success',
                        );
                    } else {
                        $result = array(
                            'msg' => __('Gagal menyimpan alokasi customer'),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan alokasi customer'),
                        'status' => 'error',
                    );
                }
            }

            if( empty($result) ) {
                $result = array(
                    'msg' => __('Gagal menyimpan alokasi customer'),
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>