<?php
class Ttuj extends AppModel {
	var $name = 'Ttuj';
	var $validate = array(
        'no_ttuj' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No TTUJ harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No TTUJ telah terdaftar',
            ),
        ),
        'ttuj_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl TTUJ harap dipilih'
            ),
        ),
        'uang_jalan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer dan Tujuan harap diisi'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'from_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Dari kota harap dipilih'
            ),
        ),
        'to_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota tujuan harap dipilih'
            ),
        ),
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
            ),
        ),
        'tgljam_berangkat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam Berangkat harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'DriverPenganti' => array(
            'className' => 'Driver',
            'foreignKey' => 'driver_penganti_id',
        ),
        'UangJalan' => array(
            'className' => 'UangJalan',
            'foreignKey' => 'uang_jalan_id',
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
    );

    var $hasMany = array(
        'TtujTipeMotor' => array(
            'className' => 'TtujTipeMotor',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'TtujTipeMotor.status' => 1,
            ),
        ),
        'TtujPerlengkapan' => array(
            'className' => 'TtujPerlengkapan',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'TtujPerlengkapan.status' => 1,
            ),
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Ttuj.status' => 'DESC',
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
            'contain' => array(
                'DriverPenganti',
                'TtujTipeMotor',
                'TtujPerlengkapan',
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
}
?>