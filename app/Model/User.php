<?php
class User extends AppModel {
	var $name = 'User';
    var $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
        ),
        // 'City' => array(
        //     'className' => 'City',
        //     'foreignKey' => 'branch_id',
        //     'conditions' => array(
        //         'City.is_branch' => 1,
        //     ),
        // ),
        'Employe' => array(
            'className' => 'Employe',
            'foreignKey' => 'employe_id',
        ),
    );
    
    var $validate = array(
        // 'branch_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Cabang harap dipilih'
        //     ),
        // ),
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Karyawan harap dipilih'
            ),
        ),
        'username' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'username harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Username sudah tersedia sebelumnya, mohon masukkan username lain.'
            ),
        ),
        'group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup user harap dipilih'
            ),
        ),
        'email' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'email harap diisi'
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'format email tidak valid'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Email sudah tersedia sebelumnya, mohon masukkan email lain.'
            ),
        ),
        'password' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Password harap diisi',
            ),
            'minLength' => array(
                'rule' => array('minLength', 6),
                'message' => 'Panjang password minimal 6 karakter',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 64),
                'message' => 'Panjang password maksimal 64 karakter',
            ),
        ),
        'new_password' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'on' => 'update',
                'message' => 'Password baru harap diisi',
            ),
            'minLength' => array(
                'rule' => array('minLength', 6),
                'on' => 'update',
                'message' => 'Panjang password baru minimal 6 karakter',
            ),
        ),
        'new_password_confirmation' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Konfirmasi password baru harap diisi',
            ),
            'notMatchNew' => array(
                'rule' => array('matchNewPasswords'),
                'on' => 'update',
                'message' => 'Konfirmasi password anda tidak sesuai',
            ),
        ),
    );
    
    /**
    *   @param array $data['new_password_confirmation'] - password baru
    *   @return boolean true or false
    */
    function matchNewPasswords($data) {
        if($this->data['User']['new_password']) {
            if($this->data['User']['new_password'] == $data['new_password_confirmation']) {
                return true;
            }
            return false; 
        } else {
            return true;
        }
    }

    function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
        return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }

    function getData($find, $options = false, $is_merge = true, $elements = array()){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'User.status' => 'DESC',
                'Employe.full_name' => 'ASC',
            ),
            'contain' => array(
                'Employe',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['User.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['User.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['User.status'] = 1;
                break;
        }

        if(!empty($options) && $is_merge){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
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

    function matchPasswords($data) {
        if($data['password_confirmation']) {
            if($this->data['User']['password'] == $data['password_confirmation']) {
                return true;
            }
            return false; 
        } else {
            return true;
        }
    }

    function getMerge( $data, $id, $with_contain = false ){
        if(empty($data['User'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'User.id' => $id
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