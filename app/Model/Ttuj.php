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
        'driver_penganti_id' => array(
            'getDriver' => array(
                'rule' => array('getDriver'),
                'message' => 'Supir pengganti harap dipilih'
            ),
        ),
        'tgljam_berangkat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam Berangkat harap dipilih'
            ),
        ),
        'tgljam_tiba' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl & Jam Tiba harap dipilih'
            ),
        ),
        'uang_jalan_1' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya Uang Jalan belum disetting'
            ),
        ),
        // 'date_sj' => array(
        //     'getSJ' => array(
        //         'rule' => array('getSJ'),
        //         'message' => 'Tgl SJ diterima harap dipilih'
        //     ),
        // ),
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
        'SuratJalan' => array(
            'className' => 'SuratJalan',
            'foreignKey' => 'ttuj_id',
            'conditions' => array(
                'SuratJalan.status' => 1,
            ),
        ),
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'Ttuj.status' => 1,
            ),
            'order'=> array(
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
            'contain' => array(
                'DriverPenganti',
                'TtujTipeMotor' => array(
                    'City',
                    'ColorMotor',
                    'TipeMotor',
                ),
                'TtujPerlengkapan',
                'UangJalan' => array(
                    'UangJalanTipeMotor',
                    'CommissionGroupMotor',
                    'AsdpGroupMotor',
                    'UangKawalGroupMotor',
                    'UangKeamananGroupMotor',
                ),
            ),
            'fields' => array(),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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

    function getDriver () {
        if( empty($this->data['Ttuj']['driver_name']) && empty($this->data['Ttuj']['driver_penganti_id']) ) {
            return false;
        } else {
            return true;
        }
    }

    function getSumUnit($data, $ttuj_id, $surat_jalan_id = false, $data_action = false){
        if( empty($data['Qty']) ){
            $data_merge = $this->TtujTipeMotor->find('first', array(
                'conditions' => array(
                    'TtujTipeMotor.status' => 1,
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                ),
                'group' => array(
                    'TtujTipeMotor.ttuj_id',
                ),
                'fields' => array(
                    'SUM(TtujTipeMotor.qty) AS qty',
                ),
            ));

            if(!empty($data_merge[0])){
                $data['Qty'] = $data_merge[0]['qty'];
            }

            $conditions = array(
                'SuratJalan.status' => 1,
                'SuratJalan.ttuj_id' => $ttuj_id,
                'SuratJalan.id <>' => $surat_jalan_id,
            );
            $data_merge = $this->SuratJalan->find('first', array(
                'conditions' => $conditions,
                'group' => array(
                    'SuratJalan.ttuj_id',
                ),
                'fields' => array(
                    'SUM(SuratJalan.qty) AS qty',
                ),
            ));

            if(!empty($data_merge[0])){
                $data['QtySJ'] = $data_merge[0]['qty'];
            }

            switch ($data_action) {
                case 'tgl_surat_jalan':
                    $data_merge = $this->SuratJalan->find('first', array(
                        'conditions' => $conditions,
                        'fields' => array(
                            'SuratJalan.tgl_surat_jalan',
                        ),
                        'order' => array(
                            'SuratJalan.tgl_surat_jalan' => 'DESC',
                            'SuratJalan.id' => 'DESC',
                        ),
                    ));

                    if(!empty($data_merge)){
                        $data['SuratJalan']['tgl_surat_jalan'] = $data_merge['SuratJalan']['tgl_surat_jalan'];
                    }
                    break;
            }
        }

        return $data;
    }

    function getSJOutstanding ( $driver_id ) {
        $sjCount = $this->getData('count', array(
            'conditions' => array(
                'Ttuj.status' => 1,
                'Ttuj.driver_id' => $driver_id,
                'Ttuj.is_sj_completed' => 0,
            ),
        ));

        return $sjCount;
    }

    function getTruckStatus ( $data, $truck_id ) {
        $truckAway = $this->getData('first', array(
            'conditions' => array(
                'Ttuj.status' => 1,
                'Ttuj.is_pool' => 0,
                'Ttuj.truck_id' => $truck_id,
            ),
        ));

        if( !empty($truckAway) ) {
            $data = array_merge($data, $truckAway);
        }

        return $data;
    }
}
?>