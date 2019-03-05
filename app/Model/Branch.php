<?php
class Branch extends AppModel {
	var $name = 'Branch';
	var $validate = array(
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode cabang harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Kode cabang telah terdaftar',
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama cabang harap diisi'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Coa harap dipilih'
            ),
        ),
        'city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota harap dipilih'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat harap diisi'
            ),
        ),
        'phone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Telepon harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
    );

    var $hasMany = array(
        'BranchCity' => array(
            'className' => 'BranchCity',
            'foreignKey' => 'branch_id',
        ),
        'BranchCoa' => array(
            'className' => 'BranchCoa',
            'foreignKey' => 'branch_id',
        ),
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'branch_id',
        ),
        'GeneralLedger' => array(
            'className' => 'GeneralLedger',
            'foreignKey' => 'branch_id',
        ),
        'GroupBranch' => array(
            'className' => 'GroupBranch',
            'foreignKey' => 'branch_id',
        ),
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'branch_id',
        ),
        'Revenue' => array(
            'className' => 'Revenue',
            'foreignKey' => 'branch_id',
        ),
        'Invoice' => array(
            'className' => 'Invoice',
            'foreignKey' => 'branch_id',
        ),
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'branch_id',
        ),
        'TtujPayment' => array(
            'className' => 'TtujPayment',
            'foreignKey' => 'branch_id',
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'branch_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['full_name'] = 'CONCAT(Branch.code, " - ", Branch.name)';
    }

	function getData($find, $options = false, $elements = array()){
        $include_city = isset($elements['include_city'])?$elements['include_city']:true;

        $default_options = array(
            'conditions'=> array(
                'Branch.status' => 1,
            ),
            'order'=> array(
                'Branch.name' => 'ASC'
            ),
            'contain' => array(
            ),
            'fields' => array(),
        );

        if( !empty($include_city) ) {
            $default_options['contain'][] = 'City';
        }

        if(!empty($options)){
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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

    function getMerge($data, $id, $fieldName = 'Branch.id'){
        if( empty($data['Branch']) && !empty($id) ){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id
                ),
                'cache' => __('Branch.ID.%s', $id),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function saveBranchCity( $data, $validate = false, $id = false ) {
        $validateBranchCity = true;

        if( empty($validate) && !empty($id) ) {
            $this->BranchCity->updateAll(array( 
                'BranchCity.status' => 0,
            ), array( 
                'BranchCity.branch_id' => $id,
                'BranchCity.status' => 1,
            ));
        }

        if( !empty($data['BranchCity']['branch_city_id']) ) {
            foreach ($data['BranchCity']['branch_city_id'] as $key => $branch_city_id) {
                $dataBranchCity = array(
                    'BranchCity' => array(
                        'branch_city_id' => $branch_city_id,
                    ),
                );

                if( !empty($id) ) {
                    $dataBranchCity['BranchCity']['branch_id'] = $id;
                }

                $resultBranchCity = $this->BranchCity->doSave($dataBranchCity, $validate, $id);
                $statusBranchCity = !empty($resultBranchCity['status'])?$resultBranchCity['status']:false;

                if( $statusBranchCity == 'error' ) {
                    $validateBranchCity = false;
                }
            }
        }

        return $validateBranchCity;
    }

    function doSave( $data, $value = false, $id = false, $controller = false ) {
        $result = false;
        $default_msg = __('menyimpan cabang');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $this->set($data);
            $validateBranch = $this->validates();
            $validateBranchCity = $this->saveBranchCity($data, true);

            if( $validateBranch && $validateBranchCity ) {
                if( $this->save() ) {
                    $head_office = !empty($data['Branch']['is_head_office'])?$data['Branch']['is_head_office']:false;

                    $id = $this->id;
                    $this->saveBranchCity($data, false, $id);

                    if( !empty($head_office) ) {
                        $this->updateAll( array(
                            'Branch.is_head_office' => 0,
                        ), array(
                            'Branch.id NOT' => $id,
                        ));
                    }

                    $result = array(
                        'id' => $id,
                        'msg' => sprintf(__('Berhasil %s'), $default_msg),
                        'status' => 'success',
                    );
                    $controller->Log->logActivity( sprintf(__('Berhasil %s #%s'), $default_msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 0, false, $id ); 
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s, mohon lengkapi semua data yang diperlukan'), $default_msg),
                        'status' => 'error',
                    );
                    $controller->Log->logActivity( sprintf(__('Gagal %s #%s'), $default_msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 0, false, $id ); 
                }
            } else {
                if( empty($validateBranchCity) ) {
                    $msg = __('Mohon pilih tujuan bongkar dari cabang?');
                } else {
                    $msg = sprintf(__('Gagal %s, mohon lengkapi semua data yang diperlukan'), $default_msg);
                }

                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $value = $this->BranchCity->getMerge($value, $id);

            if( !empty($value['BranchCity']) ) {
                $branchCities = $value['BranchCity'];
                unset($value['BranchCity']);

                foreach ($branchCities as $key => $branchCity) {
                    $branch_city_id = !empty($branchCity['BranchCity']['branch_city_id'])?$branchCity['BranchCity']['branch_city_id']:false;
                    $value['BranchCity']['branch_city_id'][$key] = $branch_city_id;
                }
            }

            $result['data'] = $value;
        }

        return $result;
    }

    public function doToggle( $id, $controller = false ) {
        $fieldName = 'status';
        $value = 0;
        $default_msg = __('menghapus cabang');

        $this->id = $id;
        $this->set($fieldName, $value);

        if( $this->save() ) {
            $result = array(
                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                'status' => 'success',
            );
            $controller->Log->logActivity( sprintf(__('Berhasil %s #%s'), $default_msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 0, false, $id ); 
        } else {
            $result = array(
                'msg' => sprintf(__('Gagal %s'), $default_msg),
                'status' => 'error',
            );
            $controller->Log->logActivity( sprintf(__('Gagal %s #%s'), $default_msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 1, false, $id ); 
        }

        return $result;
    }

    function getPlants ( $is_plant ) {
        if( !empty($is_plant) ) {
            $branch_plants = $this->getData('list', array(
                'conditions' => array(
                    'Branch.is_plant' => 1,
                ),
                'fields' => array(
                    'Branch.id', 'Branch.id',
                ),
                'cache' => 'Branch.Plant.List',
            ));

            if( !empty($branch_plants) ) {
                $branch_plants = array_values($branch_plants);
                Configure::write('__Site.Branch.Plant.id', $branch_plants);
            }
        }
    }

    function getBranch ( $city_id ) {
        return $this->getData('list', array(
            'conditions' => array(
                'Branch.city_id' => $city_id,
            ),
            'fields' => array(
                'Branch.id', 'Branch.id'
            ),
        ));
    }

    public function afterSave($created, $options = array()){
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Branch.id', $id);

        $this->GroupBranch->deleteCache();
        Cache::delete('Branch.Plant.List', 'default');

        if( !empty($id) ) {
            Cache::delete('Branch.ID.'.$id, 'default');
        }
    }
}
?>