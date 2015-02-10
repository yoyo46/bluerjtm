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
        'capacity' => array(
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Kapasitas harus berupa angka',
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
        )
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

    function check_availability($data){
        $data = $data['TarifAngkutan'];
        $check_availability = $this->find('count', array(
            'conditions' => array(
                'from_city_id' => $data['from_city_id'],
                'to_city_id' => $data['to_city_id'],
                'jenis_unit' => $data['jenis_unit'],
                'tarif' => $data['tarif'],
                'customer_id' => $data['customer_id'],
            )
        ));

        if( empty($check_availability) ){
            return true;
        }else{
            return false;
        }
    }

    function findTarif($from_city_id, $to_city_id, $customer_id, $capacity){
        $result = $this->find('first', array(
            'conditions' => array(
                'from_city_id' => $from_city_id,
                'to_city_id' => $to_city_id,
                'customer_id' => $customer_id,
                'capacity' => $capacity
            )
        ));

        if(!empty($result)){
            return array(
                'jenis_unit' => $result['TarifAngkutan']['jenis_unit'],
                'tarif' => $result['TarifAngkutan']['tarif'],
                'tarif_angkutan_id' => $result['TarifAngkutan']['id']
            );
        }else{
            return false;
        }
    }

    function uniqCustomer () {
        $conditions = array(
            'TarifAngkutan.customer_id' => $this->data['TarifAngkutan']['customer_id'],
            'TarifAngkutan.from_city_id' => $this->data['TarifAngkutan']['from_city_id'],
            'TarifAngkutan.to_city_id' => $this->data['TarifAngkutan']['to_city_id'],
            'TarifAngkutan.jenis_unit' => $this->data['TarifAngkutan']['jenis_unit'],
            'TarifAngkutan.status' => 1,
        );

        if( !empty($this->data['TarifAngkutan']['id']) ) {
            $conditions['TarifAngkutan.id <>'] = $this->data['TarifAngkutan']['id'];
        }

        $tarifAngkutan = $this->find('first', array(
            'conditions' => $conditions,
        ));

        if( !empty($tarifAngkutan) ) {
            return false;
        } else {
            return true;
        }
    }
}
?>