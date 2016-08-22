<?php
class Driver extends AppModel {
	var $name = 'Driver';

	var $validate = array(
        // 'photo' => array(
        //     'notempty' => array(
        //         'on' => 'create',
        //         'rule' => array('notempty'),
        //         'message' => 'Foto harap diisi'
        //     ),
        // ),
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
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat Rumah harap diisi'
            ),
        ),
        'city' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat Kota harap diisi'
            ),
        ),
        'provinsi' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat Provinsi harap diisi'
            ),
        ),
        'no_hp' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. HP harap diisi'
            ),
        ),
        // 'birth_date' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Lahir harap dipilih'
        //     ),
        //     'date' => array(
        //         'rule' => array('date'),
        //         'message' => 'Tgl Lahir tidak benar'
        //     ),
        // ),
        // 'tempat_lahir' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tempat Lahir harap diisi'
        //     ),
        // ),
        // 'jenis_sim_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Jenis SIM harap dipilih'
        //     ),
        // ),
        // 'no_sim' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No. SIM harap diisi'
        //     ),
        // ),
        // 'expired_date_sim' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Berakhir SIM harap dipilih'
        //     ),
        //     'date' => array(
        //         'rule' => array('date'),
        //         'message' => 'Tgl Berakhir SIM tidak benar'
        //     ),
        // ),
        'kontak_darurat_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama lengkap harap diisi'
            ),
        ),
        'kontak_darurat_no_hp' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. HP harap diisi'
            ),
        ),
        // 'driver_relation_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Hubungan kerabat harap dipilih'
        //     ),
        // ),
        // 'join_date' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl Penerimaan harap dipilih'
        //     ),
        //     'date' => array(
        //         'rule' => array('date'),
        //         'message' => 'Tgl Penerimaan tidak benar'
        //     ),
        // ),
        // 'branch_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Cabang harap dipilih'
        //     ),
        //     'numeric' => array(
        //         'rule' => array('numeric'),
        //         'message' => 'Cabang harap dipilih'
        //     ),
        // ),
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
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['driver_name'] = sprintf('CASE WHEN %s.alias = \'\' THEN %s.name ELSE CONCAT(%s.name, \' ( \', %s.alias, \' )\') END', $this->alias, $this->alias, $this->alias, $this->alias);
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

	function getData( $find, $options = false, $is_merge = true, $elements = array() ){
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
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

    function getMerge($data, $id, $modelName = 'Driver'){
        if(empty($data['Driver'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'Driver.id' => $id,
                    'Driver.is_resign' => 0,
                )
            ), true, array(
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

    function generateNoId(){
        $default_id = 1;
        $format_id = sprintf('SP-%s-%s-', date('Y'), date('m'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'Driver.no_id' => 'DESC'
            ),
            'fields' => array(
                'Driver.no_id'
            )
        ), true, array(
            'branch' => false,
        ));

        if(!empty($last_data['Driver']['no_id'])){
            $str_arr = explode('-', $last_data['Driver']['no_id']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 4,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function getListDriverPengganti ( $include_this_driver_id = false, $only_bind = false ) {
        $ttujs = $this->Truck->Ttuj->_callTtujOngoing(array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.driver_pengganti_id',
            ),
        ));

        if( !empty($include_this_driver_id) ) {
            // Ambil data Driver pengganti berikut id ini
            $conditions = array(
                'OR' => array(
                    'Driver.id' => $include_this_driver_id,
                    'Driver.id NOT' => $ttujs,
                ),
                // 'AND' => array(
                //     'OR' => array(
                //         'Driver.id' => $include_this_driver_id,
                //         'Driver.branch_id' => Configure::read('__Site.config_branch_id'),
                //     ),
                // ),
            );
        } else {
            $conditions = array(
                'Driver.id NOT' => $ttujs,
                'Driver.branch_id' => Configure::read('__Site.config_branch_id'),
            );
        }

        $branch_plant_id = Configure::read('__Site.Branch.Plant.id');
        $conditions['Truck.id'] = NULL;

        if( !empty($branch_plant_id) ) {
            $conditions['Driver.branch_id'] = $branch_plant_id;
        }

        if( empty($only_bind) ) {
            $drivers = $this->getData('list', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Driver.id', 'Driver.driver_name'
                ),
                'contain' => array(
                    'Truck',
                ),
            ), true, array(
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
        ), true, array(
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
        
        return $default_options;
    }
}
?>