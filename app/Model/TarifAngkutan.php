<?php
class TarifAngkutan extends AppModel {
	var $name = 'TarifAngkutan';
	var $validate = array(
        'name_tarif' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama  tarif harap diisi'
            ),
        ),
        'from_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'kota awal harap dipilih'
            ),
        ),
        'to_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'kota tujuan harap dipilih'
            ),
        ),
        // 'capacity' => array(
        //     'numeric' => array(
        //         'allowEmpty'=> true,
        //         'rule' => array('numeric'),
        //         'message' => 'Kapasitas harus berupa angka',
        //     ),
        // ),
        'jenis_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'jenis tarif harap diisi'
            ),
        ),
        'tarif' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tarif angkutan harap diisi'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'customer harap dipilih'
            ),
            'uniqCustomer' => array(
                'rule' => array('uniqCustomer'),
                'message' => 'Tarif Angkut sudah terdaftar'
            ),
        ),
	);

	var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'GroupMotor' => array(
            'className' => 'GroupMotor',
            'foreignKey' => 'group_motor_id',
        ),
        'FromCity' => array(
            'className' => 'City',
            'foreignKey' => 'from_city_id',
        ),
        'ToCity' => array(
            'className' => 'City',
            'foreignKey' => 'to_city_id',
        ),
    );

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'TarifAngkutan.status' => 1,
            ),
            'order'=> array(
                'TarifAngkutan.name_tarif' => 'ASC'
            ),
            'contain' => array(
                'GroupMotor',
            ),
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

    function getMerge($data, $id){
        if(empty($data['TarifAngkutan'])){
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

    function findTarif( $from_city_id, $to_city_id, $customer_id, $capacity = false, $group_motor_id = false ){
        $conditions = array(
            'from_city_id' => $from_city_id,
            'to_city_id' => $to_city_id,
            'customer_id' => $customer_id,
            'status' => 1,
        );

        $result = $this->find('first', array(
            'conditions' => $conditions,
        ));

        if(!empty($result)){
            $addConditions = $conditions;

            if( !empty($result['TarifAngkutan']['capacity']) ) {
                $addConditions['capacity'] = $capacity;
            }
            if( !empty($result['TarifAngkutan']['group_motor_id']) && !empty($group_motor_id) && $result['TarifAngkutan']['jenis_unit'] == 'per_unit' ) {
                $addConditions['group_motor_id'] = $group_motor_id;
            } else if( !empty($result['TarifAngkutan']['group_motor_id']) && $result['TarifAngkutan']['jenis_unit'] == 'per_unit' ) {
                $addConditions['group_motor_id'] = $result['TarifAngkutan']['group_motor_id'];
            }

            $result = $this->find('first', array(
                'conditions' => $addConditions,
            ));

            if( !empty($result) ) {
                return array(
                    'jenis_unit' => $result['TarifAngkutan']['jenis_unit'],
                    'tarif' => $result['TarifAngkutan']['tarif'],
                    'tarif_angkutan_id' => $result['TarifAngkutan']['id'],
                    'tarif_angkutan_type' => $result['TarifAngkutan']['type'],
                );
            }
        }else{
            return false;
        }
    }

    function uniqCustomer () {
        $conditions = array(
            'TarifAngkutan.customer_id' => $this->data['TarifAngkutan']['customer_id'],
            'TarifAngkutan.from_city_id' => $this->data['TarifAngkutan']['from_city_id'],
            'TarifAngkutan.to_city_id' => $this->data['TarifAngkutan']['to_city_id'],
            // 'TarifAngkutan.jenis_unit' => $this->data['TarifAngkutan']['jenis_unit'],
            // 'TarifAngkutan.capacity' => $this->data['TarifAngkutan']['capacity'],
            'TarifAngkutan.type' => $this->data['TarifAngkutan']['type'],
            'TarifAngkutan.status' => 1,
        );
        $tarifAngkutan = $this->find('first', array(
            'conditions' => $conditions,
        ));
        $result = true;

        if( !empty($tarifAngkutan) && !empty($this->data['TarifAngkutan']['jenis_unit']) ) {
            if( $tarifAngkutan['TarifAngkutan']['jenis_unit'] != $this->data['TarifAngkutan']['jenis_unit'] ) {
                $result = false;
            } else {
                if( !empty($this->data['TarifAngkutan']['id']) ) {
                    $conditions['TarifAngkutan.id <>'] = $this->data['TarifAngkutan']['id'];
                }
                
                if( $tarifAngkutan['TarifAngkutan']['jenis_unit'] == 'per_truck' ) {
                    $conditions['TarifAngkutan.capacity'] = $this->data['TarifAngkutan']['capacity'];

                    $tarifAngkutan = $this->find('first', array(
                        'conditions' => $conditions,
                    ));

                    if( !empty($tarifAngkutan) ) {
                        $result = false;
                    }
                } else {
                    $group_motor_id = !empty($this->data['TarifAngkutan']['group_motor_id'])?$this->data['TarifAngkutan']['group_motor_id']:0;
                    $conditions['TarifAngkutan.group_motor_id'] = $group_motor_id;

                    $tarifAngkutan = $this->find('first', array(
                        'conditions' => $conditions,
                    ));

                    if( !empty($tarifAngkutan) ) {
                        $result = false;
                    }
                }
            }
        }

        return $result;
    }
}
?>