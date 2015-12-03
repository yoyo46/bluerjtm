<?php
class TarifAngkutan extends AppModel {
	var $name = 'TarifAngkutan';
	var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
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
        // 'GroupMotor' => array(
        //     'className' => 'GroupMotor',
        //     'foreignKey' => 'group_motor_id',
        // ),
        // 'FromCity' => array(
        //     'className' => 'City',
        //     'foreignKey' => 'from_city_id',
        // ),
        // 'ToCity' => array(
        //     'className' => 'City',
        //     'foreignKey' => 'to_city_id',
        // ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'TarifAngkutan.name_tarif' => 'ASC'
            ),
            'contain' => array(),
            // 'contain' => array(
            //     'GroupMotor',
            // ),
        );

        if( !empty($branch) ) {
            $default_options['TarifAngkutan.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        switch ($status) {
            case 'all':
                $default_options['conditions']['TarifAngkutan.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['TarifAngkutan.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['TarifAngkutan.status'] = 1;
                break;
        }

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
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'TarifAngkutan.id' => $id,
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function findTarif( $from_city_id, $to_city_id, $customer_id, $capacity = false, $group_motor_id = false ){
        $conditions = array(
            'TarifAngkutan.from_city_id' => $from_city_id,
            'TarifAngkutan.to_city_id' => $to_city_id,
            'TarifAngkutan.customer_id' => $customer_id,
        );
        $capacity = !empty($capacity)?$capacity:false;
        $group_motor_id = !empty($group_motor_id)?$group_motor_id:false;
        $results = $this->getData('all', array(
            'conditions' => $conditions,
        ), true, array(
            'branch' => false,
        ));

        if(!empty($results)){
            $addConditions = $conditions;

            foreach ($results as $key => $result) {
                $tarifCapacity = !empty($result['TarifAngkutan']['capacity'])?$result['TarifAngkutan']['capacity']:false;
                $tarifGroupMotor = !empty($result['TarifAngkutan']['group_motor_id'])?$result['TarifAngkutan']['group_motor_id']:false;
                $flagTarifCapacity = false;
                $flagTarifGroupMotor = false;

                if( $tarifCapacity == $capacity ) {
                    $addConditions['TarifAngkutan.capacity'] = array( 0, '', $capacity );
                    $flagTarifCapacity = true;
                }
                if( $tarifGroupMotor == $group_motor_id ) {
                    $addConditions['TarifAngkutan.group_motor_id'] = array( 0, '', $group_motor_id );
                    $flagTarifGroupMotor = true;
                }

                if( $flagTarifCapacity && $flagTarifGroupMotor ) {
                    return array(
                        'jenis_unit' => $result['TarifAngkutan']['jenis_unit'],
                        'tarif' => $result['TarifAngkutan']['tarif'],
                        'tarif_angkutan_id' => $result['TarifAngkutan']['id'],
                        'tarif_angkutan_type' => $result['TarifAngkutan']['type'],
                    );
                }
            }

            if( empty($addConditions['TarifAngkutan.group_motor_id']) ) {
                $addConditions['TarifAngkutan.group_motor_id'] = array( 0, '' );
            }

            if( empty($addConditions['TarifAngkutan.capacity']) ) {
                $addConditions['TarifAngkutan.capacity'] = array( 0, '' );
            }

            $result = $this->getData('first', array(
                'conditions' => $addConditions,
                'order' => array(
                    'TarifAngkutan.group_motor_id' => 'DESC',
                    'TarifAngkutan.capacity' => 'DESC',
                ),
            ), true, array(
                'status' => 'all',
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
            'TarifAngkutan.type' => $this->data['TarifAngkutan']['type'],
        );

        if( !empty($this->data['TarifAngkutan']['id']) ) {
            $conditions['TarifAngkutan.id <>'] = $this->data['TarifAngkutan']['id'];
        }
        
        $tarifAngkutan = $this->getData('first', array(
            'conditions' => $conditions,
        ));
        $result = true;

        if( !empty($tarifAngkutan) && !empty($this->data['TarifAngkutan']['jenis_unit']) ) {
            if( $tarifAngkutan['TarifAngkutan']['jenis_unit'] != $this->data['TarifAngkutan']['jenis_unit'] ) {
                $result = false;
            } else {                
                if( $tarifAngkutan['TarifAngkutan']['jenis_unit'] == 'per_truck' ) {
                    $conditions['TarifAngkutan.capacity'] = $this->data['TarifAngkutan']['capacity'];

                    $tarifAngkutan = $this->getData('first', array(
                        'conditions' => $conditions,
                    ));

                    if( !empty($tarifAngkutan) ) {
                        $result = false;
                    }
                } else {
                    $group_motor_id = !empty($this->data['TarifAngkutan']['group_motor_id'])?$this->data['TarifAngkutan']['group_motor_id']:0;
                    $conditions['TarifAngkutan.group_motor_id'] = $group_motor_id;
                    $conditions['TarifAngkutan.capacity'] = $this->data['TarifAngkutan']['capacity'];

                    $tarifAngkutan = $this->getData('first', array(
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

    function getTarifAngkut ( $from_city_id, $main_city_id, $detail_city_id, $customer_id, $truck_capacity, $group_motor_id ) {
        $tarif = false;

        if(!empty($from_city_id)){
            if( !empty($detail_city_id) ) {
                $tarif = $this->findTarif($from_city_id, $detail_city_id, $customer_id, $truck_capacity, $group_motor_id);
            } else {
                $tarif = $this->findTarif($from_city_id, $main_city_id, $customer_id, $truck_capacity, $group_motor_id);
            }
        }

        return $tarif;
    }
}
?>