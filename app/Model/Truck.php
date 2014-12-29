<?php
class Truck extends AppModel {
	var $name = 'Truck';
	var $validate = array(
        'truck_brand_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'merek truk harap diisi'
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'perusahaan truk harap diisi'
            ),
        ),
        'truck_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis truk harap diisi'
            ),
        ),
        'driver_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'supir truk harap diisi'
            ),
        ),
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nopol truk harap diisi'
            ),
        ),
        'no_contract' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nomor kontrak truk harap diisi'
            ),
        ),
        'bpkb' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'BPKB truk harap diisi'
            ),
        ),
        'atas_nama' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'atas nama truk harap diisi'
            ),
        ),
        'no_stnk' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nomor STNK truk harap diisi'
            ),
        ),
        'no_rangka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nomor rangka truk harap diisi'
            ),
        ),
        'no_machine' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nomor mesin truk harap diisi'
            ),
        ),
        'capacity' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'kapasitas truk harap diisi'
            ),
        ),
        'tahun' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tahun truk harap diisi'
            ),
        ),
        'tahun_neraca' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tahun neraca truk harap diisi'
            ),
        ),
        'tgl_bpkb' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal BPKB truk harap diisi'
            ),
        ),
	);

    var $hasOne = array(
        'TruckBrand' => array(
            'className' => 'TruckBrand',
            'foreignKey' => 'truck_brand_id',
        ),
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
        ),
        'TruckCategory' => array(
            'className' => 'TruckCategory',
            'foreignKey' => 'truck_category_id',
        ),
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'truck_id',
        ),
        'Kir' => array(
            'className' => 'Kir',
            'foreignKey' => 'truck_id',
            'conditions' => array(
                'Kir.status' => 1,
                'Kir.paid' => 1,
            ),
            'order' => array(
                'Kir.tgl_kir' => 'DESC',
                'Kir.id' => 'DESC',
            )
        ),
    );

    var $belongsTo = array(
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'driver_id',
        ),
        'Stnk' => array(
            'className' => 'Stnk',
            'foreignKey' => 'truck_id',
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

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Truck.status' => 1,
            ),
            'order'=> array(
                'Truck.status' => 'DESC'
            ),
            'contain' => array(
                'Kir'
            ),
            'fields' => array(),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
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