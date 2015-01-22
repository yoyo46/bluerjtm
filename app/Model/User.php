<?php
class User extends AppModel {
	var $name = 'User';
    var $actsAs = array('Acl' => array('type' => 'requester'));
    var $belongsTo = array('Group');
    
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
                'required' => 'create',
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
    );

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
                'status' => 'DESC'
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

}
?>