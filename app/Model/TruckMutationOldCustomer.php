<?php
class TruckMutationOldCustomer extends AppModel {
	var $name = 'TruckMutationOldCustomer';
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
	);

    function getData( $find, $options = false, $is_merge = true ){
		$default_options = array(
			'conditions' => array(
				'TruckMutationOldCustomer.status' => 1,
			),
            'order' => array(
                'TruckMutationOldCustomer.id' => 'ASC',
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
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
		if( empty($data['TruckMutationOldCustomer']) ) {
			$value = $this->getData('all', array(
				'conditions' => array(
					'TruckMutationOldCustomer.truck_mutation_id'=> $truck_id,
				),
			));

			if( !empty($value) ) {
				$data['TruckMutationOldCustomer'] = $value;
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
            
            foreach ($datas as $customer_id => $customer_name) {
                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                $data['TruckMutationOldCustomer'] = array(
                	'customer_id' => $customer_id,
                	'customer_name' => $customer_name,
            	);

                if( !empty($truck_mutation_id) ) {
                    $data['TruckMutationOldCustomer']['truck_mutation_id'] = $truck_mutation_id;
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