<?php
class Truck extends AppModel {
	var $name = 'Truck';
	var $validate = array(
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nopol truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Nopol telah terdaftar',
            ),
        ),
        'truck_brand_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'merek truk harap diisi'
            ),
        ),
        'truck_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis truk harap diisi'
            ),
        ),
        'truck_facility_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Fasilitas truk harap dipilih'
            ),
        ),
        'no_rangka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor Rangka truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Nomor Rangka telah terdaftar',
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'perusahaan truk harap diisi'
            ),
        ),
        'no_machine' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor Mesin truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Nomor Mesin telah terdaftar',
            ),
        ),
        // 'driver_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Supir truk harap diisi'
        //     ),
        // ),
        'capacity' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kapasitas truk harap diisi'
            ),
        ),
        'tahun' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tahun truk harap diisi'
            ),
        ),
        'tahun_neraca' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tahun Neraca truk harap diisi'
            ),
        ),
        'atas_nama' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Atas Nama truk harap diisi'
            ),
        ),
        'bpkb' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'BPKB truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'BPKB telah terdaftar',
            ),
        ),
        'tgl_bpkb' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal BPKB truk harap diisi'
            ),
        ),
        'no_stnk' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor STNK truk harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Nomor STNK telah terdaftar',
            ),
        ),
        'tgl_stnk' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Perpanjang STNK 1thn truk harap diisi'
            ),
        ),
        'tgl_stnk_plat' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Perpanjang STNK 5thn truk harap diisi'
            ),
        ),
        'bbnkb' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya BBNKB truk harap diisi'
            ),
        ),
        'pkb' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya PKB truk harap diisi'
            ),
        ),
        'swdkllj' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya SWDKLLJ truk harap diisi'
            ),
        ),
        'tgl_siup' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Perpanjang SIUP truk harap diisi'
            ),
        ),
        'siup' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya SIUP truk harap diisi'
            ),
        ),
        'tgl_kir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Perpanjang KIR truk harap diisi'
            ),
        ),
        'kir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya KIR truk harap diisi'
            ),
        ),
	);

    var $hasOne = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
        ),
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'truck_id',
        ),
    );

    var $belongsTo = array(
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'driver_id',
            'conditions' => array(
                'Driver.status' => 1,
            ),
        ),
        'Stnk' => array(
            'className' => 'Stnk',
            'foreignKey' => 'truck_id',
        ),
        'TruckBrand' => array(
            'className' => 'TruckBrand',
            'foreignKey' => 'truck_brand_id',
        ),
        'TruckCategory' => array(
            'className' => 'TruckCategory',
            'foreignKey' => 'truck_category_id',
        ),
        'TruckFacility' => array(
            'className' => 'TruckFacility',
            'foreignKey' => 'truck_facility_id',
        ),
    );

    var $hasMany = array(
        'Siup' => array(
            'className' => 'Siup',
            'foreignKey' => 'truck_id',
        ),
        'TruckAlocation' => array(
            'className' => 'TruckAlocation',
            'foreignKey' => 'truck_id',
        ),
        'Stnk' => array(
            'className' => 'Stnk',
            'foreignKey' => 'truck_id',
        ),
        'TruckCustomer' => array(
            'className' => 'TruckCustomer',
            'foreignKey' => 'truck_id',
        ),
        'TruckPerlengkapan' => array(
            'className' => 'TruckPerlengkapan',
            'foreignKey' => 'truck_id',
        ),
    );

    function uniqueUpdate($data){
        $result = false;
        $find = $this->find('count', array(
            'conditions' => array(
                'Truck.id NOT' => $this->data['Truck']['id'],
                'Truck.nopol' => $data['nopol']
            )
        ));

        if(empty($find)){
            $result = true;
        }

        return $result;
    }

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Truck.status' => 1,
            ),
            'order'=> array(),
            'contain' => array(),
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

    function getTruck($id){
        $truck = $this->getData('first', array(
            'conditions' => array(
                'Truck.status' => 1,
                'Truck.id' => $id
            )
        ));

        if(!empty($truck)){
            $data = $truck['Truck'];

            $truck = $this->TruckCategory->getMerge($truck, $data['truck_category_id']);
            $truck = $this->TruckFacility->getMerge($truck, $data['truck_facility_id']);
            $truck = $this->TruckBrand->getMerge($truck, $data['truck_brand_id']);
            $truck = $this->Company->getMerge($truck, $data['company_id']);
            $truck = $this->Driver->getMerge($truck, $data['driver_id']);
        }
        return $truck;
    }

    function getInfoTruck( $truck_id ) {
        $result = $this->getData('first', array(
            'conditions' => array(
                'Truck.status' => 1,
                'Truck.id' => $truck_id,
            ),
            'contain' => array(
                'Driver'
            ),
        ));

        return $result;
    }
}
?>