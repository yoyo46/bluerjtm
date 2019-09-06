<?php
class Driver extends AppModel {
	var $name = 'Driver';

	var $validate = array(
        'no_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. ID harap diisi'
            ),
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'No. ID telah terdaftar',
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama lengkap harap diisi'
            ),
        ),
        'identity_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. KTP harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No identitas telah terdaftar',
            ),
        ),
        'address' => array(
            'checkImport' => array(
                'rule' => array('checkImport', 'address'),
                'message' => 'Alamat Rumah harap diisi'
            ),
        ),
        'city' => array(
            'checkImport' => array(
                'rule' => array('checkImport', 'city'),
                'message' => 'Alamat Kota harap diisi'
            ),
        ),
        'provinsi' => array(
            'checkImport' => array(
                'rule' => array('checkImport', 'provinsi'),
                'message' => 'Alamat Provinsi harap diisi'
            ),
        ),
        'no_hp' => array(
            'checkImport' => array(
                'rule' => array('checkImport', 'no_hp'),
                'message' => 'No. HP harap diisi'
            ),
        ),
        'kontak_darurat_name' => array(
            'checkImport' => array(
                'rule' => array('checkImport', 'kontak_darurat_name'),
                'message' => 'Nama lengkap harap diisi'
            ),
        ),
        'kontak_darurat_no_hp' => array(
            'checkImport' => array(
                'rule' => array('checkImport', 'kontak_darurat_no_hp'),
                'message' => 'No. HP harap diisi'
            ),
        ),
        'account_name' => array(
            'norekCheck' => array(
                'rule' => array('norekCheck'),
                'message' => 'Atas nama harap diisi'
            ),
        ),
        'account_number' => array(
            'norekCheck' => array(
                'rule' => array('norekCheck'),
                'message' => 'No. Rekening harap diisi'
            ),
        ),
        'bank_name' => array(
            'norekCheck' => array(
                'rule' => array('norekCheck'),
                'message' => 'Nama Bank harap diisi'
            ),
        ),
	);

	var $hasOne = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'driver_id',
		),
	);

    var $belongsTo = array(
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'branch_id',
        ),
        'DriverRelation' => array(
            'className' => 'DriverRelation',
            'foreignKey' => 'driver_relation_id',
        ),
        'JenisSim' => array(
            'className' => 'JenisSim',
            'foreignKey' => 'jenis_sim_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );

    var $hasMany = array(
        'DebtDetail' => array(
            'foreignKey' => 'employe_id',
            'conditions' => array(
                'DebtDetail.type' => 'Supir',
            ),
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['driver_code'] = sprintf('CASE WHEN %s.no_id = \'\' THEN %s.name ELSE CONCAT(\'[\', %s.no_id, \'] \', %s.name) END', $this->alias, $this->alias, $this->alias, $this->alias);
        $this->virtualFields['driver_name'] = sprintf('CASE WHEN %s.alias = \'\' THEN %s.name ELSE CONCAT(%s.name, \' ( \', %s.alias, \' )\') END', $this->alias, $this->alias, $this->alias, $this->alias);
    }

    function norekCheck () {
        $data = $this->data;

        $account_name = Common::hashEmptyField($data, 'Driver.account_name');
        $account_number = Common::hashEmptyField($data, 'Driver.account_number');
        $bank_name = Common::hashEmptyField($data, 'Driver.bank_name');

        if( !empty($account_name) || !empty($account_number) || !empty($bank_name) ) {
            $flag = true;

            if( empty($account_name) ) {
                $flag = false;
            }
            if( empty($account_number) ) {
                $flag = false;
            }
            if( empty($bank_name) ) {
                $flag = false;
            }

            return $flag;
        } else {
            return true;
        }
    }

    function kontakDaruratCheck () {
        $data = $this->data;

        $kontak_darurat_name = Common::hashEmptyField($data, 'Driver.kontak_darurat_name');
        $kontak_darurat_no_hp = Common::hashEmptyField($data, 'Driver.kontak_darurat_no_hp');

        if( !empty($kontak_darurat_name) || !empty($kontak_darurat_no_hp) ) {
            $flag = true;

            if( empty($kontak_darurat_name) ) {
                $flag = false;
            }
            if( empty($kontak_darurat_no_hp) ) {
                $flag = false;
            }

            return $flag;
        } else {
            return true;
        }
    }

    function uniqueUpdate($data){
        $result = false;
        $find = $this->getData('count', array(
            'conditions' => array(
                'Driver.id NOT' => $this->data['Driver']['id'],
                'Driver.identity_number' => $data['identity_number']
            )
        ));

        if(empty($find)){
            $result = true;
        }

        return $result;
    }

	function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $plant = isset($elements['plant'])?$elements['plant']:false;
        $branch_is_plant = Configure::read('__Site.config_branch_plant');

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Driver.status' => 'DESC',
                'Driver.name' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Driver.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Driver.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Driver.status'] = 1;
                break;
        }

        if( !empty($plant) && !empty($branch_is_plant) ) {
            $default_options['conditions']['Driver.branch_id'] = Configure::read('__Site.Branch.Plant.id');
        } else if( !empty($branch) ) {
            $default_options['conditions']['Driver.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) ){
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
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id, $modelName = 'Driver', $field = 'Driver.id'){
        if(empty($data[$modelName])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    $field => $id,
                )
            ), array(
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data[$modelName] = $data_merge['Driver'];
            }
        }

        return $data;
    }

    function getGenerateDate ( $data ) {
        if( !empty($data['Driver']['expired_date_sim']) && $data['Driver']['expired_date_sim'] != '0000-00-00' ) {
            $mkDate = strtotime($data['Driver']['expired_date_sim']);
            $data['Driver']['tgl_expire_sim']['day'] = date('d', $mkDate);
            $data['Driver']['tgl_expire_sim']['month'] = date('m', $mkDate);
            $data['Driver']['tgl_expire_sim']['year'] = date('Y', $mkDate);
        }
        if( !empty($data['Driver']['birth_date']) && $data['Driver']['birth_date'] != '0000-00-00' ) {
            $mkDate = strtotime($data['Driver']['birth_date']);
            $data['Driver']['tgl_lahir']['day'] = date('d', $mkDate);
            $data['Driver']['tgl_lahir']['month'] = date('m', $mkDate);
            $data['Driver']['tgl_lahir']['year'] = date('Y', $mkDate);
        }
        if( !empty($data['Driver']['join_date']) && $data['Driver']['join_date'] != '0000-00-00' ) {
            $mkDate = strtotime($data['Driver']['join_date']);
            $data['Driver']['tgl_penerimaan']['day'] = date('d', $mkDate);
            $data['Driver']['tgl_penerimaan']['month'] = date('m', $mkDate);
            $data['Driver']['tgl_penerimaan']['year'] = date('Y', $mkDate);
        }

        return $data;
    }

    function generateNoId( $branch_id, $data_defaults ){
        $default_id = 1;
        $branch = $this->Branch->getMerge(array(), $branch_id);

        $default_driver_prefix = Common::hashEmptyField($data_defaults, 'SettingGeneral.driver_prefix', 'JKT');
        $default_driver_code_digit = Common::hashEmptyField($data_defaults, 'SettingGeneral.driver_code_digit', 4);

        $driver_prefix = Common::hashEmptyField($branch, 'Branch.driver_prefix', $default_driver_prefix);
        $driver_code_digit = Common::hashEmptyField($branch, 'Branch.driver_code_digit', $default_driver_code_digit);

        $format_id = sprintf('%s', $driver_prefix);

        $last_data = $this->getData('first', array(
            'conditions' => array(
                'Driver.no_id LIKE' => $driver_prefix.'%',
            ),
            'order' => array(
                'Driver.no_id' => 'DESC'
            ),
            'fields' => array(
                'Driver.no_id'
            )
        ), array(
            'branch' => false,
        ));

        if(!empty($last_data['Driver']['no_id'])){
            $str_arr = str_replace($driver_prefix, '', $last_data['Driver']['no_id']);
            $default_id = intval($str_arr+1);
        }
        $id = str_pad($default_id, $driver_code_digit,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function getListDriverPengganti ( $include_this_driver_id = false, $only_bind = false ) {
        $ttujs = $this->Truck->Ttuj->_callTtujOngoing(array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.driver_pengganti_id',
            ),
        ));

        if( !empty($ttujs) ) {
            $ttujs = array_filter($ttujs);
        }

        if( !empty($include_this_driver_id) ) {
            // Ambil data Driver pengganti berikut id ini
            $conditions = array(
                'OR' => array(
                    'Driver.id' => $include_this_driver_id,
                    'Driver.id NOT' => $ttujs,
                ),
            );
        } else {
            $conditions = array(
                'Driver.id NOT' => $ttujs,
                'Driver.branch_id' => Configure::read('__Site.config_branch_id'),
            );
        }

        $branch_plant_id = Configure::read('__Site.Branch.Plant.id');

        if( !empty($branch_plant_id) ) {
            $conditions['Driver.branch_id'] = $branch_plant_id;
        }

        if( empty($only_bind) ) {
            $drivers = $this->getData('list', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Driver.id', 'Driver.driver_code'
                ),
            ), array(
                'branch' => false,
            ));

            return $drivers;
        } else {
            return $conditions;
        }
    }

    function checkUniq () {
        $id = !empty($this->data['Driver']['id'])?$this->data['Driver']['id']:false;
        $no_id = !empty($this->data['Driver']['no_id'])?$this->data['Driver']['no_id']:false;
        $driver = $this->getData('count', array(
            'conditions' => array(
                'Driver.no_id' => $no_id,
                'Driver.id NOT' => $id,
            ),
        ), array(
            'branch' => false,
        ));
        
        if( !empty($driver) ) {
            return false;
        } else {
            return true;
        }
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $alias = !empty($data['named']['alias'])?$data['named']['alias']:false;
        $number = !empty($data['named']['number'])?$data['named']['number']:false;
        $phone = !empty($data['named']['phone'])?$data['named']['phone']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $no_truck = !empty($data['named']['no_truck'])?$data['named']['no_truck']:false;
        $no_id = !empty($data['named']['no_id'])?$data['named']['no_id']:false;
        $driver_id = !empty($data['named']['driver_id'])?$data['named']['driver_id']:false;
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $direction = !empty($data['named']['direction'])?$data['named']['direction']:false;

        if(!empty($name)){
            $default_options['conditions']['Driver.name LIKE'] = '%'.$name.'%';
        }
        if(!empty($alias)){
            $default_options['conditions']['Driver.alias LIKE'] = '%'.$alias.'%';
        }
        if(!empty($number)){
            $default_options['conditions']['Driver.identity_number LIKE'] = '%'.$number.'%';
        }
        if(!empty($phone)){
            $default_options['conditions']['Driver.phone LIKE'] = '%'.$phone.'%';
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['Truck.id'] = $nopol;
            } else {
                $default_options['conditions']['Truck.nopol LIKE'] = '%'.$nopol.'%';
            }

            $default_options['contain'][] = 'Truck';
        }
        if(!empty($no_truck)){
            $default_options['conditions']['Truck.id'] = null;
            $default_options['contain'][] = 'Truck';
        }
        if(!empty($no_id)){
            $default_options['conditions']['Driver.no_id LIKE'] = '%'.$no_id.'%';
        }
        if(!empty($driver_id)){
            $default_options['conditions']['Driver.no_id LIKE'] = '%'.$driver_id.'%';
        }

        if( !empty($sort) ) {
            $branch = strpos($sort, 'Branch.');
            $truck = strpos($sort, 'Truck.');
            $jenisSim = strpos($sort, 'JenisSim.');
            $driverRelation = strpos($sort, 'DriverRelation.');

            if( is_numeric($branch) ) {
                $default_options['contain'][] = 'Branch';
            }
            if( is_numeric($truck) ) {
                $default_options['contain'][] = 'Truck';
            }
            if( is_numeric($jenisSim) ) {
                $default_options['contain'][] = 'JenisSim';
            }
            if( is_numeric($jenisSim) ) {
                $default_options['contain'][] = 'DriverRelation';
            }

            $default_options['order'] = array(
                $sort => $direction,
            );
        }
        
        return $default_options;
    }
}
?>