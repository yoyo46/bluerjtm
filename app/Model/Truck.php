<?php
class Truck extends AppModel {
	var $name = 'Truck';
	var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
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
        'driver_id' => array(
            'validateDriver' => array(
                'rule' => array('validateDriver'),
                'message' => 'Supir truk sudah terdaftar'
            ),
        ),
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
            'validateThnNeraca' => array(
                'rule' => array('validateThnNeraca'),
                'message' => 'Tahun Neraca truk harap diisi'
            ),
        ),
        // 'atas_nama' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Atas Nama truk harap diisi'
        //     ),
        // ),
        // 'bpkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'BPKB truk harap diisi'
        //     ),
        //     'isUnique' => array(
        //         'rule' => array('isUnique'),
        //         'message' => 'BPKB telah terdaftar',
        //     ),
        // ),
        // 'tgl_bpkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tanggal BPKB truk harap diisi'
        //     ),
        // ),
        // 'no_stnk' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Nomor STNK truk harap diisi'
        //     ),
        //     'isUnique' => array(
        //         'rule' => array('isUnique'),
        //         'message' => 'Nomor STNK telah terdaftar',
        //     ),
        // ),
        // 'tgl_stnk' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang STNK 1thn truk harap diisi'
        //     ),
        // ),
        // 'tgl_stnk_plat' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang STNK 5thn truk harap diisi'
        //     ),
        // ),
        // 'bbnkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya BBNKB truk harap diisi'
        //     ),
        // ),
        // 'pkb' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya PKB truk harap diisi'
        //     ),
        // ),
        // 'swdkllj' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya SWDKLLJ truk harap diisi'
        //     ),
        // ),
        // 'tgl_siup' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang SIUP truk harap diisi'
        //     ),
        // ),
        // 'siup' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya SIUP truk harap diisi'
        //     ),
        // ),
        // 'tgl_kir' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Perpanjang KIR truk harap diisi'
        //     ),
        // ),
        // 'kir' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Biaya KIR truk harap diisi'
        //     ),
        // ),
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
                'Driver.is_resign' => 0,
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
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
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

    function uniqueUpdate($data, $id = false){
        $result = false;
        $id = !empty($this->data['Truck']['id'])?$this->data['Truck']['id']:$id;

        $find = $this->find('count', array(
            'conditions' => array(
                'Truck.id NOT' => $id,
                'Truck.nopol' => $data['nopol']
            )
        ));

        if(empty($find)){
            $result = true;
        }

        return $result;
    }



    function validateDriver(){
        if( !empty($this->data['Truck']['driver_id']) ) {
            $truck_id = !empty($this->data['Truck']['id'])?$this->data['Truck']['id']:false;
            $find = $this->find('count', array(
                'conditions' => array(
                    'Truck.driver_id' => $this->data['Truck']['driver_id'],
                    'Truck.id <>' => $truck_id,
                    'Truck.status' => 1,
                )
            ));

            if( !empty($find) ) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function validateThnNeraca () {
        $is_asset = !empty($this->data['Truck']['is_asset'])?$this->data['Truck']['is_asset']:false;
        $tahun_neraca = !empty($this->data['Truck']['tahun_neraca'])?$this->data['Truck']['tahun_neraca']:false;

        if( !empty($is_asset) && empty($tahun_neraca) ) {
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
            'order' => array(
                'Truck.nopol' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Truck.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Truck.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Truck.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['Truck.branch_id'] = Configure::read('__Site.config_branch_id');
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

    function getTruck( $id, $options = array() ){
        $truck = $this->getData('first', array(
            'conditions' => array(
                'Truck.id' => $id
            )
        ), true, $options);

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

    function getInfoTruck( $truck_id, $branch_id = false ) {
        $conditions = array(
            'Truck.id' => $truck_id,
        );

        if( !empty($branch_id) ) {
            $conditions['Truck.branch_id'] = $branch_id;
        }

        $result = $this->getData('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'Driver'
            ),
        ));

        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['Truck'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Truck.id' => $id,
                    'Truck.status' => 1,
                )
            ), true, array(
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getListTruck ( $include_this_truck_id = false, $only_bind = false, $nopol = false, $branch_id = false ) {
        $this->bindModel(array(
            'hasOne' => array(
                'Ttuj' => array(
                    'className' => 'Ttuj',
                    'foreignKey' => 'truck_id',
                    'conditions' => array(
                        'Ttuj.status' => 1,
                        'Ttuj.is_pool' => 0,
                        'Ttuj.is_laka' => 0,
                        'Ttuj.completed' => 0,
                    ),
                )
            )
        ), false);
        
        if( !empty($include_this_truck_id) ) {
            // Ambil data truck berikut id ini
            $conditions = array(
                'OR' => array(
                    'Truck.id' => $include_this_truck_id,
                    'Ttuj.id' => NULL,
                ),
                'AND' => array(
                    'OR' => array(
                        'Truck.id' => $include_this_truck_id,
                        'Truck.branch_id' => Configure::read('__Site.config_branch_id'),
                    ),
                ),
            );
        } else {
            $conditions = array(
                'Ttuj.id' => NULL,
                'Truck.branch_id' => Configure::read('__Site.config_branch_id'),
            );
        }

        if( !empty($branch_id) ) {
            $conditions['Truck.branch_id'] = $branch_id;
        }

        if( empty($only_bind) ) {
            $trucks = $this->getData('list', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Truck.id', 'Truck.nopol'
                ),
                'contain' => array(
                    'Ttuj'
                ),
                'order' => array(
                    'Truck.nopol'
                ),
            ), false);

            if( !empty($nopol) && !empty($trucks[$include_this_truck_id]) ) {
                $trucks[$include_this_truck_id] = $nopol;
            }

            return $trucks;
        } else {
            return $conditions;
        }
    }
}
?>