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
        'tarif_extra' => array(
            'checkTarifExtra' => array(
                'rule' => array('checkTarifExtra'),
                'message' => 'Mohon lengkapi tarif extra'
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
    );

    function checkTarifExtra() {
        $data = $this->data;
        $tarif_extra = Common::hashEmptyField($data, 'TarifAngkutan.tarif_extra');
        $min_capacity = Common::hashEmptyField($data, 'TarifAngkutan.min_capacity');

        if( !empty($tarif_extra) && empty($min_capacity) ) {
            return false;
        } else if( !empty($min_capacity) && empty($tarif_extra) ) {
            return false;
        } else {
            return true; 
        }
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'TarifAngkutan.name_tarif' => 'ASC'
            ),
            'contain' => array(),
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
            if(isset($options['order'])){
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

    function findTarif( $from_city_id, $to_city_id, $customer_id, $capacity = false, $group_motor_id = false, $total_muatan = false ){
        $conditions = array(
            'TarifAngkutan.from_city_id' => $from_city_id,
            'TarifAngkutan.to_city_id' => $to_city_id,
            'TarifAngkutan.customer_id' => $customer_id,
        );
        $capacity = !empty($capacity)?$capacity:false;
        $group_motor_id = !empty($group_motor_id)?$group_motor_id:false;
        $results = $this->getData('all', array(
            'conditions' => $conditions,
            'order' => false,
        ), true, array(
            'branch' => false,
        ));

        if(!empty($results)){
            $addConditions = $conditions;

            foreach ($results as $key => $result) {
                $tarifCapacity = !empty($result['TarifAngkutan']['capacity'])?$result['TarifAngkutan']['capacity']:false;
                $tarifGroupMotor = !empty($result['TarifAngkutan']['group_motor_id'])?$result['TarifAngkutan']['group_motor_id']:false;
                $min_capacity = Common::hashEmptyField($result, 'TarifAngkutan.min_capacity', 0);
                $tarif_extra = Common::hashEmptyField($result, 'TarifAngkutan.tarif_extra', 0);
                $tarif = Common::hashEmptyField($result, 'TarifAngkutan.tarif', 0);

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

                if( $tarifGroupMotor == $group_motor_id || empty($tarifGroupMotor) ) {
                    if( !empty($total_muatan) && !empty($min_capacity) ) {
                        if( $total_muatan > $min_capacity ) {
                            $tarif = $tarif + $tarif_extra;
                        }
                    }
                }

                if( $flagTarifCapacity && $flagTarifGroupMotor ) {
                    return array(
                        'jenis_unit' => $result['TarifAngkutan']['jenis_unit'],
                        'tarif' => $tarif,
                        'tarif_angkutan_id' => $result['TarifAngkutan']['id'],
                        'tarif_angkutan_type' => $result['TarifAngkutan']['type'],
                    );
                } else if( ($flagTarifGroupMotor && empty($tarifCapacity)) || ($flagTarifCapacity && empty($tarifGroupMotor)) ) {
                    $tmpResult = array(
                        'jenis_unit' => $result['TarifAngkutan']['jenis_unit'],
                        'tarif' => $tarif,
                        'tarif_angkutan_id' => $result['TarifAngkutan']['id'],
                        'tarif_angkutan_type' => $result['TarifAngkutan']['type'],
                    );
                }
            }

            if( !empty($tmpResult) ) {
                return $tmpResult;
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
                $min_capacity = Common::hashEmptyField($result, 'TarifAngkutan.min_capacity', 0);
                $tarif_extra = Common::hashEmptyField($result, 'TarifAngkutan.tarif_extra', 0);
                $tarif = Common::hashEmptyField($result, 'TarifAngkutan.tarif', 0);
                $tarifGroupMotor = Common::hashEmptyField($result, 'TarifAngkutan.group_motor_id');

                if( empty($tarifGroupMotor) ) {
                    if( !empty($total_muatan) && !empty($min_capacity) ) {
                        if( $total_muatan > $min_capacity ) {
                            $tarif = $tarif + $tarif_extra;
                        }
                    }
                }

                return array(
                    'jenis_unit' => $result['TarifAngkutan']['jenis_unit'],
                    'tarif' => $tarif,
                    'tarif_angkutan_id' => $result['TarifAngkutan']['id'],
                    'tarif_angkutan_type' => $result['TarifAngkutan']['type'],
                );
            } else if( !empty($results[0]) ) {
                $result = $results[0];
                
                $min_capacity = Common::hashEmptyField($result, 'TarifAngkutan.min_capacity', 0);
                $tarif_extra = Common::hashEmptyField($result, 'TarifAngkutan.tarif_extra', 0);
                $tarif = Common::hashEmptyField($result, 'TarifAngkutan.tarif', 0);
                $tarifGroupMotor = Common::hashEmptyField($result, 'TarifAngkutan.group_motor_id');

                if( empty($tarifGroupMotor) ) {
                    if( !empty($total_muatan) && !empty($min_capacity) ) {
                        if( $total_muatan > $min_capacity ) {
                            $tarif = $tarif + $tarif_extra;
                        }
                    }
                }

               return array(
                    'jenis_unit' => !empty($results[0]['TarifAngkutan']['jenis_unit'])?$results[0]['TarifAngkutan']['jenis_unit']:'per_truck',
                    'tarif' => $tarif,
                    'tarif_angkutan_id' => !empty($results[0]['TarifAngkutan']['id'])?$results[0]['TarifAngkutan']['id']:false,
                    'tarif_angkutan_type' => !empty($results[0]['TarifAngkutan']['type'])?$results[0]['TarifAngkutan']['type']:'angkut',
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

    function getTarifAngkut ( $from_city_id, $main_city_id, $detail_city_id, $customer_id, $truck_capacity, $group_motor_id, $total_muatan = false ) {
        $tarif = false;

        if(!empty($from_city_id)){
            if( !empty($detail_city_id) ) {
                $tarif = $this->findTarif($from_city_id, $detail_city_id, $customer_id, $truck_capacity, $group_motor_id, $total_muatan);
            } else {
                $tarif = $this->findTarif($from_city_id, $main_city_id, $customer_id, $truck_capacity, $group_motor_id, $total_muatan);
            }
        }

        return $tarif;
    }
}
?>