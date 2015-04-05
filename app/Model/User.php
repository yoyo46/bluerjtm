<?php
class User extends AppModel {
	var $name = 'User';
    var $actsAs = array('Acl' => array('type' => 'requester'));
    var $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );
    
    var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'first_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nama depan harap diisi'
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
                'message' => 'group harap dipilih'
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
        'phone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Telepon harap diisi'
            ),
        ),
        'gender' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'jenis kelamin harap diisi'
            ),
        ),
        'birthdate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal lahir harap diisi'
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
        'password_confirmation' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Konfirmasi password harap diisi',
            ),
            'notMatch' => array(
                'rule' => array('matchPasswords'),
                'message' => 'Konfirmasi password anda tidak sesuai',
            ),
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['full_name'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
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

    function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'User.status' => 'DESC'
            ),
            'contain' => array(),
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
            $data_merge = $this->find('first', array(
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