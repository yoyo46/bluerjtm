<?php
class Employe extends AppModel {
	var $name = 'Employe';
	var $validate = array(
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
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'branch_id',
        ),
    );

    function getData($find, $options = false, $is_merge = true, $elements = array()){
        $status = isset($elements['status'])?$elements['status']:'active';
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
                'City',
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

        if(!empty($options) && $is_merge){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if(!empty($options['contain'])){
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
        } else if( !$is_merge ) {
            $default_options = $options;
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
}
?>