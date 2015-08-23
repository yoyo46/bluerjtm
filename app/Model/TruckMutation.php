<?php
class TruckMutation extends AppModel {
	var $name = 'TruckMutation';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. dokumen harap diisi'
            ),
        ),
        'mutation_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal mutasi harap dipilih'
            ),
        ),
        'description' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Keterangan mutasi harap diisi'
            ),
        ),
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Pol truk harap dipilih'
            ),
        ),
        'change_nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Pol truk harap diisi'
            ),
        ),
        'change_branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'change_truck_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis truk harap dipilih'
            ),
        ),
        'change_truck_facility_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Fasilitas truk harap dipilih'
            ),
        ),
        'change_driver_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Supir harap dipilih'
            ),
        ),
        'change_capacity' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kapasitas harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

    var $hasMany = array(
        'TruckMutationOldCustomer' => array(
            'className' => 'TruckMutationOldCustomer',
            'foreignKey' => 'truck_mutation_id',
        ),
        'TruckMutationCustomer' => array(
            'className' => 'TruckMutationCustomer',
            'foreignKey' => 'truck_mutation_id',
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order' => array(
                'TruckMutation.status' => 'DESC',
                'TruckMutation.created' => 'DESC',
                'TruckMutation.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'non-active':
                $default_options['conditions']['TruckMutation.status'] = 0;
                break;
            
            case 'active':
                $default_options['conditions']['TruckMutation.status'] = 1;
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

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $allowNopol = true;
        $default_msg = __('menyimpan data mutasi truk');

        if ( !empty($data) ) {
            if( empty($id) ) {
                $this->create();
            } else {
                $this->id = $id;
            }

            $data['TruckMutation']['truck_id'] = $truck_id = !empty($data['Truck']['truck_id'])?$data['Truck']['truck_id']:false;
            $dataTruckMutation = false;
            $oldDataTruck = $this->Truck->getData('first', array(
                'conditions' => array(
                    'Truck.id' => $truck_id,
                ),
                'contain' => array(
                    'TruckCategory',
                    'TruckFacility',
                    'Branch',
                    'Driver',
                ),
            ), true, array(
                'branch' => false,
            ));
            unset($data['Truck']);
            $old_nopol = !empty($oldDataTruck['Truck']['nopol'])?$oldDataTruck['Truck']['nopol']:false;
            $data['TruckMutation']['nopol'] = $old_nopol;

            $old_branch_name = !empty($oldDataTruck['Branch']['name'])?$oldDataTruck['Branch']['name']:false;
            $old_branch_id = !empty($oldDataTruck['Truck']['branch_id'])?$oldDataTruck['Truck']['branch_id']:false;
            $data['TruckMutation']['branch_name'] = $old_branch_name;
            $data['TruckMutation']['old_branch_id'] = $old_branch_id;

            $old_truck_category_name = !empty($oldDataTruck['TruckCategory']['name'])?$oldDataTruck['TruckCategory']['name']:false;
            $old_truck_category_id = !empty($oldDataTruck['Truck']['truck_category_id'])?$oldDataTruck['Truck']['truck_category_id']:false;
            $data['TruckMutation']['category'] = $old_truck_category_name;
            $data['TruckMutation']['old_truck_category_id'] = $old_truck_category_id;

            $old_truck_facility_name = !empty($oldDataTruck['TruckFacility']['name'])?$oldDataTruck['TruckFacility']['name']:false;
            $old_truck_facility_id = !empty($oldDataTruck['Truck']['truck_facility_id'])?$oldDataTruck['Truck']['truck_facility_id']:false;
            $data['TruckMutation']['facility'] = $old_truck_facility_name;
            $data['TruckMutation']['old_truck_facility_id'] = $old_truck_facility_id;

            $old_driver_id = !empty($oldDataTruck['Truck']['driver_id'])?$oldDataTruck['Truck']['driver_id']:false;
            $old_driver_name = !empty($oldDataTruck['Driver']['driver_name'])?$oldDataTruck['Driver']['driver_name']:false;
            $data['TruckMutation']['driver_name'] = $old_driver_name;
            $data['TruckMutation']['old_driver_id'] = $old_driver_id;
            
            $old_capacity = !empty($oldDataTruck['Truck']['capacity'])?$oldDataTruck['Truck']['capacity']:false;
            $data['TruckMutation']['capacity'] = $old_capacity;

            if( !empty($oldDataTruck) ) {
                $oldDataTruck = $this->Truck->TruckCustomer->getMergeTruckCustomer($oldDataTruck, $truck_id);

                if( !empty($oldDataTruck['TruckCustomer']) ) {
                    foreach ($oldDataTruck['TruckCustomer'] as $key => $value) {
                        $customer_id = !empty($value['TruckCustomer']['id'])?$value['TruckCustomer']['id']:false;
                        $customer_name = !empty($value['Customer']['customer_name_code'])?$value['Customer']['customer_name_code']:false;

                        $oldTruckCustomers[$customer_id] = $customer_name;
                    }
                }
            }

            if( !empty($data['DataMutation']) || !empty($data['TruckCustomer']) ) {
                if( !empty($data['DataMutation']) ) {
                    $validateEmptyField = array_filter($data['DataMutation']);
                    $dataTruckMutation = $data['DataMutation'];
                    $data['TruckMutation'] = array_merge($data['TruckMutation'], $data['DataMutation']);

                    if( !empty($validateEmptyField) ) {
                        if( !empty($dataTruckMutation['change_nopol']) ) {
                            $nopol = $dataTruckMutation['change_nopol'];

                            $dataValidateTruck = array(
                                'nopol' => $nopol,
                            );
                            $allowNopol = $this->Truck->uniqueUpdate($dataValidateTruck, $truck_id);
                            $data['Truck']['nopol'] = $nopol;
                            $data['TruckMutation']['change_nopol'] = $nopol;
                        }

                        if( !empty($dataTruckMutation['change_branch_id']) ) {
                            $branch_id = $dataTruckMutation['change_branch_id'];
                            $branch = $this->Truck->Branch->getMerge(array(), $branch_id);

                            $branch_name = !empty($branch['Branch']['name'])?$branch['Branch']['name']:false;

                            $data['Truck']['branch_id'] = $branch_id;
                            $data['TruckMutation']['change_branch_id'] = $branch_id;
                            $data['TruckMutation']['change_branch_name'] = $branch_name;
                        }
                        if( !empty($dataTruckMutation['change_truck_category_id']) ) {
                            $truck_category_id = $dataTruckMutation['change_truck_category_id'];
                            $truck_category = $this->Truck->TruckCategory->getMerge(array(), $truck_category_id);

                            $truck_category_name = !empty($truck_category['TruckCategory']['name'])?$truck_category['TruckCategory']['name']:false;

                            $data['Truck']['truck_category_id'] = $truck_category_id;
                            $data['TruckMutation']['change_truck_category_id'] = $truck_category_id;
                            $data['TruckMutation']['change_category'] = $truck_category_name;
                        }
                        if( !empty($dataTruckMutation['change_truck_facility_id']) ) {
                            $truck_facility_id = $dataTruckMutation['change_truck_facility_id'];
                            $truck_facility = $this->Truck->TruckFacility->getMerge(array(), $truck_facility_id);

                            $truck_facility_name = !empty($truck_facility['TruckFacility']['name'])?$truck_facility['TruckFacility']['name']:false;

                            $data['Truck']['truck_facility_id'] = $truck_facility_id;
                            $data['TruckMutation']['change_truck_facility_id'] = $truck_facility_id;
                            $data['TruckMutation']['change_facility'] = $truck_facility_name;
                        }
                        if( !empty($dataTruckMutation['change_driver_id']) ) {
                            $driver_id = $dataTruckMutation['change_driver_id'];
                            $driver = $this->Truck->Driver->getMerge(array(), $driver_id);

                            $driver_name = !empty($driver['Driver']['driver_name'])?$driver['Driver']['driver_name']:false;
                            $driver_id = !empty($driver['Driver']['id'])?$driver['Driver']['id']:false;

                            $data['Truck']['driver_id'] = $driver_id;
                            $data['TruckMutation']['change_driver_id'] = $driver_id;

                            $data['TruckMutation']['change_driver_name'] = $driver_name;

                            $data['Truck']['driver_id'] = $driver_id;
                        }
                        if( !empty($dataTruckMutation['change_capacity']) ) {
                            $capacity = $dataTruckMutation['change_capacity'];

                            $data['Truck']['capacity'] = $capacity;
                            $data['TruckMutation']['change_capacity'] = $capacity;
                        }
                    }
                }

                if( !empty($data['TruckCustomer']) ) {
                    $truckCustomers = array_filter($data['TruckCustomer']['customer_id']);
                    
                    if( !empty($truckCustomers) ) {
                        $validateEmptyField = true;
                    }
                }
            } else {
                $validateEmptyField = false;
            }

            $this->Truck->id = $truck_id;
            $this->set($data);
            $this->Truck->set($data);

            $validateTruckMutation = $this->validates();
            $validateTruck = $this->Truck->validates();
            $validateTruckCustomer = true;
            $saveTruckMutationCustomer = true;
            $saveTruckMutationOldCustomer = true;

            if( !empty($truckCustomers) ) {
                $saveTruckCustomer = $this->Truck->TruckCustomer->doSave($truckCustomers, false, false, false, true);
                $saveTruckMutationCustomer = $this->TruckMutationCustomer->doSave($truckCustomers, false, false, false, true);

                if( !empty($oldTruckCustomers) ) {
                    $saveTruckMutationOldCustomer = $this->TruckMutationOldCustomer->doSave($oldTruckCustomers, false, false, false, true);
                }

                if( !empty($saveTruckCustomer['status']) && $saveTruckCustomer['status'] == 'success' ) {
                    $validateTruckCustomer = true;
                }
            }

            if( $validateTruckMutation && $validateTruck && $validateTruckCustomer && !empty($validateEmptyField) && !empty($allowNopol) ) {
                if( $this->save($data) ) {
                    $id = $this->id;

                    if( !empty($data['Truck']) ) {
                        $this->Truck->save($data);
                    }

                    if( !empty($truckCustomers) ) {
                        $this->Truck->TruckCustomer->doSave($truckCustomers, false, false, $truck_id);
                        $this->TruckMutationCustomer->doSave($truckCustomers, false, false, $id);

                        if( !empty($oldTruckCustomers) ) {
                            $this->TruckMutationOldCustomer->doSave($oldTruckCustomers, false, false, $id);
                        }
                    }

                    $result = array(
                        'msg' => sprintf(__('Berhasil %s'), $default_msg),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                    );
                }
            } else {
                if( empty($allowNopol) ) {
                    $msg = __('No. Pol telah terdaftar. Harap masukan No. Pol truk lain');
                } else if( empty($validateEmptyField) ) {
                    $msg = __('Mohon isi salah satu data mutasi truk');
                } else {
                    $msg = sprintf(__('Gagal %s. Mohon lengkapi data mutasi truk'), $default_msg);
                }
                $result = array(
                    'msg' => $msg,
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