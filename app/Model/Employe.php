<?php
class Employe extends AppModel {
	var $name = 'Employe';
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
        'first_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nama depan harap diisi'
            ),
        ),
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'gender_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis kelamin harap dipilih'
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
                'message' => 'No. Telepon harap diisi'
            ),
        ),
        'employe_position_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Posisi Karyawan harap diisi'
            ),
        ),
        'birthdate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal lahir harap diisi'
            ),
        ),
	);
    var $hasOne = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'employe_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['full_name'] = 'CONCAT(Employe.first_name, " ", Employe.last_name)';
        $this->virtualFields['name'] ='CONCAT(Employe.first_name, " ", Employe.last_name)';
    }

    var $belongsTo = array(
        'EmployePosition' => array(
            'className' => 'EmployePosition',
            'foreignKey' => 'employe_position_id',
        ),
        // 'City' => array(
        //     'className' => 'City',
        //     'foreignKey' => 'branch_id',
        // ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );

    function checkUniq () {
        $id = !empty($this->data['Employe']['id'])?$this->data['Employe']['id']:false;
        $no_id = !empty($this->data['Employe']['no_id'])?$this->data['Employe']['no_id']:false;
        $value = $this->getData('count', array(
            'conditions' => array(
                'Employe.no_id' => $no_id,
                'Employe.id NOT' => $id,
            ),
        ), array(
            'branch' => false,
        ));
        
        if( !empty($value) ) {
            return false;
        } else {
            return true;
        }
    }

    function generateNoId(){
        $default_id = 1;
        $format_id = sprintf('STAFF-%s-%s-', date('Y'), date('m'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'Employe.no_id' => 'DESC'
            ),
            'fields' => array(
                'Employe.no_id'
            )
        ), array(
            'branch' => false,
        ));

        if(!empty($last_data['Employe']['no_id'])){
            $str_arr = explode('-', $last_data['Employe']['no_id']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 4,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function getData($find, $options = false, $elements = array()){
        $status = isset($elements['status'])?$elements['status']:'active';
        $position = isset($elements['position'])?$elements['position']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $default_options = array(
            'conditions'=> array(
                'Employe.status' => 1,
            ),
            'order'=> array(
                'Employe.status' => 'DESC',
                'Employe.full_name' => 'ASC',
            ),
            'contain' => array(
                'EmployePosition',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Employe.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Employe.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Employe.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['contain'][] = 'Branch';
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( !empty($position) ) {
            $default_options['conditions']['EmployePosition.name'] = 'mekanik';
            $default_options['contain'] = 'EmployePosition';
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        if(empty($data['Employe'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'Employe.id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getListByPosition( $position_id ){
        return $this->getData('list', array(
            'conditions' => array(
                'Employe.employe_position_id' => $position_id
            ),
            'fields' => array(
                'Employe.id', 'Employe.id',
            ),
        ));
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $employe_position_id = !empty($data['named']['employe_position_id'])?$data['named']['employe_position_id']:false;
        $no_id = !empty($data['named']['no_id'])?$data['named']['no_id']:false;

        if(!empty($name)){
            $default_options['conditions']['Employe.full_name LIKE'] = '%'.$name.'%';
        }
        if(!empty($employe_position_id)){
            $default_options['conditions']['Employe.employe_position_id'] = $employe_position_id;
        }
        if(!empty($no_id)){
            $default_options['conditions']['Employe.no_id LIKE'] = '%'.$no_id.'%';
        }
        
        return $default_options;
    }
}
?>